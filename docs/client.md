Client
======

`Note: Make sure you've setup the helper before starting with this.`

Ratchet's client is a simple wrapper around [Autobahn](http://autobahn.ws/) adding some handy functionality. Such as auto-connecting on page load, resubscrining after reconnect and `onconnect`/`onhangup` events.

## Pub/Sub ##

The examples below are usage examples, for a more detailed article about Autobahns pub/sub workings check [here](http://autobahn.ws/js/tutorials/pubsub).

### Subscribing ###

```javascript
cakeWamp.subscribe('Plugin.TopicName', function(topicUri, event) {
	// Do your stuff
});
```

## Publishing ###

```javascript
cakeWamp.publish('Plugin.TopicName', {
	type: 'eventObject'
});
```

## Unsubscribing ###

```javascript
cakeWamp.unsubscribe('Plugin.TopicName');
```

## RPC ##

```javascript
cakeWamp.call('Plugin.TopicName', {
	type: 'eventObject'
});
```

## Events ##

### Onconnect ###

```javascript
cakeWamp.onconnectListeners.push(function(session) {
	console.log('Connected!');
});
```

### Onhangup ###

```javascript
cakeWamp.onhangupListeners.push(function(session) {
	console.log('Poof!');
});
```
