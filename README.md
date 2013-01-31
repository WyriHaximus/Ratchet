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
        - Open /app/Config/bootstrap.php and add "CakePlugin::load('Ratchet');" to the file
    
    4. Install Ratchet (assuming composer.phar is in your home dir)
        > cd {Your Cake Project}/app/Plugin/Ratchet
        > php ~/composer.phar install
    
    5. Start Ratchet
        > cd {Your Cake Project}/app/Console/
        > chmod +x cake
        > ./cake Ratchet.websocket run
