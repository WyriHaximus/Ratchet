<?php

Configure::write('Ratchet', array(
    'Client' => array(
        'retryDelay' => 500, // Not the best option but it speeds up development
        'maxRetries' => 500, // Keep on trying! (Also not the best option)
    ),
    'Connection' => array(
        'flashPolicy' => array(
            'address' => '0.0.0.0',
            'port' => 12001,
        ),
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
    ),
    'Queue' => array(
        /*'type' => 'Predis',
        'key' => 'test_reddis_opuapugfoyiufgiawe',
        'server' => array(
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
            'database' => 12,
        ),*/
        'type' => 'ZMQ',
        'server' => 'tcp://127.0.0.1:13001',
    ),
));

App::uses('CakeEventManager', 'Event');

App::uses('RatchetCallUrlListener', 'Ratchet.Event');
App::uses('RatchetConnectionStatisticsListener', 'Ratchet.Event');
App::uses('RatchetKeepAliveListener', 'Ratchet.Event');
App::uses('RatchetModelUpdateListener', 'Ratchet.Event');

CakeEventManager::instance()->attach(new RatchetCallUrlListener());
CakeEventManager::instance()->attach(new RatchetConnectionStatisticsListener());
CakeEventManager::instance()->attach(new RatchetKeepAliveListener());
CakeEventManager::instance()->attach(new RatchetModelUpdateListener());