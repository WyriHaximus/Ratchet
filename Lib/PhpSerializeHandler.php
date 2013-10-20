<?php

/*
* This file is part of Ratchet for CakePHP.
*
** (c) 2012 - 2013 Cees-Jan Kiewiet
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use Ratchet\Session\Serialize\HandlerInterface;

class PhpSerializeHandler implements HandlerInterface {

/**
 * {@inheritdoc}
 */
	public function serialize(array $data) {
		return serialize($data);
	}

/**
 * {@inheritdoc}
 */
	public function unserialize($raw) {
		$data = unserialize($raw);
		if (!$data) {
			$data = array();
		}

		return array(
			'_sf2_attributes' => $data,
		);
	}
}
