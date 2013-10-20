<?php

/*
* This file is part of Ratchet for CakePHP.
*
** (c) 2012 - 2013 Cees-Jan Kiewiet
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
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
