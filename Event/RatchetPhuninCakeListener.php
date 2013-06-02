<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventListener', 'Event');

App::uses('RatchetPhuninConnections', 'Ratchet.Lib/Phunin');
App::uses('RatchetMessageQueueGetConnectionsCommand', 'Ratchet.Lib/MessageQueue/Command');

App::uses('RatchetPhuninUptime', 'Ratchet.Lib/Phunin');
App::uses('RatchetMessageQueueGetUptimeCommand', 'Ratchet.Lib/MessageQueue/Command');

App::uses('RatchetPhuninMemoryUsage', 'Ratchet.Lib/Phunin');
App::uses('RatchetMessageQueueGetMemoryUsageCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetPhuninCakeListener implements CakeEventListener {
    
    /**
     * The ReactPHP event
     * 
     * @var \React\EventLoop\LoopInterface 
     */
    private $loop;
    
    /**
     * The PhuninNode server
     * 
     * @var \PhuninNode\Node 
     */
    private $node;
    
    /**
     * Return an array with events this listener implements
     * @return array
     */
    public function implementedEvents() {
        return array(
            'PhuninCake.Node.start' => 'start',
        );
    }
    
    /**
     * Attach all PhuninNode plugins for the Ratchet plugin
     * 
     * @param CakeEvent $event
     */
    public function start(CakeEvent $event) {
        $this->loop = $event->data['loop'];
        $this->node = $event->data['node'];
        
        $this->node->addPlugin(new RatchetPhuninConnections($this->loop));
        $this->node->addPlugin(new RatchetPhuninUptime($this->loop));
        $this->node->addPlugin(new RatchetPhuninMemoryUsage($this->loop));
    }
}