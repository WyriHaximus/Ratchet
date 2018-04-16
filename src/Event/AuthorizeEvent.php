<?php

/**
 * This file is part of Ratchet.
 *
 ** (c) 2016 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WyriHaximus\Ratchet\Event;

use Cake\Event\Event;
use Cake\Event\EventManager;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Thruway\Message\ActionMessageInterface;
use Thruway\Session;

class AuthorizeEvent extends Event
{
    const EVENT = 'WyriHaximus.Ratchet.%s.authorize';

    public static function realmEvent($realm)
    {
        return sprintf(self::EVENT, $realm);
    }

    /**
     * @var Deferred
     */
    private $deferred;

    /**
     * @param LoopInterface $loop
     * @return static
     */
    public static function create($realm, Session $session, ActionMessageInterface $actionMsg)
    {
        return new static(self::realmEvent($realm), $actionMsg, [
            'realm' => $realm,
            'session' => $session,
            'actionMessage' => $actionMsg,
        ]);
    }

    public function __construct($name, $subject = null, $data = null)
    {
        parent::__construct($name, $subject, $data);
        $this->deferred = new Deferred();
    }

    /**
     * @return Session
     */
    public function getRealm()
    {
        return $this->getData()['realm'];
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->getData()['session'];
    }

    /**
     * @return ActionMessageInterface
     */
    public function getActionMessage()
    {
        return $this->getData()['actionMessage'];
    }

    /**
     * @return PromiseInterface
     */
    public function promise()
    {
        return $this->deferred->promise();
    }

    public function reject()
    {
        $this->deferred->reject();
    }
}