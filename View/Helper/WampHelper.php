<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class WampHelper extends AppHelper {

	public $helpers = array(
		'Html',
		'AssetCompress.AssetCompress',
	);

	public function init() {
		$block = '';
		$block .= 'WEB_SOCKET_SWF_LOCATION = "' . $this->Html->url('/Ratchet/swf/WebSocketMain.swf', true) . '";' . PHP_EOL;
		$block .= 'var cakeWamp = window.cakeWamp || {};' . PHP_EOL;
		$block .= 'cakeWamp.options = {';
		$block .= 'retryDelay: ' . (int)Configure::read('Ratchet.Client.retryDelay') . ',';
		$block .= 'maxRetries: ' . (int)Configure::read('Ratchet.Client.maxRetries') . '';
		$block .= '};' . PHP_EOL;
		$block .= 'var wsuri = "';
		$block .= ((Configure::read('Ratchet.Connection.external.secure')) ? 'wss' : 'ws') . '://';
		$block .= Configure::read('Ratchet.Connection.external.hostname');
		$block .= ':' . Configure::read('Ratchet.Connection.external.port');
		$block .= '/' . Configure::read('Ratchet.Connection.external.path') . '";';
		$this->Html->scriptBlock($block, array(
			'inline' => false,
		));
		$this->AssetCompress->script('Ratchet.wamp', array('block' => 'script'));
	}

}
