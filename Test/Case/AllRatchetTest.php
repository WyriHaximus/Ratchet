<?php

/**
 * All Ratchet plugin tests
 */
class AllRatchetTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Ratchet test');

		$path = CakePlugin::path('Ratchet') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
