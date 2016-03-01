<?php

namespace WyriHaximus\Ratchet\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use React\EventLoop\LoopInterface;
use Thruway\Authentication\AllPermissiveAuthorizationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Authentication\WampCraAuthProvider;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use Thruway\Authentication\AuthenticationManager;
use WyriHaximus\Ratchet\Event\WebsocketStartEvent;
use WyriHaximus\Ratchet\Websocket\InternalClient;

class WebsocketShell extends Shell
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    public function start()
    {
        $this->loop = \WyriHaximus\Ratchet\loopResolver();

        $router = new Router($this->loop);

        $router->addInternalClient(new InternalClient('first', $this->loop));
        $router->addTransportProvider(
            new RatchetTransportProvider(
                Configure::read('WyriHaximus.Ratchet.internal.address'),
                Configure::read('WyriHaximus.Ratchet.internal.port')
            )
        );
        //$router->getRealmManager()->setDefaultAuthorizationManager(new AllPermissiveAuthorizationManager());

        EventManager::instance()->dispatch(WebsocketStartEvent::create($this->loop));

        $router->start(false);

        $this->loop->run();
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
                'help' => __('Starts and runs both the websocket service')
            ]
        )->description(__('Ratchet Websocket service.'))->addOption(
            'verbose',
            [
                'help' => 'Enable verbose output',
                'short' => 'v',
                'boolean' => true
            ]
        );
    }
}
