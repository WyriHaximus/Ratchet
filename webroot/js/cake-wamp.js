var cakeWamp = window.cakeWamp || {};

/**
 * Array with the subscriptions to keep track off and auto (re)subscribe when (re)connected
 * @type Array
 */
cakeWamp.subscriptions = [];

cakeWamp.connect = function() {
    ab.connect(wsuri, function(session) {
        cakeWamp.session = session;
        
        cakeWamp.onconnect();
    }, cakeWamp.options);
};

cakeWamp.onconnect = function() {  
    cakeWamp.session.subscribe('Rachet.connection.keepAlive', function (topic, event) {});
    
    for (var i in cakeWamp.subscriptions) {
        cakeWamp.session.subscribe(cakeWamp.subscriptions[i].topic, cakeWamp.subscriptions[i].callback);
    }
};

cakeWamp.call = function() {
    cakeWamp.session.call.apply(cakeWamp.session, arguments);
};

cakeWamp.subscribe = function() {
    cakeWamp.subscriptions.push({
        topic: arguments[0],
        callback: arguments[1]
    });
    
    cakeWamp.session.subscribe.apply(cakeWamp.session, arguments);
};

cakeWamp.unsubscribe = function() {
    cakeWamp.session.unsubscribe.apply(cakeWamp.session, arguments);
};

cakeWamp.publish = function() {
    cakeWamp.session.publish.apply(cakeWamp.session, arguments);
};

cakeWamp.connect();