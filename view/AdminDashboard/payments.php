<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css">
    <title>Loans</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<main class="mainContent">
    <div class="cardsColumn">
        <div class="card">
            <div class="cardHeader">
                <h2>Payment Request</h2>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="20%">
                        <col width="15%">
                        <col width="10%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Method</th>
                            <th class="text-center">Option</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Database connection
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "go_loan";

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Retrieve loan list with cardHolder and note where status is "pending"
                    $sql = "SELECT payment.payment_id, payment.payment_amount, payment.payment_date, payment.payment_method, payment.payment_status
                            FROM payment
                            JOIN billing ON payment.payment_id  = payment.payment_id 
                            WHERE payment.payment_status = 'pending'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='text-center'>" . $row["payment_id"] . "</td>";
                            echo "<td class='text-center'>" . $row["payment_amount"] . "</td>";
                            echo "<td class='text-center'>" . $row["payment_date"] . "</td>";
                            echo "<td class='text-center'>" . $row["payment_method"] . "</td>";
                            echo "<td class='text-center'>
                                    <button style='background-color: blue; color: white; margin-right: 10px;' onclick=\"location.href='../../controller/approve_loan.php?id=" . $row["payment_id"] . "'\">Approve</button>
                                    <button style='background-color: red; color: white;' onclick=\"location.href='../../controller/delete_loan.php?id=" . $row["payment_id"] . "'\">Reject</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No requests found</td></tr>";
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
