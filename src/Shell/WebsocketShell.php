<?php

namespace WyriHaximus\Ratchet\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use React\EventLoop\LoopInterface;
use WyriHaximus\Ratchet\Event\ConstructEvent;

class WebsocketShell extends Shell
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    public function start()
    {
        $this->loop = \WyriHaximus\Ratchet\loopResolver();
        EventManager::instance()->dispatch(ConstructEvent::create($this->loop));
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
