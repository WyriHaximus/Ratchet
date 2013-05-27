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
        $this->timer = $this->loop->addPeriodicTimer(Configure::read('Ratchet.Connection.keepaliveInterval'), function() use ($event) {
            $event->data['topic']->broadcast('ping');
        }, true);
        
        $event->data['topic']->broadcast('ping');
    }
    
    public function onUnSubscribeStaleTopic($event) {
        $this->loop->cancelTimer($this->timer);
    }
}