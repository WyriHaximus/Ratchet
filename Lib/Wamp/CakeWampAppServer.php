<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEvent', 'Event');
App::uses('CakeEventManager', 'Event');

use Ratchet\ConnectionInterface as Conn;

class CakeWampAppServer implements Ratchet\Wamp\WampServerInterface {

/**
 * WebsocketShell instance
 *
 * @var WebsocketShell
 */
	protected $_shell;

/**
 * ReactPHP Eventloop
 *
 * @var \React\EventLoop\LoopInterface
 */
	protected $_loop;

/**
 * Ratchet Topic Manager
 *
 * @var \Ratchet\Wamp\TopicManager
 */
	protected $_topicManager;

/**
 * Flag wether or not to display verbose output for debugging or troubleshooting
 *
 * @var boolean
 */
	protected $_verbose;

/**
 * Contains metadata for all open connections
 *
 * @var array
 */
	protected $_connections = [];

/**
 * Contains all active topics
 *
 * @var type
 */
	protected $_topics = [];

/**
 * Assigns the Shell and Loop
 *
 * @param WebsocketShell $shell
 * @param \React\EventLoop\LoopInterface $loop
 * @param boolean $verbose
 */
	public function __construct($shell, \React\EventLoop\LoopInterface $loop, $verbose = false) {
		$this->_shell = $shell;
		$this->_loop = $loop;
		$this->_verbose = $verbose;

		$this->outVerbose('Event begin: Rachet.WampServer.construct');
		CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.construct', $this, [
			'loop' => $this->_loop,
		]));
		$this->outVerbose('Event end: Rachet.WampServer.construct');
	}

/**
 *
 * @return WebsocketShell
 */
	public function getShell() {
		return $this->_shell;
	}

/**
 *
 * @return \React\EventLoop\LoopInterface
 */
	public function getLoop() {
		return $this->_loop;
	}

/**
 *
 * @return \React\EventLoop\LoopInterface
 */
	public function getVerbose() {
		return $this->_verbose;
	}

/**
 *
 * @return array
 */
	public function getTopics() {
		return $this->_topics;
	}

/**
 *
 * @param \Ratchet\Wamp\TopicManager $topicManager
 */
	public function setTopicManager(\Ratchet\Wamp\TopicManager $topicManager) {
		$this->_topicManager = $topicManager;
	}

