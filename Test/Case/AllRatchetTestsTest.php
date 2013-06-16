<?php

/*
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

CakePlugin::loadAll(array(
	'AssetCompress' => array(
		'bootstrap' => true
	),
	'Ratchet' => array(
		'bootstrap' => true
	),
	'PhuninCake' => array(
		'bootstrap' => true
	),
));

App::import('Ratchet.Test', array('file' => 'Case/CakeRatchetTestCase'));

class AllRatchetTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite method, defines tests for this suite.
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Tests');
		$suite->addTestDirectoryRecursive(App::pluginPath('Ratchet') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}
}
