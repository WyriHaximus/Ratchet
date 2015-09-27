<?php

namespace WyriHaximus\Ratchet\Tests\Event;

abstract class AbstractEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEventConst()
    {
        $fqcn = static::FQCN;
        $this->assertInternalType('string', $fqcn::EVENT);
        $this->assertTrue(strlen($fqcn::EVENT) > 0);
    }
}
