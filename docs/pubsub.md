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

[onPublish example]
