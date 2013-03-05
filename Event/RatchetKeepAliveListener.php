<?php

App::uses('CakeEventListener', 'Event');

class RatchetKeepAliveListener implements CakeEventListener {

    private $loop;
    private $timer;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.construct' => 'construct',
            'Rachet.WampServer.onSubscribeNewTopic.Rachet.connection.keepAlive' => 'onSubscribeNewTopic',
            'Rachet.WampServer.onUnSubscribeStaleTopic.Rachet.connection.keepAlive' => 'onUnSubscribeStaleTopic',
        );
    }
    
    public function construct($event) {
        $this->loop = $event->data['loop'];
    }
    
    public function onSubscribeNewTopic($event) {
        $this->timer = $this->loop->addPeriodicTimer(5, function() use ($event) {
            $event->data['topic']->broadcast('ping');
        }, true);
        
        $event->data['topic']->broadcast('ping');
    }
    
    public function onUnSubscribeStaleTopic($event) {
        $this->loop->cancelTimer($this->timer);
    }
}