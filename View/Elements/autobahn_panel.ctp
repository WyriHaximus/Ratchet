<h3>Debug</h3>
<?php
echo $this->Form->input('debugws', array(
    'label' => 'WebSocket Protocol',
    'type' => 'checkbox',
    'onchange' => 'if(this.checked) { ab._debugws = true; } else { ab._debugws = false; }',
));
echo $this->Form->input('debugrpc', array(
    'label' => 'RPC\'s',
    'type' => 'checkbox',
    'onchange' => 'if(this.checked) { ab._debugrpc = true; } else { ab._debugrpc = false; }',
));
echo $this->Form->input('debugpubsub', array(
    'label' => 'PubSub',
    'type' => 'checkbox',
    'onchange' => 'if(this.checked) { ab._debugpubsub = true; } else { ab._debugpubsub = false; }',
));
?>

<script>
    if (ab._debugws) {
        var ab_debugws_element = document.getElementById('debugws_');
        if (ab_debugws_element) {
            ab_debugws_element.checked = true;
        }
    }
    
    if (ab._debugrpc) {
        var ab_debugrpc_element = document.getElementById('debugrpc_');
        if (ab_debugrpc_element) {
            ab_debugrpc_element.checked = true;
        }
    }
    
    if (ab._debugpubsub) {
        var ab_debugpubsub_element = document.getElementById('debugpubsub_');
        if (ab_debugpubsub_element) {
            ab_debugpubsub_element.checked = true;
        }
    }
</script>