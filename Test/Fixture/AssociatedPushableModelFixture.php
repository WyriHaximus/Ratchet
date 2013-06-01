<?php

class AssociatedPushableModelFixture extends CakeTestFixture {
    
    public $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'url' => array('type' => 'string', 'length' => 255, 'null' => true),
        'title' => array('type' => 'string', 'length' => 255, 'null' => false),
        'slug' => array('type' => 'string', 'length' => 255, 'null' => false),
        'pushable_model_associated_id' => array('type' => 'integer'),
    );
    
    public $records = array(
        array(
            'id' => 1,
            'url' => 'http://tweakers.net/',
            'title' => 'Tweakers',
            'slug' => 'tweakers',
            'pushable_model_associated_id' => 1,
        ),
        array(
            'id' => 2,
            'url' => 'http://arstechnica.com/',
            'title' => 'Ars Technica',
            'slug' => 'arstechnica',
            'pushable_model_associated_id' => 2,
        ),
    );
    
}