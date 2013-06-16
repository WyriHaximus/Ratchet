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
     *
     * @var boolen
     */
    protected $verbose;


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
    public function __construct($shell, $loop, $verbose = false) {
        $this->shell = $shell;
        $this->loop = $loop;
        $this->verbose = $verbose;
        
        $this->outVerbose('Event begin: Rachet.WampServer.construct');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.construct', $this, array(
            'loop' => $this->loop,
        )));
        $this->outVerbose('Event end: Rachet.WampServer.construct');
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
     * @return \React\EventLoop\LoopInterface
     */
    public function getVerbose() {
        return $this->verbose;
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
        
        $this->outVerbose('Event begin: Rachet.WampServer.onPublish.<info>' . $topic->getId() . '</info>');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onPublish.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'exclude' => $exclude,
            'eligible' => $eligible,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onPublish.<info>' . $topic->getId() . '</info>');
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
        $this->outVerbose('Event begin: Rachet.WampServer.onPublish.<info>' . $topic->getId() . '</info>');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.Rpc.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'id' => $id,
            'topic' => $topic,
            'params' => $params,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onPublish.<info>' . $topic->getId() . '</info>');
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
            
            $this->outVerbose('Event begin: Rachet.WampServer.onSubscribeNewTopic.<info>' . $topic->getId() . '</info>');
            CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onSubscribeNewTopic.' . $topic->getId(), $this, array(
                'connection' => $conn,
                'topic' => $topic,
                'wampServer' => $this,
                'connectionData' => $this->connections[$conn->WAMP->sessionId],
            )));
            $this->outVerbose('Event end: Rachet.WampServer.onSubscribeNewTopic.<info>' . $topic->getId() . '</info>');
        }
        
        $this->outVerbose('Event begin: Rachet.WampServer.onSubscribe.<info>' . $topic->getId() . '</info>');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onSubscribe.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onSubscribe.<info>' . $topic->getId() . '</info>');
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
            
            $this->outVerbose('Event begin: Rachet.WampServer.onUnSubscribeStaleTopic.<info>' . $topic->getId() . '</info>');
            CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onUnSubscribeStaleTopic.' . $topic->getId(), $this, array(
                'connection' => $conn,
                'topic' => $topic,
                'wampServer' => $this,
                'connectionData' => $this->connections[$conn->WAMP->sessionId],
            )));
            $this->outVerbose('Event end: Rachet.WampServer.onUnSubscribeStaleTopic.<info>' . $topic->getId() . '</info>');
        }
        
        $this->outVerbose('Event begin: Rachet.WampServer.onUnSubscribe.<info>' . $topic->getId() . '</info>');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onUnSubscribe.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'topic' => $topic,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onUnSubscribe.<info>' . $topic->getId() . '</info>');
    }
    
    /**
     * Stores session information and fires the onOpen event for listening listeners
     * 
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onOpen(Conn $conn) {
        $this->outVerbose('New connection: <info>' . $conn->WAMP->sessionId . '</info>');
        
        $this->connections[$conn->WAMP->sessionId] = array(
            'session' => $conn->Session->all(),
        );
        
        $this->outVerbose('Event begin: Rachet.WampServer.onOpen');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onOpen', $this, array(
            'connection' => $conn,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onOpen');
    }
    
    /**
     * Dispatches on a closing link, cleans up sesion and other connection data for this connection
     * 
     * @param \Ratchet\ConnectionInterface $conn
     */
    public function onClose(Conn $conn) {
        $this->outVerbose('Event begin: Rachet.WampServer.onClose');
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onClose', $this, array(
            'connection' => $conn,
            'wampServer' => $this,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        $this->outVerbose('Event end: Rachet.WampServer.onClose');
        
        unset($this->connections[$conn->WAMP->sessionId]);
        
        $this->outVerbose('Closed connection: <info>' . $conn->WAMP->sessionId . '</info>');
    }
    
    /**
     * Error catching
     * 
     * @param \Ratchet\ConnectionInterface $conn
     * @param \Exception $e
     * @todo do da error handling shuffle
     */
    public function onError(Conn $conn, \Exception $e) {}
    
    /**
     * Output $message when verbose mode is on
     * 
     * @param string $message
     */
    public function outVerbose($message) {
        if ($this->verbose) {
            $time = microtime(true);
            $time = explode('.', $time);
            $time[1] = str_pad($time[1], 4, 0);
            $time = implode('.', $time);
            $this->shell->out('[<info>' . $time . '</info>] ' . $message);
        }
    }
}