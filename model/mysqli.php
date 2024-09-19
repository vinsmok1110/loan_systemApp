<?php
// Function to establish a MySQLi database connection
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "go_loan";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>