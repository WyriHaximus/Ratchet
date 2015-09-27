<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Tests\Event;

use Phake;
use WyriHaximus\Ratchet\Event\OnSessionEndEvent;

class OnSessionEndEventTest extends AbstractEventTest
{
    const FQCN = 'WyriHaximus\Ratchet\Event\OnSessionEndEvent';

    public function testCreate()
    {
        $realm = 'realm1';
        $session = Phake::mock('Thruway\ClientSession');
        $transport = Phake::mock('Thruway\Transport\TransportInterface');
        $event = OnSessionEndEvent::create($realm, $session);
        $this->assertSame($session, $event->getSession());
        $this->assertSame([
            'realm' => $realm,
        ], $event->data());
    }
}
