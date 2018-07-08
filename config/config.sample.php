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
            'realm_salt' => 'CHANGE_THIS_TO_A_UNIQUE_AND_SECURE_VALUE',
            'realm_auth_key_salt' => 'CHANGE_THIS_TO_A_UNIQUE_AND_SECURE_VALUE',
            'realms' => [
                'realm1' => [], // Always has to be an array
                'secure' => [
                    'auth' => true,
                    'auth_key' => 'CHANGE_THIS_TO_A_UNIQUE_AND_SECURE_VALUE',
                    'max_retries' => 13,
                ],
            ],
        ],
    ],
];
