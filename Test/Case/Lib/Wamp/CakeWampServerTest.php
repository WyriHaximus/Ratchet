<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeWampServer', 'Ratchet.Lib/Wamp');
App::uses('CakeWampAppServer', 'Ratchet.Lib/Wamp');

class CakeWampServerTest extends CakeRatchetTestCase {
    
    public function testConstruct() {
        $WebsocketShell = $this->getMock('WebsocketShell');
        $React_EventLoop_LoopInterface = $this->getMock('React\\EventLoop\\LoopInterface');
        $CakeWampAppServer = $this->getMock('CakeWampAppServer', array(), array(
            $WebsocketShell,
            $React_EventLoop_LoopInterface,
        ));
        $CakeWampAppServer->expects($this->once())->method('setTopicManager')->with($this->isInstanceOf('Ratchet\\Wamp\\TopicManager'));
        
        new CakeWampServer($CakeWampAppServer);
    }
    
}