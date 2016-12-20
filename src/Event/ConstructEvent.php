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

class ConstructEvent extends Event
{
    const EVENT = 'WyriHaximus.Ratchet.construct';

    /**
     * @param LoopInterface $loop
     * @return static
     */
    public static function create(LoopInterface $loop, EventManager $eventManager)
    {
        return new static(static::EVENT, $loop, [
            'loop' => $loop,
            'eventManager' => $eventManager,
        ]);
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->data()['loop'];
    }

    /**
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->data()['eventManager'];
    }
}