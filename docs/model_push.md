Model Push
==========

Out of the box Ratchet does only client to client (via the server) communication. But with model push any changes in a models dataset can but send to listening clients.

## Configuration and Dependencies ##

As this is one a more advanced feature of Ratchet additional dependencies are required. There are 2 flavours to pick one, either `0MQ` or `Redis`. **Either of those 2 has to be configured in the configuration otherwise this feature won't work!** Check the [configuration documentation](configuration.md#queue) to configure it before you use it.

## Setting up the behaviour ##

The following code example attaches the Pushable behavior to the `WyriProject` model. The events do the following in order:

1. Fires on creating on a new record and binds to `WyriProject.created`.
2. Fires on updating any record and binds to `WyriProject.updated`.
3. Fires on any updating record and binds to `WyriProject.updated.{id}` where `{id}` is the `id` for that record. So if `id` is `1` it binds to `WyriProject.updated.1`.

The data is passed into the event as a 1 dimensional array.

```php
    public $actsAs = array(
        'Ratchet.Pushable' => array(
            'events' => array(
                array(
                    'eventName' => 'WyriProject.created',
                    'created' => true,
                ),
                array(
                    'eventName' => 'WyriProject.updated',
                    'created' => false,
                ),
                array(
                    'eventName' => 'WyriProject.updated.{id}',
                    'created' => false,
                    'fields' => true,
                ),
            ),
        ),
    );
```

## Client side ##

On the client side the only thing required is subscribing to the event:

```javascript
cakeWamp.subscribe('WyriProject.updated.1', function(topicUri, event) {
	// Do your stuff with the data
});
```