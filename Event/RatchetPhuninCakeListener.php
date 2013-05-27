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

    private $loop;
    
    public function implementedEvents() {
        return array(
            'PhuninCake.Node.start' => 'start',
        );
    }
    
    public function start($event) {
        $this->loop = $event->data['loop'];
        $this->node = $event->data['node'];
        
        $this->node->addPlugin(new RatchetPhuninConnections($this->loop));
        $this->node->addPlugin(new RatchetPhuninUptime($this->loop));
        $this->node->addPlugin(new RatchetPhuninMemoryUsage($this->loop));
    }
}