Events
======

Ratchet is build on events to decouple everything. 

## RPC ##

Creating an RPC is simple and requires you to listen to `Rachet.WampServer.Rpc.YOURRPCNAME` where YOURRPCNAME is the desires RPC name that can be called from the client. For example:

```php
class PlusOnelListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Rachet.WampServer.Rpc.plusOne' => 'plusOne'
        );
    }
}
```

The plusOne function has to call `callResult` sending the RPC result back to the client.

```php
	public function plusOne($event) {
		$event->data['connection']->callResult($event->data['id'], array(
			++$event->data['value'],
		));
	}
```