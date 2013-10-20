<?php

/*
* This file is part of Ratchet for CakePHP.
*
** (c) 2012 - 2013 Cees-Jan Kiewiet
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

abstract class CakeRatchetTestCase extends CakeTestCase {

	private $__preservedEventListeners = array();

	protected function _hibernateListeners($eventKey) {
		$this->__preservedEventListeners[$eventKey] = CakeEventManager::instance()->listeners($eventKey);

		foreach ($this->__preservedEventListeners[$eventKey] as $eventListener) {
			CakeEventManager::instance()->detach($eventListener['callable'], $eventKey);
		}
	}

	protected function _wakeupListeners($eventKey) {
		if (isset($this->__preservedEventListeners[$eventKey])) {
			return;
		}

		foreach ($this->__preservedEventListeners[$eventKey] as $eventListener) {
			CakeEventManager::instance()->attach($eventListener['callable'], $eventKey, array(
				'passParams' => $eventListener['passParams'],
			));
		}

		$this->__preservedEventListeners = array();
	}

}
