
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bing Maps Example</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?callback=loadMapScenario&key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR" async defer></script>
    <style>
        html, body {
            padding: 0;
            margin: 0;
            height: 100%;
        }

        #distances {
            position: fixed;
            bottom: 0;
            left: 0;
            background-color: white;
            padding: 10px;
            overflow: auto;
            max-height: 30%;
            width: 200px;
            z-index: 1;
            text-align: center;
        }

        #myMap {
            position: relative;
            width: 100%;
            height: 100%;
            float: right;
        }

        #locationsInput, #fromInput, #toInput, #adjacencyInput {
            margin: 10px;
        }

        #locationsInput {
            display: none;
        }
    </style>
</head>
<body>
    <div>
        <textarea id="locationsInput" placeholder="Enter locations array" rows="2" cols="50" name="location">
 <?php
 session_start();
// Include the database connection file
require "db.php";
if (!isset($_SESSION['email'])) {
    // Redirect the user to the signin page
    header("Location: signin.php");
    exit(); // Make sure to exit after redirection
}
$email= $_SESSION['email'];
// Fetch locations from the 'users' table
$sql = "SELECT `email`, `location`,`hospital_email`,`fire_station_email`,`police_station_email` FROM `users` WHERE email= '$email'"; // Include 'user_id' in the SELECT query
$result = $conn->query($sql);

// Create an array to store location data
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Assuming 'location' is a JSON string in the database
            $coordinates = json_decode($row['location'], true);

            // Assuming 'hospital_email', 'fire_station_email', 'police_station_email' are fields in the database
            $hospitalEmail = $row['hospital_email'];
            $fireStationEmail = $row['fire_station_email'];
            $policeStationEmail = $row['police_station_email'];

            // Print the formatted data
            echo json_encode($coordinates, JSON_PRETTY_PRINT);
        }
    } else {
        echo "No records found in the 'users' table.";
    }
}

// Close the database connection
$conn->close();
?>
        </textarea>
    </div>
    <div id="myMap"></div>
    <div id="distances"></div>

    <script type="text/javascript">
        function calculateCenter(locations) {
            const sumLat = locations.reduce((sum, loc) => sum + loc.lat, 0);
            const sumLon = locations.reduce((sum, loc) => sum + loc.lon, 0);

            const avgLat = sumLat / locations.length;
            const avgLon = sumLon / locations.length;

            return new Microsoft.Maps.Location(avgLat, avgLon);
        }

        function loadMapScenario() {
            var locationsInput = document.getElementById('locationsInput').value;
            var locations = JSON.parse(locationsInput);

            var centerLocation = calculateCenter(locations);
            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                center: centerLocation,
                zoom: 1
            });

            var zoomLevel = 0;
            var maxZoom = 19;
            var zoomInterval = 50;

            function increaseZoom() {
                if (zoomLevel <= maxZoom) {
                    map.setView({ zoom: zoomLevel });
                    zoomLevel++;
                    setTimeout(increaseZoom, zoomInterval);
                }
            }

            increaseZoom();
function createPushpinClickHandler(pushpin, pointNumber) {
        return function (args) {
        // Display a confirm dialog with the point number
        var confirmed = confirm('Do you want to Alert ' + pointNumber);

        // If the user confirms, toggle the color
        if (confirmed) {
            var lat = pushpin.getLocation().latitude;
            var lon = pushpin.getLocation().longitude;
            var hospitalEmail = '<?php echo $hospitalEmail; ?>'; // PHP variable containing hospital email
            var fireStationEmail = '<?php echo $fireStationEmail; ?>'; // PHP variable containing fire station email
            var policeStationEmail = '<?php echo $policeStationEmail; ?>'; // PHP variable containing police station email
            var color = pushpin.getColor();

            // Toggle the color
            if (color === 'blue') {
                pushpin.setOptions({ color: 'red' });
            } else {
                pushpin.setOptions({ color: 'blue' });
            }

            // Send data to alert.php using AJAX
             sendAlertData(lat, lon, hospitalEmail, fireStationEmail, policeStationEmail);
        }
    };
}
function sendAlertData(lat, lon, hospitalEmail, fireStationEmail, policeStationEmail) {
            var xhr = new XMLHttpRequest();
            var url = 'alert.php';
            var params = 'lat=' + lat + '&lon=' + lon + '&hospitalEmail=' + hospitalEmail + '&fireStationEmail=' + fireStationEmail + '&policeStationEmail=' + policeStationEmail;

            xhr.open('POST', url, true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // You can handle the response from alert.php here
                    console.log(xhr.responseText);
                }
            };

            xhr.send(params);
        }

         // Loop through locations and create pushpins with click event handlers
for (var i = 0; i < locations.length; i++) {
    var location = new Microsoft.Maps.Location(locations[i].lat, locations[i].lon);
    var pushpin = new Microsoft.Maps.Pushpin(location, {
        title: 'Point ' + (i + 1),
        htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (i + 1) + '</div>',
        color: 'blue'
    });

    // Add a click event listener to each pushpin using the created handler function
    Microsoft.Maps.Events.addHandler(pushpin, 'click', createPushpinClickHandler(pushpin, i + 1));

    map.entities.push(pushpin);
}
        }
    </script>
</body>
</html>
