<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('WebsocketShell', 'Ratchet.Console/Command');
App::uses('CakeWampAppServer', 'Ratchet.Lib/Wamp');
App::uses('CakeRatchetTestCase', 'Ratchet.Test/Case');

class SessionHandlerImposer {

	public function all() {
		return [];
	}

}

class CakeWampAppServerTest extends CakeRatchetTestCase {

	private $__expectedOutput = [];

/**
 * {@inheritdoc}
 */
	public function setUp() {
		parent::setUp();

		$this->_hibernateListeners('Rachet.WampServer.construct');

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.construct#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.construct#';

		$this->loop = $this->getMock('React\\EventLoop\\LoopInterface');
		$this->AppServer = new CakeWampAppServer($this, $this->loop, true);
	}

/**
 * {@inheritdoc}
 */
	public function tearDown() {
		unset($this->AppServer);

		$this->_wakeupListeners('Rachet.WampServer.construct');

		parent::tearDown();
	}

	public function testGetShell() {
		$this->assertEquals($this->AppServer->getShell(), $this);
	}

	public function testGetLoop() {
		$this->assertEquals($this->AppServer->getLoop(), $this->loop);
	}

	public function testGetVerbose() {
		$this->assertEquals($this->AppServer->getVerbose(), true);
	}

	public function testGetTopics() {
		$this->assertEquals($this->AppServer->getTopics(), []);
		$this->assertEquals(count($this->AppServer->getTopics()), 0);
	}

