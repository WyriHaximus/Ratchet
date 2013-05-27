Ratchet
=======

CakePHP plugin wrapping Ratchet

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