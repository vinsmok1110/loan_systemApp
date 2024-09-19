<?php
class LoanManager {
    private $conn;

    // Constructor to establish the database connection
    public function __construct($servername, $username, $password, $dbname) {
        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Function to reject a loan
    public function rejectLoan($loan_id) {
        $sql = "UPDATE loan_tbl SET status='rejected' WHERE loan_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $loan_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }

        $stmt->close();
    }

    // Destructor to close the database connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "go_loan";

// Create an instance of LoanManager
$loanManager = new LoanManager($servername, $username, $password, $dbname);

// Check if loan ID is provided
if (isset($_GET['id'])) {
    $loan_id = $_GET['id'];

    // Reject the loan
    if ($loanManager->rejectLoan($loan_id)) {
        echo "Loan ID $loan_id has been rejected.";
    } else {
        echo "Error updating record.";
    }
}

// Redirect back to the loans page
header("Location:../../view/AdminDashboard/loans.php");
exit;
?>
