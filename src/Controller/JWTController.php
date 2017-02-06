<?php

namespace WyriHaximus\Ratchet\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
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
        $this->set(
            'token',
            JWT::encode(
                [
                    'authId' => $user === null ? 0 : get_in($user, ['id'], 0),
                ],
                $realms[$realm]['auth_key']
            )
        );
        $this->set('_serialize', ['token']);
    }
}
