<?php
session_start();
require_once 'DatabaseManager.php'; // Include the DatabaseManager file

class WithdrawalManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createWithdrawalRequest($user_id, $amount) {
        $conn = $this->db->conn;

        // Validate the amount
        if ($amount < 500 || $amount > 5000) {
            return "Amount should be between 500 and 5000.";
        }

        // Check if the user has enough balance
        $sql = "SELECT current_amount FROM savingstransactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $current_amount = 0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_amount = $row['current_amount'];
        }

        if ($amount > $current_amount) {
            return "Insufficient balance.";
        }

        // Insert the withdrawal request
        $sql_insert = "INSERT INTO WithdrawalRequests (user_id, amount, status) VALUES (?, ?, 'Pending')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('id', $user_id, $amount);

        if ($stmt_insert->execute()) {
            return "Withdrawal request created successfully!";
        } else {
            return "Failed to create withdrawal request: " . $stmt_insert->error;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['withdraw_amount'];

    $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
    $withdrawalManager = new WithdrawalManager($database);
    $message = $withdrawalManager->createWithdrawalRequest($user_id, $amount);
    echo "<p>$message</p>";
    $database->closeConnection();
}
?>
