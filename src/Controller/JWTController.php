<?php

namespace WyriHaximus\Ratchet\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use function igorw\get_in;

class JWTController extends Controller
{
    public function initialize()
    {
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Auth');
        $this->Auth->allow(['token']);
    }

    public function token()
    {
        $realm = $this->request->query('realm');
        $realms = Configure::read('WyriHaximus.Ratchet.realms');
        if (!isset($realms[$realm])) {
            throw new \InvalidArgumentException('Unknown realm');
        }
        if (!isset($realms[$realm]['auth_key'])) {
            throw new \InvalidArgumentException('Unknown realm');
        }

        $user = $this->Auth->user();

        $realmSalt = Configure::read('WyriHaximus.Ratchet.realm_salt');
        $authKeySalt = Configure::read('WyriHaximus.Ratchet.realm_auth_key_salt');
        $hashedRealm = hash('sha512', $realmSalt . $realm . $realmSalt);
        $hashedRealm = base64_encode($hashedRealm);
        $token = (new Builder())
            ->setIssuer($hashedRealm)
            ->setAudience($hashedRealm)
            ->setId(bin2hex(random_bytes(mt_rand(256, 512))), true)
            ->setIssuedAt(time())
            ->setNotBefore(time() - 13)
            ->setExpiration(time() + 13)
            ->set('authid', $user === null ? 0 : get_in($user, ['id'], 0))
            ->sign(new Sha256(), $authKeySalt . $realms[$realm]['auth_key'] . $authKeySalt)
            ->getToken();

        $this->set('token', (string)$token);
        $this->set('_serialize', ['token']);
    }
}
