Helper
======

The `Ratchet` plugin contains a helper that sets all the client side details for you. First of all make sure it's loaded in you (App)Controller:

```php
$helpers = [
  'Ratchet.Wamp',
];
```

Then in your view or layout template add this:

```php
<?php $this->Wamp->init(); ?>
```

That will generate the needed code to configure and setup the websocket clientside tools.
