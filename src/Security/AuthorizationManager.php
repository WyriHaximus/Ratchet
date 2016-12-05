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

use Thruway\Event\MessageEvent;
use Thruway\Event\NewRealmEvent;
use Thruway\Module\RealmModuleInterface;
use Thruway\Module\RouterModuleClient;

class AuthorizationManager extends RouterModuleClient implements RealmModuleInterface
{
    /**
     * Listen for Router events.
     * Required to add the authorization module to the realm
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'new_realm' => ['handleNewRealm', 10]
        ];
    }

    /**
     * @param NewRealmEvent $newRealmEvent
     */
    public function handleNewRealm(NewRealmEvent $newRealmEvent)
    {
        $realm = $newRealmEvent->realm;

        if ($realm->getRealmName() === $this->getRealm()) {
            $realm->addModule($this);
        }
    }

    /**
     * @return array
     */
    public function getSubscribedRealmEvents()
    {
        return [
            'PublishMessageEvent'   => ['authorize', 100],
            'SubscribeMessageEvent' => ['authorize', 100],
            'RegisterMessageEvent'  => ['authorize', 100],
            'CallMessageEvent'      => ['authorize', 100],
        ];
    }

    /**
     * @param MessageEvent $msg
     */
    public function authorize(MessageEvent $msg)
    {
        /**
         * WIP auth handling
         */
    }
}
