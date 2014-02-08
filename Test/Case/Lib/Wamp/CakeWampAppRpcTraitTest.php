<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeRatchetTestCase', 'Ratchet.Test/Case');

class CakeWampAppRpcTraitTest extends CakeRatchetTestCase {

	public function testOnCallProvider() {
		return [
			[
				'test',
			],
			[
				new \Ratchet\Wamp\Topic('test'),
			],
		];
	}

/**
 * @dataProvider testOnCallProvider
 */
	public function testOnCall($topic) {
		$topicName = (string)$topic;
		$results = [
			'foo:bar',
		];
		$mock = $this->getMock(
			'\\Ratchet\\ConnectionInterface',
			[
			'send',
			'close',
			]
		);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(
			function ($results) {
			},
			function ($results) {
			}
		);
		$conn = $this->getMock(
			'\\Ratchet\\Wamp\\WampConnection',
			[
			'callResult',
			],
			[
			$mock,
			]
		);
		$conn->expects($this->once())
			->method('callResult')
			->with(1, $results);
		$conn->Session = new SessionHandlerImposer();

		$asserts = [];
		$this->_expectedEventCalls(
			$asserts,
			[
			'Rachet.WampServer.Rpc' => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic) {
						$this->assertEquals(
							$event->data,
							[
							'topicName' => $topicName,
							'connection' => $conn,
							'id' => 1,
							'topic' => $topic,
							'params' => [
								'foo' => 'bar',
							],
							'wampServer' => $this->AppServer,
							'connectionData' => [
								'topics' => [],
								'session' => [],
							],
							]
						);
					},
				],
			],
			'Rachet.WampServer.Rpc.' . $topicName => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic, $deferred, $results) {
						$resolver = $deferred->resolver();

						$this->assertEquals(
							$event->data,
							[
							'connection' => $conn,
							'promise' => $resolver,
							'id' => 1,
							'topic' => $topic,
							'params' => [
								'foo' => 'bar',
							],
							'wampServer' => $this->AppServer,
							'connectionData' => [
								'topics' => [],
								'session' => [],
							],
							]
						);

						$event->data['promise']->resolve($results);
					},
				],
			],
			]
		);
		$this->AppServer->onOpen($conn);
		$this->AppServer->onCall(
			$conn,
			1,
			$topic,
			[
			'foo' => 'bar',
			]
		);

		foreach ($asserts as $assert) {
			$this->assertTrue($assert);
		}
	}

	public function testOnCallFailedProvider() {
		return [
			[
				'test',
			],
			[
				new \Ratchet\Wamp\Topic('test'),
			],
		];
	}

/**
 * @dataProvider testOnCallFailedProvider
 */
	public function testOnCallFailed($topic) {
		$topicName = (string)$topic;
		$results = 'foo:bar';

		$mock = $this->getMock(
			'\\Ratchet\\ConnectionInterface',
			[
			'send',
			'close',
			]
		);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(
			function ($results) {
			},
			function ($results) {
			}
		);
		$conn = $this->getMock(
			'\\Ratchet\\Wamp\\WampConnection',
			[
			'callError',
			],
			[
			$mock,
			]
		);

		$rejectReason = [
			1,
			2,
			3,
		];

		$conn->expects($this->once())
			->method('callError')
			->with(1, $rejectReason, '', null);
		$conn->Session = new SessionHandlerImposer();

		$asserts = [];
		$this->_expectedEventCalls(
			$asserts,
			[
			'Rachet.WampServer.Rpc' => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic) {
						$this->assertEquals(
							$event->data,
							[
								'topicName' => $topicName,
								'connection' => $conn,
								'id' => 1,
								'topic' => $topic,
								'params' => [
									'foo' => 'bar',
								],
								'wampServer' => $this->AppServer,
								'connectionData' => [
									'topics' => [],
									'session' => [],
								],
							]
						);
					},
				],
			],
			'Rachet.WampServer.Rpc.' . $topicName => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic, $deferred, $rejectReason) {
						$resolver = $deferred->resolver();

						$this->assertEquals(
							$event->data,
							[
								'connection' => $conn,
								'promise' => $resolver,
								'id' => 1,
								'topic' => $topic,
								'params' => [
									'foo' => 'bar',
								],
								'wampServer' => $this->AppServer,
								'connectionData' => [
									'topics' => [],
									'session' => [],
								],
							]
						);

						$event->data['promise']->reject($rejectReason);
					},
				],
			],
			'Rachet.WampServer.RpcFailed' => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic) {
						$this->assertEquals(
							$event->data,
							[
								'topicName' => $topicName,
								'connection' => $conn,
								'id' => 1,
								'topic' => $topic,
								'params' => [
									'foo' => 'bar',
								],
								'wampServer' => $this->AppServer,
								'connectionData' => [
									'topics' => [],
									'session' => [],
								],
							]
						);
					},
				],
			],
			]
		);
		$this->AppServer->onOpen($conn);
		$this->AppServer->onCall(
			$conn,
			1,
			$topic,
			[
			'foo' => 'bar',
			]
		);

		foreach ($asserts as $assert) {
			$this->assertTrue($assert);
		}
	}

	public function testOnCallBlockProvider() {
		return [
			[
				'test',
			],
			[
				new \Ratchet\Wamp\Topic('test'),
			],
		];
	}

/**
 * @dataProvider testOnCallBlockProvider
 */
	public function testOnCallBlock($topic) {
		$topicName = (string)$topic;

		$mock = $this->getMock(
			'\\Ratchet\\ConnectionInterface',
			[
			'send',
			'close',
			]
		);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(
			function ($results) {
			},
			function ($results) {
			}
		);
		$conn = $this->getMock(
			'\\Ratchet\\Wamp\\WampConnection',
			[
			'callError',
			],
			[
			$mock,
			]
		);

		$blockReason = [
			'error_uri' => 1,
			'desc' => 2,
			'details' => 3,
		];

		$conn->expects($this->once())
			->method('callError')
			->with(1, $blockReason['error_uri'], $blockReason['desc'], $blockReason['details']);
		$conn->Session = new SessionHandlerImposer();

		$asserts = [];
		$this->_expectedEventCalls(
			$asserts,
			[
			'Rachet.WampServer.Rpc' => [
				'callback' => [
					function ($event) use ($blockReason) {
						$event->result['stop_reason'] = $blockReason;
						$event->stopPropagation();
					},
				],
			],
			'Rachet.WampServer.RpcBlocked' => [
				'callback' => [
					function ($event) use ($conn, $topicName, $topic, $blockReason) {
						$this->assertEquals(
							$event->data,
							[
								'topicName' => $topicName,
								'connection' => $conn,
								'id' => 1,
								'reason' => $blockReason,
								'connectionData' => [
									'topics' => [],
									'session' => [],
								],
							]
						);
					},
				],
			],
			]
		);
		$this->AppServer->onOpen($conn);
		$this->AppServer->onCall(
			$conn,
			1,
			$topic,
			[
			'foo' => 'bar',
			]
		);

		foreach ($asserts as $assert) {
			$this->assertTrue($assert);
		}
	}
}
