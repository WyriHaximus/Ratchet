<?php

interface RatchetMessageQueueInterface {
    public function __construct($serverConfiguration, $key = '');
    public function queueMessage(RatchetMessageQueueCommand $command);
}