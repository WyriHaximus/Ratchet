<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetPhuninConnections', 'Ratchet.Lib/Phunin');

class RatchetPhuninConnectionsTest extends AbstractPhuninPluginTest {
    
    public function setUp() {
        parent::setUp();
        $this->plugin = new RatchetPhuninConnections($this->loop);
        $this->node->addPlugin($this->plugin);
    }
    
}