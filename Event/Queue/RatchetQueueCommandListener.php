<?php

App::uses('CakeEventListener', 'Event');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

abstract class RatchetQueueCommandListener implements CakeEventListener {

    protected $loop;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.construct' => 'construct',
        );
    }
    
}