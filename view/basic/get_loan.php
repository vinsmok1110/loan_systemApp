<?php
function getLoanData($user_id) {
    // Database connection details
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

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM loans WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Close connection
    $stmt->close();
    $conn->close();

    return $result;
}
?>


