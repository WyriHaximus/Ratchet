<?php

class WampHelper extends AppHelper {
    
    public $helpers = array(
        'Html',
    );
    
    public function init() {
        
        $this->Html->scriptBlock('WEB_SOCKET_SWF_LOCATION = "' . $this->Html->url('/Ratchet/swf/WebSocketMain.swf', true) . '"', array(
            'inline' => false,
        ));
        $this->Html->script('cache/Ratchet.wamp', array('block' => 'script'));
        $this->Html->script('/Ratchet/js/cake-wamp', array('block' => 'script'));
        
    }
    
}