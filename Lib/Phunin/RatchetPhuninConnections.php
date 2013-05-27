<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetConnectionsCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetPhuninConnections implements \PhuninNode\Interfaces\Plugin {
    
    private $node;
    private $configuration;
    private $loop;
    
    public function __construct($loop) {
        $this->loop = $loop;
    }
    
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    public function getSlug() {
        return 'ratchet_connections';
    }
    
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'ratchet');
        $this->configuration->setPair('graph_title', 'Connection Counts');
        
        $this->configuration->setPair('users.label', 'Users');
        $this->configuration->setPair('guests.label', 'Guests');
        
        $deferredResolver->resolve($this->configuration);
    }
    
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $command = new RatchetMessageQueueGetConnectionsCommand();
        $command->setDeferedResolver($deferredResolver);
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }
    
}