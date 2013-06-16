<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CakeRatchetTestCase extends CakeTestCase {
    
    private $preservedEventListeners = array();
    
    protected function hibernateListeners($eventKey) {
        $this->preservedEventListeners = CakeEventManager::instance()->listeners($eventKey);
        
        foreach ($this->preservedEventListeners as $eventListener) {
            CakeEventManager::instance()->detach($eventListener['callable'], $eventKey);
        }
    }
    
    protected function wakeupListeners($eventKey) {
        foreach ($this->preservedEventListeners as $eventListener) {
            CakeEventManager::instance()->attach($eventListener['callable'], $eventKey, array(
                'passParams' => $eventListener['passParams'],
            ));
        }
        
        $this->preservedEventListeners = array();
    }
    
}