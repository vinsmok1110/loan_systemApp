<?php
require_once '../model/DatabaseManager.php'; 

class LoanApprovalManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function approveLoan($loan_id) {
        $conn = $this->db->conn;

        // Update the status of the loan to "approved"
        $sql_update = "UPDATE loans SET status = 'approved' WHERE loan_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $loan_id);

        if ($stmt_update->execute()) {
            echo "Loan status updated to approved.";
            header("Location: ../view/AdminDashboard/loans.php");
        } else {
            echo "Error updating loan status: " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}

// Usage
if (isset($_GET['id'])) {
    $loan_id = $_GET['id'];
    $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
    $loanApprovalManager = new LoanApprovalManager($database);
    $loanApprovalManager->approveLoan($loan_id);
    $database->closeConnection();
} else {
    echo "No ID provided for approval.";
}
?>
