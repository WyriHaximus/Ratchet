<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventListener', 'Event');

class RatchetMemoryUsageListener implements CakeEventListener {
    
    /**
     * Returns an array with the events this listener hooks into
     * 
     * @return array
     */
    public function implementedEvents() {
        return array(
            'Rachet.WebsocketServer.getMemoryUsage' => 'getMemoryUsage',
        );
    }
    
    /**
     * Returns an array with the current memory usage and the peak memory usage
     * 
     * @param CakeEvent $event
     */
    public function getMemoryUsage(CakeEvent $event) {
        $event->result = array(
            'memory_usage' => memory_get_usage(true),
            'memory_peak_usage' => memory_get_peak_usage(true),
        );
    }
}