<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ratchet\ConnectionInterface;

class CakeWampSessionProvider extends \Ratchet\Session\SessionProvider {

/**
 * {@inheritdoc}
 */
	public function onOpen(ConnectionInterface $conn) {
		$iniSessionName = ini_get('session.name');
		ini_set('session.name', Configure::read('Session.cookie'));
		$return = parent::onOpen($conn);
		ini_set('session.name', $iniSessionName);
		return $return;
	}

}
