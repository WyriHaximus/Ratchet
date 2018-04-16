<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'WyriHaximus/Ratchet',
    function (RouteBuilder $routes) {
        $routes->setExtensions(['json']);
        $routes->fallbacks('DashedRoute');
    }
);
