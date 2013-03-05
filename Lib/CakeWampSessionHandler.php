<?php

class CakeWampSessionHandler implements SessionHandlerInterface {
    
    public function __construct() {
        session_start();
    }
    
    /**
     * Open session.
     *
     * @see http://php.net/sessionhandlerinterface.open
     *
     * @param string $savePath    Save path.
     * @param string $sessionName Session Name.
     *
     * @throws \RuntimeException If something goes wrong starting the session.
     *
     * @return boolean
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    /**
     * Close session.
     *
     * @see http://php.net/sessionhandlerinterface.close
     *
     * @return boolean
     */
    public function close() {
        return true;
    }

    /**
     * Read session.
     *
     * @param string $sessionId
     *
     * @see http://php.net/sessionhandlerinterface.read
     *
     * @throws \RuntimeException On fatal error but not "record not found".
     *
     * @return string String as stored in persistent storage or empty string in all other cases.
     */
    public function read($sessionId) {
        $sessionData = Cache::read($sessionId, Configure::read('Session.handler.config'));
        session_decode($sessionData);
        $restoredSessionData = $_SESSION;
        foreach ($_SESSION as $key => $value){
            unset($_SESSION[$key]);
        }
        return serialize($restoredSessionData);
    }

    /**
     * Commit session to storage.
     *
     * @see http://php.net/sessionhandlerinterface.write
     *
     * @param string $sessionId Session ID.
     * @param string $data      Session serialized data to save.
     *
     * @return boolean
     */
    public function write($sessionId, $data) {
        return true;
    }

    public function destroy($sessionId) {
        return true;
    }

    public function gc($lifetime) {
        return true;
    }
    
}