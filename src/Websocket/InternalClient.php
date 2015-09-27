<?php
namespace WyriHaximus\Ratchet\Websocket;

use Cake\Event\EventManager;
use Thruway\Peer\Client;
use WyriHaximus\Ratchet\Event\OnSessionEndEvent;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;

class InternalClient extends Client
{
    /**
     * @param \Thruway\ClientSession $session
     * @param \Thruway\Transport\TransportInterface $transport
     */
    public function onSessionStart($session, $transport)
    {
        EventManager::instance()->dispatch(OnSessionStartEvent::create($this->getRealm(), $session, $transport));
    }

    /**
     * @param \Thruway\ClientSession $session
     */
    public function onSessionEnd($session)
    {
        EventManager::instance()->dispatch(OnSessionEndEvent::create($this->getRealm(), $session));
    }
}
