Requirements
============

Ratchet for `CakePHP` has a few requirements to work.

## PHP 5.4+ ##

This plugin is written to work with `PHP` 5.4 and up.

## PHP PCNTL Extension ##

Intercepts signals from the OS.

## CakePHP 2.x ##

While a `CakePHP` plugin obviously requires `CakePHP` if you want to use `Ratchet` as a standalone tool check out it's [website](http://socketo.me/).

## Composer ##

`Composer` is a packagemanager for `PHP`. Ratchet uses it to fetch all it's dependencie with it. ([Read more here on Composer and CakePHP 2.x.](http://book.cakephp.org/2.0/en/installation/advanced-installation.html#installing-cakephp-with-composer))

## SSH/Shell access ##

Ratchet requires to be ran as background service and can't be run using a HTTP server like `Apache` or `NGINX`. This is because Ratchet is a long running process. It's reccomended to use a tool like `supervisord` to keep an eye on it.

## ext-libevent ##

While not required it is highly reccomended to install `ext-libevent` for better performence.