<?php
use Cake\Core\Configure;
?>
    <script>
        WEB_SOCKET_SWF_LOCATION = "<?= $this->Url->build('WyriHaximus/Ratchet/swf/WebSocketMain.swf', true) ?>";
        var cakeWamp = window.cakeWamp || {};
        cakeWamp.options = {
            <?php if (Configure::read('debug') == 2): ?>
            debugWamp: true,
            <?php endif; ?>
            retryDelay: "<?= (int)Configure::read('WyriHaximus.Ratchet.Client.retryDelay') ?>",
            maxRetries: "<?= (int)Configure::read('WyriHaximus.Ratchet.Client.maxRetries') ?>"
        };
        <?php
        $uri = (Configure::read('WyriHaximus.Ratchet.Connection.Websocket.secure') ? 'wss' : 'ws') . '://';
        $uri .= Configure::read('WyriHaximus.Ratchet.Connection.Websocket.address');
        $uri .= ':' . Configure::read('WyriHaximus.Ratchet.Connection.Websocket.port');
        ?>
        wsuri = "<?= $uri; ?>";
        <?php if (Configure::read('WyriHaximus.Ratchet.Connection.keepaliveInterval') > 0): ?>
        cakeWamp.subscribe('WyriHaximus.Ratchet.Connection.keepAlive', function (topic, event) {});
        <?php endif; ?>
    </script>
<?= $this->Html->script('WyriHaximus/Ratchet.ratchet.min.js') ?>