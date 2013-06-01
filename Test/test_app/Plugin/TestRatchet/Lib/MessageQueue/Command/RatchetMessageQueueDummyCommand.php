<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueDummyCommand extends RatchetMessageQueueCommand {
    
    protected $callback;
    
    public function setCallback($callback) {
        $this->callback = $callback;
    }
    
    public function execute($eventSubject) {
        return 1;
    }
    
    public function response($response) {
        call_user_func($this->callback, $response);
    }
}