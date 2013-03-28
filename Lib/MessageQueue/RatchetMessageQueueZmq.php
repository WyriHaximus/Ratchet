<?php

class RatchetMessageQueueZmq implements RatchetMessageQueueInterface {
    private $serverConfiguration = null;
    public function __construct($serverConfiguration, $key = '') {
        $this->serverConfiguration = $serverConfiguration;
        $zmq = new ZMQContext(1);
        $this->serverConnection = $zmq->getSocket(ZMQ::SOCKET_PUSH, 'xyz');
        $this->serverConnection->connect($serverConfiguration);
    }
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $this->serverConnection->send(serialize($command));
    }
}