<?php
// Start the session
session_start();

require_once '../model/DatabaseManager.php'; 

class TransactionManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function deposit($user_id, $amount) {
        $conn = $this->db->conn;

        // Validate the amount
        if ($amount < 100 || $amount > 1000) {
            return "Amount should be between 100 and 1000.";
        }

        // Fetch the user's current amount
        $sql_select = "SELECT current_amount FROM savingstransactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param('i', $user_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        $current_amount = 0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_amount = $row['current_amount'];
        }

        // Calculate the new amount
        $new_amount = $current_amount + $amount;

        // Insert the deposit transaction
        $transaction_id = uniqid('txn_'); // Unique transaction ID
        $transaction_type = 'deposit';
        $status = 'completed';
        $created_at = date('Y-m-d H:i:s');

        $sql_insert = "INSERT INTO savingstransactions (user_id, transaction_id, transaction_type, amount, current_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('issdiss', $user_id, $transaction_id, $transaction_type, $amount, $new_amount, $status, $created_at);

        if ($stmt_insert->execute()) {
            return "Deposit successful!";
        } else {
            return "Failed to deposit amount: " . $stmt_insert->error;
        }
    }
}

// Usage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deposit'])) {
        $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
        $amount = $_POST['amount'];

        $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
        $transactionManager = new TransactionManager($database);
        $result = $transactionManager->deposit($user_id, $amount);
        $database->closeConnection();

        echo $result;
    }
}
?>
