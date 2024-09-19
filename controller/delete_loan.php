<?php
require_once '../model/DatabaseManager.php'; 

class LoanManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function moveToTrash($loan_id) {
        $conn = $this->db->conn;
        
        // Retrieve lsadasdoan details
        $sql_select = "SELECT * FROM loans WHERE loan_id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->bind_param("i", $loan_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();

        if ($result->num_rows > 0) {
            $loan = $result->fetch_assoc();

            // Move to trash
            $sql_insert = "INSERT INTO trash (loan_id, loan_amount, date) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ids", $loan['loan_id'], $loan['loan_amount'], $loan['date']);
            $stmt_insert->execute();
            $stmt_insert->close();

            // Delete from loans table
            $sql_delete = "DELETE FROM loans WHERE loan_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $loan_id);
            $stmt_delete->execute();
            $stmt_delete->close();

            echo "Loan moved to trash and will be deleted after 30 days.";
        } else {
            echo "Loan not found.";
        }

        $stmt_select->close();
    }
}

// Usage
if (isset($_GET['id'])) {
    $loan_id = intval($_GET['id']);
    $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
    $loanManager = new LoanManager($database);
    $loanManager->moveToTrash($loan_id);
    $database->closeConnection();
}
?>
