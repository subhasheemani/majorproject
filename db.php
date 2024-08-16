<?php
$conn = new mysqli("localhost","root","root", "hi");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
