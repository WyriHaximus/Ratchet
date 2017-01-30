<?php

/*
 * This file is part of Ratchet.
 *
 ** (c) 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Security;

use Thruway\Authentication\AbstractAuthProviderClient;
use Thruway\Message\HelloMessage;
use Thruway\Message\Message;

/**
 * Class WampCraAuthProvider
 *
 * @package Thruway\Authentication
 */
class WampCraAuthProvider extends AbstractAuthProviderClient
{
    /**
     * @return string
     */
    public function getMethodName()
    {
        return 'wampcra';
    }

    /**
     * The arguments given by the server are the actual hello message ($args[0])
     * and some session information ($args[1])
     *
     * The session information is an associative array that contains the sessionId and realm
     *
     * @param array $args
     * @return array
     */
    public function processHello(array $args)
    {
        debug($args);
        $helloMsg    = array_shift($args);
        $sessionInfo = array_shift($args);

        if (!is_array($helloMsg)) {
            return ["ERROR"];
        }

        if (!is_object($sessionInfo)) {
            return ["ERROR"];
        }

        $helloMsg = Message::createMessageFromArray($helloMsg);

        if (!$helloMsg instanceof HelloMessage
            || !$sessionInfo
            || !isset($helloMsg->getDetails()->authid)
            || !$this->getUserDb() instanceof WampCraUserDbInterface
        ) {
            return ["ERROR"];
        }

        $authid = $helloMsg->getDetails()->authid;
        $user   = $this->getUserDb()->get($authid);

        if (!$user) {
            return ["FAILURE"];
        }

        // create a challenge
        $nonce        = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
        $authRole     = "user";
        $authMethod   = "wampcra";
        $authProvider = "userdb";
        $now          = new \DateTime();
        $timeStamp    = $now->format($now::ISO8601);
        if (!isset($sessionInfo->sessionId)) {
            return ["ERROR"];
        }
        $sessionId    = $sessionInfo->sessionId;

        $challenge = [
            "authid"       => $authid,
            "authrole"     => $authRole,
            "authprovider" => $authProvider,
            "authmethod"   => $authMethod,
            "nonce"        => $nonce,
            "timestamp"    => $timeStamp,
            "session"      => $sessionId
        ];

        $serializedChallenge = json_encode($challenge);

        $challengeDetails = [
            "challenge"        => $serializedChallenge,
            "challenge_method" => $this->getMethodName()
        ];

        if ($user['salt'] !== null) {
            // we are using salty password
            $saltInfo = [
                "salt"       => $user['salt'],
                "keylen"     => 32,
                "iterations" => 1000
            ];

            $challengeDetails = array_merge($challengeDetails, $saltInfo);
        }

        return ["CHALLENGE", (object)$challengeDetails];

    }

    /**
     * Process authenticate
     *
     * @param mixed $signature
     * @param mixed $extra
     * @return array
     */
    public function processAuthenticate($signature, $extra = null)
    {
debug([$signature, $extra]);
        $challenge = $this->getChallengeFromExtra($extra);

        if (!$challenge
            || !isset($challenge->authid)
            || !$this->getUserDb() instanceof WampCraUserDbInterface
        ) {
            return ["FAILURE"];
        }

        $authid = $challenge->authid;
        $user   = $this->getUserDb()->get($authid);

        if (!$user) {
            return ["FAILURE"];
        }

        $keyToUse = $user['key'];
        $token    = base64_encode(hash_hmac('sha256', json_encode($challenge), $keyToUse, true));

        if ($token != $signature) {
            return ["FAILURE"];
        }

        $authDetails = [
            "authmethod"   => "wampcra",
            "authrole"     => "user",
            "authid"       => $challenge->authid,
            "authprovider" => $challenge->authprovider
        ];

        return ["SUCCESS", $authDetails];

    }
}
