<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('TransportProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetConnectionsCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetPhuninConnections implements \PhuninNode\Interfaces\Plugin {
    
    /**
     * PhuninNode server
     * 
     * @var \PhuninNode\Node
     */
    private $node;
    
    /**
     * Configuration object for this plugin, 
     * 
     * @var \PhuninNode\PluginConfiguration
     */
    private $configuration;
    
    /**
     * ReactPHP Eventloop
     * 
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;
    
    /**
     * 
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct(\React\EventLoop\LoopInterface $loop) {
        $this->loop = $loop;
    }
    
    /**
     * Sets the PhuninNode server instance for later reference, this plugin doesn't need it but gets it pass anyway due to the interface contract
     * 
     * @param \PhuninNode\Node $node
     */
    public function setNode(\PhuninNode\Node $node) {
        $this->node = $node;
    }
    
    /**
     * Returns the unique slug for this plugin
     * 
     * @return string
     */
    public function getSlug() {
        return 'ratchet_connections';
    }
    
    /**
     * Populate the configuration object, store it in an attribute and pass it into the resolver
     * 
     * @param \React\Promise\DeferredResolver $deferredResolver
     */
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
    
    /**
     * Retrive the current connection count values from the server
     * 
     * @param \React\Promise\DeferredResolver $deferredResolver
     */
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $command = new RatchetMessageQueueGetConnectionsCommand();
        $command->setDeferedResolver($deferredResolver);
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        TransportProxy::instance()->queueMessage($command);
    }
    
}