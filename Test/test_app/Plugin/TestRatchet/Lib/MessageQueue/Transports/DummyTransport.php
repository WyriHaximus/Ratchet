<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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