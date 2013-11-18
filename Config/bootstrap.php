<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Configuration
 */
Configure::write('Ratchet', array(
	'Client' => array(
		'retryDelay' => 5000, // Not the best option but it speeds up development
		'maxRetries' => 25, // Keep on trying! (Also not the best option)
	),
	'Connection' => array(
		'websocket' => array(
			'address' => '0.0.0.0',
			'port' => 11001,
		),
		'external' => array(
			'hostname' => 'localhost',
			'port' => 80,
			'path' => 'websocket',
			'secure' => false,
		),
		'keepaliveInterval' => 23,
	),
));

App::uses('CakeEventManager', 'Event');

/**
 * Client services listener
 */

App::uses('RatchetKeepAliveListener', 'Ratchet.Event');
CakeEventManager::instance()->attach(new RatchetKeepAliveListener());
