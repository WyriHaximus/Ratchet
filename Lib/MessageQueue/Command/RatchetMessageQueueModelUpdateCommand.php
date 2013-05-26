<?php

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueModelUpdateCommand extends RatchetMessageQueueCommand {
    
    const EVENT_PREFIX = 'Rachet.WampServer.ModelUpdate.';
    
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
    
    public function execute($eventSubject) {
        $eventSubject->getLoop()->addTimer(.5, function() use ($eventSubject) {
            $topics = $eventSubject->getTopics();
            if (isset($topics[self::EVENT_PREFIX . $this->event])) {
                $topics[self::EVENT_PREFIX . $this->event]->broadcast($this->data);
            }
        });
        
        return true;
    }
    
    public function response($response) {
        //
    }
}