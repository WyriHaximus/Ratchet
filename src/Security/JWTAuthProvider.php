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

use Cake\Core\Configure;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Thruway\Authentication\AbstractAuthProviderClient;
use function React\Promise\resolve;
use Lcobucci\JWT\Parser;

/**
 * Class WampCraAuthProvider
 *
 * @package Thruway\Authentication
 */
final class JWTAuthProvider extends AbstractAuthProviderClient
{
    private $activeRealms = [];

    public function __construct(array $authRealms, LoopInterface $loop = null)
    {
        parent::__construct($authRealms, $loop);

        $realmSalt = Configure::read('WyriHaximus.Ratchet.realm_salt');
        $authKeySalt = Configure::read('WyriHaximus.Ratchet.realm_auth_key_salt');
        foreach (Configure::read('WyriHaximus.Ratchet.realms') as $realm => $options) {
            if (!isset($options['auth'])) {
                continue;
            }

            if (!$options['auth']) {
                continue;
            }

            if (!isset($options['auth_key'])) {
                continue;
            }

            if (empty($options['auth_key'])) {
                continue;
            }

            $hash = hash('sha512', $realmSalt . $realm . $realmSalt);
            $this->activeRealms[$hash] = $authKeySalt . $options['auth_key'] . $authKeySalt;
        }
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return 'jwt';
    }

    /**
     * Process authenticate
     *
     * @param mixed $signature
     * @param mixed $extra
     * @return PromiseInterface
     */
    public function processAuthenticate($signature, $extra = null)
    {
        $this->getRealm();
        $token = (new Parser())->parse($signature);

        $iss = $token->getClaim('iss');
        $iss = base64_decode($iss);
        if (!isset($this->activeRealms[$iss])) {
            return resolve(["FAILURE"]);
        }

        if ($token->verify(new Sha256(), $this->activeRealms[$iss])) {
            return resolve(["SUCCESS", ['authId' => $token->getClaim('authId')]]);
        }

        return resolve(["FAILURE"]);
    }
}
