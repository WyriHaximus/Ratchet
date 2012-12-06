<?php

App::uses('AppController', 'Controller');
App::uses('Router', 'Routing');
App::uses('Dispatcher', 'Routing');
App::uses('RatchetAuthenticate', 'Ratchet.Controller/Component/Auth');


use Ratchet\ConnectionInterface as Conn;

class CakeWampServer implements Ratchet\Wamp\WampServerInterface {
    
    private $shell;
    protected $connections = array();
    protected $guestConnectionCount = 0;
    protected $userConnectionCount = 0;
    protected $rpcs = array();
    protected $topics = array();
    
    public function __construct($shell) {
        $this->shell = $shell;
        $this->rpcs = array(
            'connectionCount' => function() {
                return array(
                    'guests' => $this->guestConnectionCount,
                    'users' => $this->userConnectionCount,
                );
            },
            'callUrl' => function(Conn $conn, $id, $topic, $params) {
                try {
                    putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');
                    $result = $this->requestAction($conn, $params['url'], $params['data']);
                    //$result = $this->shell->requestAction('/ratchet/ratchet/probeer', array(
                    $this->shell->out('Call result: ' . print_r($result, true));
                    $conn->callResult($id, array(
                        $result,
                    ));
                } catch (Exception $e) {
                    $this->shell->out('Exception: ' . $e->getMessage());
                    $conn->callResult($id, array(
                        'Fubar!',
                    ));
                }
            },
        );
    }
    
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        //debug($conn->Session->all());
        $this->shell->out('<success>Call with ID: ' . $id . '</success>');
        if (isset($this->rpcs[$topic->getId()])) {
            $conn->callResult($id, array(
                $this->rpcs[$topic->getId()]($conn, $id, $topic, $params),
            ));
        } else {
            $conn->callResult($id, array(
                'Fubar!',
            ));
        }
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically
    public function onSubscribe(Conn $conn, $topic) {
        if (!isset($this->topics[$topic->getId()])) {
            $this->topics[$topic->getId()] = $topic;
        }
    }
    
    public function onUnSubscribe(Conn $conn, $topic) {
        if ($this->topics[$topic->getId()]->count() > 0) {
            unset($this->topics[$topic->getId()]);
        }
    }

    public function onOpen(Conn $conn) {
        $this->connections[$conn->WAMP->sessionId] = array(
            'session' => $conn->Session->all(),
        );
        
        if (isset($this->connections[$conn->WAMP->sessionId]['session']['Auth']['User']['id'])) {
            $this->userConnectionCount++;
        } else {
            $this->guestConnectionCount++;
        }
        
        if (isset($this->topics['connectionCount'])) {
            $this->topics['connectionCount']->broadcast($this->rpcs['connectionCount']());
        }
    }
    public function onClose(Conn $conn) {
        if (isset($this->connections[$conn->WAMP->sessionId]['session']['Auth']['User']['id'])) {
            $this->userConnectionCount--;
        } else {
            $this->guestConnectionCount--;
        }
        
        if (isset($this->topics['connectionCount'])) {
            $this->topics['connectionCount']->broadcast($this->rpcs['connectionCount']());
        }
        
        unset($this->connections[$conn->WAMP->sessionId]);
    }
    public function onError(Conn $conn, \Exception $e) {}
    
    /**
     * 
     * @todo Not sure if Router::popRequest(); is needed or not
     * @param Ratchet\ConnectionInterface $conn
     * @param string $url
     * @param array $data
     * @return string 
     */
    private function requestAction($conn, $url, $data) {
        $request = new CakeRequest($url);
        $request->data = $data;
        $request->sessionAuth = array(
            'id' => $this->connections[$conn->WAMP->sessionId]['session']['Auth']['User']['id'],
            'username' => $this->connections[$conn->WAMP->sessionId]['session']['Auth']['User']['username'],
        );
        $dispatcher = new Dispatcher();
        ob_start();
        $dispatcher->dispatch($request, new CakeResponse());
        $result = ob_get_clean();
        Router::popRequest();
        return $result;
    }
    
}