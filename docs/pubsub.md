PubSub
======

### Server side

The server can publish items on a topic

The following example will publish the time every second: 

```php
<?php declare(strict_types=1);

namespace App\Listener;

use Cake\Event\EventListenerInterface;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use Thruway\ClientSession;
use WyriHaximus\Ratchet\Event\OnSessionEndEvent;
use WyriHaximus\Ratchet\Event\OnSessionStartEvent;

final class ColoursListener implements EventListenerInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ClientSession
     */
    private $session;

    /**
     * @var TimerInterface
     */
    private $timer;

    /**
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function implementedEvents()
    {
        return [
            OnSessionStartEvent::realmEvent('realm_name')  => 'onSessionStart',
            OnSessionEndEvent::realmEvent('realm_name')    => 'onSessionEnd',
        ];
    }

    public function onSessionStart(OnSessionStartEvent $event): void
    {
        $this->session = $event->getSession();

        $this->timer = $this->loop->addPeriodicTimer(1, function (): void {
            $this->session->publish('time', [time()]);
        });
    }

    public function onSessionEnd(OnSessionEndEvent $event): void
    {
        $this->session = null;
        $this->loop->cancelTimer($this->timer);
    }
}
```

### Client side

The following javascript code subscribes to the topic published to in the listener:

```javascript
window.WyriHaximus.Ratchet.realm_name.subscribe('time', (args) => console.log(args));
```