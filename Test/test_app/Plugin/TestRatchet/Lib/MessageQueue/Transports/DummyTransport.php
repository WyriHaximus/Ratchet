<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DummyTransportTestEvent {
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    public function broadcast($data) {
        call_user_func($this->callback, $data);
    }
}

class DummyTransportTestLoop {
    public function addTimer($timeout, $callback) {
        call_user_func($callback);
    }
}

class DummyTransportEventSubjectTestImposer {
    
    public function __construct($callbacks, $topics) {
        $this->callbacks = $callbacks;
        $this->topics = $topics;
    }
    
    public function getLoop() {
        return new DummyTransportTestLoop();
    }
    
    public function getTopics() {
        return $this->topics;
    }
}

class DummyTransport implements RatchetMessageQueueTransportInterface {
    
    /**
     * Event subject dummy used for unit testing the transport proxy
     * 
     * @var stdClass
     */
    private $eventSubject;
    
    /**
     * {@inheritdoc}
     */
    public function __construct($serverConfiguration) {
        $this->eventSubject = new stdClass();
    }
    
    /**
     * Sets tje event subject used in the queueMessage method
     * 
     * @param stdClass $eventSubject
     */
    public function setEventSubject($eventSubject) {
        $this->eventSubject = $eventSubject;
    }
    
    /**
     * {@inheritdoc}
     */
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $command->response($command->execute($this->eventSubject));
    }
}