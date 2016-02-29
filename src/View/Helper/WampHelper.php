<?php

namespace WyriHaximus\Ratchet\View\Helper;

use Cake\View\Helper;

class WampHelper extends Helper
{
    public function beforeLayout()
    {
        $this->_View->append('script', $this->_View->element('WyriHaximus\Ratchet.wamp'));
    }
}
