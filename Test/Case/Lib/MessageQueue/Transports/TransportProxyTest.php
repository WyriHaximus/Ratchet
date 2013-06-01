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
App::uses('RatchetMessageQueueDummyCommand', 'TestRatchet.Lib/MessageQueue/Command');

class TransportProxyTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        
        $this->_pluginPath = App::pluginPath('Ratchet');
        App::build(array(
            'Plugin' => array($this->_pluginPath . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS )
        ));
        CakePlugin::load('TestRatchet');
        
        Configure::write('Ratchet.Queue', array(
            'transporter' => 'TestRatchet.DummyTransport',
            'configuration' => array(
                'server' => 'tcp://127.0.0.1:13001',
            ),
        ));
        
        $this->TransportProxy = TransportProxy::instance();
    }
    
    public function tearDown() {
        parent::tearDown();
        
        unset($this->TransportProxy);
        CakePlugin::unload('TestRatchet');
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