Directory Structure
===================

The directory structure can be intimidating for someone jumping into the project for the first time. This document explains the purpose and contents for each directory.

- **Ratchet**
    - **Config** - [Configuration](configuration.html) of the plugin, bootstrap, assets etc.
    - **Console**
        - **Command** - This directory contains the [websocket shell](shell.html) command that is used to start and stop the server.
    - **Event** - Contains all the [event listeners](events.html)
        - **Queue** - Queue command reladed listeners
        - **Statistics** - Statistic gathering listeners. (Used by statistical commands from Lib\MessageQueue\Command\Statistics.)
    - **Lib**
        - **MessageQueue** - [Message Queue](queue.html)
            - **Command** - Different commands to interact with the websocket server from within your application
                - **Statistics** - Statistical commands used for metric gathering from the websocket server 
            - **Interfaces** - Interfaces for the MessageQueue commands and transports
            - **Transports** - Different ways to communicate with the websocket server from within your application
        - **Panel** - DebugKit panel
        - **Phunin** - PhuninCake plugins that supply statistics about the websocket server to a munin server
        - **Wamp** - Contains the heart of the plugin, the websocket server
    - **Model**
        - **Behavior** - The [Pushable](model_push.html) behavior allowing models to push data via the websocket server directly to subscribing clients
        - **Datasource** - Contains the session handler reading the session data from the websocket server
    - **Test** - Contains all tests, fixtures and test app files for this plugin
    - **View**
        - **Elements** - Contains the different debugkit panels for this plugin
        - **Helper** - Contains the WampHelper used to setup the cakeWamp client with all the details 
    - **docs** - This is where all the documentation resides
    - **webroot**
        - **js** - Contains all the [javascripting](client.html)
            - **autobahn** - AutoBahn.js, the underlying websocket client for cakeWamp.js
            - **cryptojs** - crypto.js, cryptographic library in javascript
            - **web-socket-js** - Wrapper around the Flash fallback for non-websocket capable browsers providing the websocket interface
            - **when** - when.js javascript promise objects
        - **swf** - Contains the Flash fallback for non-websocket capable browsers 