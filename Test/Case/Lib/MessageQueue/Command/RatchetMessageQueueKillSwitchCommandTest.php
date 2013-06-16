<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueKillSwitchCommand', 'Ratchet.Lib/MessageQueue/Command');

class RatchetMessageQueueKillSwitchCommandTestShell {
    
    public $txt;
    
    public function out($txt) {
        $this->txt = $txt;
    }
}

class RatchetMessageQueueKillSwitchCommandTest extends AbstractCommandTest {
    
    /**
     * {@inheritdoc}
     */
    public function setUp() {
        parent::setUp();
        
        $this->Command = new RatchetMessageQueueKillSwitchCommand();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown() {
        parent::tearDown();
    }
    
    public function testExecute() {
        $shell = new RatchetMessageQueueKillSwitchCommandTestShell();
        $this->Command->setShell($shell);
        $eventSubject = parent::testExecute();
        
        $this->assertTrue($eventSubject->getLoop()->addTimerCalled);
        $this->assertTrue($eventSubject->getLoop()->stopCalled);
        $this->assertSame('<success>Server stopping</success>', $shell->txt);
    }
    
}