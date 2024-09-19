<?php
function insertLoan($user_id, $loan_amount, $transaction_id, $status, $note, $date, $payable_months) {
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

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO loans (user_id, loan_amount, transaction_id, status, note, date, payable_months) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idisssi", $user_id, $loan_amount, $transaction_id, $status, $note, $date, $payable_months);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New loan record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>
