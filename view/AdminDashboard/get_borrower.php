<?php
require_once __DIR__ . '/../model/db_connect.php'; // Adjust the path as needed

function get_borrower_user($user_id) {
    $conn = connectDB();

    $sql = "SELECT * FROM loans WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Failed to prepare the SQL statement: " . $conn->error);
    }

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    $stmt->close();
    $conn->close();

    return $transactions;
}
?>
