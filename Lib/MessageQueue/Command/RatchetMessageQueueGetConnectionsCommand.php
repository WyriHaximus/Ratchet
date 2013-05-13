<?php

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueGetConnectionsCommand extends RatchetMessageQueueCommand {
    
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
        $event = new CakeEvent('Rachet.WebsocketServer.getConnectionCounts', $this, array());
        CakeEventManager::instance()->dispatch($event);
        
        return $event->result;
    }
    
    public function response($response) {
        
        $values = new \SplObjectStorage;
        
        $users = new \PhuninNode\Value();
        $users->setKey('users');
        $users->setValue($response['users']);
        $values->attach($users);
        
        $guests = new \PhuninNode\Value();
        $guests->setKey('guests');
        $guests->setValue($response['guests']);
        $values->attach($guests);
        
        $this->resolver->resolve($values);
    }
}