<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('Security', 'Utility');
App::uses('CakeWampServer', 'Ratchet.Lib/Wamp');
App::uses('CakeWampAppServer', 'Ratchet.Lib/Wamp');
App::uses('PhpSerializeHandler', 'Ratchet.Lib');
App::uses('CakeWampSessionProvider', 'Ratchet.Lib/Wamp');
App::uses('CakeWampSessionHandler', 'Ratchet.Lib/Wamp');
App::uses('RatchetCakeSession', 'Ratchet.Lib');
App::uses('RatchetMessageQueueProxy', 'Ratchet.Lib/MessageQueue/Transports');
App::uses('RatchetMessageQueueModelUpdateCommand', 'Ratchet.Lib/MessageQueue/Command');
App::uses('RatchetMessageQueueKillSwitchCommand', 'Ratchet.Lib/MessageQueue/Command');

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server as Reactor;
use React\EventLoop\Factory as LoopFactory;
use Ratchet\Server\FlashPolicy;

class WebsocketShell extends Shell {
    
    private $loop;
    private $ioServer;
    private $flashPolicy;
    
    public function start() {
        
        $this->loop = LoopFactory::create();
        
        // Flash Policy
        $flashSock = new Reactor($this->loop);
        $flashSock->listen(Configure::read('Ratchet.Connection.flashPolicy.port'), Configure::read('Ratchet.Connection.flashPolicy.address'));
        $policy = new FlashPolicy;
        $policy->addAllowedAccess('*', '*');
        $this->flashPolicy = new IoServer($policy, $flashSock);
        
        // Websocket
        $socket = new Reactor($this->loop);
        $socket->listen(Configure::read('Ratchet.Connection.websocket.port'), Configure::read('Ratchet.Connection.websocket.address'));
        $this->ioServer = new IoServer(new WsServer(
            new CakeWampSessionProvider(
                new CakeWampServer(
                    new CakeWampAppServer($this, $this->loop)
                ),
                new CakeWampSessionHandler(),
                array(),
                new PhpSerializeHandler()
            )
        ), $socket, $this->loop);
        
        $this->loop->run();
    }
    
    public function stop() {
        $command = new RatchetMessageQueueKillSwitchCommand();
        $command->setShell($this);
        $command->setHash(Security::hash(serialize(Configure::read('PhuninCake.Node')), 'sha256', true));
        
        $this->out('<info>Sending stop command</info>');
        
        RatchetMessageQueueProxy::instance()->queueMessage($command);
    }
    
    function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('start', array(
            'help' => __('Starts and runs both the websocket service and the flashpolicy.')
        ))->description(__('Ratchet Websocket service.'));
        return $parser;
    }
    
}