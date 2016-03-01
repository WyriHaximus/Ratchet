<?php

return [
    'WyriHaximus' => [
        'Ratchet' => [
            'realms' => [
                'realm1' => [
                    'internal' => [
                        'address' => '0.0.0.0',
                        'port' => 12345,
                    ],
                    'external' => [
                        'hostname' => '127.0.0.1',
                        'port' => 80,
                    ],
                ],
                'secure' => [
                    'internal' => [
                        'address' => '0.0.0.0',
                        'port' => 12345,
                    ],
                    'external' => [
                        'hostname' => '127.0.0.1',
                        'port' => 443,
                        'secure' => true,
                    ],
                ],
            ],
        ],
    ],
];
