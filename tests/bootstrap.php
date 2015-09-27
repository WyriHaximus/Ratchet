<?php

use Psr\Log\NullLogger;
use Thruway\Logging\Logger;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Logger::set(new NullLogger());
