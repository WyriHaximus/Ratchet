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

class RatchetUptimeListener implements CakeEventListener {

    private $startTime = 0;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.construct' => 'construct',
            'Rachet.WebsocketServer.getUptime' => 'getUptime',
        );
    }
    
    public function construct($event) {
        $this->startTime = time();
    }
    
    public function getUptime($event) {
        $event->result = (time() - $this->startTime);
    }
}