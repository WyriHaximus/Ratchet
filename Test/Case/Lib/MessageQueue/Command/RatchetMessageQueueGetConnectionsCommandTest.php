<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueGetConnectionsCommand', 'Ratchet.Lib/MessageQueue/Command');
App::uses('AbstractCommandTest', 'Ratchet.Test/Case/Lib/MessageQueue/Command');

class RatchetMessageQueueGetConnectionsCommandTest extends AbstractCommandTest {
    
    const EXECUTE_RESULT_USERS = 234; // Just some random number
    const EXECUTE_RESULT_GUESTS = 64; // Just some other random number
    
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();
        
        $this->hibernateListeners('Rachet.WebsocketServer.getConnectionCounts');
        $this->eventCallback = function($event) {
            $event->result = array(
                'users' => RatchetMessageQueueGetConnectionsCommandTest::EXECUTE_RESULT_USERS,
                'guests' => RatchetMessageQueueGetConnectionsCommandTest::EXECUTE_RESULT_GUESTS,
            );
        };
        CakeEventManager::instance()->attach($this->eventCallback, 'Rachet.WebsocketServer.getConnectionCounts');
        
        $this->Command = new RatchetMessageQueueGetConnectionsCommand();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        $this->wakeupListeners('Rachet.WebsocketServer.getConnectionCounts');
        
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
            $that->assertSame('users', $objectStorage->current()->getKey());
            $that->assertSame(RatchetMessageQueueGetConnectionsCommandTest::EXECUTE_RESULT_USERS, $objectStorage->current()->getValue());
            
            $objectStorage->next();
            
            $that->assertInstanceOf('\PhuninNode\Value', $objectStorage->current());
            $that->assertSame('guests', $objectStorage->current()->getKey());
            $that->assertSame(RatchetMessageQueueGetConnectionsCommandTest::EXECUTE_RESULT_GUESTS, $objectStorage->current()->getValue());
            
            $callbackFired = true;
        });
        $this->Command->setDeferedResolver($deferred->resolver());
        parent::testExecute();
        
        $this->assertTrue($callbackFired);
    }
    
}