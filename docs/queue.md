Queue
=====

The message queue allows you to communicate with the websocket process.

## Supported flavours ##

Ratchet supports 2 true a-sync message queue flavours.

### 0MQ ###

The ZMQ transport utilizes [0MZ](http://www.zeromq.org/) as a message queue.

### Predis ###

The Predis transport utilizes [redis](http://redis.io/)'s pub/sub functionality as a message queue.