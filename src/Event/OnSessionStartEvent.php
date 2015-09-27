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
use Thruway\ClientSession;
use Thruway\Transport\TransportInterface;

class OnSessionStartEvent extends Event
{
    const EVENT = 'WyriHaximus.Ratchet.onSessionStart';

    public static function create($realm, ClientSession $session, TransportInterface $transport)
    {
        return new static(static::EVENT, $session, [
            'realm' => $realm,
            'transport' => $transport,
        ]);
    }

    /**
     * @return ClientSession
     */
    public function getSession()
    {
        return $this->subject();
    }
}
