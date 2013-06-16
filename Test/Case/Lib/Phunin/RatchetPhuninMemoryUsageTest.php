<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetPhuninMemoryUsage', 'Ratchet.Lib/Phunin');

class RatchetPhuninMemoryUsageTest extends AbstractPhuninPluginTest {
    
    public function setUp() {
        parent::setUp();
        $this->plugin = new RatchetPhuninMemoryUsage($this->loop);
        $this->node->addPlugin($this->plugin);
    }
    
}