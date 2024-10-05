<?php
$servername = "localhost";
$username = "root";  // Default username for WAMP
$password = "";      // Default password for WAMP
$dbname = "school_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
