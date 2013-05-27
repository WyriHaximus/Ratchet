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
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue');

abstract class RatchetQueueCommandListener implements CakeEventListener {

    protected $loop;
    
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.construct' => 'construct',
        );
    }
    
}