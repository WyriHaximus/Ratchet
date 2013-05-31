<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('TransportProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueDummyCommand', 'Ratchet.Lib/MessageQueue/Command');

class TransportProxyTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        
        Configure::write('Ratchet.Queue', array(
            'transporter' => 'Ratchet.DummyTransport',
            'configuration' => array(
                'server' => 'tcp://127.0.0.1:13001',
            ),
        ));
        
        $this->TransportProxy = TransportProxy::instance();
    }
    
    public function tearDown() {
        parent::tearDown();
        
        unset($this->TransportProxy);
    }
    
    public function testQueueMessage() {
        $command = new RatchetMessageQueueDummyCommand();
        $command->setCallback(array($this, 'dummyCommandAssertResponse'));
        $this->TransportProxy->queueMessage($command);
    }
    
    public function dummyCommandAssertResponse($response) {
        $this->assertEqual($response, 1);
    }
    
}