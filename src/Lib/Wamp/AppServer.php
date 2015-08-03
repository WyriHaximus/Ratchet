<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Lib\Wamp;

use Cake\Event\EventManager;
use Ratchet\ConnectionInterface as Conn;
use Ratchet\Wamp\WampServerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\Ratchet\Console\Command\WebsocketShell;

class AppServer implements WampServerInterface
{

    use ConnectionTrait;
    use PubSubTrait;
    use RpcTrait;

    protected $shell;
    protected $loop;
    protected $eventManager;
    protected $topicManager;
    protected $verbose;

    public function __construct(
        WebsocketShell $shell,
        LoopInterface $loop,
        EventManager $eventManager,
        $verbose = false
    ) {
        $this->shell = $shell;
        $this->loop = $loop;
        $this->eventManager = $eventManager;
        $this->verbose = $verbose;

        $this->dispatchEvent(
            'WyriHaximus.Rachet.WampServer.construct',
            $this,
            [
                'loop' => $this->loop,
            ]
        );
    }

    /**
     *
     * @return WebsocketShell
     */
    public function getShell()
    {
        return $this->shell;
    }

    /**
     *
     * @return \React\EventLoop\LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     *
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    /**
     * Silently ignore exceptions
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(Conn $conn, \Exception $e)
    {
        $this->outVerbose(get_class($e) . ' for connection <info>' . $conn->WAMP->sessionId . '</info>: <error>' . $e->getMessage() . '</error>');
        CakeLog::write('ratchetWampServer', 'Something did not work');
    }

    /**
     * Syntactic sugar improving the readability for on* methods
     *
     * @param string $eventName
     * @param AppServer $scope
     * @param array $params
     */
    public function dispatchEvent($eventName, $scope, $params)
    {
        $event = new CakeEvent($eventName, $scope, $params);

        $this->outVerbose('Event begin: ' . $eventName);
        $this->eventManager->dispatch($event);
        $this->outVerbose('Event end: ' . $eventName);

        return $event;
    }

    /**
     * Output $message when verbose mode is on
     *
     * @param string $message
     */
    public function outVerbose($message)
    {
        if ($this->verbose) {
            $time = microtime(true);
            $time = explode('.', $time);
            if (!isset($time[1])) {
                $time[1] = 0;
            }
            $time[1] = str_pad($time[1], 4, 0);
            $time = implode('.', $time);
            $this->shell->out('[<info>' . $time . '</info>] ' . $message);
        }
    }

    /**
     * @param string|\Ratchet\Wamp\Topic $topic
     *
     * @return string
     */
    static public function getTopicName($topic)
    {
        if ($topic instanceof \Ratchet\Wamp\Topic) {
            return $topic->getId();
        } else {
            return $topic;
        }
    }
}
