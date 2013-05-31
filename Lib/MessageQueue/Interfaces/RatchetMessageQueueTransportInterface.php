<?php

interface RatchetMessageQueueTransportInterface {
    public function __construct($serverConfiguration);
    public function queueMessage(RatchetMessageQueueCommand $command);
}