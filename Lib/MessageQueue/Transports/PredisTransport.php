<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class PredisTransport implements RatchetMessageQueueTransportInterface {
    private $serverConfiguration;
    private $serverConnection;
    public function __construct($serverConfiguration) {
        $this->serverConfiguration = $serverConfiguration;
        $this->serverConnection = new Predis\Client($this->serverConfiguration['server']);
    }
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $client = new Predis\Client($this->serverConfiguration['server']);
        $pubsub = $client->pubSub();
        $pubsub->subscribe($this->serverConfiguration['key'] . ':main');
        $pubsub->current();
        $this->serverConnection->publish($this->serverConfiguration['key'] . ':main', serialize($command));
        $command->response(unserialize($pubsub->current()->payload));
        $client->disconnect();
    }
}