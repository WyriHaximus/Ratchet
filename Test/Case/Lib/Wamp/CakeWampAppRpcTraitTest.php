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
}
