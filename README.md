Ratchet
=======

[![Build Status](https://travis-ci.org/WyriHaximus/Ratchet.png)](https://travis-ci.org/WyriHaximus/Ratchet)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/Ratchet/v/stable.png)](https://packagist.org/packages/WyriHaximus/Ratchet)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/Ratchet/downloads.png)](https://packagist.org/packages/WyriHaximus/Ratchet)
[![Coverage Status](https://coveralls.io/repos/WyriHaximus/Ratchet/badge.png)](https://coveralls.io/r/WyriHaximus/Ratchet)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/WyriHaximus/ratchet/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

CakePHP plugin wrapping Ratchet

## Current status ##

Currently I'm rewriting this plugin for CakePHP v3.

## What is Ratchet ##

Ratchet for CakePHP brings [the Ratchet websocket](http://socketo.me/) package to CakePHP. Websockets allow you to utilize near-real-time communication between your application and it's visitors. For example notifying a page the associated record in the database has been updated using the [Pushable behaviour](http://wyrihaximus.net/projects/cakephp/ratchet/documentation/model-push.html).

## Getting started ##

Keep in mind that this should only be used to push data around. Handling and processing that data is the applications job this plugin is only to get it from and to the client.

## Installation ##

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `~`.

```bash
composer require wyrihaximus/ratchet 
```

## Bootstrap ##

Add the following to your `config/bootstrap.php` to load the plugin.

```php
Plugin::load('WyriHaximus/Ratchet', [
    'bootstrap' => true,
]);
```

For a proper way to boot the server check out the [Ratchet example supervisor configuration](http://socketo.me/docs/deploy#supervisor).

## Documentation ##

For more abundant documentation on this project, check the [project documentation site](http://wyrihaximus.net/projects/cakephp/ratchet/documentation.html "Ratchet for CakePHP documentation").

## Plugin License ##

(The MIT License)

Copyright © 2012 - 2015 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the ‘Software’), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED ‘AS IS’, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