	public function testGetTopicNameProvider() {
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
 * @dataProvider testGetTopicNameProvider
 */
	public function testGetTopicName($topic) {
		$this->assertSame('test', CakeWampAppServer::getTopicName($topic));
	}

	public function testOnOpen() {
		$this->_hibernateListeners('Rachet.WampServer.onOpen');

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';

		$callbackFired = false;
		$eventCallback = function($event) use(&$callbackFired, $conn) {
			$this->assertEquals($event->data, [
				'connection' => $conn,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired = true;
		};
		CakeEventManager::instance()->attach($eventCallback, 'Rachet.WampServer.onOpen');

		$this->AppServer->onOpen($conn);

		$this->assertTrue($callbackFired);
		$this->assertSame(0, count($this->__expectedOutput));
		CakeEventManager::instance()->detach($eventCallback, 'Rachet.WampServer.onOpen');
		$this->_wakeupListeners('Rachet.WampServer.onOpen');
	}

	public function testOnClose() {
		$this->_hibernateListeners('Rachet.WampServer.onClose');

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onClose#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onClose#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Closed connection: [<info>0-9a-zA-Z</info>]+#';

		$callbackFired = false;
		$eventCallback = function($event) use(&$callbackFired, $conn) {
			$this->assertEquals($event->data, [
				'connection' => $conn,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired = true;
		};
		CakeEventManager::instance()->attach($eventCallback, 'Rachet.WampServer.onClose');

		$this->AppServer->onOpen($conn);
		$this->AppServer->onClose($conn);

		$this->assertTrue($callbackFired);
		$this->assertSame(0, count($this->__expectedOutput));
		CakeEventManager::instance()->detach($eventCallback, 'Rachet.WampServer.onClose');
		$this->_wakeupListeners('Rachet.WampServer.onClose');
	}

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
		$callbackFired = false;
		$results = [
			'foo:bar',
		];

		$this->_hibernateListeners('Rachet.WampServer.Rpc.' . $topic);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface', [
			'send',
			'close',
		]);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(function($results) {
		}, function($results) {
		});
		$conn = $this->getMock('\\Ratchet\\Wamp\\WampConnection', [
			'callResult',
		], [
			$mock,
		]);
		$conn->expects($this->once())
			->method('callResult')
			->with(1, $results);
		$conn->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info> Rachet.WampServer.Rpc.test call (1) took <info>[0-9]+.[0-9]+s</info>] and succeeded#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc.' . $topic . '#';

		$eventCallback = function($event) use(&$callbackFired, $conn, $topic, $deferred, $results) {
			$resolver = $deferred->resolver();

			$this->assertEquals($event->data, [
				'connection' => $conn,
				'promise' => $resolver,
				'id' => 1,
				'topic' => $topic,
				'params' => [
					'foo' => 'bar',
				],
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired = true;

			$event->data['promise']->resolve($results);
		};
		CakeEventManager::instance()->attach($eventCallback, 'Rachet.WampServer.Rpc.' . $topic);

		$this->AppServer->onOpen($conn);
		$this->AppServer->onCall($conn, 1, $topic, [
			'foo' => 'bar',
		]);

		$this->assertTrue($callbackFired);
		$this->assertSame(0, count($this->__expectedOutput));
		CakeEventManager::instance()->detach($eventCallback, 'Rachet.WampServer.Rpc.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.Rpc.' . $topic);
	}

	public function testOnCallRejectProvider() {
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
 * @dataProvider testOnCallRejectProvider
 */
	public function testOnCallReject($topic) {
		$callbackFired = false;
		$results = 'foo:bar';

		$this->_hibernateListeners('Rachet.WampServer.Rpc.' . $topic);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface', [
			'send',
			'close',
		]);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(function($results) {
		}, function($results) {
		});
		$conn = $this->getMock('\\Ratchet\\Wamp\\WampConnection', [
			'callError',
		], [
			$mock,
		]);
		$conn->expects($this->once())
			->method('callError')
			->with(1, $results, '', null);
		$conn->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info> Rachet.WampServer.Rpc.test call (1) took <info>[0-9]+.[0-9]+s</info>] and failed#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc.' . $topic . '#';

		$eventCallback = function($event) use(&$callbackFired, $conn, $topic, $deferred, $results) {
			$resolver = $deferred->resolver();

			$this->assertEquals($event->data, [
				'connection' => $conn,
				'promise' => $resolver,
				'id' => 1,
				'topic' => $topic,
				'params' => [
					'foo' => 'bar',
				],
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired = true;

			$event->data['promise']->reject($results);
		};
		CakeEventManager::instance()->attach($eventCallback, 'Rachet.WampServer.Rpc.' . $topic);

		$this->AppServer->onOpen($conn);
		$this->AppServer->onCall($conn, 1, $topic, [
			'foo' => 'bar',
		]);

		$this->assertTrue($callbackFired);
		$this->assertSame(0, count($this->__expectedOutput));
		CakeEventManager::instance()->detach($eventCallback, 'Rachet.WampServer.Rpc.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.Rpc.' . $topic);
	}

	public function testOnPublishProvider() {
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
 * @dataProvider testOnPublishProvider
 */
	public function testOnPublish($topic) {
		$this->_hibernateListeners('Rachet.WampServer.onPublish.' . $topic);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onPublish#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onPublish#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onPublish.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onPublish.' . $topic . '#';

		$exclude = [
			'foo' => 'bar',
		];
		$eligible = [
			'bar' => 'foo',
		];
		$eventData = [
			'faa' => 'bor',
		];

		$callbackFired = false;
		$eventCallback = function($event) use(&$callbackFired, $conn, $topic, $exclude, $eligible, $eventData) {
			$this->assertEquals($event->data, [
				'connection' => $conn,
				'topic' => $topic,
				'event' => $eventData,
				'exclude' => $exclude,
				'eligible' => $eligible,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired = true;
		};
		CakeEventManager::instance()->attach($eventCallback, 'Rachet.WampServer.onPublish.' . $topic);

		$this->AppServer->onOpen($conn);
		$this->AppServer->onPublish($conn, $topic, $eventData, $exclude, $eligible);

		$this->assertTrue($callbackFired);
		$this->assertSame(0, count($this->__expectedOutput));
		CakeEventManager::instance()->detach($eventCallback, 'Rachet.WampServer.onPublish.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.onPublish.' . $topic);
	}

	public function testOnSubscribeProvider() {
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
 * @dataProvider testOnSubscribeProvider
 */
	public function testOnSubscribe($topic) {
		$this->_hibernateListeners('Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		$this->_hibernateListeners('Rachet.WampServer.onSubscribe.' . $topic);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn1 = new Ratchet\Wamp\WampConnection($mock);
		$conn1->Session = new SessionHandlerImposer();
		$conn2 = new Ratchet\Wamp\WampConnection($mock);
		$conn2->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribeNewTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribeNewTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribeNewTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribeNewTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe.' . $topic . '#';

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe.' . $topic . '#';

		$callbackFired = [
			false,
			false,
			false,
		];
		$callbackFiredI = 0;

		$eventCallback1 = function($event) use(&$callbackFired, &$callbackFiredI, $conn1, $topic) {
			$this->assertEquals($event->data, [
				'connection' => $conn1,
				'topic' => $topic,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired[$callbackFiredI++] = true;
		};
		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);

		$this->AppServer->onOpen($conn1);
		$this->AppServer->onSubscribe($conn1, $topic);

		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);
		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);

		$eventCallback2 = function($event) use(&$callbackFired, &$callbackFiredI, $conn2, $topic) {
			$this->assertEquals($event->data, [
				'connection' => $conn2,
				'topic' => $topic,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired[$callbackFiredI++] = true;
		};
		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onSubscribe.' . $topic);

		$this->AppServer->onOpen($conn2);
		$this->AppServer->onSubscribe($conn2, $topic);

		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onSubscribe.' . $topic);

		$this->assertTrue($callbackFired[0]);
		$this->assertTrue($callbackFired[1]);
		$this->assertTrue($callbackFired[2]);
		$this->assertSame(3, $callbackFiredI);
		$this->assertSame(0, count($this->__expectedOutput));

		$this->_wakeupListeners('Rachet.WampServer.onSubscribe.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.onSubscribeNewTopic.' . $topic);
	}

	public function testOnUnSubscribeProvider() {
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
 * @dataProvider testOnUnSubscribeProvider
 */
	public function testOnUnSubscribe($topic) {
		$this->_hibernateListeners('Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		$this->_hibernateListeners('Rachet.WampServer.onSubscribe.' . $topic);
		$this->_hibernateListeners('Rachet.WampServer.onUnSubscribe.' . $topic);
		$this->_hibernateListeners('Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn1 = new Ratchet\Wamp\WampConnection($mock);
		$conn1->Session = new SessionHandlerImposer();
		$conn2 = new Ratchet\Wamp\WampConnection($mock);
		$conn2->Session = new SessionHandlerImposer();

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribeNewTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribeNewTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribeNewTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribeNewTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe.' . $topic . '#';

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] New connection: [<info>0-9a-zA-Z</info>]+#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onSubscribe.' . $topic . '#';

		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribeStaleTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribeStaleTopic#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribe#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onUnSubscribe.' . $topic . '#';
		$this->__expectedOutput[] = '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onUnSubscribe.' . $topic . '#';

		$callbackFired = [
			false,
			false,
			false,
			false,
			false,
			false,
		];
		$callbackFiredI = 0;

		$eventCallback1 = function($event) use(&$callbackFired, &$callbackFiredI, $conn1, $topic) {
			$this->assertEquals($event->data, [
				'connection' => $conn1,
				'topic' => $topic,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired[$callbackFiredI++] = true;
		};

		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);
		$this->AppServer->onOpen($conn1);
		$this->AppServer->onSubscribe($conn1, $topic);
		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);
		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onSubscribe.' . $topic);

		$eventCallback2 = function($event) use(&$callbackFired, &$callbackFiredI, $conn2, $topic) {
			$this->assertEquals($event->data, [
				'connection' => $conn2,
				'topic' => $topic,
				'wampServer' => $this->AppServer,
				'connectionData' => [
					'session' => [],
				],
			]);
			$callbackFired[$callbackFiredI++] = true;
		};
		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onSubscribe.' . $topic);
		$this->AppServer->onOpen($conn2);
		$this->AppServer->onSubscribe($conn2, $topic);
		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onSubscribe.' . $topic);

		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback1, 'Rachet.WampServer.onUnSubscribe.' . $topic);
		$this->AppServer->onUnSubscribe($conn1, $topic);
		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);
		CakeEventManager::instance()->detach($eventCallback1, 'Rachet.WampServer.onUnSubscribe.' . $topic);

		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);
		CakeEventManager::instance()->attach($eventCallback2, 'Rachet.WampServer.onUnSubscribe.' . $topic);
		$this->AppServer->onUnSubscribe($conn2, $topic);
		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);
		CakeEventManager::instance()->detach($eventCallback2, 'Rachet.WampServer.onUnSubscribe.' . $topic);

		$this->assertTrue($callbackFired[0]);
		$this->assertTrue($callbackFired[1]);
		$this->assertTrue($callbackFired[2]);
		$this->assertTrue($callbackFired[3]);
		$this->assertTrue($callbackFired[4]);
		$this->assertTrue($callbackFired[5]);
		$this->assertSame(6, $callbackFiredI);
		$this->assertSame(0, count($this->__expectedOutput));

		$this->_wakeupListeners('Rachet.WampServer.onSubscribe.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.onSubscribeNewTopic.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.onUnSubscribe.' . $topic);
		$this->_wakeupListeners('Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic);
	}

	public function out($message) {
		$expectedMessage = array_shift($this->__expectedOutput);
		$this->assertTrue(!is_null($expectedMessage), 'Expected output string missing');
		$this->assertRegExp($expectedMessage, $message);
	}

}
