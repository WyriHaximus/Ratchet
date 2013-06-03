<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('RatchetMessageQueueTransportInterface', 'Ratchet.Lib/MessageQueue/Interfaces');
App::uses('RatchetMessageQueuePredis', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueZmq', 'Ratchet.Lib/MessageQueue/Transports');

class TransportProxy implements RatchetMessageQueueTransportInterface {
    
    /**
     * Singleton self reference trick
     * @var TransportProxy 
     */
    protected static $_generalMessageQueueProxy = null;
    
    /**
     *
     * @var RatchetMessageQueueTransportInterface
     */
    private $transport;
    
    /**
     * Construct the proxy
     * 
     * @param array $serverConfiguration
     */
    public function __construct($serverConfiguration) {
        list($plugin, $transporter) = pluginSplit(Configure::read('Ratchet.Queue.transporter'), true);
        App::uses($transporter, $plugin . 'Lib/MessageQueue/Transports');
        $this->transport = new $transporter($serverConfiguration);
    }
    
    /**
     * Singleton constructor
     * 
     * @return TransportProxy
     */
    public static function instance() {
        if (empty(self::$_generalMessageQueueProxy)) {
            self::$_generalMessageQueueProxy = new TransportProxy(Configure::read('Ratchet.Queue.configuration'));
        }
        
        return self::$_generalMessageQueueProxy;
    }
    
    /**
     * {@inheritdoc}
     */
    public function queueMessage(RatchetMessageQueueCommand $command) {
        $this->transport->queueMessage($command);
    }
    
    /**
     * Return the used transporter
     * 
     * @return RatchetMessageQueueTransportInterface
     */
    public function getTransport() {
        return $this->transport;
    }
}