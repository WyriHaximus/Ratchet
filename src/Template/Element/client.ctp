<?php
use Cake\Core\Configure;
?>
<script>
    window.WyriHaximus = window.WyriHaximus || {};
    window.WyriHaximus.Ratchet = window.WyriHaximus.Ratchet || {};
    window.WyriHaximus.Ratchet.<?= $realm ?> = new persistentAutobahn.PersistentAutobahn(<?= json_encode(\WyriHaximus\Ratchet\realmConfiguration($realm)) ?>);
</script>
