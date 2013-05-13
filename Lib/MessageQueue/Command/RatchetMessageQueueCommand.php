<?php

App::uses('RatchetMessageQueueCommandInterface', 'Ratchet.Lib/MessageQueue/Interfaces');

abstract class RatchetMessageQueueCommand implements RatchetMessageQueueCommandInterface, Serializable  {
    public function serialize() {
        return serialize($this);
    }
    public function unserialize($commandString) {
        return unserialize($commandString);
    }
    public function execute($topics) {
        throw new Exeception('Must override execute method!');
    }
}