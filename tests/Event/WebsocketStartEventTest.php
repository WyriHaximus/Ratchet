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

use React\EventLoop\Factory;
use WyriHaximus\Ratchet\Event\WebsocketStartEvent;

class WebsocketStartEventTest extends AbstractEventTest
{
    const FQCN = 'WyriHaximus\Ratchet\Event\WebsocketStartEvent';

    public function testCreate()
    {
        $loop = Factory::create();
        $event = WebsocketStartEvent::create($loop);
        $this->assertSame($loop, $event->getLoop());
    }
}
