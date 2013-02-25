<?php

use Ratchet\ConnectionInterface as Conn;

class CakeWampAppServer implements Ratchet\Wamp\WampServerInterface {
    
    private $shell;
    private $loop;
    protected $connections = array();
    
    public function __construct($shell, $loop) {
        $this->shell = $shell;
        $this->loop = $loop;
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.construct', $this, array(
            'loop' => $this->loop,
        )));
    }
    
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

    public function onCall(Conn $conn, $id, $topic, array $params) {
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.Rpc.' . $topic->getId(), $this, array(
            'connection' => $conn,
            'id' => $id,
            'topic' => $topic,
            'params' => $params,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }

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
    
    public function onUnSubscribe(Conn $conn, $topic) {
        if ($this->topics[$topic->getId()]->count() > 0) {
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

    public function onOpen(Conn $conn) {
        $this->connections[$conn->WAMP->sessionId] = array(
            'session' => $conn->Session->all(),
        );
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onOpen', $this, array(
            'connection' => $conn,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
    }
    public function onClose(Conn $conn) {
        CakeEventManager::instance()->dispatch(new CakeEvent('Rachet.WampServer.onClose', $this, array(
            'connection' => $conn,
            'connectionData' => $this->connections[$conn->WAMP->sessionId],
        )));
        
        unset($this->connections[$conn->WAMP->sessionId]);
    }
    
    public function onError(Conn $conn, \Exception $e) {}
}