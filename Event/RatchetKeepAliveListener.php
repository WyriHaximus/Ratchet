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

class RatchetKeepAliveListener implements CakeEventListener {

/**
 * The ReactPHP event
 *
 * @var \React\EventLoop\LoopInterface
 */
	private $__loop;

/**
 * Timer instance signature
 *
 * @var string
 */
	private $__timer = null;

/**
 * Return an array with events this listener implements
 * @return array
 */
	public function implementedEvents() {
		return [
			'Rachet.WampServer.construct' => 'construct',
			'Rachet.WampServer.onSubscribeNewTopic.Rachet.connection.keepAlive' => 'onSubscribeNewTopic',
			'Rachet.WampServer.onUnSubscribeStaleTopic.Rachet.connection.keepAlive' => 'onUnSubscribeStaleTopic',
		];
	}

/**
 * References the ReactPHP eventloop for later use
 *
 * @param CakeEvent $event
 */
	public function construct(CakeEvent $event) {
		$this->__loop = $event->data['loop'];
	}

/**
 * Start the keep alive timer when the first client subscribes
 *
 * @param CakeEvent $event
 */
	public function onSubscribeNewTopic(CakeEvent $event) {
		if (Configure::read('Ratchet.Connection.keepaliveInterval') > 0) {
			$this->__timer = $this->__loop->addPeriodicTimer(Configure::read('Ratchet.Connection.keepaliveInterval'), function() use ($event) {
				$event->data['topic']->broadcast('ping');
			}, true);

			$event->data['topic']->broadcast('ping');
		}
	}

/**
 * Stop timer when the last client unsubscribes
 *
 * @param CakeEvent $event
 */
	public function onUnSubscribeStaleTopic(CakeEvent $event) {
		if ($this->__timer !== null) {
			$this->__loop->cancelTimer($this->__timer);
		}
	}
}
