<?php

class PushableModelFixture extends CakeTestFixture {
    
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'url' => array('type' => 'string', 'length' => 255, 'null' => true),
        'title' => array('type' => 'string', 'length' => 255, 'null' => false),
        'slug' => array('type' => 'string', 'length' => 255, 'null' => false),
    );
    
    public $records = array(
        array(
            'id' => 1,
            'url' => 'http://tweakers.net/',
            'title' => 'Tweakers',
            'slug' => 'tweakers',
        ),
    );
    
}