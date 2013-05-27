<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class RatchetMessageQueuePredis implements RatchetMessageQueueInterface {
    private $serverConfiguration = null;
    private $key = null;
    public function __construct($serverConfiguration, $key = '') {
        $this->serverConfiguration = $serverConfiguration;
        $this->key = $key;
        $this->serverConnection = new Predis\Client($serverConfiguration);
    }
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $client = new Predis\Client(Configure::read('Ratchet.Queue.server'));
        $pubsub = $client->pubSub();
        $pubsub->subscribe(Configure::read('Ratchet.Queue.key') . ':' . md5('qw38947tg89w73478tgw34'));
        $pubsub->current();
        $this->serverConnection->publish($this->key, serialize($command));
        $command->response(unserialize($pubsub->current()->payload));
        $client->disconnect();
    }
}