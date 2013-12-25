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
    public $actsAs = [
        'Ratchet.Pushable' => [
            'events' => [
                [ // Only fire when a reccord is created
                    'eventName' => 'Model.created',
                    'created' => true,
                ],
                [ // Fires for all updated reccords
                    'eventName' => 'Model.updated',
                    'created' => false,
                ],
                [ // Fires 'Model.updated.1' for model with the id 1
                    'eventName' => 'Model.updated.{id}',
                    'created' => false,
                ],
            ],
        ),
    );