<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueInterface', 'Ratchet.Lib/MessageQueue/Interfaces');
App::uses('RatchetMessageQueuePredis', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueZmq', 'Ratchet.Lib/MessageQueue/Transports');

class RatchetMessageQueueProxy implements RatchetMessageQueueInterface {
    
    protected static $_generalMessageQueueProxy = null;
    private $queue = null;
    
    public function __construct($serverConfiguration = null, $key = null) {
        //throw new Exception('Use RatchetMessageQueueProxy::instance()');
    }
    
    public static function instance() {
        if (empty(self::$_generalMessageQueueProxy)) {
            self::$_generalMessageQueueProxy = new RatchetMessageQueueProxy;
        }

        return self::$_generalMessageQueueProxy;
    }
    
    public function queueMessage(RatchetMessageQueueCommand $command) {
        if (empty($this->queue)) {
            $this->determenQueue();
        }
        
        $this->queue->queueMessage($command);
    }
    
    private function determenQueue() {
        switch (Configure::read('Ratchet.Queue.type')) {
            case 'Predis':
                $this->queue = new RatchetMessageQueuePredis(Configure::read('Ratchet.Queue.server'), Configure::read('Ratchet.Queue.key'));
                break;
            case 'ZMQ':
                $this->queue = new RatchetMessageQueueZmq(Configure::read('Ratchet.Queue.server'));
                break;
            default:
                throw new Exception('Unknown queue type:' . Configure::read('Ratchet.Queue.type'));
                break;
        }
    }
    
    public function handleResponse() {
        
    }
}