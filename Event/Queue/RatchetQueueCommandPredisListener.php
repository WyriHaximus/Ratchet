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

class RatchetQueueCommandPredisListener extends RatchetQueueCommandListener {
    
    /**
     * Eventlistener for the Rachet.WampServer.construct event and 
     * waits for incoming commands over he message queue
     * 
     * @param CakeEvent $event
     */
    public function construct(CakeEvent $event) {
        $this->loop = $event->data['loop'];
        
        $client = new Predis\Async\Client(Configure::read('Ratchet.Queue.server'), array(
            'eventloop' => $this->loop,
        ));
        $client->connect(function ($client) use ($event) {
            $client->select(Configure::read('Ratchet.Queue.server.database'));
            $client->pubsub(Configure::read('Ratchet.Queue.key') . ':main', function ($publishedEvent) use ($event) {
                $command = unserialize($publishedEvent->payload);
                $client = new Predis\Client(Configure::read('Ratchet.Queue.server'));
                $client->publish(Configure::read('Ratchet.Queue.key') . ':' . md5('qw38947tg89w73478tgw34'), serialize($command->execute($event->subject())));
                $client->disconnect();
            });
        });
    }
}