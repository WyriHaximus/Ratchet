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
    private $eventSubject;
    public function __construct($serverConfiguration) {
        $this->eventSubject = new stdClass();
    }
    public function setEventSubject($eventSubject) {
        $this->eventSubject = $eventSubject;
    }
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $command->response($command->execute($this->eventSubject));
    }
}