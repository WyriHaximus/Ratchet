<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueCommandInterface', 'Ratchet.Lib/MessageQueue/Interfaces');

abstract class RatchetMessageQueueCommand implements RatchetMessageQueueCommandInterface, Serializable  {
    public function serialize() {
        return serialize($this);
    }
    public function unserialize($commandString) {
        return unserialize($commandString);
    }
    public function execute($topics) {
        throw new Exeception('Must override execute method!');
    }
}