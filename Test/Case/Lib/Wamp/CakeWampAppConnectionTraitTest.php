<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('CakeRatchetTestCase', 'Ratchet.Test/Case');

class CakeWampAppConnectionTraitTest extends CakeRatchetTestCase {

	public function testOnOpen() {
		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$asserts = [];
		$this->_expectedEventCalls(
			$asserts,
			[
			'Rachet.WampServer.onOpen' => [
				'output' => [
					'#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onOpen#',
					'#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onOpen#',
				],
				'callback' => [
					function ($event) use ($conn) {
						$this->assertEquals(
							$event->data,
							[
							'connection' => $conn,
							'wampServer' => $this->AppServer,
							'connectionData' => [
								'topics' => [],
								'session' => [],
							],
							]
						);
					},
				],
			],
			]
		);
		$this->AppServer->onOpen($conn);

		foreach ($asserts as $assert) {
			$this->assertTrue($assert);
		}
	}

	public function testOnClose() {
		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$asserts = [];
		$this->_expectedEventCalls(
			$asserts,
			[
			'Rachet.WampServer.onClose' => [
				'output' => [
					'#\[<info>[0-9]+.[0-9]+</info>] Event begin: Rachet.WampServer.onClose#',
					'#\[<info>[0-9]+.[0-9]+</info>] Event end: Rachet.WampServer.onClose#',
				],
				'callback' => [
					function ($event) use ($conn) {
						$this->assertEquals(
							$event->data,
							[
							'connection' => $conn,
							'wampServer' => $this->AppServer,
							'connectionData' => [
								'topics' => [],
								'session' => [],
							],
							]
						);
					},
				],
			],
			]
		);
		$this->AppServer->onOpen($conn);
		$this->AppServer->onClose($conn);

		foreach ($asserts as $assert) {
			$this->assertTrue($assert);
		}
	}
}
