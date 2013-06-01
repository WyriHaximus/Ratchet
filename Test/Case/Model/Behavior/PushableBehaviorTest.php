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
    public function addTimer($timeout, $callback) {
        call_user_func($callback);
    }
}

class PushableBehaviorEventSubjectTestImposer {
    
    public function __construct($callbacks) {
        $this->callbacks = $callbacks;
    }
    
    public function getLoop() {
        return new PushableBehaviorTestLoop();
    }
    
    public function getTopics() {
        return array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new PushableBehaviorTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new PushableBehaviorTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new PushableBehaviorTestEvent($this->callbacks['refetch']),
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
    
    public $expectedData = array(
        'PushableModel' => array(
            'id' => 2,
            'url' => 'http://arstechnica.com/',
            'title' => 'Ars Technica',
            'slug' => 'arstechnica',
        ),
    );
    
    public $expectedDataUpdated = array(
        'PushableModel' => array(
            'id' => 1,
            'url' => 'http://tweakers.net/',
            'title' => 'Tweakers',
            'slug' => 'tweakers',
        ),
    );
    
    public $expectedDataRefetch = array(
        'PushableModel' => array(
            'id' => 1,
            'url' => 'http://www.tweakers.net/',
            'title' => 'Tweakers',
            'slug' => 'tweakers',
        ),
    );
    
    public $callbacks = array();
    
    public function setUp() {
        parent::setUp();
        
        $this->callbacks = array(
            'created' => function() {},
            'updated' => function() {},
            'refetch' => function() {},
        );
        
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
        
        $this->PushableModel = ClassRegistry::init('Ratchet.PushableModel');
        $this->TransportProxy = TransportProxy::instance();
        $this->PushableBehaviorTestCapsule = new PushableBehaviorTestCapsule();
        
        $this->PushableModel->Behaviors->load('Ratchet.Pushable', array(
            'events' => array(
                array(
                    'eventName' => 'Ratchet.Pushable.created',
                    'created' => true,
                ),
                array(
                    'eventName' => 'Ratchet.Pushable.updated',
                ),
                array(
                    'eventName' => 'Ratchet.Pushable.refetch',
                    'refetch' => true,
                ),
            ),
        ));
    }
    
    public function tearDown() {
        $this->PushableModel->Behaviors->unload('Ratchet.Pushable');
        
        unset($this->PushableModel);
        unset($this->TransportProxy);
        unset($this->PushableBehaviorTestCapsule);
        
        CakePlugin::unload('TestRatchet');
        
        parent::tearDown();
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
    
    public function testAfterSaveCreated() {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['created'] = function($resultData) use ($that, &$callbackFired) {
            $that->assertEqual($resultData, $that->expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new PushableBehaviorEventSubjectTestImposer($this->callbacks));
        
        $this->PushableModel->create();
        $this->PushableModel->save($this->expectedData);
        
        $this->assertTrue($callbackFired);
    }
    
    public function testAfterSaveUpdated() {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['updated'] = function($resultData) use ($that, &$callbackFired) {
            $that->assertEqual($resultData, $that->expectedDataUpdated);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new PushableBehaviorEventSubjectTestImposer($this->callbacks));
        
        $this->PushableModel->id = 1;
        $this->PushableModel->save($this->expectedDataUpdated);
        
        $this->assertTrue($callbackFired);
    }
    
    public function testAfterSaveRefetched() {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['refetch'] = function($resultData) use ($that, &$callbackFired) {
            $that->assertEqual($resultData, $that->expectedDataRefetch);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new PushableBehaviorEventSubjectTestImposer($this->callbacks));
        
        $this->PushableModel->id = 1;
        $this->PushableModel->save($this->expectedDataRefetch);
        
        $this->assertTrue($callbackFired);
    }
    
}