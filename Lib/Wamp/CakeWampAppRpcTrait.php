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

trait CakeWampAppRpcTrait {

/**
 * Dispatches an event for the called RPC
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string $id
 * @param string|\Ratchet\Wamp\Topic $topic
 * @param array $params
 */
	public function onCall(Conn $conn, $id, $topic, array $params) {
		$topicName = self::getTopicName($topic);

		$event = $this->dispatchEvent(
			'Rachet.WampServer.Rpc',
			$this,
			[
			'topicName' => $topicName,
			'connection' => $conn,
			'id' => $id,
			'topic' => $topic,
			'params' => $params,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		if ($event->isStopped()) {
			$conn->callError(
				$id,
				$event->result['stop_reason']['error_uri'],
				$event->result['stop_reason']['desc'],
				$event->result['stop_reason']['details']
			);

			$this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') was blocked');

			$this->dispatchEvent(
				'Rachet.WampServer.RpcBlocked',
				$this,
				[
					'topicName' => $topicName,
					'connection' => $conn,
					'id' => $id,
					'reason' => $event->result['stop_reason'],
					'connectionData' => $this->_connections[$conn->WAMP->sessionId],
				]
			);

			return false;
		}

		$start = microtime(true);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(
			function ($results) use ($conn, $id, $topicName, $start) {
				$end = microtime(true);
				$conn->callResult(
					$id,
					$results
				);

				$this->outVerbose(
					'Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and succeeded'
				);

				$this->dispatchEvent(
					'Rachet.WampServer.RpcSuccess',
					$this,
					[
						'topicName' => $topicName,
						'connection' => $conn,
						'id' => $id,
						'results' => $results,
						'connectionData' => $this->_connections[$conn->WAMP->sessionId],
					]
				);
			},
			function ($errorUri, $desc = '', $details = null) use ($conn, $id, $topicName, $start) {
				$end = microtime(true);

				$conn->callError(
					$id,
					$errorUri,
					$desc,
					$details
				);

				$this->outVerbose(
					'Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and failed'
				);

				$this->dispatchEvent(
					'Rachet.WampServer.RpcFailed',
					$this,
					[
						'topicName' => $topicName,
						'connection' => $conn,
						'id' => $id,
						'reason' => [
							$errorUri,
							$desc,
							$details,
						],
						'connectionData' => $this->_connections[$conn->WAMP->sessionId],
					]
				);
			}
		);

		$this->dispatchEvent(
			'Rachet.WampServer.Rpc.' . $topicName,
			$this,
			[
			'connection' => $conn,
			'promise' => $deferred,
			'id' => $id,
			'topic' => $topic,
			'params' => $params,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);
	}
} 