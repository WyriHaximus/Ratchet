<?php

namespace WyriHaximus\Ratchet\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use PipingBag\Di\PipingBag;
use React\EventLoop\LoopInterface;
use Thruway\Authentication\AllPermissiveAuthorizationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Authentication\WampCraAuthProvider;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use Thruway\Authentication\AuthenticationManager;
use WyriHaximus\Ratchet\Event\WebsocketStartEvent;
use WyriHaximus\Ratchet\Websocket\InternalClient;
use Zikarsky\React\Gearman\Factory;

class WebsocketShell extends Shell
{
    protected $loop;

    public function start()
    {
        $this->loop = $this->loopResolver();

        $router = new Router($this->loop);

        $router->addInternalClient(new InternalClient('first', $this->loop));
        $router->addTransportProvider(new RatchetTransportProvider('0.0.0.0', 9000));
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

    protected function loopResolver()
    {
        if (
            Configure::check('WyriHaximus.Ratchet.loop') &&
            Configure::read('WyriHaximus.Ratchet.loop') instanceof LoopInterface
        ) {
            return Configure::read('WyriHaximus.Ratchet.loop');
        }

        if (class_exists('PipingBag\Di\PipingBag') && Configure::check('WyriHaximus.Ratchet.pipingbag')) {
            return PipingBag::get(Configure::read('WyriHaximus.Ratchet.pipingbag'));
        }

        return Factory::create();
    }
}
