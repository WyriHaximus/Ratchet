<?php

App::uses('RatchetMessageQueueProxy', 'Ratchet.Lib/MessageQueue');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

class PushableBehavior extends ModelBehavior {
    
    private $defaults = array(
        'events' => array(
            /*array(
                'eventName' => 'test.created',
                'created' => true,
            ),
            array(
                'eventName' => 'test.updated',
                'created' => false,
            ),
            array(
                'eventName' => 'test.updated.{id}',
                'created' => false,
                'fields' => true,
            ),*/
        ),
    );
    
    public function setup(Model $Model, $settings = array()) {
        if (!isset($this->settings[$Model->alias])) {
            $this->settings[$Model->alias] = $this->defaults;
        }
        $this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], (array) $settings);
    }
    
    public function afterSave(Model $Model, $created) {
        array_walk($this->settings[$Model->alias]['events'], array($this, 'afterSaveEventCheck'), array(
            'id' => $Model->id,
            'data' => $Model->data[$Model->alias],
            'created' => $created,
        ));
    }
    
    private function afterSaveEventCheck($event, $key, $data) {
        if ($event['created'] !== $data['created']) {
            return;
        }
        
        $eventName = $this->afterSavePrepareEventName($event['eventName'], $data['id'], $data['data']);
        
        $this->afterSaveDispatchEvent($eventName, $data['data']);
    }
    
    private function afterSavePrepareEventName($eventName, $id, $data) {
        $before = array(
            '{id}',
        );
        $after = array(
            $id,
        );
        
        foreach ($data as $key => $value) {
            $before[] = '{' . $key . '}';
            $after[] = $value;
        }
        
        return str_replace($before, $after, $eventName);
    }
    
    private function afterSaveDispatchEvent($eventName, $eventData) {
        $command = new RatchetMessageQueueModelUpdateCommand();
        $command->setEvent($eventName);
        $command->setData($eventData);
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }

}