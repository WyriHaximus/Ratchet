Events
======

Ratchet is build on events to decouple everything. 

## RPC ##

`RPC`stands for `Remote Procedure Call` and allows you to call API's on the otherside of the line.

### Pre-emit ###

Before emitting the actual event Ratchet first emits `Rachet.WampServer.Rpc`. Any listener an hook into it and stop it. Stopping is done as stated in [the CakePHP Cookbook event section](http://book.cakephp.org/2.0/en/core-libraries/events.html#stopping-events):

```php
public function listeningMethod(CakeEvent $event) {
    $event->stopPropagation();
    // OR 
    return false;
}
```

In case the event is stopped actual RPC event won't be emitted. This can be used to prevent certain connections to specific calls or to throttle  the amount of calls. When this happens another event is emitted: `Rachet.WampServer.RpcBlocked`.

### Emit ###

Listening for the actual RPC event is simple and requires you to listen to `Rachet.WampServer.Rpc.YOURRPCNAME` where YOURRPCNAME is the desired RPC name that can be called from the client. For example:

```php
class PlusOnelListener implements CakeEventListener {
    public function implementedEvents() {
        return [
            'Rachet.WampServer.Rpc.plusOne' => 'plusOne'
        ];
    }
}
```

The `plusOne` function has to call `callResult` sending the RPC result back to the client.

```php
	public function plusOne($event) {
		$event->data['promise']->resolve(++$event->data['value']);
	}
```

### Post-emit ###

Once the event listener has done it's job it can either `resolve` or `reject` the given promise. Wether the promise is resolved or rejected it starts sending the result back to the client before calling the `Rachet.WampServer.RpcSuccess` or `Rachet.WampServer.RpcFailed` events.

## Pub/Sub ##

PubSub events work slightly differently and have more then 1 event to listen on.

- `Rachet.WampServer.onSubscribeNewTopic.YOURTOPICNAME` when a topic is `fresh` a.k.a. no clients are listening on it untill now.
- `Rachet.WampServer.onSubscribe.YOURTOPICNAME` when a client subscribes to a topic with other clients listening on it.
- `Rachet.WampServer.onUnSubscribe.YOURTOPICNAME` when a client unsubscribes to a topic with other clients listening on it.
- `Rachet.WampServer.onUnSubscribeStaleTopic.YOURTOPICNAME` becomes `stale` as the last client listening on it has unsubscribed.
