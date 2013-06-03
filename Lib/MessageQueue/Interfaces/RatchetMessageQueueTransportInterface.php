<?php

interface RatchetMessageQueueTransportInterface {
    
    /**
     * Construct the transport
     * 
     * @param array $serverConfiguration
     */
    public function __construct($serverConfiguration);
    
    /**
     * Pass the gieven command and executes it on the Ratchet server
     * 
     * @param RatchetMessageQueueCommand $command
     */
    public function queueMessage(RatchetMessageQueueCommand $command);
    
}