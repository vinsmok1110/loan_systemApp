<?php
require_once '../model/DatabaseManager.php'; 

class TransactionManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getLatestSavingsTransactions() {
        $conn = $this->db->conn;

        $sql = "SELECT u.name, t1.user_id, t1.current_amount
                FROM savingstransactions t1
                INNER JOIN (
                    SELECT user_id, MAX(created_at) AS latest_transaction
                    FROM savingstransactions
                    GROUP BY user_id
                ) t2 ON t1.user_id = t2.user_id AND t1.created_at = t2.latest_transaction
                INNER JOIN user_tbl u ON t1.user_id = u.user_id
                ORDER BY t1.user_id";

        return $conn->query($sql);
    }

    public function getWithdrawalRequests() {
        $conn = $this->db->conn;

        $sql = "SELECT wr.request_id, wr.user_id, u.name, wr.amount, wr.status, wr.created_at
                FROM withdrawalrequests wr
                INNER JOIN user_tbl u ON wr.user_id = u.user_id
                ORDER BY wr.created_at DESC";

        return $conn->query($sql);
    }
}

// Usage
$database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
$transactionManager = new TransactionManager($database);
$result_savings = $transactionManager->getLatestSavingsTransactions();
$result_withdrawals = $transactionManager->getWithdrawalRequests();
$database->closeConnection();
?>

