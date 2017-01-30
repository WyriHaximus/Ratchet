<?php
use Cake\Core\Configure;
?>
<script>
    window.config = window.config || {};
    window.config.WyriHaximus = window.config.WyriHaximus || {};
    window.config.WyriHaximus.Ratchet = window.config.WyriHaximus.Ratchet || {};
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?> = <?= json_encode(\WyriHaximus\Ratchet\realmConfiguration($realm)) ?>;
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>.onchallenge = function (session, method, extra) {
        console.log([session, method, extra]);
        //return true;
    };
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>.authmethods = ['wampcra'];
    window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>.authid = '147145';
    console.log(window.config.WyriHaximus);
    window.WyriHaximus = window.WyriHaximus || {};
    window.WyriHaximus.Ratchet = window.WyriHaximus.Ratchet || {};
    window.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?> = new persistentAutobahn.PersistentAutobahn(window.config.WyriHaximus.Ratchet.<?= str_replace('-', '_', $realm) ?>);
</script>
