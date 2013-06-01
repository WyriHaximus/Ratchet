<?php

class PushableModelAssociated extends AppModel {
    public $hasOne = array(
        'TestRatchet.AssociatedPushableModel',
    );
}