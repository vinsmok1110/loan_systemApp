<?php
require_once '../model/DatabaseManager.php'; 

class WithdrawalRequestHandler {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function updateWithdrawalRequest($request_id, $status) {
        $conn = $this->db->conn;
        $sql = "UPDATE withdrawalrequests SET status=? WHERE request_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $status, $request_id);

        if ($stmt->execute()) {
            echo "Withdrawal request updated successfully.";
            header("Location: ../../view/AdminDashboard/savings.php");
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $stmt->close();
    }
}

// Usage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
    $withdrawalHandler = new WithdrawalRequestHandler($database);

    $request_id = $_POST["request_id"];
    $status = '';

    if (isset($_POST['approve'])) {
        $status = 'Completed';
    } elseif (isset($_POST['reject'])) {
        $status = 'Rejected';
    }

    $withdrawalHandler->updateWithdrawalRequest($request_id, $status);

    $database->closeConnection();
    exit; // Exit after processing
}

// If not a POST request, redirect back to the admin dashboard
header("Location: ../../view/AdminDashboard/savings.php");
exit;
?>
