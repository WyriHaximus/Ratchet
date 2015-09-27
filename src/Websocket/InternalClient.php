<?php
namespace WyriHaximus\Ratchet\Websocket;

use Cake\Event\EventManager;
use Thruway\Peer\Client;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;

class InternalClient extends Client
{
    /**
     * This is meant to be overridden so that the client can do its
     * thing
     *
     * @param \Thruway\ClientSession $session
     * @param \Thruway\Transport\TransportInterface $transport
     */
    public function onSessionStart($session, $transport)
    {
        EventManager::instance()->dispatch(OnSessionStartEvent::create($this->getRealm(), $session, $transport));
    }

    public function onSessionEnd($session)
    {
        EventManager::instance()->dispatch(OnSessionStartEvent::create($this->getRealm(), $session));
    }
}
