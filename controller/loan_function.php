<?php
class Loan {
    private $db;
    private $user_id;
    private $loan_amount;
    private $note;
    private $payable_months;

    public function __construct($db, $user_id, $loan_amount, $note, $payable_months) {
        $this->db = $db;
        $this->user_id = $user_id;
        $this->loan_amount = $loan_amount;
        $this->note = $note;
        $this->payable_months = $payable_months;
    }

    private function generateTransactionId() {
        return mt_rand(100000, 999999);
    }

    private function checkAndIncreaseLoanLimit() {
        // Check if all previous loan payments are completed and on time
        $sql_check_payments = "
            SELECT b.payment_status, b.due_date, b.payment_date
            FROM billing b
            JOIN loans l ON b.loan_id = l.loan_id
            WHERE l.user_id = ? AND b.payment_status = 'completed'
        ";
        $stmt_check_payments = $this->db->conn->prepare($sql_check_payments);
        $stmt_check_payments->bind_param("i", $this->user_id);
        $stmt_check_payments->execute();
        $result_check_payments = $stmt_check_payments->get_result();
    
        $allOnTime = true;
        while ($row = $result_check_payments->fetch_assoc()) {
            if ($row['payment_date'] > $row['due_date']) {
                $allOnTime = false;
                break;
            }
        }
    
        if ($allOnTime && $result_check_payments->num_rows > 0) {
            // Get current max loan amount
            $sql_max_loan = "SELECT max_loan_amount FROM user_tbl WHERE user_id = ?";
            $stmt_max_loan = $this->db->conn->prepare($sql_max_loan);
            $stmt_max_loan->bind_param("i", $this->user_id);
            $stmt_max_loan->execute();
            $result_max_loan = $stmt_max_loan->get_result();
            $row_max_loan = $result_max_loan->fetch_assoc();
            $current_max_loan_amount = $row_max_loan['max_loan_amount'];
            $stmt_max_loan->close();
    
            // Double the max loan amount and add 5000
            $new_max_loan_amount = min(($current_max_loan_amount * 2) + 5000, 50000);
    
            // Update user's max loan limit
            $sql_update_limit = "UPDATE user_tbl SET max_loan_amount = ? WHERE user_id = ?";
            $stmt_update_limit = $this->db->conn->prepare($sql_update_limit);
            $stmt_update_limit->bind_param("di", $new_max_loan_amount, $this->user_id);
            $stmt_update_limit->execute();
            $stmt_update_limit->close();
        }
    
        $stmt_check_payments->close();
    }
    

    public function processLoan() {
        if ($this->loan_amount < 5000) {
            echo "Minimum loan amount is $5000.";
            return;
        }

        $this->checkAndIncreaseLoanLimit();

        // Get the current maximum loan limit
        $sql_max_loan = "SELECT max_loan_amount FROM user_tbl WHERE user_id = ?";
        $stmt_max_loan = $this->db->conn->prepare($sql_max_loan);
        $stmt_max_loan->bind_param("i", $this->user_id);
        $stmt_max_loan->execute();
        $result_max_loan = $stmt_max_loan->get_result();
        $row_max_loan = $result_max_loan->fetch_assoc();
        $max_loan_amount = $row_max_loan['max_loan_amount'];
        $stmt_max_loan->close();

        // Check if the user has already reached the maximum loan amount
        $sql_total_loan = "SELECT SUM(loan_amount) AS total_loan FROM loans WHERE user_id = ?";
        $stmt_total_loan = $this->db->conn->prepare($sql_total_loan);
        $stmt_total_loan->bind_param("i", $this->user_id);
        $stmt_total_loan->execute();
        $result_total_loan = $stmt_total_loan->get_result();
        $row_total_loan = $result_total_loan->fetch_assoc();
        $total_loan = $row_total_loan['total_loan'];
        $stmt_total_loan->close();

        // Calculate the remaining amount the user can loan
        $remaining_amount = $max_loan_amount - $total_loan;

        if ($remaining_amount <= 0) {
            echo "You have reached the maximum loan amount of $max_loan_amount.";
        } elseif ($this->loan_amount > $remaining_amount) {
            echo "You can only loan $remaining_amount more.";
        } else {
            // Generate transaction ID
            $transaction_id = $this->generateTransactionId();

            // Prepare SQL statement for inserting the loan
            $sql = "INSERT INTO loans (user_id, loan_amount, status, date, transaction_id, note, payable_months) VALUES (?, ?, 'pending', CURDATE(), ?, ?, ?)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bind_param("iissi", $this->user_id, $this->loan_amount, $transaction_id, $this->note, $this->payable_months);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Waiting for admin approval.";
            } else {
                echo "Error: " . $sql . "<br>" . $this->db->conn->error;
            }

            // Close statement
            $stmt->close();
        }
    }
}
?>
