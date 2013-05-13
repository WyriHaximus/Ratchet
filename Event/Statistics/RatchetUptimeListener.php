<?php

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