<?php

/**
 * This file is part of Ratchet for CakePHP.
 *
 ** (c) 2012 - 2013 Cees-Jan Kiewiet
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

App::uses('AppHelper', 'View/Helper');

class WampHelper extends AppHelper {

	public $helpers = [
		'Html',
		'AssetCompress.AssetCompress',
	];

	public function init() {
		$block = '';
		$block .= 'WEB_SOCKET_SWF_LOCATION = "' . $this->Html->url('/Ratchet/swf/WebSocketMain.swf', true) . '";' . PHP_EOL;
		$block .= 'var cakeWamp = window.cakeWamp || {};' . PHP_EOL;
		$block .= 'cakeWamp.options = {';
		if (Configure::read('debug') == 2) {
			//$block .= 'debugWs: true,';
			$block .= 'debugWamp: true,';
		}
		$block .= 'retryDelay: ' . (int)Configure::read('Ratchet.Client.retryDelay') . ',';
		$block .= 'maxRetries: ' . (int)Configure::read('Ratchet.Client.maxRetries');
		$block .= '};' . PHP_EOL;
		$block .= 'var wsuri = "';
		$block .= ((Configure::read('Ratchet.Connection.external.secure')) ? 'wss' : 'ws') . '://';
		$block .= Configure::read('Ratchet.Connection.external.hostname');
		$block .= ':' . Configure::read('Ratchet.Connection.external.port');
		$block .= '/' . Configure::read('Ratchet.Connection.external.path') . '";';
		$this->Html->scriptBlock($block, [
			'inline' => false,
		]);
		$this->AssetCompress->script('Ratchet.wamp', ['block' => 'script']);

		if (Configure::read('Ratchet.Connection.keepaliveInterval') > 0) {
			$block = 'cakeWamp.subscribe(\'Rachet.connection.keepAlive\', function (topic, event) {});';
			$this->Html->scriptBlock($block, [
				'inline' => false,
			]);
		}
	}

}
