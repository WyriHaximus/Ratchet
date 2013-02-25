<?php

use Ratchet\Session\Serialize\HandlerInterface;

class PhpSerializeHandler implements HandlerInterface {
    
    /**
     * {@inheritdoc}
     */
    function serialize(array $data) {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($raw) {
        $data = unserialize($raw);
        if (!$data) {
            $data = array();
        }
        
        return array(
            '_sf2_attributes' => $data,
        );
    }
}