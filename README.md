Ratchet
=======

[![Build Status](https://travis-ci.org/WyriHaximus/Ratchet.png)](https://travis-ci.org/WyriHaximus/Ratchet)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/Ratchet/v/stable.png)](https://packagist.org/packages/WyriHaximus/Ratchet)
[![Total Downloads](https://poser.pugx.org/WyriHaximus/Ratchet/downloads.png)](https://packagist.org/packages/WyriHaximus/Ratchet)
[![Coverage Status](https://coveralls.io/repos/WyriHaximus/Ratchet/badge.png)](https://coveralls.io/r/WyriHaximus/Ratchet)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/WyriHaximus/ratchet/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

CakePHP plugin wrapping Ratchet

## What is Ratchet ##

Ratchet for CakePHP brings [the Ratchet websocket](http://socketo.me/) package to CakePHP. Websockets allow you to utilize near-real-time communication between your application and it's visitors. For example notifying a page the associated record in the database has been updated using the [Pushable behaviour](http://wyrihaximus.net/projects/cakephp/ratchet/documentation/model-push.html).

## Getting started ##

Keep in mind that this should only be used to push data around. Handling and processing that data is the applications job this plugin is only to get it from and to the client.

#### 0. Requirements ####

* PHP 5.4+
* CakePHP 2.x
* Composer
* SSH/Shell access
* ext-libevent is highly reccomended

#### 1. Installation ####

Installation is easy with [composer](http://getcomposer.org/) just add Ratchet to your composer.json. ([Read more here on Composer and CakePHP 2.x.](http://book.cakephp.org/2.0/en/installation/advanced-installation.html#installing-cakephp-with-composer))

```json
{
	"require": {
		"wyrihaximus/ratchet": "dev-master"
	}
}
```

Composer makes sure [Ratchet](https://github.com/cboden/Ratchet), yes I named this plugin after the underlying library, and other components are pulled in.

#### 2. Setup the plugin ####

Make sure you load `Ratchet` with the `bootstrap` option set to true:
```php
CakePlugin::load('Ratchet', ['bootstrap' => true]);
```

#### 3. Using the helper ####

Add the helper to the `AppController` or to specific controllers that will use it.

```php
$helpers = [
  'Ratchet.Wamp',
];
```

Then in your view or layout template add this:

```php
<?php $this->Wamp->init(); ?>
```

#### 4. Start and stopping the server ####

The server can be started with the following command (the verbose flag gives you debug information to see what is going on internally):

```bash
./cake Ratchet.websocket start --verbose
```

If you've configurated the queue correctly you can stop the server with the following command:

```bash
./cake Ratchet.websocket stop
```

For a proper way to boot the server check out the [Ratchet example supervisor configuration](http://socketo.me/docs/deploy#supervisor).

## Documentation ##

For more abundant documentation on this project, check the [project documentation site](http://wyrihaximus.net/projects/cakephp/ratchet/documentation.html "Ratchet for CakePHP documentation").

## Plugin License ##

(The MIT License)

Copyright © 2012 - 2013 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the ‘Software’), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED ‘AS IS’, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

