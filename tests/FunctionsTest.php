<?php

namespace WyriHaximus\Ratchet\Tests;

use Cake\TestSuite\TestCase;

class FunctionsTest extends TestCase
{
    public function providerCreateUrl()
    {
        yield [
            true,
            'example.com',
            443,
            'ws',
            'wss://example.com/ws',
        ];

        yield [
            true,
            'example.com',
            80,
            'ws',
            'wss://example.com:80/ws',
        ];

        yield [
            false,
            'example.com',
            80,
            'ws',
            'ws://example.com/ws',
        ];

        yield [
            false,
            'example.com',
            443,
            'ws',
            'ws://example.com:443/ws',
        ];

        yield [
            true,
            'example.com',
            9001,
            '',
            'wss://example.com:9001/',
        ];
    }

    /**
     * @dataProvider providerCreateUrl
     */
    public function testCreateUrl($secure, $hostname, $port, $path, $output)
    {
        $this->assertSame($output, \WyriHaximus\Ratchet\createUrl($secure, $hostname, $port, $path));
    }
}
