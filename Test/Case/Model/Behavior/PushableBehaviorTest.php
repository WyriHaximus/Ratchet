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
App::uses('PushableBehavior', 'Ratchet.Model/Behavior');

class PushableBehaviorTestEvent {
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    public function broadcast($data) {
        call_user_func($this->callback, $data);
    }
}

class PushableBehaviorTestLoop {
    public function addTimer($seconds, $callback) {
        call_user_func($callback);
    }
}

class PushableBehaviorEventSubjectTestImposer {
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    public function getLoop() {
        return new PushableBehaviorTestLoop();
    }
    
    public function getTopics() {
        return array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new PushableBehaviorTestEvent($this->callback),
        );
    }
}

class PushableBehaviorTestCapsule extends PushableBehavior {
    public function afterSavePrepareEventNameTest($eventName, $id, $data) {
        return parent::afterSavePrepareEventName($eventName, $id, $data);
    }
}

class PushableBehaviorTest extends CakeTestCase {
    
    public $fixtures = array(
        'plugin.ratchet.pushable_model',
    );
    
    public function setUp() {
        parent::setUp();
        
        Configure::write('Ratchet.Queue', array(
            'transporter' => 'Ratchet.DummyTransport',
            'configuration' => array(
                'server' => 'tcp://127.0.0.1:13001',
            ),
        ));
        
        $this->PushableModel = ClassRegistry::init('Ratchet.PushableModel');
        $this->TransportProxy = TransportProxy::instance();
        $this->PushableBehaviorTestCapsule = new PushableBehaviorTestCapsule();
    }
    
    public function tearDown() {
        parent::tearDown();
        
        unset($this->PushableModel);
        unset($this->TransportProxy);
        unset($this->PushableBehaviorTestCapsule);
    }
    
    public function testAfterSavePrepareEventNameId() {
        $result = $this->PushableBehaviorTestCapsule->afterSavePrepareEventNameTest('Ratchet.Model.test.{id}', 1, array());
        $this->assertEqual($result, 'Ratchet.Model.test.1');
    }
    
    public function testAfterSavePrepareEventNameData() {
        $result = $this->PushableBehaviorTestCapsule->afterSavePrepareEventNameTest('Ratchet.Model.test.{uuid}', 1, array(
            'uuid' => 'kads-asdef-awsefg-234213',
        ));
        $this->assertEqual($result, 'Ratchet.Model.test.kads-asdef-awsefg-234213');
    }
    
    public function testAfterSave() {
        
        $callbackFired = false;
        $that = $this;
        $this->TransportProxy->getTransport()->setEventSubject(new PushableBehaviorEventSubjectTestImposer(function($data) use ($that, &$callbackFired) {
            $that->assertEqual($data, array(
                'PushableModel' => array(
                    'id' => (int) 2,
                    'url' => 'http://arstechnica.com/',
                    'title' => 'Ars Technica',
                    'slug' => 'arstechnica',
                ),
            ));
            $callbackFired = true;
        }));
        
        $this->PushableModel->Behaviors->load('Ratchet.Pushable', array(
            'events' => array(
                array(
                    'eventName' => 'Ratchet.Pushable.created',
                    'created' => true,
                ),
            ),
        ));
        $this->PushableModel->create();
        $this->PushableModel->save(array(
            'id' => 2,
            'url' => 'http://arstechnica.com/',
            'title' => 'Ars Technica',
            'slug' => 'arstechnica',
        ));
        
        $this->PushableModel->Behaviors->unload('Ratchet.Pushable');
        
        $this->assertTrue($callbackFired);
    }
    
}