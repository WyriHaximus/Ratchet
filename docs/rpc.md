RPC
===

### Server side

To create a `RPC` you have to set up a listener to hook into the `OnSessionStart` event.

The following example will echo what ever you throw into it back to the client side: 

```php
<?php declare(strict_types=1);

namespace App\Listener;

use Cake\Event\EventListenerInterface;
use Thruway\Session;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;
use function React\Promise\resolve;

final class EchoRpcListener implements EventListenerInterface
{
    private const RPC_NAME  = 'name.space.to.your.rpc.name';
    
    public function implementedEvents()
    {
        return [
            OnSessionStartEvent::realmEvent('realm_name')  => 'onSessionStart',
        ];
    }

    public function onSessionStart(OnSessionStartEvent $event): void
    {
        /** @var Session $session */
        $session = $event->getSession();
        $session->register(self::RPC_NAME, function (array $arguments) {
            return resolve(['arguments' => $arguments, 'time' => time()]);
        });
    }
}
```

### Client side

The following javascript code calls the RPC as registered in the listener:

```javascript
window.WyriHaximus.Ratchet.realm_name.rpc(
    'name.space.to.your.rpc.name',
    [
        1,
        2,
        3,
    ]
).then((args) => console.log(args))
```