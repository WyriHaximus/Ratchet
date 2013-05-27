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
        'keepaliveInterval' => 23,
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

/**
 * Statistical listeners
 */

App::uses('RatchetConnectionsListener', 'Ratchet.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetConnectionsListener());

App::uses('RatchetUptimeListener', 'Ratchet.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetUptimeListener());

App::uses('RatchetMemoryUsageListener', 'Ratchet.Event/Statistics');
CakeEventManager::instance()->attach(new RatchetMemoryUsageListener());

/**
 * Client services listener
 */

App::uses('RatchetKeepAliveListener', 'Ratchet.Event');
CakeEventManager::instance()->attach(new RatchetKeepAliveListener());

/**
 * Queue handler listeners
 */

switch (Configure::read('Ratchet.Queue.type')) {
    case 'Predis':
        App::uses('RatchetQueueCommandPredisListener', 'Ratchet.Event/Queue');
        CakeEventManager::instance()->attach(new RatchetQueueCommandPredisListener());
        break;
    case 'ZMQ':
        App::uses('RatchetQueueCommandZmqListener', 'Ratchet.Event/Queue');
        CakeEventManager::instance()->attach(new RatchetQueueCommandZmqListener());
        break;
    default:
        // Untill a release is tagged this Exception is thrown
        throw new Exception('Unknown queue type:' . Configure::read('Ratchet.Queue.type'));
        break;
}

/**
 * PhuninCake listener
 */

if (CakePlugin::loaded('PhuninCake')) {
    App::uses('RatchetPhuninCakeListener', 'Ratchet.Event');
    CakeEventManager::instance()->attach(new RatchetPhuninCakeListener());
}