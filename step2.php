<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $placeName = $_POST['placeName'];
    $phoneNo = $_POST['phoneNo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hospitalEmail = $_POST['hospitalEmail'];
    $fireStationEmail = $_POST['fireStationEmail'];
    $policeStationEmail = $_POST['policeStationEmail'];
    $location = json_decode($_POST['location'], true); // Decode JSON string to PHP array
    $adjMatrix = json_decode($_POST['adj_matrix'], true); // Decode JSON string to PHP array

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL statement to insert data into the 'users' table
    $stmt = $conn->prepare("INSERT INTO users (place_name, phone_number, email, password, hospital_email, fire_station_email, police_station_email, location, adj_matrix) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $placeName, $phoneNo, $email, $hashedPassword, $hospitalEmail, $fireStationEmail, $policeStationEmail, json_encode($location), json_encode($adjMatrix));

    if ($stmt->execute()) {
        //echo "Registration successful!";
        header("Location: signin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>

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
     #locationsInput{
            display:none;
        }
    </style>
</head>
<body>
    <div>
        <form action="#" method="POST">
        <label for="fromInput">From Point:</label>
        <input type="number" id="fromInput" placeholder="Enter from point">
        <label for="toInput">To Point:</label>
        <input type="number" id="toInput" placeholder="Enter to point">
         <input type="hidden" value="<?= isset($_GET['placeName']) ? $_GET['placeName'] : '' ?>" name="placeName" placeholder="Place Name">
        <input type="hidden" value="<?= isset($_GET['phoneNo']) ? $_GET['phoneNo'] : '' ?>" name="phoneNo" placeholder="Phone Number">
        <input type="hidden" value="<?= isset($_GET['email']) ? $_GET['email'] : '' ?>" name="email" placeholder="Email">
        <input type="hidden" value="<?= isset($_GET['password']) ? $_GET['password'] : '' ?>" name="password" placeholder="Password">
        <input type="hidden" name="hospitalEmail" value="<?= isset($_GET['hospitalEmail']) ? $_GET['hospitalEmail'] : '' ?>">
        <input type="hidden" name="fireStationEmail" value="<?= isset($_GET['fireStationEmail']) ? $_GET['fireStationEmail'] : '' ?>">
        <input type="hidden" name="policeStationEmail" value="<?= isset($_GET['policeStationEmail']) ? $_GET['policeStationEmail'] : '' ?>">
        <textarea id="locationsInput" placeholder="Enter locations array" rows="2" cols="50" name="location">
 <?php
// admin.php

if (isset($_GET['location'])) {
    // Decode the URL-encoded JSON data
    $jsonData = urldecode($_GET['location']);

    // Decode the JSON string into a PHP array
    $coordinates = json_decode($jsonData, true);

    // Transform the array to the desired format
    $formattedCoordinates = array_map(function($coord) {
        return array("lat" => $coord['latitude'], "lon" => $coord['longitude']);
    }, $coordinates);

    // Print the formatted data
    echo json_encode($formattedCoordinates, JSON_PRETTY_PRINT);
} else {
    echo "No JSON data received.";
}
?>
</textarea>
        <label for="adjacencyInput">Adjacency Information:</label>
        <input type="text" id="adjacencyInput" placeholder='Enter adjacency information JSON like: {"1": [2, 3], "2": [1, 3, 4], ...}' value='{"1": [2, 3], "2": [1, 3, 4], "3":[2,5],"4":[2,7],"5":[3,4,6],"6":[5,7],"7":[6,4]}' name="adj_matrix">
          </form>
        <button onclick="drawShortestPath()" style="background-color: #4CAF50; color: white; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; border: none;">Draw Shortest Path</button>

  <button style="background-color: #008CBA; color: white; padding: 10px 15px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; border: none;" id="submit">Submit</button>
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

        function haversine(lat1, lon1, lat2, lon2) {
            const toRadians = (angle) => (angle * Math.PI) / 180;
            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const radius = 6371;
            const distance = radius * c * 1000;

            return distance;
        }

        function findShortestPathIndices(locations, from, to, adjacencyInfo) {
            const n = locations.length;

            // Build adjacency matrix from the adjacency information
            const adjacencyMatrix = Array.from({ length: n }, () => Array(n).fill(Infinity));
            for (const point in adjacencyInfo) {
                const pointIndex = parseInt(point) - 1;
                for (const connectedPoint of adjacencyInfo[point]) {
                    const connectedPointIndex = parseInt(connectedPoint) - 1;
                    const distance = haversine(locations[pointIndex].lat, locations[pointIndex].lon, locations[connectedPointIndex].lat, locations[connectedPointIndex].lon);
                    adjacencyMatrix[pointIndex][connectedPointIndex] = adjacencyMatrix[connectedPointIndex][pointIndex] = distance;
                }
            }

            const dist = Array(n).fill(Infinity);
            const visited = Array(n).fill(false);
            const path = Array(n).fill(-1);

            dist[from] = 0;

            for (let count = 0; count < n - 1; count++) {
                let u = -1;
                for (let i = 0; i < n; i++) {
                    if (!visited[i] && (u === -1 || dist[i] < dist[u])) {
                        u = i;
                    }
                }

                visited[u] = true;

                for (let v = 0; v < n; v++) {
                    if (!visited[v] && adjacencyMatrix[u][v] !== Infinity && dist[u] + adjacencyMatrix[u][v] < dist[v]) {
                        dist[v] = dist[u] + adjacencyMatrix[u][v];
                        path[v] = u;
                    }
                }
            }

            let current = to;
            const shortestPathIndices = [current];

            while (current !== from) {
                current = path[current];
                shortestPathIndices.push(current);
            }

            return { indices: shortestPathIndices.reverse(), cost: dist[to] };
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

            for (var i = 0; i < locations.length; i++) {
                var location = new Microsoft.Maps.Location(locations[i].lat, locations[i].lon);
                var pushpin = new Microsoft.Maps.Pushpin(location, {
                    title: 'Point ' + (i + 1),
                    htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (i + 1) + '</div>',
                    color: 'blue'
                });
                map.entities.push(pushpin);
            }

            let distancesHTML = '';
            let shortestPathHTML = '';

            for (let i = 0; i < locations.length - 1; i++) {
                let short = 99999;
                let shortIndex = -1;

                for (let j = i + 1; j < locations.length; j++) {
                    const location1 = locations[i];
                    const location2 = locations[j];
                    const distance = haversine(location1.lat, location1.lon, location2.lat, location2.lon);

                    if (distance < short) {
                        short = distance;
                        shortIndex = j;
                    }

                    distancesHTML += `<p>${i + 1} <span class="material-symbols-outlined" style="font-size:12px;">sync_alt</span> ${j + 1}: ${distance.toFixed(2)} m</p>`;
                }

                shortestPathHTML += `(${i + 1},${shortIndex + 1}):${short.toFixed(2)}m`;
            }
            document.getElementById('distances').innerHTML = '<h4>Distances Between</h4>' + distancesHTML;
        }

        function drawShortestPath() {
            var locationsInput = document.getElementById('locationsInput').value;
            var locations = JSON.parse(locationsInput);
            var fromPoint = parseInt(document.getElementById('fromInput').value) - 1;
            var toPoint = parseInt(document.getElementById('toInput').value) - 1;
            var adjacencyInput = document.getElementById('adjacencyInput').value;
            var adjacencyInfo = JSON.parse(adjacencyInput);

            if (
                isNaN(fromPoint) ||
                isNaN(toPoint) ||
                fromPoint < 0 ||
                toPoint < 0 ||
                fromPoint >= locations.length ||
                toPoint >= locations.length ||
                !adjacencyInfo
            ) {
                alert('Invalid input for "From" or "To" points or adjacency information.');
                return;
            }

            var result = findShortestPathIndices(locations, fromPoint, toPoint, adjacencyInfo);
            var shortestPathIndices = result.indices;
            var shortestPathLocations = shortestPathIndices.map(index => new Microsoft.Maps.Location(locations[index].lat, locations[index].lon));

            var map = new Microsoft.Maps.Map(document.getElementById('myMap'), {
                mapTypeId: Microsoft.Maps.MapTypeId.aerial,
                center: shortestPathLocations[0],
                zoom: 1
            });

            var zoomLevel = 0;
            var maxZoom = 19;
            var zoomInterval = 200;

            function increaseZoom() {
                if (zoomLevel <= maxZoom) {
                    map.setView({ zoom: zoomLevel });
                    zoomLevel++;
                    setTimeout(increaseZoom, zoomInterval);
                }
            }

            increaseZoom();

            for (var i = 0; i < locations.length; i++) {
                var location = new Microsoft.Maps.Location(locations[i].lat, locations[i].lon);
                var pushpin = new Microsoft.Maps.Pushpin(location, {
                    title: 'Point ' + (i + 1),
                    htmlContent: '<div style="font-weight: bold; font-size: 12px; color: blue;">' + (i + 1) + '</div>',
                    color: 'blue'
                });
                map.entities.push(pushpin);
            }
            var shortestPathPolyline = new Microsoft.Maps.Polyline(shortestPathLocations, {
                strokeColor: 'green',
                strokeThickness: 3
            });

            map.entities.push(shortestPathPolyline);
            console.log("Total Cost:"+result.cost.toFixed(2)+"meters");
        }
    document.getElementById('submit').addEventListener('click', function () {
        // Manually submit the form
        document.querySelector('form').submit();
    });
        </script>
</body>
</html>
