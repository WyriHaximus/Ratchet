<?php

require_once CakePlugin::path('Ratchet') . 'Vendor' . DS . 'autoload.php';
App::uses('CakeWampServer', 'Ratchet.Lib');
App::uses('CakeWampAppServer', 'Ratchet.Lib');
App::uses('PhpSerializeHandler', 'Ratchet.Lib');
App::uses('CakeWampSessionProvider', 'Ratchet.Lib');
App::uses('CakeWampSessionHandler', 'Ratchet.Lib');

use Ratchet\Wamp\WampServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server as Reactor;
use React\EventLoop\Factory as LoopFactory;
//use Ratchet\Server\FlashPolicy;

class WebsocketShell extends Shell {
    
    private $loop;
    private $ioServer;
    private $flashPolicy;
    
    public function __construct($stdout = null, $stderr = null, $stdin = null) {
        parent::__construct($stdout, $stderr, $stdin);
        
        $this->loop = LoopFactory::create();
        
        // Flash Policy
        /*$flashSock = new Reactor($this->loop);
        $flashSock->listen(843, '0.0.0.0');
        $policy = new FlashPolicy;
        $policy->addAllowedAccess('*', '*');
        $this->flashPolicy = new IoServer($policy, $flashSock);*/
        
        // Websocket
        $socket = new Reactor($this->loop);
        $socket->listen(54321, '0.0.0.0');
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
    }
    
    public function run() {
        $this->loop->run();
    }
    
}