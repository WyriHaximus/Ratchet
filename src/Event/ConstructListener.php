<?php

/*
 * This file is part of Ratchet.
 *
 ** (c) 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Event;

use Cake\Core\Configure;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use WyriHaximus\Ratchet\Websocket\InternalClient;

class ConstructListener implements EventListenerInterface
{
    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            ConstructEvent::EVENT => 'construct',
        ];
    }

    /**
     * @param ConstructEvent $event
     */
    public function construct(ConstructEvent $event)
    {
        $router = new Router($event->getLoop());

        foreach (Configure::read('WyriHaximus.Ratchet.realms') as $realm => $config) {
            $router->addInternalClient(new InternalClient($realm, $event->getLoop()));
        }
        $router->addTransportProvider(
            new RatchetTransportProvider(
                Configure::read('WyriHaximus.Ratchet.internal.address'),
                Configure::read('WyriHaximus.Ratchet.internal.port')
            )
        );
        //$router->getRealmManager()->setDefaultAuthorizationManager(new AllPermissiveAuthorizationManager());

        EventManager::instance()->dispatch(WebsocketStartEvent::create($event->getLoop()));

        $router->start(false);
    }
}