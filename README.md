Ratchet
=======

[![Build Status](https://travis-ci.org/WyriHaximus/Ratchet.png)](https://travis-ci.org/WyriHaximus/Ratchet)
[![Latest Stable Version](https://poser.pugx.org/WyriHaximus/Ratchet/v/stable.png)](https://packagist.org/packages/WyriHaximus/Ratchet)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/WyriHaximus/ratchet/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

CakePHP plugin wrapping Ratchet

## What is Ratchet ##

Ratchet for CakePHP brings [the Ratchet websocket](http://socketo.me/) server to CakePHP. Websockets allow you to utilize near-real-time communication between your application and it's visitors. For example notifying a page the associated record in the database has been updated using the [Pushable behaviour](http://wyrihaximus.net/projects/cakephp/ratchet/documentation/model-push.html).

## Getting started ##

#### 1. Requirements ####

This plugin depends on the following plugins and libraries and are pulled in by composer later on:

- [Ratchet](https://github.com/cboden/Ratchet)
- [AssetCompress](https://github.com/markstory/asset_compress) v0.9+

#### 2. Composer ####

Make sure you have [composer](http://getcomposer.org/) installed and configured with the autoloader registering during bootstrap as described [here](http://ceeram.github.io/blog/2013/02/22/using-composer-with-cakephp-2-dot-x/). Make sure you have a composer.json and add the following to your required section.

```json
"wyrihaximus/ratchet": "dev-master"
```

When you've set everything up, run `composer install`.

#### 3. Setup the plugin ####

Make sure you load `Ratchet` and `AssetCompress` in your bootstrap and setup `AssetCompress` properly.

#### 4. Start and stopping the server ####

The server can be started with the following command:

```bash
./cake Ratchet.websocket start
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

