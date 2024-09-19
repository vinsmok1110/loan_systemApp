<?php
require_once '../../model/DatabaseManager.php'; 

class BorrowersManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getBorrowersList() {
        $conn = $this->db->conn;

        // Retrieve loan list from database with user information
        $sql = "SELECT loans.*, user_tbl.name 
                FROM loans 
                JOIN user_tbl ON loans.user_id = user_tbl.user_id";
        return $conn->query($sql);
    }
}

// Include header and sidebar
include 'header.php';
include 'sidebar.php';

// Usage
$database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
$borrowersManager = new BorrowersManager($database);
$result = $borrowersManager->getBorrowersList();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css">
    <title>Borrowers</title>
</head>
<body>
<main class="mainContent">
    <div class="cardsColumn">
        <div class="card">
            <div class="cardHeader">
                <h2>Borrowers</h2>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <colgroup>
                        <col width="10%">
                        <col width="25%">
                        <col width="25%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">User_id</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Transaction ID</th>
                            <th class="text-center">Loan Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["user_id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["transaction_id"] . "</td>";
                            echo "<td>" . $row["loan_amount"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No requests found</td></tr>";
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>
