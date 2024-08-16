<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bing Maps Example</title>
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
</head>
<body>
    <div id="myMap" style="width: 100%; height: 100%;"></div>

    <script type="text/javascript">
        function loadMapScenario() {
            var centerLocation = new Microsoft.Maps.Location(15.47928, 80.02014);
            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                center: centerLocation,
                zoom: 19
            });

            var locations = [
                { lat: 15.479652, lon: 80.019475 }
            ];

            // Add blue pushpins with unique numbers and buttons to the map
            for (var i = 0; i < locations.length; i++) {
                (function (index) {
                    var location = new Microsoft.Maps.Location(locations[index].lat, locations[index].lon);

                    var pushpin = new Microsoft.Maps.Pushpin(location, {
                        htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (index + 1) +
                            '</div><button id="btn' + index + '">Click me</button>',
                        color: 'green'
                    });

                    map.entities.push(pushpin);

                Microsoft.Maps.Events.addHandler(pushpin, 'click', function (e) {
    var confirmation = confirm('Do you want to change the state of Point ' + (index + 1) + ' to Normal/danger ?');
    if (confirmation) {
        if (pushpin.getColor() === 'blue') {
            pushpin.setOptions({ color: 'red' });
        } else {
            pushpin.setOptions({ color: 'blue' });
        }
    }
});
                })(i);
            }
        }
    </script>
</body>
</html>
