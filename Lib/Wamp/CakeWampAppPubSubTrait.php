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
                'topicName' => $topicName,
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
                    'topicName' => $topicName,
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
                'topicName' => $topicName,
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
                    'topicName' => $topicName,
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
                'topicName' => $topicName,
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
} 