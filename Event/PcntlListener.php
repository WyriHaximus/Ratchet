<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeEventListener', 'Event');

class PcntlListener implements CakeEventListener {

/**
 * Return an array with events this listener implements
 *
 * @return array
 */
	public function implementedEvents() {
		return [
			'Rachet.WampServer.construct' => 'construct',
		];
	}

/**
 * References the ReactPHP eventloop for later use
 *
 * @param CakeEvent $event
 */
	public function construct(CakeEvent $event) {

        if (!function_exists('pcntl_signal')) {
            $event->subject()->getShell()->out(
                '<warning>Your configuration doesn\'t seem to support \'ext-pcntl\'. It is highly recomended that you install and configure it as it provides OS signaling support!</warning>'
            );
        } else {
            $pcntl = new MKraemer\ReactPCNTL\PCNTL($event->data['loop']);

            $pcntl->on(SIGTERM, function () use ($event) {
                $event->data['loop']->stop();
            });

            $pcntl->on(SIGINT, function () use ($event) {
                $event->data['loop']->stop();
            });
        }
	}
}
