<?php

App::uses('CakeEventListener', 'Event');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

class RatchetModelUpdateListener implements CakeEventListener {

    private $loop;
    private $timer;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.construct' => 'construct',
        );
    }
    
    public function construct($event) {
        $this->loop = $event->data['loop'];
        
        switch (Configure::read('Ratchet.Queue.type')) {
            case 'Predis':
                $client = new Predis\Async\Client(Configure::read('Ratchet.Queue.server'), array(
                    'eventloop' => $this->loop,
                ));
                $client->connect(function ($client) use ($event) {
                    $client->select(Configure::read('Ratchet.Queue.server.database'));
                    $client->pubsub(Configure::read('Ratchet.Queue.key'), function ($publishedEvent) use ($event) {
                        $command = unserialize($publishedEvent->payload);
                        $command->execute($event->subject()->topics);
                    });
                });
                break;
            case 'ZMQ':
                $context = new React\ZMQ\Context($this->loop);
                $socket = $context->getSocket(ZMQ::SOCKET_PULL);
                $socket->bind(Configure::read('Ratchet.Queue.server'));
                $socket->on('message', function($msg) use ($event) {
                    $command = unserialize($msg);
                    debug($event->subject()->topics);
                    $command->execute($event->subject()->topics);
                });
                break;
            default:
                throw new Exception('Unknown queue type:' . Configure::read('Ratchet.Queue.type'));
                break;
        }
    }
}