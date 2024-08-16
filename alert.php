<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the required files for PHPMailer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Function to send email using PHPMailer
function sendEmail($email, $subject, $body) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abhiram1532003@gmail.com'; // Your Gmail username
        $mail->Password   = 'vhljwouykobhwxst';        // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('abhiram1532003@gmail.com', 'Alert');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log any errors
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the latitude and longitude from the POST request
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];

    // Perform database connection (make sure to include your database connection logic)
    require "db.php";

    // Insert the data into the 'alert' table
    $sql = "INSERT INTO alert (lat, lon) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dd", $lat, $lon);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo 'Data inserted successfully into the alert table.';

        // Send email alerts to hospital, fire, and police departments
        $hospitalEmail = $_POST['hospitalEmail'];
        $fireStationEmail = $_POST['fireStationEmail'];
        $policeStationEmail = $_POST['policeStationEmail'];    

        $subject = 'Emergency Alert';
        $googleMapsLink = 'https://www.google.com/maps?q=' . $lat . ',' . $lon;
        $body = "
            <html>
            <head>
                <title>Emergency Alert</title>
                <style>
                    body {
                        font-family: 'Roboto', sans-serif;
                        background-color: #f5f5f5;
                        margin: 0;
                        padding: 20px;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #fff;
                        border-radius: 5px;
                        padding: 20px;
                    }
                    h2 {
                        color: #333;
                    }
                    p {
                        color: #666;
                    }
                    .map-img {
                        width: 100%;
                        max-width: 400px;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Emergency Alert</h2>
                    <p>Emergency detected at coordinates: Latitude {$lat}, Longitude {$lon}.</p>
                    <p>Click <a href='{$googleMapsLink}'>here</a> to view the location on Google Maps.</p>
                    <a href='{$googleMapsLink}'><img src='https://vfstr.in/icons/l.png' alt='Emergency Location' class='map-img'><a>
                </div>
            </body>
            </html>
        ";

        // Send emails to respective departments
        sendEmail($hospitalEmail, $subject, $body);
        sendEmail($fireEmail, $subject, $body);
        sendEmail($policeEmail, $subject, $body);
    } else {
        echo 'Error inserting data into the alert table: ' . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    // Handle other HTTP methods or provide an error message
    echo 'Invalid request method';
}
?>
