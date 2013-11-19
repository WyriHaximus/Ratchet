<h3>Debug to console</h3>
<fieldset>
	<?php echo $this->Form->input('debugws', array(
		'label' => 'WebSocket Protocol',
		'type' => 'checkbox',
		'onchange' => 'if(this.checked) { ab._debugws = true; } else { ab._debugws = false; }',
	)); ?>
</fieldset>
<fieldset>
	<?php echo $this->Form->input('debugrpc', array(
    'label' => 'RPC\'s',
    'type' => 'checkbox',
    'onchange' => 'if(this.checked) { ab._debugrpc = true; } else { ab._debugrpc = false; }',
)); ?>
</fieldset>
<fieldset>
	<?php echo $this->Form->input('debugpubsub', array(
    'label' => 'PubSub',
    'type' => 'checkbox',
    'onchange' => 'if(this.checked) { ab._debugpubsub = true; } else { ab._debugpubsub = false; }',
)); ?>
</fieldset>

<ul class="neat-array depth-0" id="autobahn_subscriptions">
</ul>

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

	setInterval(function() {
		var html = '';
		for (var i in cakeWamp.subscriptions) {
			html += '<li>' + cakeWamp.subscriptions[i].topic + '</li>';
		}
		document.getElementById('autobahn_subscriptions').innerHTML = html;
	}, 1000)
</script>