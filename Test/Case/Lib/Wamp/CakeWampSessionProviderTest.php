<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

App::uses('CakeWampSessionProvider', 'Ratchet.Lib/Wamp');

class CakeWampSessionProviderTest extends CakeRatchetTestCase {

	protected function _newConn() {
		$conn = $this->getMock('Ratchet\\ConnectionInterface');

		$headers = $this->getMock('Guzzle\\Http\\Message\\Request', array('getCookie'), array('POST', '/', array()));
		$headers->expects($this->once())->method('getCookie', array(ini_get('session.name')))->will($this->returnValue(null));

		$conn->WebSocket					= new \StdClass;
		$conn->WebSocket->request	= $headers;

		return $conn;
	}

	public function testOnOpenBubbles() {
		$conn = $this->_newConn();
		$mock = $this->getMock('Ratchet\\MessageComponentInterface');
		$comp = new CakeWampSessionProvider($mock, new NullSessionHandler);

		$mock->expects($this->once())->method('onOpen')->with($conn);
		$comp->onOpen($conn);
	}
}
