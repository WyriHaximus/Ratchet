<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('PhpSerializeHandler', 'Ratchet.Lib');
App::uses('CakeEvent', 'Event');
App::uses('CakeEventManager', 'Event');

abstract class AbstractCommandTest extends CakeRatchetTestCase {
    
    private $preservedEventListeners = array();
    
    /**
     * {@inheritdoc}
     */
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
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        unset($this->TransportProxy);
        unset($this->Command);
        
        CakePlugin::unload('TestRatchet');
        
        parent::tearDown();
    }
    
    public function testImplementation() {
        $classImplements = class_implements($this->Command);
        $this->assertTrue(isset($classImplements['RatchetMessageQueueCommandInterface']));
        $this->assertTrue(isset($classImplements['Serializable']));
    }
    
    public function testExtending() {
        $classImplements = class_parents($this->Command);
        $this->assertTrue(isset($classImplements['RatchetMessageQueueCommand']));
    }
    
    public function testExecute() {
        $eventSubject = new DummyTransportEventSubjectTestImposer(array(), array());
        $this->TransportProxy->getTransport()->setEventSubject($eventSubject);
        $this->TransportProxy->queueMessage($this->Command);
        return $eventSubject;
    }
    
}