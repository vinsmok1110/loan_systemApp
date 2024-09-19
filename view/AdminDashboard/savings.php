<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "go_loan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_savings = "SELECT u.name, t1.user_id, t1.current_amount
        FROM savingstransactions t1
        INNER JOIN (
            SELECT user_id, MAX(created_at) AS latest_transaction
            FROM savingstransactions
            GROUP BY user_id
        ) t2 ON t1.user_id = t2.user_id AND t1.created_at = t2.latest_transaction
        INNER JOIN user_tbl u ON t1.user_id = u.user_id
        ORDER BY t1.user_id";

$result_savings = $conn->query($sql_savings);

$sql_withdrawals = "SELECT wr.request_id, wr.user_id, u.name, wr.amount, wr.status, wr.created_at
        FROM withdrawalrequests wr
        INNER JOIN user_tbl u ON wr.user_id = u.user_id
        ORDER BY wr.created_at DESC";

$result_withdrawals = $conn->query($sql_withdrawals);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css">
    <title>Savings</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>
    <main class="mainContent">
        <div class="cardsColumn">
            <div class="card">
                <div class="cardHeader">
                    <h2>Savings</h2>
                </div>
                <div class="card-body">
                    
                    <table class="table table-bordered" id="withdrawal-list">
                        <colgroup>
                            <col width="10%">
                            <col width="10%">
                            <col width="30%">
                            <col width="20%">
                            <col width="10%">
                            <col width="20%">
                        </colgroup> 
                        <thead>
                            <tr>
                                <th class="text-center">Request ID</th>
                                <th class="text-center">User ID</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result_withdrawals->num_rows > 0) {
                            while($row = $result_withdrawals->fetch_assoc()) {
                                echo "<tr>
                                        <td class='text-center'>" . $row["request_id"]. "</td>
                                        <td class='text-center'>" . $row["user_id"]. "</td>
                                        <td class='text-center'>" . $row["name"]. "</td>
                                        <td class='text-center'>" . $row["amount"]. "</td>
                                        <td class='text-center'>" . $row["status"]. "</td>
                                        <td class='text-center'>
                                            <form method='POST' action='../../controller/approval_withdraw.php'>
                                                <input type='hidden' name='request_id' value='" . $row["request_id"] . "'>
                                                <button type='submit' name='approve' class='btn btn-success'>Approve</button>
                                                <button type='submit' name='reject' class='btn btn-danger'>Reject</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No requests found</td></tr>";
                        }
                        $conn->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
