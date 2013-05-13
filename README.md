Ratchet
=======

CakePHP plugin wrapping Ratchet

Please note that this repo is a reflection of my down and dirty development situation. Some classes might already be deprecated but still there, code will be messy and most likely wrong. If you have thoughts, idea's, code or anything please submit it.

Getting started
---------------
    1. Install Composer (It's the only way to get Ratchet)
        > curl -s https://getcomposer.org/installer | php
        - or -
        > php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
    
    2. Create a Ratchet plugin folder and put these files there.
        > mkdir {Your Cake Project}/app/Plugin/Ratchet
        
    3. Load Ratchet Plugin
        - Open /app/Config/bootstrap.php and add "CakePlugin::load('Ratchet',array('bootstrap' => true));" to the file
    
    4. Install Ratchet (assuming composer.phar is in your home dir)
        > cd {Your Cake Project}/app/Plugin/Ratchet
        > php ~/composer.phar install
    
    5. Start Ratchet
        > cd {Your Cake Project}/app/Console/
        > chmod +x cake
        > ./cake Ratchet.websocket start


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

Model push todo:
----------------
a) Proper finishing off the classes for it.
b) Contain and refetch options