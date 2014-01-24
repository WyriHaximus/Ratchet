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

class CakeWampAppPubSubTraitTest extends CakeRatchetTestCase {

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
}
