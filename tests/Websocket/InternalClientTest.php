<?php

namespace WyriHaximus\Ratchet\Tests\Websocket;

use Cake\Event\EventManager;
use Phake;
use WyriHaximus\Ratchet\Event\OnSessionEndEvent;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;
use WyriHaximus\Ratchet\Websocket\InternalClient;

class InternalClientTest extends \PHPUnit_Framework_TestCase
{
    public function testOnSessionStart()
    {
        $callbackFired = false;
        $func = function ($event) use (&$callbackFired) {
            $this->assertInstanceOf('WyriHaximus\Ratchet\Event\OnSessionStartEvent', $event);
            $callbackFired = true;
        };

        $client = new InternalClient('test1');

        EventManager::instance()->on(OnSessionStartEvent::EVENT, $func);
        $client->onSessionStart(Phake::mock('Thruway\ClientSession'), Phake::mock('Thruway\Transport\TransportInterface'));
        EventManager::instance()->off(OnSessionStartEvent::EVENT, $func);

        $this->assertTrue($callbackFired);
    }

    public function testOnSessionEnd()
    {
        $callbackFired = false;
        $func = function ($event) use (&$callbackFired) {
            $this->assertInstanceOf('WyriHaximus\Ratchet\Event\OnSessionEndEvent', $event);
            $callbackFired = true;
        };

        $client = new InternalClient('test1');

        EventManager::instance()->on(OnSessionEndEvent::EVENT, $func);
        $client->onSessionEnd(Phake::mock('Thruway\ClientSession'));
        EventManager::instance()->off(OnSessionEndEvent::EVENT, $func);

        $this->assertTrue($callbackFired);
    }
}
