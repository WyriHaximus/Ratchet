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

The server side is a little more complicated.