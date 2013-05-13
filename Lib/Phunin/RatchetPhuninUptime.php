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
App::uses('RatchetMessageQueueGetUptimeCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetPhuninUptime implements \PhuninNode\Interfaces\Plugin {
    
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
        return 'ratchet_uptime';
    }
    
    public function getConfiguration(\React\Promise\DeferredResolver $deferredResolver) {
        if ($this->configuration instanceof \PhuninNode\PluginConfiguration) {
            $deferredResolver->resolve($this->configuration);
            return;
        }
        
        $this->configuration = new \PhuninNode\PluginConfiguration();
        $this->configuration->setPair('graph_category', 'ratchet');
        $this->configuration->setPair('graph_title', 'Uptime');
        $this->configuration->setPair('graph_args', '--base 1000 -l 0');
        $this->configuration->setPair('graph_vlabel', 'uptime in days');
        $this->configuration->setPair('uptime.label', 'uptime');
        $this->configuration->setPair('uptime.draw', 'AREA');
        
        $deferredResolver->resolve($this->configuration);
    }
    
    public function getValues(\React\Promise\DeferredResolver $deferredResolver) {
        $command = new RatchetMessageQueueGetUptimeCommand();
        $command->setDeferedResolver($deferredResolver);
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }
    
}