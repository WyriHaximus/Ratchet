<?php

interface RatchetMessageQueueCommandInterface {
    public function execute($topics);
}