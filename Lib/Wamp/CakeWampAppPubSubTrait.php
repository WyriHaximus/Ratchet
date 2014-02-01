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

trait CakeWampAppPubSubTrait {

/**
 * Contains all active topics
 *
 * @var array
 */
	protected $_topics = [];

/**
 * Contains all active subscribers
 *
 * @var array
 */
	protected $_subscribers = [];

/**
 *
 * @return array
 */
	public function getTopics() {
		return $this->_topics;
	}

/**
 * When topic is been listened to broadcast to it
 *
 * @param string $topic
 * @param array $payload
 */
	public function broadcast($topic, array $payload) {
		$topicName = $this->getTopicName($topic);
		if (isset($this->_topics[$topicName]['topic'])) {
			$this->_topics[$topicName]['topic']->broadcast($payload);

			$this->dispatchEvent(
				'Rachet.WampServer.broadcast',
				$this,
				[
					'topicName' => $topicName,
					'payload' => $payload,
				]
			);
		}
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

		$this->dispatchEvent(
			'Rachet.WampServer.onPublish',
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'event' => $event,
				'exclude' => $exclude,
				'eligible' => $eligible,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		$this->dispatchEvent(
			'Rachet.WampServer.onPublish.' . $topicName,
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'event' => $event,
				'exclude' => $exclude,
				'eligible' => $eligible,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);
	}

/**
 * Dispatches  anew topic event when this is the first client subscribing to this topic, also always firing a normal subscribe event
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string|\Ratchet\Wamp\Topic $topic
 */
	public function onSubscribe(Conn $conn, $topic) {
		$topicName = self::getTopicName($topic);

		$this->_connections[$conn->WAMP->sessionId]['topics'][$topicName] = true;

		if (!isset($this->_topics[$topicName])) {
			$this->_topics[$topicName] = [
				'listeners' => [],
			];

			if ($topic instanceof \Ratchet\Wamp\Topic) {
				$this->_topics[$topicName]['topic'] = $topic;
			}

			$this->dispatchEvent(
				'Rachet.WampServer.onSubscribeNewTopic',
				$this,
				[
					'topicName' => $topicName,
					'connection' => $conn,
					'wampServer' => $this,
					'connectionData' => $this->_connections[$conn->WAMP->sessionId],
				]
			);

			$this->dispatchEvent(
				'Rachet.WampServer.onSubscribeNewTopic.' . $topicName,
				$this,
				[
					'topicName' => $topicName,
					'connection' => $conn,
					'wampServer' => $this,
					'connectionData' => $this->_connections[$conn->WAMP->sessionId],
				]
			);
		}

		$this->dispatchEvent(
			'Rachet.WampServer.onSubscribe',
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		$this->dispatchEvent(
			'Rachet.WampServer.onSubscribe.' . $topicName,
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		$this->_topics[$topicName]['listeners'][$conn->WAMP->sessionId] = true;
	}

/**
 * Fires a stale topic event if this is the last client ubsubcribing and also always firing a ubsubscribe event
 *
 * @param \Ratchet\ConnectionInterface $conn
 * @param string|\Ratchet\Wamp\Topic $topic
 */
	public function onUnSubscribe(Conn $conn, $topic) {
		$topicName = self::getTopicName($topic);

		$this->dispatchEvent(
			'Rachet.WampServer.onUnSubscribe',
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		$this->dispatchEvent(
			'Rachet.WampServer.onUnSubscribe.' . $topicName,
			$this,
			[
				'topicName' => $topicName,
				'connection' => $conn,
				'wampServer' => $this,
				'connectionData' => $this->_connections[$conn->WAMP->sessionId],
			]
		);

		unset($this->_topics[$topicName]['listeners'][$conn->WAMP->sessionId], $this->_connections[$conn->WAMP->sessionId]['topics'][$topicName]);

		if (isset($this->_topics[$topicName]) && count($this->_topics[$topicName]['listeners']) == 0) {
			unset($this->_topics[$topicName]);

			$this->dispatchEvent(
				'Rachet.WampServer.onUnSubscribeStaleTopic',
				$this,
				[
					'topicName' => $topicName,
					'connection' => $conn,
					'wampServer' => $this,
					'connectionData' => $this->_connections[$conn->WAMP->sessionId],
				]
			);

			$this->dispatchEvent(
				'Rachet.WampServer.onUnSubscribeStaleTopic.' . $topicName,
				$this,
				[
					'topicName' => $topicName,
					'connection' => $conn,
					'wampServer' => $this,
					'connectionData' => $this->_connections[$conn->WAMP->sessionId],
				]
			);
		}
	}
} 