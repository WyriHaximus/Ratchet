<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Event;

use Cake\Event\Event;
use React\EventLoop\LoopInterface;

class WebsocketStartEvent extends Event
{
    const EVENT = 'WyriHaximus.Ratchet.WebsocketStart';

    /**
     * @param LoopInterface $loop
     * @return static
     */
    public static function create(LoopInterface $loop)
    {
        return new static(static::EVENT, $loop, [
            'loop' => $loop,
        ]);
    }

    /**
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->subject();
    }
}
