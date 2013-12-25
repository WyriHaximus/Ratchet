Events
======

Ratchet is build on events to decouple everything. 

## RPC ##

Creating an RPC is simple and requires you to listen to `Rachet.WampServer.Rpc.YOURRPCNAME` where YOURRPCNAME is the desires RPC name that can be called from the client. For example:

```php
class PlusOnelListener implements CakeEventListener {
    public function implementedEvents() {
        return [
            'Rachet.WampServer.Rpc.plusOne' => 'plusOne'
        ];
    }
}
```

The plusOne function has to call `callResult` sending the RPC result back to the client.

```php
	public function plusOne($event) {
		$event->data['connection']->callResult($event->data['id'], [
			++$event->data['value'],
		]);
	}
```

## pub/sub ##

Pubsub events work slightly differently and have more then 1 event to listen on.

- `Rachet.WampServer.onSubscribeNewTopic.YOURTOPICNAME` when a topic is `fresh` a.k.a. no clients are listening on it untill now.
- `Rachet.WampServer.onSubscribe.YOURTOPICNAME` when a client subscribes to a topic with other clients listening on it.
- `Rachet.WampServer.onUnSubscribe.YOURTOPICNAME` when a client unsubscribes to a topic with other clients listening on it.
- `Rachet.WampServer.onUnSubscribeStaleTopic.YOURTOPICNAME` becomes `stale` as the last client listening on it has unsubscribed.