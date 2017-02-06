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
use React\Promise\PromiseInterface;
use Thruway\Authentication\AbstractAuthProviderClient;
use function React\Promise\resolve;

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
        $JWT = JWT::decode($signature, $this->key, ['HS256']);

        if (isset($JWT->authId)) {
            return resolve(["SUCCESS", ['authId' => $JWT->authId]]);
        }

        return resolve(["FAILURE"]);
    }
}
