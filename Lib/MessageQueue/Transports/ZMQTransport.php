<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ZMQTransport implements RatchetMessageQueueTransportInterface {
    
    /**
     * Contains the server connection
     * 
     * @var array
     */
    private $serverConnection;
    
    /**
     * {@inheritdoc}
     */
    public function __construct($serverConfiguration) {
        $zmq = new ZMQContext(1);
        $this->serverConnection = $zmq->getSocket(ZMQ::SOCKET_REQ);
        $this->serverConnection->connect($serverConfiguration['server']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $this->serverConnection->send(serialize($command));
        $command->response(unserialize($this->serverConnection->recv()));
    }
}