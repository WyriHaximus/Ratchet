<?php

namespace WyriHaximus\Ratchet\Console\Command;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Session\SessionProvider;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;
use WyriHaximus\Ratchet\Lib\PhpSerializeHandler;
use WyriHaximus\Ratchet\Lib\Wamp\AppServer;
use WyriHaximus\Ratchet\Model\Datasource\Session\SessionHandler;

class WebsocketShell extends Shell
{
    protected $loop;

    public function start()
    {
        $this->loop = Configure::read('WyriHaximus.Ratchet.Loop');

        $this->checkEventLoop();
        $this->setUpRatchet();

        $this->loop->run();
    }

    protected function checkEventLoop()
    {
        if ($this->loop instanceof \React\EventLoop\StreamSelectLoop) {
            $this->out(
            '<warning>Your configuration doesn\'t seem to support \'ext-libevent\'. It is highly reccomended that you install and configure it as it provides significant performance gains over stream select!</warning>'
            );
        }
    }

    protected function setUpRatchet()
    {
        new IoServer(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new WampServer(
                            new AppServer(
                                $this,
                                $this->loop,
                                EventManager::instance(),
                                $this->params['verbose']
                            )
                        ),
                        new SessionHandler(),
                        [],
                        new PhpSerializeHandler()
                    )
                )
            ),
            $this->createSocket(),
            $this->loop
        );
    }

    protected function createSocket()
    {
        $socket = new Server($this->loop);
        $socket->listen(
        Configure::read('WyriHaximus.Ratchet.Connection.websocket.port'),
        Configure::read('WyriHaximus.Ratchet.Connection.websocket.address')
        );
        return $socket;
    }

    /**
     * Set options for this console
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        return parent::getOptionParser()->addSubcommand(
        'start',
        [
        'help' => __('Starts and runs both the websocket service and the flashpolicy.')
        ]
        )->description(__('Ratchet Websocket service.'))->addOption(
        'verbose',
        [
        'help' => 'Enable verbose output.',
        'short' => 'v',
        'boolean' => true
        ]
        );
    }
}
