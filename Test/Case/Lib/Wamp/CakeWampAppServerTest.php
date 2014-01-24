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

class CakeWampAppServerTest extends CakeRatchetTestCase {

	public function testGetShell() {
		$this->assertEquals($this->AppServer->getShell(), $this);
	}

	public function testGetLoop() {
		$this->assertEquals($this->AppServer->getLoop(), $this->loop);
	}

	public function testGetVerbose() {
		$this->assertEquals($this->AppServer->getVerbose(), true);
	}

	public function testGetTopics() {
		$this->assertEquals($this->AppServer->getTopics(), []);
		$this->assertEquals(count($this->AppServer->getTopics()), 0);
	}

	public function testGetTopicNameProvider() {
		return [
			[
				'test',
			],
			[
				new \Ratchet\Wamp\Topic('test'),
			],
		];
	}

/**
 * @dataProvider testGetTopicNameProvider
 */
	public function testGetTopicName($topic) {
		$this->assertSame('test', CakeWampAppServer::getTopicName($topic));
	}
}
