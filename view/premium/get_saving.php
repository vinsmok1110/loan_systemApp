<?php

// Define the connectDB function to establish a database connection
function connectDB() {
    // Replace these with your actual database credentials
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

// Function to fetch savings transactions for a given user
function get_saving_file($user_id) {
    // Establish a database connection
    $conn = connectDB();

    // Prepare SQL statement
    $sql = "SELECT * FROM savingstransactions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if preparing the SQL statement failed
    if ($stmt === false) {
        throw new Exception("Failed to prepare the SQL statement.");
    }

    // Bind parameters and execute statement
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch transactions
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    return $transactions;
}

// Usage example: Get saving transactions for user with ID 2
try {
    $user_id = 2;
    $transactions = get_saving_file($user_id);
    print_r($transactions); // Print the transactions array
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
