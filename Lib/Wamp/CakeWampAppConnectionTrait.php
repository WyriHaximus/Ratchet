<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ratchet\ConnectionInterface as Conn;

trait CakeWampAppConnectionTrait {

/**
 * @return mixed
 */
	abstract function outVerbose();

/**
 * @return mixed
 */
	abstract function dispatchEvent();

/**
 * @return mixed
 */
	abstract function onUnSubscribe();

/**
 * Contains metadata for all open connections
 *
 * @var array
 */
	protected $_connections = [];

/**
 *
 * @return array
 */
	public function getConnections() {
		return $this->_connections;
	}

/**
 * Stores session information and fires the onOpen event for listening listeners
 *
 * @param \Ratchet\ConnectionInterface $conn
 */
	public function onOpen(Conn $conn) {
		$this->outVerbose('New connection: <info>' . $conn->WAMP->sessionId . '</info>');

		$this->_connections[$conn->WAMP->sessionId] = [
			'topics' => [],
			'session' => $conn->Session->all(),
		];

		$this->dispatchEvent(
			'Rachet.WampServer.onOpen',
			$this,
			[
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);
	}

/**
 * Dispatches on a closing link, cleans up sesion and other connection data for this connection
 *
 * @param \Ratchet\ConnectionInterface $conn
 */
	public function onClose(Conn $conn) {
		foreach ($this->_connections[$conn->WAMP->sessionId]['topics'] as $topicName => $topic) {
			$this->onUnSubscribe($conn, $topic);
		}

		$this->dispatchEvent(
			'Rachet.WampServer.onClose',
			$this,
			[
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		unset($this->_connections[$conn->WAMP->sessionId]);

		$this->outVerbose('Closed connection: <info>' . $conn->WAMP->sessionId . '</info>');
	}
}
