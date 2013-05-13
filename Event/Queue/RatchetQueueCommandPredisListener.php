<?php

App::uses('RatchetQueueCommandListener', 'Ratchet.Event/Queue');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

class RatchetQueueCommandPredisListener extends RatchetQueueCommandListener {
    
    public function construct($event) {
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