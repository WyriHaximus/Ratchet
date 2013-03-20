var cakeWamp = window.cakeWamp || {};

/**
 * Array with the subscriptions to keep track off and auto (re)subscribe when (re)connected
 * @type Array
 */
cakeWamp.subscriptions = [];

/**
 * Array with onconnect listeners
 * @type Array
 */
cakeWamp.onconnectListeners = [];

/**
 * Array with onhangup listeners
 * @type Array
 */
cakeWamp.onhangupListeners = [];

cakeWamp.connect = function() {
    ab.connect(wsuri, function(session) {
        cakeWamp.session = session;
        
        cakeWamp.onconnect();
    }, function(code, reason) {
        cakeWamp.onhangup(code, reason);
    }, cakeWamp.options);
};

cakeWamp.onconnect = function() {
    cakeWamp.session.subscribe('Rachet.connection.keepAlive', function (topic, event) {});
    
    for (var i in cakeWamp.onconnectListeners) {
        cakeWamp.onconnectListeners[i](cakeWamp.session);
    }
    
    for (var i in cakeWamp.subscriptions) {
        cakeWamp.session.subscribe(cakeWamp.subscriptions[i].topic, cakeWamp.subscriptions[i].callback);
    }
};

cakeWamp.onhangup = function(code, reason) {
    for (var i in cakeWamp.onhangupListeners) {
        cakeWamp.onhangupListeners[i](code, reason);
    }
};

/**
 * Wrapper around AB's session.call method
 */
cakeWamp.call = function() {
    cakeWamp.session.call.apply(cakeWamp.session, arguments);
};

/**
 * Wrapper around AB's session.subscribe method
 */
cakeWamp.subscribe = function() {
    cakeWamp.subscriptions.push({
        topic: arguments[0],
        callback: arguments[1]
    });
    
    if (cakeWamp.session && cakeWamp.session._websocket_connected) {
        cakeWamp.session.subscribe.apply(cakeWamp.session, arguments);
    }
};

/**
 * Wrapper around AB's session.unsubscribe method
 */
cakeWamp.unsubscribe = function() {
    cakeWamp.session.unsubscribe.apply(cakeWamp.session, arguments);
};

/**
 * Wrapper around AB's session.publish method
 */
cakeWamp.publish = function() {
    cakeWamp.session.publish.apply(cakeWamp.session, arguments);
};

cakeWamp.connect();