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

use Firebase\JWT\JWT;
use Lcobucci\JWT\Signer\Hmac\Sha256;
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
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;
        return $this;
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
        $token = (new Parser())->parse($signature);

        if ($token->verify(new Sha256(), $this->key)) {
            return resolve(["SUCCESS", ['authId' => $token->getClaim('authId')]]);
        }

        return resolve(["FAILURE"]);
    }
}
