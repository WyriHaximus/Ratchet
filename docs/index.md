Ratchet
=======

CakePHP plugin wrapping Ratchet

Model Push
----------

On the client side:
-------------------
    cakeWamp.subscribe('Rachet.WampServer.ModelUpdate.Model.updated', function (topic, event) {console.log(event);});

On the serverside in your model file:
-------------------------------------
    public $actsAs = array(
        'Ratchet.Pushable' => array(
            'events' => array(
                array( // Only fire when a reccord is created
                    'eventName' => 'Model.created',
                    'created' => true,
                ),
                array( // Fires for all updated reccords
                    'eventName' => 'Model.updated',
                    'created' => false,
                ),
                array( // Fires 'Model.updated.1' for model with the id 1
                    'eventName' => 'Model.updated.{id}',
                    'created' => false,
                ),
            ),
        ),
    );