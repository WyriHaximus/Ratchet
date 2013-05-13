<?php

/*
 * This file is part of Ratchet.
 *
 ** (c) 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueGetMemoryUsageCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetPhuninMemoryUsage implements \PhuninNode\Interfaces\Plugin {
    
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
        return 'ratchet_memory_usage';
    }
    
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'ratchet');
        $this->configuration->setPair('graph_title', 'Memory Usage');
        $this->configuration->setPair('memory_usage.label', 'Current Memory Usage');
        $this->configuration->setPair('memory_peak_usage.label', 'Peak Memory Usage');
        
        $deferredResolver->resolve($this->configuration);
    }
    
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $command = new RatchetMessageQueueGetMemoryUsageCommand();
        $command->setDeferedResolver($deferredResolver);
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }
    
}