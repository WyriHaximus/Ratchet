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
App::uses('CakeWampAppConnectionTrait', 'Ratchet.Lib/Wamp');
App::uses('CakeWampAppRpcTrait', 'Ratchet.Lib/Wamp');
App::uses('CakeWampAppPubSubTrait', 'Ratchet.Lib/Wamp');
App::uses('WebsocketShell', 'Ratchet.Console/Command');

use Ratchet\ConnectionInterface as Conn;

class CakeWampAppServer implements Ratchet\Wamp\WampServerInterface {

	use CakeWampAppConnectionTrait;
	use CakeWampAppPubSubTrait;
	use CakeWampAppRpcTrait;

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
 * CakeEventManager
 *
 * @var CakeEventManager
 */
	protected $_eventManager;

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
	public function __construct(
		WebsocketShell $shell,
		\React\EventLoop\LoopInterface $loop,
		CakeEventManager $eventManager,
		$verbose = false
	) {
		$this->_shell = $shell;
		$this->_loop = $loop;
		$this->_eventManager = $eventManager;
		$this->_verbose = $verbose;

		$this->dispatchEvent(
			'Rachet.WampServer.construct',
			$this,
			[
				'loop' => $this->_loop,
			]
		);
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
		$event = new CakeEvent($eventName, $scope, $params);

		$this->outVerbose('Event begin: ' . $eventName);
		$this->_eventManager->dispatch($event);
		$this->outVerbose('Event end: ' . $eventName);

		return $event;
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

/**
 * @param string|\Ratchet\Wamp\Topic $topic
 *
 * @return string
 */
	static public function getTopicName($topic) {
		if ($topic instanceof \Ratchet\Wamp\Topic) {
			return $topic->getId();
		} else {
			return $topic;
		}
	}
}
