<?php

App::uses('Router', 'Routing');

use Ratchet\ConnectionInterface as Conn;

class CakeWampServer implements Ratchet\Wamp\WampServerInterface {
    
    private $shell;
    
    public function __construct($shell) {
        $this->shell = $shell;
    }
    
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
    }

    public function onCall(Conn $conn, $id, $topic, array $params) {
        $this->shell->out('<success>Call with ID: ' . $id . '</success>');
        try {
            putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');
            //$result = $this->shell->requestAction($topic->getId(), array(
            $result = $this->shell->requestAction('/ratchet/ratchet/probeer', array(
                'return' => true,
                'data' => $params,
            ));
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
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically
    public function onSubscribe(Conn $conn, $topic) {}
    public function onUnSubscribe(Conn $conn, $topic) {}

    public function onOpen(Conn $conn) {}
    public function onClose(Conn $conn) {}
    public function onError(Conn $conn, \Exception $e) {}
    
}