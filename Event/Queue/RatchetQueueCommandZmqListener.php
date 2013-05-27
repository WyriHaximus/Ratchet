<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetQueueCommandListener', 'Ratchet.Event/Queue');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

class RatchetQueueCommandZmqListener extends RatchetQueueCommandListener {
    
    public function construct($event) {
        $this->loop = $event->data['loop'];
        
        $context = new React\ZMQ\Context($this->loop);
        $socket = $context->getSocket(ZMQ::SOCKET_REP);
        $socket->bind(Configure::read('Ratchet.Queue.server'));
        $socket->on('message', function($msg) use ($event, $socket) {
            $command = unserialize($msg);
            $socket->send(serialize($command->execute($event->subject())));
        });
    }
}