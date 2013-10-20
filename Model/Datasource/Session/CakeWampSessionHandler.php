<?php

/*
* This file is part of Ratchet for CakePHP.
*
** (c) 2012 - 2013 Cees-Jan Kiewiet
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

class CakeWampSessionHandler implements SessionHandlerInterface {

/**
 * Start the session on instance construction
 */
	public function __construct() {
		session_start();
	}

/**
 * {@inheritdoc}
 */
	public function open($savePath, $sessionName) {
		return true;
	}

/**
 * {@inheritdoc}
 */
	public function close() {
		return true;
	}

/**
 * {@inheritdoc}
 */
	public function read($sessionId) {
		$sessionData = Cache::read($sessionId, Configure::read('Session.handler.config'));
		session_decode($sessionData);
		$restoredSessionData = $_SESSION;
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		return serialize($restoredSessionData);
	}

/**
 * {@inheritdoc}
 */
	public function write($sessionId, $data) {
		return true;
	}

/**
 * {@inheritdoc}
 */
	public function destroy($sessionId) {
		return true;
	}

/**
 * {@inheritdoc}
 */
	public function gc($lifetime) {
		return true;
	}

}
