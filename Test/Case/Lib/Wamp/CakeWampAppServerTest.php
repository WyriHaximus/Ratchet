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

		$this->loop = $this->getMock('React\\EventLoop\\LoopInterface');
		$this->eventManagerOld = CakeEventManager::instance();
		$this->eventManager = CakeEventManager::instance(new CakeEventManager());
		$this->AppServer = new CakeWampAppServer($this, $this->loop, $this->eventManager, true);
	}

/**
 * {@inheritdoc}
 */
	public function tearDown() {
		unset($this->AppServer, $this->eventManager);

        CakeEventManager::instance($this->eventManagerOld);

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
        $mock = $this->getMock('\\Ratchet\\ConnectionInterface');
        $conn = new Ratchet\Wamp\WampConnection($mock);
        $conn->Session = new SessionHandlerImposer();

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.onOpen' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#',
                ],
                'callback' => [
                    function($event) use($conn) {
                        $this->assertEquals($event->data, [
                            'connection' => $conn,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
        ]);
        $this->AppServer->onOpen($conn);

        foreach ($asserts as $assert) {
            $this->assertTrue($assert);
        }
	}

	public function testOnClose() {
        $mock = $this->getMock('\\Ratchet\\ConnectionInterface');
        $conn = new Ratchet\Wamp\WampConnection($mock);
        $conn->Session = new SessionHandlerImposer();

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.onClose' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onClose#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onClose#',
                ],
                'callback' => [
                    function($event) use($conn) {
                        $this->assertEquals($event->data, [
                            'connection' => $conn,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
        ]);
        $this->AppServer->onOpen($conn);
        $this->AppServer->onClose($conn);

        foreach ($asserts as $assert) {
            $this->assertTrue($assert);
        }
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
        $topicName = (string) $topic;
        $results = [
            'foo:bar',
        ];
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

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.Rpc' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
                            'connection' => $conn,
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
                    },
                ],
            ],
            'Rachet.WampServer.Rpc.' . $topicName => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic, $deferred, $results) {
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

                        $event->data['promise']->resolve($results);
                    },
                ],
            ],
        ]);
        $this->AppServer->onOpen($conn);
        $this->AppServer->onCall($conn, 1, $topic, [
            'foo' => 'bar',
        ]);

        foreach ($asserts as $assert) {
            $this->assertTrue($assert);
        }
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
        $topicName = (string) $topic;
        $results = 'foo:bar';

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

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.Rpc' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
                            'connection' => $conn,
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
                    },
                ],
            ],
            'Rachet.WampServer.Rpc.' . $topicName => [
                'eventname' => 'Rachet.WampServer.Rpc.' . $topicName,
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic, $deferred, $results) {
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

                        $event->data['promise']->reject($results);
                    },
                ],
            ],
        ]);
        $this->AppServer->onOpen($conn);
        $this->AppServer->onCall($conn, 1, $topic, [
                'foo' => 'bar',
            ]);

        foreach ($asserts as $assert) {
            $this->assertTrue($assert);
        }
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
        $topicName = (string) $topic;

        $mock = $this->getMock('\\Ratchet\\ConnectionInterface');
        $conn = new Ratchet\Wamp\WampConnection($mock);
        $conn->Session = new SessionHandlerImposer();

        $exclude = [
            'foo' => 'bar',
        ];
        $eligible = [
            'bar' => 'foo',
        ];
        $eventData = [
            'faa' => 'bor',
        ];

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.onPublish' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic, $exclude, $eligible, $eventData) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
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
                    },
                ],
            ],
            'Rachet.WampServer.onPublish.' . $topicName => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn, $topicName, $topic, $exclude, $eligible, $eventData) {
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
                    },
                ],
            ],
        ]);

        $this->AppServer->onOpen($conn);
        $this->AppServer->onPublish($conn, $topic, $eventData, $exclude, $eligible);

        foreach ($asserts as $assert) {
            $this->assertTrue($assert);
        }
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
        $topicName = (string) $topic;

        $mock = $this->getMock('\\Ratchet\\ConnectionInterface');
        $conn1 = new Ratchet\Wamp\WampConnection($mock);
        $conn1->Session = new SessionHandlerImposer();
        $conn2 = new Ratchet\Wamp\WampConnection($mock);
        $conn2->Session = new SessionHandlerImposer();

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
            'Rachet.WampServer.onSubscribeNewTopic' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn1, $topicName, $topic) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
                            'connection' => $conn1,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
            'Rachet.WampServer.onSubscribeNewTopic.' . $topicName => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn1, $topic) {
                        $this->assertEquals($event->data, [
                            'connection' => $conn1,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
            'Rachet.WampServer.onSubscribe' => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn1, $topicName, $topic) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
                            'connection' => $conn1,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                    function($event) use($conn2, $topicName, $topic) {
                        $this->assertEquals($event->data, [
                            'topicName' => $topicName,
                            'connection' => $conn2,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
            'Rachet.WampServer.onSubscribe.' . $topicName => [
                'output' => [
                    '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                    '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                ],
                'callback' => [
                    function($event) use($conn1, $topic) {
                        $this->assertEquals($event->data, [
                            'connection' => $conn1,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                    function($event) use($conn2, $topic) {
                        $this->assertEquals($event->data, [
                            'connection' => $conn2,
                            'topic' => $topic,
                            'wampServer' => $this->AppServer,
                            'connectionData' => [
                                'session' => [],
                            ],
                        ]);
                    },
                ],
            ],
        ]);

        $this->AppServer->onOpen($conn1);
        $this->AppServer->onSubscribe($conn1, $topic);
        $this->AppServer->onOpen($conn2);
        $this->AppServer->onSubscribe($conn2, $topic);

        foreach ($asserts as $key => $assert) {
            $this->assertTrue($assert, $key);
        }
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
        $topicName = (string) $topic;

        $mock = $this->getMock('\\Ratchet\\ConnectionInterface');
        $conn1 = new Ratchet\Wamp\WampConnection($mock);
        $conn1->Session = new SessionHandlerImposer();
        $conn2 = new Ratchet\Wamp\WampConnection($mock);
        $conn2->Session = new SessionHandlerImposer();

        $asserts = [];
        $this->_expectedEventCalls($asserts, [
                'Rachet.WampServer.onUnSubscribeStaleTopic' => [
                    'output' => [
                        '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                        '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                    ],
                    'callback' => [
                        function($event) use($conn1, $topicName, $topic) {
                            $this->assertEquals($event->data, [
                                    'topicName' => $topicName,
                                    'connection' => $conn1,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                    ],
                ],
                'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topicName => [
                    'output' => [
                        '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                        '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                    ],
                    'callback' => [
                        function($event) use($conn2, $topic) {
                            $this->assertEquals($event->data, [
                                    'connection' => $conn2,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                    ],
                ],
                'Rachet.WampServer.onUnSubscribe' => [
                    'output' => [
                        '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                        '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                    ],
                    'callback' => [
                        function($event) use($conn1, $topicName, $topic) {
                            $this->assertEquals($event->data, [
                                    'topicName' => $topicName,
                                    'connection' => $conn1,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                        function($event) use($conn1, $topicName, $topic) {
                            $this->assertEquals($event->data, [
                                    'topicName' => $topicName,
                                    'connection' => $conn1,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                    ],
                ],
                'Rachet.WampServer.onUnSubscribe.' . $topicName => [
                    'output' => [
                        '#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.Rpc#',
                        '#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.Rpc#',
                    ],
                    'callback' => [
                        function($event) use($conn1, $topic) {
                            $this->assertEquals($event->data, [
                                    'connection' => $conn1,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                        function($event) use($conn2, $topic) {
                            $this->assertEquals($event->data, [
                                    'connection' => $conn2,
                                    'topic' => $topic,
                                    'wampServer' => $this->AppServer,
                                    'connectionData' => [
                                        'session' => [],
                                    ],
                                ]);
                        },
                    ],
                ],
            ]);

        $this->AppServer->onOpen($conn1);
        $this->AppServer->onSubscribe($conn1, $topic);
        $this->AppServer->onOpen($conn2);
        $this->AppServer->onSubscribe($conn2, $topic);

        $this->AppServer->onUnSubscribe($conn1, $topic);
        $this->AppServer->onUnSubscribe($conn2, $topic);

        foreach ($asserts as $key => $assert) {
            $this->assertTrue($assert, $key);
        }
	}

	public function out($message) {
		//$expectedMessage = array_shift($this->__expectedOutput);
		//$this->assertTrue(!is_null($expectedMessage), 'Expected output string missing');
		//$this->assertRegExp($expectedMessage, $message);
	}

}
