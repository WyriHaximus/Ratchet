<?php

interface RatchetMessageQueueCommandInterface {
    public function execute($topics);
    public function response($response);
}