<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueGetUptimeCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueGetUptimeCommandTest extends AbstractCommandTest {
    
    const EXECUTE_RESULT = 325412; // Just some random number
    
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();
        
        $this->hibernateListeners('Rachet.WebsocketServer.getUptime');
        $this->eventCallback = function($event) {
            $event->result = RatchetMessageQueueGetUptimeCommandTest::EXECUTE_RESULT;
        };
        CakeEventManager::instance()->attach($this->eventCallback, 'Rachet.WebsocketServer.getUptime');
        
        $this->Command = new RatchetMessageQueueGetUptimeCommand();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        $this->wakeupListeners('Rachet.WebsocketServer.getUptime');
        
        parent::tearDown();
    }
    
    public function testExecute() {
        $callbackFired = true;
        $deferred = new \React\Promise\Deferred();
        $that = $this;
        $deferred->promise()->then(function($objectStorage) use ($that, &$callbackFired) {
            $that->assertInstanceOf('SplObjectStorage', $objectStorage);
            $that->assertSame(1, $objectStorage->count());
            $that->assertInstanceOf('\PhuninNode\Value', $objectStorage->current());
            $that->assertSame('uptime', $objectStorage->current()->getKey());
            $that->assertSame(RatchetMessageQueueGetUptimeCommandTest::EXECUTE_RESULT, $objectStorage->current()->getValue());
            $callbackFired = true;
        });
        $this->Command->setDeferedResolver($deferred->resolver());
        parent::testExecute();
        
        $this->assertTrue($callbackFired);
    }
    
}