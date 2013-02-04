<?php

App::uses('CakeEventListener', 'Event');
App::uses('ClassRegistry', 'Utilitty');
App::uses('AppController', 'Controller');
App::uses('Router', 'Routing');
App::uses('Dispatcher', 'Routing');
App::uses('RatchetAuthenticate', 'Ratchet.Controller/Component/Auth');

class RatchetCallUrlListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Rachet.WampServer.Rpc.callUrl' => 'callUrl'
        );
    }

    public function callUrl($event) {
        try {
            putenv('HTTP_X_REQUESTED_WITH=XMLHttpRequest');
            $result = $this->requestAction($event->data['params']['url'], $event->data['params']['data'], $event->data['connectionData']);
            $event->data['connection']->callResult($event->data['id'], array(
                $result,
            ));
        } catch (Exception $e) {
            $event->data['connection']->callResult($event->data['id'], array(
                'Fubar!',
            ));
        }
    }
    
    /**
     * 
     * @todo Not sure if Router::popRequest(); is needed or not
     * @param \Ratchet\ConnectionInterface $conn
     * @param string $url
     * @param array $data
     * @param array $connData
     * @return string 
     */
    private function requestAction($url, $data, $connData) {
        $request = new CakeRequest($url);
        $request->data = $data;
        $request->sessionAuth = array(
            'id' => $connData['session']['Auth']['User']['id'],
            'username' => $connData['session']['Auth']['User']['username'],
        );
        $dispatcher = new Dispatcher();
        ob_start();
        $dispatcher->dispatch($request, new CakeResponse());
        $result = ob_get_clean();
        Router::popRequest();
        return $result;
    }
}