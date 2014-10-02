Ratchet
=======

`Ratchet` for CakePHP wraps the `Ratchet` library and brings `websockets` to CakePHP. It's build on to of `ReactPHP` to bring non-blocking asynchronous IO to `PHP`.

## Components ##

The plugin initially started out as with all the components in one package. This resulted in a huge unfeasible plugin so I've cut in into components (each component in a separate plugin).

### Ratchet ###

This foundation plugin that provides everything that is needed to build websocket-enabled sites and plugins.

* Shell command to start the service
* Events to hoook into and respond to client-server interactions
* `RPC` and `PubSub` to use high-level client-server interaction patterns

### RatchetCommands ###

This communication component makes interaction between normal parts of your application and the websocket service possible.

* Transports to communicate over

### RatchetModelPush ###

Notifies clients over websocket `PubSub` channels about changes in your applications stored data.

* Uses `RatchetCommands` to communicate with the websocket service
* Supports different types of events for creation and updates to your data

## Additional Components ##

The following components don't directly add new features for the engineer designing the system but provide insights into the service's state and health

### RatchetStatistics ###

Hooks for `PhuninNode` to monitor the websocket service with `munin`.

### RatchetAdmin ###

For now just an idea to have an admin page with an overview of the websocket service's internal state and health

