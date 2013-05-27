<?php

App::uses('ModelBehavior', 'Model');
App::uses('RatchetMessageQueueProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue/Command');

class PushableBehavior extends ModelBehavior {
    
    private $defaults = array(
        'events' => array(),
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
            'data' => $Model->data,
            'created' => $created,
            'model' => $Model,
        ));
    }
    
    protected function afterSaveEventCheck($event, $key, $data) {
        if ($event['created'] !== $data['created']) {
            return;
        }
        
        if ($event['refetch']) {
            $resultSet = $data['model']->findById($data['id']);
        } else {
            $resultSet = $data['data'];
        }
        
        $eventName = $this->afterSavePrepareEventName($event['eventName'], $data['id'], $resultSet[$data['model']->alias]);
        
        $this->afterSaveDispatchEvent($eventName, $resultSet);
    }
    
    protected function afterSavePrepareEventName($eventName, $id, $data) {
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
    
    protected function afterSaveDispatchEvent($eventName, $eventData) {
        $command = new RatchetMessageQueueModelUpdateCommand();
        $command->setEvent($eventName);
        $command->setData($eventData);
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }

}