/**
 * Breadcast the $event to all subscribers on $topic
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string|\Ratchet\Wamp\Topic $topic
 * @param string $event
 * @param array $exclude
 * @param array $eligible
 */
	public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible) {
		if ($topic instanceof \Ratchet\Wamp\Topic) {
			$topic->broadcast($event);
		}

		$topicName = self::getTopicName($topic);

		$this->dispatchEvent('Rachet.WampServer.onPublish', $this, [
			'connection' => $conn,
			'topic' => $topic,
			'event' => $event,
			'exclude' => $exclude,
			'eligible' => $eligible,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);

		$this->dispatchEvent('Rachet.WampServer.onPublish.' . $topicName, $this, [
			'connection' => $conn,
			'topic' => $topic,
			'event' => $event,
			'exclude' => $exclude,
			'eligible' => $eligible,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);
	}

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

		$start = microtime(true);

		$deferred = new \React\Promise\Deferred();
		$deferred->promise()->then(function($results) use ($conn, $id, $topicName, $start) {
			$end = microtime(true);
			$conn->callResult(
				$id,
				$results
			);

			$this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and succeeded');
		}, function($errorUri, $desc = '', $details = null) use ($conn, $id, $topicName, $start) {
			$end = microtime(true);

			$conn->callError(
				$id,
				$errorUri,
				$desc,
				$details
			);

			$this->outVerbose('Rachet.WampServer.Rpc.' . $topicName . ' call (' . $id . ') took <info>' . ($end - $start) . 's</info> and failed');
		});

		$this->dispatchEvent('Rachet.WampServer.Rpc.' . $topicName, $this, [
			'connection' => $conn,
			'promise' => $deferred->resolver(),
			'id' => $id,
			'topic' => $topic,
			'params' => $params,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);
	}

/**
 * Dispatches  anew topic event when this is the first client subscribing to this topic, also always firing a normal subscribe event
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string|\Ratchet\Wamp\Topic $topic
 */
	public function onSubscribe(Conn $conn, $topic) {
		$topicName = self::getTopicName($topic);

		if (!isset($this->_topics[$topicName])) {
			$this->_topics[$topicName] = [];


            $this->dispatchEvent('Rachet.WampServer.onSubscribeNewTopic', $this, [
                'connection' => $conn,
                'topic' => $topic,
                'wampServer' => $this,
                'connectionData' => $this->_connections[$conn->WAMP->sessionId],
            ]);

			$this->dispatchEvent('Rachet.WampServer.onSubscribeNewTopic.' . $topicName, $this, [
				'connection' => $conn,
				'topic' => $topic,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]);
		}

		$this->dispatchEvent('Rachet.WampServer.onSubscribe', $this, [
			'connection' => $conn,
			'topic' => $topic,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);

        $this->dispatchEvent('Rachet.WampServer.onSubscribe.' . $topicName, $this, [
			'connection' => $conn,
			'topic' => $topic,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);

		$this->_topics[$topicName][$conn->WAMP->sessionId] = true;
	}

/**
 * Fires a stale topic event if this is the last client ubsubcribing and also always firing a ubsubscribe event
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string|\Ratchet\Wamp\Topic $topic
 */
	public function onUnSubscribe(Conn $conn, $topic) {
		$topicName = self::getTopicName($topic);

		$this->_topics[$topicName]--;

		if (isset($this->_topics[$topicName]) && count($this->_topics[$topicName]) == 0) {
			unset($this->_topics[$topicName]);

			$this->dispatchEvent('Rachet.WampServer.onUnSubscribeStaleTopic', $this, [
				'connection' => $conn,
				'topic' => $topic,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]);

			$this->dispatchEvent('Rachet.WampServer.onUnSubscribeStaleTopic.' . $topicName, $this, [
				'connection' => $conn,
				'topic' => $topic,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]);
		}

        $this->dispatchEvent('Rachet.WampServer.onUnSubscribe', $this, [
            'connection' => $conn,
            'topic' => $topic,
            'wampServer' => $this,
            'connectionData' => $this->_connections[$conn->WAMP->sessionId],
        ]);

		$this->dispatchEvent('Rachet.WampServer.onUnSubscribe.' . $topicName, $this, [
			'connection' => $conn,
			'topic' => $topic,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);

		unset($this->_topics[$topicName][$conn->WAMP->sessionId]);
	}

/**
 * Stores session information and fires the onOpen event for listening listeners
 *
 * @param \Ratchet\ConnectionInterface $conn
 */
	public function onOpen(Conn $conn) {
		$this->outVerbose('New connection: <info>' . $conn->WAMP->sessionId . '</info>');

		$this->_connections[$conn->WAMP->sessionId] = [
			'session' => $conn->Session->all(),
		];

		$this->dispatchEvent('Rachet.WampServer.onOpen', $this, [
			'connection' => $conn,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);
	}

/**
 * Dispatches on a closing link, cleans up sesion and other connection data for this connection
 *
 * @param \Ratchet\ConnectionInterface $conn
 */
	public function onClose(Conn $conn) {
		foreach ($this->_topics as $topicName => $connections) {
			foreach ($connections as $connectionId => $boolean) {
				if ($connectionId == $conn->WAMP->sessionId) {
					$this->onUnSubscribe($conn, $topicName);
				}
			}
		}

		$this->dispatchEvent('Rachet.WampServer.onClose', $this, [
			'connection' => $conn,
			'wampServer' => $this,
			'connectionData' => $this->_connections[$conn->WAMP->sessionId],
		]);

		unset($this->_connections[$conn->WAMP->sessionId]);

		$this->outVerbose('Closed connection: <info>' . $conn->WAMP->sessionId . '</info>');
	}

/**
 * Silently ignore exceptions
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param \Exception $e
 */
	public function onError(Conn $conn, \Exception $e) {
	}

/**
 * Syntactic sugar improving the readability for on* methods
 *
 * @param string $eventName
 * @param object $scope
 * @param array $params
 */
	public function dispatchEvent($eventName, $scope, $params) {
		$this->outVerbose('Event begin: ' . $eventName);
		CakeEventManager::instance()->dispatch(new CakeEvent($eventName, $scope, $params));
		$this->outVerbose('Event end: ' . $eventName);
	}

/**
 * Output $message when verbose mode is on
 *
 * @param string $message
 */
	public function outVerbose($message) {
		if ($this->_verbose) {
			$time = microtime(true);
			$time = explode('.', $time);
			if (!isset($time[1])) {
				$time[1] = 0;
			}
			$time[1] = str_pad($time[1], 4, 0);
			$time = implode('.', $time);
			$this->_shell->out('[<info>' . $time . '</info>] ' . $message);
		}
	}

	static public function getTopicName($topic) {
		if ($topic instanceof \Ratchet\Wamp\Topic) {
			return $topic->getId();
		} else {
			return $topic;
		}
	}

}
