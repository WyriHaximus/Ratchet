<?php
namespace WyriHaximus\Ratchet\Websocket;

use Cake\Event\EventManager;
use Thruway\Peer\Client;
use WyriHaximus\Ratchet\Event\OnSessionEndEvent;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;

class InternalClient extends Client
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @param EventManager $eventManager
     */
    public function setEventManager(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Thruway\ClientSession $session
     * @param \Thruway\Transport\TransportInterface $transport
     */
    public function onSessionStart($session, $transport)
    {
        $this->eventManager->dispatch(OnSessionStartEvent::create($this->getRealm(), $session, $transport));
    }

    /**
     * @param \Thruway\ClientSession $session
     */
    public function onSessionEnd($session)
    {
        $this->eventManager->dispatch(OnSessionEndEvent::create($this->getRealm(), $session));
    }
}
