// WAMP session object
var sess;

// WAMP server
var wsuri = "ws://localhost:54321";

ab._debugrpc = true;

connect();

function connect() {
    console.log("Connecting!");
   // establish session to WAMP server
   sess = new ab.Session(wsuri,

      // fired when session has been opened
      function() {
         console.log("Connected!");
         
        sess.subscribe("ping",
            // on event publication callback
            function (topic, event) {
               connectionCountDraw(event);
               console.log(topic);
               console.log(event);
                /*sess.call('callUrl', {
                'url': url,
                'data': data
                }).then(
                function (res) {
                console.log(res);
                callback(res[0]);
                },
                function (error, desc) {
                console.log("error: " + desc);
                }
                );*/
         });
      },

      // fired when session has been closed
      function(reason) {
          console.log(reason);
         switch (reason) {
            case ab.CONNECTION_CLOSED:
               console.log("Connection was closed properly - done.");
               break;
            case ab.CONNECTION_UNREACHABLE:
               console.log("Connection could not be established.");
               
               // automatically reconnect after 1s
               window.setTimeout(connect, 10000);
               break;
            case ab.CONNECTION_UNSUPPORTED:
               console.log("Browser does not support WebSocket.");
               break;
            case ab.CONNECTION_LOST:
               console.log("Connection lost - reconnecting ...");

               // automatically reconnect after 1s
               window.setTimeout(connect, 1000);
               break;
         }
      }
   );
};


function CakeGet(url, data, callback) {
    
    if (sess && sess._websocket_connected) {
        console.log([url, data, callback]);
        sess.call('callUrl', {
                'url': url,
                'data': data
            }).then(
            function (res) {
                console.log(res);
                callback(res[0]);
            },
            function (error, desc) {
                console.log("error: " + desc);
            }
        );
    } else {
        $.get(url, data, callback);
    }
    
}