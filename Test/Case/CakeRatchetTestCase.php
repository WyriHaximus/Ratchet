<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventManager', 'Event');

abstract class CakeRatchetTestCase extends CakeTestCase {

	private $__cbi = [];

    public function setUp() {
        parent::setUp();

        $this->__cbi = [];
    }

    protected function _expectedEventCalls(&$asserts, $events) {
        foreach ($events as $eventName => $event) {
            $count = count($events[$eventName]['callback']);
            for ($i = 0; $i < $count; $i++) {
                $asserts[$eventName . '_' . $i] = false;
            }
            $this->__cbi[$eventName] = 0;
            $this->eventManager->attach(function($event) use(&$events, $eventName, &$asserts) {
                $asserts[$eventName . '_' . $this->__cbi[$eventName]] = true;
                call_user_func($events[$eventName]['callback'][$this->__cbi[$eventName]], $event);
                    $this->__cbi[$eventName]++;
            }, $eventName);
        }

        return $asserts;
    }

}
