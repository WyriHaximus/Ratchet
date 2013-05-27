<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('PushableBehavior', 'Ratchet.Model/Behavior');

class PushableBehaviorTestCapsule extends PushableBehavior {
    public function afterSavePrepareEventNameTest($eventName, $id, $data) {
        return parent::afterSavePrepareEventName($eventName, $id, $data);
    }
}

class PushableBehaviorTest extends CakeTestCase {
    
    public function setUp() {
        parent::setUp();
        
        $this->PushableBehaviorTestCapsule = new PushableBehaviorTestCapsule();
    }
    
    public function tearDown() {
        parent::tearDown();
        
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
    
}