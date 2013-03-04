<?php

Configure::write('Ratchet', array(
    'Client' => array(
        'retryDelay' => 500, // Not the best option but it speeds up development
        'maxRetries' => 500, // Keep on trying! (Also not the best option)
    ),
    'Connection' => array(
        'flashPolicy' => array(
            'address' => '0.0.0.0',
            'port' => 843,
        ),
        'websocket' => array(
            'address' => '0.0.0.0',
            'port' => 54321,
        ),
        'external' => array(
            'hostname' => 'localhost',
            'port' => 80,
            'path' => 'websocket',
            'secure' => false,
        ),
    ),
));

App::uses('CakeEventManager', 'Event');

App::uses('RatchetCallUrlListener', 'Ratchet.Event');
App::uses('RatchetConnectionStatisticsListener', 'Ratchet.Event');
App::uses('RatchetKeepAliveListener', 'Ratchet.Event');

CakeEventManager::instance()->attach(new RatchetCallUrlListener());
CakeEventManager::instance()->attach(new RatchetConnectionStatisticsListener());
CakeEventManager::instance()->attach(new RatchetKeepAliveListener());