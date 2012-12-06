{{ html.addCrumb('Ratchet') }}
{{ html.addCrumb('Live Dashboard') }}

{% set colours = [
    'steelblue',
    'red',
    'green',
    'yellow',
    'black',
    'grey',
] %}

<style>
    {% for colour in colours %}

        .colour{{- colour|camelize -}} {
            stroke: {{- colour -}};
        }

    {% endfor %}
</style>

<div class="span3 well" id="connectionCount" style="height: 250px;"></div>
<div class="span3 well" id="continentDistribution" style="height: 250px;"></div>
<div class="span3 well" id="connectionCount3" style="height: 250px;"></div>

{{ html.script('cache/raphael.js')|raw }}
{{ html.script('morris.js')|raw }}


<script>
    var connectionCountDraw = function(data) {

        $('#connectionCount svg').remove();
        
        $('#connectionCount').data(data);
        
        Morris.Donut({
            element: 'connectionCount',
            data: [
                {label: "Guests", value: data.guests},
                {label: "Users", value: data.users}
            ]
        });
        
    };

    connectionCountDraw({
        users: 0,
        guests: 0
    });

    $(window).resize(function () {
        connectionCountDraw($('#connectionCount').data());
    });
    
    setTimeout(function() {
        /*sess.call('connectionCount', {}).then(
            function (res) {
                console.log(res);
            },
            function (error, desc) {
                console.log("error: " + desc);
            }
        );*/
        sess.subscribe("connectionCount",
 
            // on event publication callback
            function (topic, event) {
                console.log(topic);
               connectionCountDraw(event);
               console.log(event);
         });
    }, 5000);
</script>