<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueGetMemoryUsageCommand', 'Ratchet.Lib/MessageQueue/Command');
App::uses('AbstractCommandTest', 'Ratchet.Test/Case/Lib/MessageQueue/Command');

class RatchetMessageQueueGetMemoryUsageCommandTest extends AbstractCommandTest {
    
    const EXECUTE_RESULT_NORMAL = 23479; // Just some random number
    const EXECUTE_RESULT_PEAK = 7894123; // Just some other random number
    
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();
        
        $this->hibernateListeners('Rachet.WebsocketServer.getMemoryUsage');
        $this->eventCallback = function($event) {
            $event->result = array(
                'memory_usage' => RatchetMessageQueueGetMemoryUsageCommandTest::EXECUTE_RESULT_NORMAL,
                'memory_peak_usage' => RatchetMessageQueueGetMemoryUsageCommandTest::EXECUTE_RESULT_PEAK,
            );
        };
        CakeEventManager::instance()->attach($this->eventCallback, 'Rachet.WebsocketServer.getMemoryUsage');
        
        $this->Command = new RatchetMessageQueueGetMemoryUsageCommand();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        $this->wakeupListeners('Rachet.WebsocketServer.getMemoryUsage');
        
        parent::tearDown();
    }
    
    public function testExecute() {
        $callbackFired = true;
        $deferred = new \React\Promise\Deferred();
        $that = $this;
        $deferred->promise()->then(function($objectStorage) use ($that, &$callbackFired) {
            $that->assertInstanceOf('SplObjectStorage', $objectStorage);
            $that->assertSame(2, $objectStorage->count());
            
            $that->assertInstanceOf('\PhuninNode\Value', $objectStorage->current());
            $that->assertSame('memory_usage', $objectStorage->current()->getKey());
            $that->assertSame(RatchetMessageQueueGetMemoryUsageCommandTest::EXECUTE_RESULT_NORMAL, $objectStorage->current()->getValue());
            
            $objectStorage->next();
            
            $that->assertInstanceOf('\PhuninNode\Value', $objectStorage->current());
            $that->assertSame('memory_peak_usage', $objectStorage->current()->getKey());
            $that->assertSame(RatchetMessageQueueGetMemoryUsageCommandTest::EXECUTE_RESULT_PEAK, $objectStorage->current()->getValue());
            
            $callbackFired = true;
        });
        $this->Command->setDeferedResolver($deferred->resolver());
        parent::testExecute();
        
        $this->assertTrue($callbackFired);
    }
    
}