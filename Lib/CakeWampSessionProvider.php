<?php

use Ratchet\ConnectionInterface;

class CakeWampSessionProvider extends \Ratchet\Session\SessionProvider {
    
    /**
     * {@inheritdoc}
     */
    function onOpen(ConnectionInterface $conn) {
        $iniSessionName = ini_get('session.name');
        ini_set('session.name', Configure::read('Session.cookie'));
        $return = parent::onOpen($conn);
        ini_set('session.name', $iniSessionName);
        return $return;
    }
    
}