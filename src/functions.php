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

/**
 * @param string $realm
 * @return array
 */
function realmConfiguration($realm)
{
    $defaults = [
        'secure' => false,
    ];

    $options = array_merge($defaults, Configure::read('WyriHaximus.Ratchet.realms.' . $realm));

    $config = [
        'realm' => $realm,
        'url' => createUrl($options['hostname'], $options['port'], $options['secure'], $options['path']),
    ];

    return array_merge($options['config'], $config);
}

/**
 * @param boolean $secure
 * @param string $hostname
 * @param integer $port
 * @param string $path
 * @return string
 */
function createUrl($secure, $hostname, $port, $path)
{
    $url = 'ws';
    if ($secure) {
        $url .= 's';
    }
    $url .= '://';

    $url .= $hostname;

    if (
        !($port == 80 && !$secure) &&
        !($port == 443 && $secure)
    ) {
        $url .= ':';
        $url .= $port;
    }

    $url .= '/';
    $url .= $path;

    return $url;
}
