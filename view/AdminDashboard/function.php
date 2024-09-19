<?php

class LoanManager {
    private $conn;

    // Constructor to establish the database connection
    public function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "go_loan";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Function to count total number of loan IDs
    public function countTotalLoanIDs() {
        $sql = "SELECT COUNT(loan_id) AS total_ids FROM loans";
        $result = $this->conn->query($sql);
        $totalIDs = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalIDs = $row["total_ids"];
        }

        return $totalIDs;
    }

    // Function to get saving transactions for a specific user
    public function getSavingTransactions($user_id) {
        $sql = "SELECT * FROM savingstransactions WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Failed to prepare the SQL statement.");
        }

        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        $stmt->close();

        return $transactions;
    }

    // Function to count unique user IDs
    public function countUniqueUserIDs() {
        $sql = "SELECT COUNT(DISTINCT user_id) AS total_unique_users FROM loans";
        $result = $this->conn->query($sql);
        $totalUniqueUsers = 0;

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalUniqueUsers = $row["total_unique_users"];
        }

        return $totalUniqueUsers;
    }

    // Destructor to close the database connection
    public function __destruct() {
        $this->conn->close();
    }
}

// Example usage
$loanManager = new LoanManager();
$user_id = 123; // Replace 123 with the actual user ID
$totalLoanIDs = $loanManager->countTotalLoanIDs();
$savingTransactions = $loanManager->getSavingTransactions($user_id);
$totalUniqueUserIDs = $loanManager->countUniqueUserIDs();

?>
