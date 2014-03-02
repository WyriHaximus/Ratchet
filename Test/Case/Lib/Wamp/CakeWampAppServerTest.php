<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('AbstractCakeRatchetTestCase', 'Ratchet.Test/Case');

class CakeWampAppServerTest extends AbstractCakeRatchetTestCase {

	public function testGetShell() {
		$this->assertEquals($this->AppServer->getShell(), $this->shell);
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

	public function testOnErrorProvider() {
		return [
			[
				new Exception('error message'),
				'#\[<info>[0-9]+.[0-9]+</info>] Exception for connection <info>[0-9a-z]+</info>: <error>error message</error>#',
			],
			[
				new BadMethodCallException('function does not exist'),
				'#\[<info>[0-9]+.[0-9]+</info>] BadMethodCallException for connection <info>[0-9a-z]+</info>: <error>function does not exist</error>#',
			],
		];
	}

/**
 * @dataProvider testOnErrorProvider
 */
	public function testOnError($exception, $messageRegexp) {
		$this->shell->expects($this->at(3))
			->method('out')
			->with(
				$this->callback(
					function ($message) use($messageRegexp) {
						$this->assertRegexp($messageRegexp, $message);
						return true;
					}
				)
			);

		$mock = $this->getMock('\\Ratchet\\ConnectionInterface');
		$conn = new Ratchet\Wamp\WampConnection($mock);
		$conn->Session = new SessionHandlerImposer();

		$this->AppServer->onOpen($conn);
		$this->AppServer->onError($conn, $exception);
	}
}
