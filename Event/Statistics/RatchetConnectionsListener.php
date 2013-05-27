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

class RatchetConnectionsListener implements CakeEventListener {

    private $userConnectionCount = 0;
    private $guestConnectionCount = 0;
    private $topic = false;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.onOpen' => 'onOpen',
            'Rachet.WampServer.onClose' => 'onClose',
            'Rachet.WampServer.onSubscribeNewTopic.connectionCount' => 'onSubscribeNewTopic',
            'Rachet.WampServer.onSubscribe.connectionCount' => 'onSubscribe',
            'Rachet.WampServer.onUnSubscribeStaleTopic.connectionCount' => 'onUnSubscribeStaleTopic',
            'Rachet.WebsocketServer.getConnectionCounts' => 'getConnectionCounts',
        );
    }

    public function onOpen($event) {
        if (isset($event->data['connectionData']['session']['Auth']['User']['id'])) {
            $this->userConnectionCount++;
        } else {
            $this->guestConnectionCount++;
        }
        
        if ($this->topic instanceof \Ratchet\Wamp\Topic) {
            $this->topic->broadcast(array(
                'guests' => $this->guestConnectionCount,
                'users' => $this->userConnectionCount,
            ));
        }
    }

    public function onClose($event) {
        if (isset($event->data['connectionData']['session']['Auth']['User']['id'])) {
            $this->userConnectionCount--;
        } else {
            $this->guestConnectionCount--;
        }
        
        if ($this->topic instanceof \Ratchet\Wamp\Topic) {
            $this->topic->broadcast(array(
                'guests' => $this->guestConnectionCount,
                'users' => $this->userConnectionCount,
            ));
        }
    }
    
    public function onSubscribeNewTopic($event) {
        $this->topic = $event->data['topic'];
    }
    
    public function onSubscribe($event) {
        $event->data['connection']->event($this->topic->getId(), array(
            'guests' => $this->guestConnectionCount,
            'users' => $this->userConnectionCount,
        ));
    }
    
    public function onUnSubscribeStaleTopic($event) {
        $this->topic = false;
    }
    
    public function getConnectionCounts($event) {
        $event->result = array(
            'guests' => $this->guestConnectionCount,
            'users' => $this->userConnectionCount,
        );
    }
}