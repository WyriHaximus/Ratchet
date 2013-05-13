<?php

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