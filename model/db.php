<?php
// Function to establish a PDO database connection
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "go_loan";

    try {
        // Create PDO connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        // Handle connection errors
        die("Connection failed: " . $e->getMessage());
    }
}
?>
