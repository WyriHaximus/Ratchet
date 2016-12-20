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

class OnSessionEndEvent extends Event
{
    const EVENT = 'WyriHaximus.Ratchet.%s..onSessionEnd';

    public static function realmEvent($realm)
    {
        return sprintf(self::EVENT, $realm);
    }

    public static function create($realm, ClientSession $session)
    {
        return new static(self::realmEvent($realm), $session, [
            'realm' => $realm,
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
