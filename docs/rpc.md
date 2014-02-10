RPC
===

RPC's are used to request data from the server or invoke something on the server.

## Client side ##

The following code calls the RPC `Plugin.TopicName` with an object that has one attribute named `foo`, value `bar`. Then it passed a callback into the returned promise that logs the `responseData` to the developer console.

```javascript
cakeWamp.call('Plugin.TopicName', {
  foo: 'bar'
}).then(function(responseData) {
  console.log(responseData);
});
```

## Server side ##

The server side is a little more complicated. First you have to setup an `Event Listener`. In our example we'll create `SearchListener.php` in `./Event` with the following contents:
```php
<?php

App::uses('CakeEventListener', 'Event');

class SearchListener implements CakeEventListener {

	public function implementedEvents() {
		return array(
			'Rachet.WampServer.Rpc.search' => 'search',
		);
	}

	public function search(CakeEvent $event) {
		// With this function we'll search
	}
}
```

(Make sure you'll attach this listener during bootstrap!)

In that search function you'll do your magic and get the results from what ever async source you are using and fulfill the promise. (For example you could use [react/http-client](https://github.com/reactphp/http-client) to retrive the search results from elasticsearch.)

```php
public function search(CakeEvent $event) {
    $event->data['promise']->resolve('results!');
}
```

That returns the string `results!` back to the client side promise waiting for a result.

A promise can also be rejected.

```php
public function search(CakeEvent $event) {
    $event->data['promise']->reject([

    ]);
}
```
