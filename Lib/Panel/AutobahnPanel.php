<?php
/**
 * Autobahn Panel for DebugKit.Toolbar
 *
 * PHP 5
 *
 * Copyright 2013, Cees-Jan Kiewiet, The Netherlands
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     2013 Cees-Jan Kiewiet, The Netherlands
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('DebugPanel', 'DebugKit.Lib');
App::uses('ClearCache', 'ClearCache.Lib');

/**
 * Autobahn Panel for DebugKit.Toolbar
 *
 * Provides a simple way to en- and disable Autobahn debug flags
 *
 * @package       Autobahn.Lib.Panel
 */
class AutobahnPanel extends DebugPanel {

/**
 * Plugin name
 *
 * @var string
 */
	public $plugin = 'Ratchet';

/**
 * Panel element name
 *
 * @var string
 */
	public $elementName = 'autobahn_panel';
}