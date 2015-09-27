<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2015 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WyriHaximus\Ratchet\Event\Listener;

use Cake\Event\EventListenerInterface;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;

class OnSessionStartListener implements EventListenerInterface
{
    public function implementedEvents()
    {
        return [
            OnSessionStartEvent::EVENT => 'onSessionStart',
        ];
    }

    public function onSessionStart(OnSessionStartEvent $event)
    {
        $event->returnSession()->onClose()
    }
}
