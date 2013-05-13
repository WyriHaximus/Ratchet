<?php

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueGetMemoryUsageCommand extends RatchetMessageQueueCommand {
    
    public function serialize() {
        return serialize(array(
            'hash' => $this->hash,
        ));
    }
    public function unserialize($commandString) {
        $commandString = unserialize($commandString);
        $this->setHash($commandString['hash']);
    }
    
    public function setHash($hash) {
        $this->hash = $hash;
    }
    
    public function setDeferedResolver($resolver) {
        $this->resolver = $resolver;
    }
    
    public function execute($eventSubject) {
        $event = new CakeEvent('Rachet.WebsocketServer.getMemoryUsage', $this, array());
        CakeEventManager::instance()->dispatch($event);
        
        return $event->result;
    }
    
    public function response($response) {
        $values = new \SplObjectStorage;
        
        $memory_usage = new \PhuninNode\Value();
        $memory_usage->setKey('memory_usage');
        $memory_usage->setValue($response['memory_usage']);
        $values->attach($memory_usage);
        
        $memory_peak_usage = new \PhuninNode\Value();
        $memory_peak_usage->setKey('memory_peak_usage');
        $memory_peak_usage->setValue($response['memory_peak_usage']);
        $values->attach($memory_peak_usage);
        
        $this->resolver->resolve($values);
    }
}