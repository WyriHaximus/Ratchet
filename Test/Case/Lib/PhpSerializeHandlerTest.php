<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('PhpSerializeHandler', 'Ratchet.Lib');

class PhpSerializeHandlerTest extends CakeTestCase {

/**
 * {@inheritdoc}
 */
	public function setUp() {
		parent::setUp();

		$this->PhpSerializeHandler = new PhpSerializeHandler();
	}

/**
 * {@inheritdoc}
 */
	public function tearDown() {
		unset($this->PhpSerializeHandler);

		parent::tearDown();
	}

/**
 * {@inheritdoc}
 */
	public function testSerialize() {
		$result = $this->PhpSerializeHandler->serialize([
			'movies' => [
				'The Expendables',
				'THX 1138',
			],
			'infrastructure' => [
				'AWS',
				'Sensson',
				'Netgear',
				'Samsung',
			],
		]);
		$expected = 'a:2:{s:6:"movies";a:2:{i:0;s:15:"The Expendables";i:1;s:8:"THX 1138";}s:14:"infrastructure";a:4:{i:0;s:3:"AWS";i:1;s:7:"Sensson";i:2;s:7:"Netgear";i:3;s:7:"Samsung";}}';

		$this->assertEqual($result, $expected);
	}

/**
 * {@inheritdoc}
 */
	public function testUnserialize() {
		$result = $this->PhpSerializeHandler->unserialize('a:2:{s:6:"movies";a:2:{i:0;s:15:"The Expendables";i:1;s:8:"THX 1138";}s:14:"infrastructure";a:4:{i:0;s:3:"AWS";i:1;s:7:"Sensson";i:2;s:7:"Netgear";i:3;s:7:"Samsung";}}');
		$expected = [
			'_sf2_attributes' => [
				'movies' => [
					'The Expendables',
					'THX 1138',
				],
				'infrastructure' => [
					'AWS',
					'Sensson',
					'Netgear',
					'Samsung',
				],
			],
		];

		$this->assertEqual($result, $expected);
	}

	public function testUnserializeFalse() {
		$result = $this->PhpSerializeHandler->unserialize('');
		$expected = [
			'_sf2_attributes' => [],
		];

		$this->assertEqual($result, $expected);
	}

}
