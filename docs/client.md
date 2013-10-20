Client
======

Ratchet's client is a simple wrapper around [Autobahn](http://autobahn.ws/) adding some handy functionality. Such as auto-connecting on page load, resubscrining after reconnect and `onconnect`/`onhangup` events.

## pub/sub ##

The examples below are usage examples, for a more detailed article about Autobahns pub/sub workings check [here](http://autobahn.ws/js/tutorials/pubsub).

### subscribing ###

```javascript
cakeWamp.subscribe('Plugin.TopicName', function(topicUri, event) {
	// Do your stuff
});
```

## publishing ###

```javascript
cakeWamp.publish('Plugin.TopicName', {
	type: 'eventObject'
});
```

## unsubscribing ###

```javascript
cakeWamp.unsubscribe('Plugin.TopicName');
```

## RPC ##

```javascript
cakeWamp.call('Plugin.TopicName', {
	type: 'eventObject'
});
```

## events ##

### onconnect ###

```javascript
cakeWamp.onconnectListeners.push(function(session) {
	console.log('Connected!');
});
```

### onhangup ###

```javascript
cakeWamp.onhangupListeners.push(function(session) {
	console.log('Poof!');
});
```