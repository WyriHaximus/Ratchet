<?php

App::uses('RatchetMessageQueueInterface', 'Ratchet.Lib/MessageQueue');
App::uses('RatchetMessageQueuePredis', 'Ratchet.Lib/MessageQueue');
App::uses('RatchetMessageQueueZmq', 'Ratchet.Lib/MessageQueue');

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
}