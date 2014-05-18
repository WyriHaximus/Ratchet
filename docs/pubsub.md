PubSub
======

## Client side ##

```javascript
cakeWamp.subscribe('Plugin.TopicName', function (topic, event) {
    console.log (event.epoch);
});
```

## Server side ##



### Broadcast ###

```php
<?php

App::uses('CakeEventListener', 'Event');

class EpochListener implements CakeEventListener {

	public function implementedEvents() {
		return [
			'Rachet.WampServer.onSubscribeNewTopic.Plugin.TopicName' => 'epoch',
		];
	}

	public function epoch(CakeEvent $cakeEvent) {
        $cakeEvent->subject()->broadcast('Plugin.TopicName', ['epoch' => time()]);
	}
}
```

(Make sure you'll attach this listener during bootstrap!)

### onPublish ###

When a client broadcasts a message the server emits two events, `Rachet.WampServer.onPublish` and `Rachet.WampServer.onPublish.TOPICNAME`. Both with identical payload. The first first listeners interested in all messages from clients and the second only interested in messages for that specific topic.
