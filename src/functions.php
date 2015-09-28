<?php

namespace WyriHaximus\Ratchet;

use Cake\Core\Configure;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use PipingBag\Di\PipingBag;

/**
 * @return LoopInterface
 */
function loopResolver()
{
    if (
        Configure::check('WyriHaximus.Ratchet.loop') &&
        Configure::read('WyriHaximus.Ratchet.loop') instanceof LoopInterface
    ) {
        return Configure::read('WyriHaximus.Ratchet.loop');
    }

    if (class_exists('PipingBag\Di\PipingBag') && Configure::check('WyriHaximus.Ratchet.pipingbag.loop')) {
        return PipingBag::get(Configure::read('WyriHaximus.Ratchet.pipingbag.loop'));
    }

    return Factory::create();
}
