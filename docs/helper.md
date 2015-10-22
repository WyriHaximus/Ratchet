Helper
======

The `Ratchet` plugin contains a helper that sets all the client side details for you. First of all make sure it's loaded in you (App)Controller:

```php
$helpers = [
  'WyriHaximus/Ratchet.Wamp',
];
```

The helper will automatically append the required JavaScript to the `script` block. If you're not fetching the script block in your view, you can do so by adding the following:

```php
<?= $this->fetch('script') ?>
```

That will generate the needed code to configure and setup the websocket clientside tools.
