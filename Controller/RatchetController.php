<?php

class RatchetController extends AppController {
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array('probeer'));
    }
    
    public function probeer() {
        return 'tadaaaaaaaa!';
    }
    
}