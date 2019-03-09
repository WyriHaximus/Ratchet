PubSub
======

PubSub utilizes `channels` to transport messages from and to clients. A channel can have multiple clients and all clients (and the server) can broadcast to it.

## Client side ##

```javascript
cakeWamp.subscribe('Plugin.TopicName', function (topic, event) {
    console.log(event); // {foo: 'bar'}
});
```

### Broadcast ###

```javascript
cakeWamp.publish('Plugin.TopicName', {
    foo:'bar',
});
```

## Server side ##

```php
<?php

App::uses('CakeEventListener', 'Event');

class EpochListener implements CakeEventListener {

	public function implementedEvents() {
		return [
			'Rachet.WampServer.onSubscribeNewTopic.Plugin.TopicName' => 'fooBar',
		];
	}

	public function fooBar(CakeEvent $cakeEvent) {
        $cakeEvent->subject()->broadcast('Plugin.TopicName', [
            'foo' => 'bar',
        ]);
	}
}
```

### Broadcast ###

```php
<?php

App::uses('CakeEventListener', 'Event');

class EpochListener implements CakeEventListener {

	public function implementedEvents() {
		return [
			'Rachet.WampServer.onSubscribeNewTopic.Plugin.TopicName' => 'fooBar',
		];
	}

	public function fooBar(CakeEvent $cakeEvent) {
	    debug($cakeEvent->data['event); // {foo: 'bar'}
	}
}
```

(Make sure you'll attach this listener during bootstrap! See [the docs on this](http://book.cakephp.org/2.0/en/core-libraries/events.html#registering-listeners).)

### onPublish ###

When a client broadcasts a message the server emits two events, `Rachet.WampServer.onPublish` and `Rachet.WampServer.onPublish.TOPICNAME`. Both with identical payload. The first first listeners interested in all messages from clients and the second only interested in messages for that specific topic.
