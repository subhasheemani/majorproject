<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Microsoft Maps API Example</title>
    <style>
        #mapContainer {
            height: 500px;
        }
    </style>
</head>
<body>
    <h2>Geofence your Emergency Area</h2>
    <div id="mapContainer"></div>
    <input type="hidden" id="latitude" readonly>
    <input type="hidden" id="longitude" readonly>

    <form action="step2.php" method="GET">
        <input type="hidden" value="<?= isset($_GET['placeName']) ? $_GET['placeName'] : '' ?>" name="placeName" placeholder="Place Name">
        <input type="hidden" value="<?= isset($_GET['phoneNo']) ? $_GET['phoneNo'] : '' ?>" name="phoneNo" placeholder="Phone Number">
        <input type="hidden" value="<?= isset($_GET['email']) ? $_GET['email'] : '' ?>" name="email" placeholder="Email">
        <input type="hidden" value="<?= isset($_GET['password']) ? $_GET['password'] : '' ?>" name="password" placeholder="Password">
         <input type="hidden" name="hospitalEmail" value="<?= isset($_GET['hospitalEmail']) ? $_GET['hospitalEmail'] : '' ?>">
        <input type="hidden" name="fireStationEmail" value="<?= isset($_GET['fireStationEmail']) ? $_GET['fireStationEmail'] : '' ?>">
        <input type="hidden" name="policeStationEmail" value="<?= isset($_GET['policeStationEmail']) ? $_GET['policeStationEmail'] : '' ?>">
        <!-- End of hidden fields -->
        

        <center>
            <button style="font-size:18px;padding:10px;border-radius:20px;display: block; margin: 10px; background: linear-gradient(to right, #3498db, #2ecc71); color: white;" onclick="submitCoordinates()">Submit</button>
        </center>

        <!-- Include the Microsoft Maps API script with your API key -->
        <script type='text/javascript' src='https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario' async defer></script>

        <script>
            var savedCoordinates = [];
            var map;

            function loadMapScenario() {
                // Initialize the map
                map = new Microsoft.Maps.Map(document.getElementById('mapContainer'), {
                    credentials: 'AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR', // Replace with your actual API key
                    center: new Microsoft.Maps.Location(0, 0), // Initial map center
                    zoom: 2 // Initial zoom level
                });

                // Add a click event listener to the map
                Microsoft.Maps.Events.addHandler(map, 'click', function (e) {
                    // Display the mouse coordinates in the input fields
                    document.getElementById('latitude').value = e.location.latitude;
                    document.getElementById('longitude').value = e.location.longitude;

                    // Add a pushpin to the map at the clicked location
                    var pushpin = new Microsoft.Maps.Pushpin(e.location);
                    map.entities.push(pushpin);

                    // Save the coordinates to the array
                    savedCoordinates.push({
                        latitude: e.location.latitude,
                        longitude: e.location.longitude
                    });
                });
            }

            function submitCoordinates() {
                // Display all saved coordinates in JSON format
                var jsonCoordinates = JSON.stringify(savedCoordinates, null, 2);
                alert('JSON Coordinates:\n' + jsonCoordinates);

                // Append the JSON data to the form
                var jsonInput = document.createElement('input');
                jsonInput.type = 'hidden';
                jsonInput.name = 'location';
                jsonInput.value = jsonCoordinates;
                document.querySelector('form').appendChild(jsonInput);

                // Submit the form
                document.querySelector('form').submit();
            }
        </script>
    </form>
</body>
</html>
