<?php

return [
    'WyriHaximus' => [
        'Ratchet' => [
            'internal' => [
                'address' => '0.0.0.0',
                'port' => 12345,
            ],
            'external' => [
                'hostname' => '127.0.0.1',
                'port' => 80,
            ],
            'defaults' => [
                'retry_delay_growth' => 0.25,
            ],
            'realms' => [
                'realm1' => [], // Always has to be an array
                'secure' => [
                    'max_retries' => 13,
                ],
            ],
        ],
    ],
];
