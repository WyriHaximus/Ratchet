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

class PushableBehaviorTestCapsule extends PushableBehavior {
    public function afterSavePrepareEventNameTest($eventName, $id, $data) {
        return parent::afterSavePrepareEventName($eventName, $id, $data);
    }
}

class PushableBehaviorTest extends CakeTestCase {
    
    public $fixtures = array(
        'plugin.ratchet.pushable_model',
        'plugin.ratchet.associated_pushable_model',
        'plugin.ratchet.pushable_model_associated',
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
        
        $this->PushableModel = ClassRegistry::init('TestRatchet.PushableModel');
        $this->AssociatedPushableModel = ClassRegistry::init('TestRatchet.AssociatedPushableModel');
        $this->PushableModelAssociated = ClassRegistry::init('TestRatchet.PushableModelAssociated');
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
        
        $this->PushableModelAssociated->Behaviors->load('Ratchet.Pushable', array(
            'events' => array(
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
    
    public function providerAfterSaveCreated() {
        return array(
            array(
                array(
                    'PushableModel' => array(
                        'id' => 2,
                        'url' => 'http://arstechnica.com/',
                        'title' => 'Ars Technica',
                        'slug' => 'arstechnica',
                    ),
                )
            )
        );
    }
    /**
     * @dataProvider providerAfterSaveCreated
     */
    public function testAfterSaveCreated($expectedData) {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['created'] = function($resultData) use ($that, &$callbackFired, $expectedData) {
            $that->assertEqual($resultData, $expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new DummyTransportEventSubjectTestImposer($this->callbacks, array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new DummyTransportTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new DummyTransportTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new DummyTransportTestEvent($this->callbacks['refetch']),
        )));
        
        $this->PushableModel->create();
        $this->PushableModel->save($expectedData);
        
        $this->assertTrue($callbackFired);
    }
    
    public function providerAfterSaveUpdated() {
        return array(
            array(
                array(
                    'PushableModel' => array(
                        'id' => 1,
                        'url' => 'http://tweakers.net/',
                        'title' => 'Tweakers',
                        'slug' => 'tweakers',
                    ),
                )
            )
        );
    }
    /**
     * @dataProvider providerAfterSaveUpdated
     */
    public function testAfterSaveUpdated($expectedData) {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['updated'] = function($resultData) use ($that, &$callbackFired, $expectedData) {
            $that->assertEqual($resultData, $expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new DummyTransportEventSubjectTestImposer($this->callbacks, array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new DummyTransportTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new DummyTransportTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new DummyTransportTestEvent($this->callbacks['refetch']),
        )));
        
        $this->PushableModel->id = 1;
        $this->PushableModel->save($expectedData);
        
        $this->assertTrue($callbackFired);
    }
    
    public function providerAfterSaveRefetched() {
        return array(
            array(
                array(
                    'PushableModel' => array(
                        'id' => 1,
                        'url' => 'http://www.tweakers.net/',
                        'title' => 'Tweakers',
                        'slug' => 'tweakers',
                    ),
                )
            )
        );
    }
    /**
     * @dataProvider providerAfterSaveRefetched
     */
    public function testAfterSaveRefetched($expectedData) {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['refetch'] = function($resultData) use ($that, &$callbackFired, $expectedData) {
            $that->assertEqual($resultData, $expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new DummyTransportEventSubjectTestImposer($this->callbacks, array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new DummyTransportTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new DummyTransportTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new DummyTransportTestEvent($this->callbacks['refetch']),
        )));
        
        $this->PushableModel->id = 1;
        $this->PushableModel->save($expectedData);
        
        $this->assertTrue($callbackFired);
    }
    
    public function providerAfterSaveUpdatedAssociated() {
        return array(
            array(
                array(
                    'PushableModel' => array(
                        'id' => 1,
                        'url' => 'http://tweakers.net/',
                        'title' => 'Tweakers',
                        'slug' => 'tweakers',
                    ),
                )
            )
        );
    }
    /**
     * @dataProvider providerAfterSaveUpdatedAssociated
     */
    public function testAfterSaveUpdatedAssociated($expectedData) {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['updated'] = function($resultData) use ($that, &$callbackFired, $expectedData) {
            $that->assertEqual($resultData, $expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new DummyTransportEventSubjectTestImposer($this->callbacks, array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new DummyTransportTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new DummyTransportTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new DummyTransportTestEvent($this->callbacks['refetch']),
        )));
        
        $this->PushableModel->id = 1;
        $this->PushableModel->save($expectedData);
        
        $this->assertTrue($callbackFired);
    }
    
    public function providerAfterSaveRefetchedAssociated() {
        return array(
            array(
                array(
                    'PushableModelAssociated' => array(
                        'id' => 1,
                        'url' => 'http://www.tweakers.net/',
                        'title' => 'Tweakers',
                        'slug' => 'tweakers',
                    ),
                    'AssociatedPushableModel' => array(
                        'id' => 1,
                        'url' => 'http://tweakers.net/',
                        'title' => 'Tweakers',
                        'slug' => 'tweakers',
                        'pushable_model_associated_id' => 1,
                    ),
                )
            )
        );
    }
    /**
     * @dataProvider providerAfterSaveRefetchedAssociated
     */
    public function testAfterSaveRefetchedAssociated($expectedData) {
        $callbackFired = false;
        $that = $this;
        $this->callbacks['refetch'] = function($resultData) use ($that, &$callbackFired, $expectedData) {
            $that->assertEqual($resultData, $expectedData);
            $callbackFired = true;
        };
        $this->TransportProxy->getTransport()->setEventSubject(new DummyTransportEventSubjectTestImposer($this->callbacks, array(
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.created' => new DummyTransportTestEvent($this->callbacks['created']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.updated' => new DummyTransportTestEvent($this->callbacks['updated']),
            RatchetMessageQueueModelUpdateCommand::EVENT_PREFIX . 'Ratchet.Pushable.refetch' => new DummyTransportTestEvent($this->callbacks['refetch']),
        )));
        
        $this->PushableModelAssociated->id = 1;
        $this->PushableModelAssociated->save($expectedData['PushableModelAssociated']);
        
        $this->assertTrue($callbackFired);
    }
    
}