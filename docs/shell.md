Shell
=====

This shell is the intiater for everything else. Aside from the `Helper` everything runs within this process. It is recommended to set it up with `supervisor` (for example see SOCKETOMELINK).

## Starting the websocket server ##

Start the websocket server with the following command

```bash
./cake WyriHaximus/Ratchet.websocket start
```

### Options ###

The start action has a few options:

- --verbose|-v Turn verbose mode on. Verbose mode outputs all emitted events to the console. This is great for debugging your code and have a clue what is going on inside your project.

[EXAMPLEVERBOSEOUTPUTIMAGE]

### Note ###

The shell will give a warning if you are using the default `stream_select` loop recommending a better performing event-loop. The server will function just fine but not at optimal levels.

[EXAMPLENOTICEIMAGE]
