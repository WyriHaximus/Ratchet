<?php

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue');

class RatchetMessageQueueModelUpdateCommand extends RatchetMessageQueueCommand {
    
    public function serialize() {
        return serialize(array(
            'event' => $this->event,
            'data' => $this->data,
        ));
    }
    public function unserialize($commandString) {
        $commandString = unserialize($commandString);
        $this->setEvent($commandString['event']);
        $this->setData($commandString['data']);
    }
    
    public function setEvent($event) {
        $this->event = $event;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    public function execute($topics) {
        if (isset($topics['Rachet.WampServer.ModelUpdate.' . $this->event])) {
            $topics['Rachet.WampServer.ModelUpdate.' . $this->event]->broadcast($this->data);
        }
    }
}