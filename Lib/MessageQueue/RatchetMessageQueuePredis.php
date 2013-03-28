<?php

class RatchetMessageQueuePredis implements RatchetMessageQueueInterface {
    private $serverConfiguration = null;
    private $key = null;
    public function __construct($serverConfiguration, $key = '') {
        $this->serverConfiguration = $serverConfiguration;
        $this->key = $key;
        $this->serverConnection = new Predis\Client($serverConfiguration);
    }
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $this->serverConnection->publish($this->key, serialize($command));
    }
}