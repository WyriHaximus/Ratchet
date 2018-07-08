<?php
use Cake\Routing\Router;
?>
<script>
    window.config = window.config || {};
    window.config.WyriHaximus = window.config.WyriHaximus || {};
    window.config.WyriHaximus.Ratchet = window.config.WyriHaximus.Ratchet || {};
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?> = <?= json_encode(\WyriHaximus\Ratchet\realmConfiguration($realm)) ?>;
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>.onchallenge = function (session, method, extra) {
        try {
            if (method === "jwt") {
                return new Promise(function (resolve, reject) {
                    var oReq = new XMLHttpRequest();
                    oReq.addEventListener("load", function () {
                        resolve(JSON.parse(this.responseText).token);
                    });
                    oReq.open("GET", '<?= Router::url(['plugin' => 'WyriHaximus/Ratchet', 'controller' => 'JWT', 'action' => 'token', 'prefix' => false, '_ext' => 'json', 'realm' => $realm]) ?>');
                    oReq.send();
                });
            }

            throw "don't know how to authenticate using '" + method + "'";
        } catch (e) {
            console.log(e);
            throw e;
        }
    };
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>.authmethods = ['jwt'];

    window.WyriHaximus = window.WyriHaximus || {};
    window.WyriHaximus.Ratchet = window.WyriHaximus.Ratchet || {};
    window.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?> = new persistentAutobahn.PersistentAutobahn(window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>);
</script>
