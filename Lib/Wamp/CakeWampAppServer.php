<?php

/*
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
    protected $shell;
    
    /**
     * ReactPHP Eventloop
     * 
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;
    
    /**
     * Contains metadata for all open connections
     * 
     * @var array 
     */
    protected $connections = array();
    
    /**
     * Contains all active topics
     * 
     * @var type 
     */
    protected $topics = array();
    
    /**
     * Assigns the Shell and Loop
     * 
     * @param WebsocketShell $shell
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct($shell, $loop) {
        $this->shell = $shell;
        $this->loop = $loop;
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.construct', $this, array(
            'loop' => $this->loop,
        )));
    }
    
    /**
     * 
     * @return WebsocketShell
     */
    public function getShell() {
        return $this->shell;
    }
    
    /**
     * 
     * @return \React\EventLoop\LoopInterface
     */
    public function getLoop() {
        return $this->loop;
    }
    
    /**
     * 
     * @return array
     */
    public function getTopics() {
        return $this->topics;
    }
    
    /**
     * Breadcast the $event to all subscribers on $topic
     * 
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|\Ratchet\Wamp\Topic $topic
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     * @todo Add a test if $topic is a string
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onPublish.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'exclude' => $exclude,
            'eligible' => $eligible,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
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
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.Rpc.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'id' => $id,
            'topic' => $topic,
            'params' => $params,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }
    
    /**
     * Dispatches  anew topic event when this is the first client subscribing to this topic, also always firing a normal subscribe event
     * 
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|\Ratchet\Wamp\Topic $topic
     */
    public function onSubscribe(Conn $conn, $topic) {
        if (!isset($this->topics[$topic->getId()])) {
            $this->topics[$topic->getId()] = $topic;
            
            CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onSubscribeNewTopic.' . $topic->getId(), $this, array(
                'connection' => $conn,
                'topic' => $topic,
                'connectionData' => $this->connections[$conn->WAMP->sessionId],
            )));
        }
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onSubscribe.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }
    
    /**
     * Fires a stale topic event if this is the last client ubsubcribing and also always firing a ubsubscribe event
     * 
     * @param \Ratchet\ConnectionInterface $conn
     * @param string|\Ratchet\Wamp\Topic $topic
     */
    public function onUnSubscribe(Conn $conn, $topic) {
        if (isset($this->topics[$topic->getId()]) && $topic->count() > 0) {
            unset($this->topics[$topic->getId()]);
            
            CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic->getId(), $this, array(
                'connection' => $conn,
                'topic' => $topic,
                'connectionData' => $this->connections[$conn->WAMP->sessionId],
            )));
        }
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onUnSubscribe.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }
    
    /**
     * Stores session information and fires the onOpen event for listening listeners
     * 
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onOpen(Conn $conn) {
        $this->connections[$conn->WAMP->sessionId] = array(
            'session' => $conn->Session->all(),
        );
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onOpen', $this, array(
            'connection' => $conn,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }
    
    /**
     * Dispatches on a closing link, cleans up sesion and other connection data for this connection
     * 
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onClose(Conn $conn) {
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onClose', $this, array(
            'connection' => $conn,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        
        unset($this->connections[$conn->WAMP->sessionId]);
    }
    
    /**
     * Error catching
     * 
     * @param \Ratchet\ConnectionInterface $conn
     * @param \Exception $e
     * @todo do da error handling shuffle
     */
    public function onError(Conn $conn, \Exception $e) {}
}