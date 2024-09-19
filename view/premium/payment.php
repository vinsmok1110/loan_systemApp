<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paymentAmount = $_POST['paymentAmount'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO payments (amount) VALUES (?)");
    $stmt->bind_param("d", $paymentAmount);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Payment recorded successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
