<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css">
    <title>Users</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 800px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .modal img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<main class="mainContent">
    <div class="cardsColumn">
        <div class="card2">
            <div class="cardHeader">
                <h2>Registration Request</h2>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="loan-list">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Plan</th>
                            <th>Name</th>
                            <th>Password</th>
                            <th>Address</th>
                            <th>Gender</th>
                            <th>Birthdate</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>Card Holder</th>
                            <th>TIN</th>
                            <th>Company Name</th>
                            <th>Company Address</th>
                            <th>Company Phone</th>
                            <th>Position</th>
                            <th>Monthly Earnings</th>
                            <th>Status</th>
                            <th>Proof of Billing</th>
                            <th>Valid ID Primary</th>
                            <th>COE</th>
                            <th>Actions</th>
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

                        // Retrieve user list from database
                        $sql = "SELECT * FROM user_tbl";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $proofOfBilling = base64_encode($row["proof_of_billing"]);
                                $validIdPrimary = base64_encode($row["valid_id_primary"]);
                                $coe = base64_encode($row["coe"]);
                                
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["plan"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["password"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["address"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["gender"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["birthdate"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["contact"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["bankname"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["bankAccount"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["cardHolder"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["tin"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["companyName"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["companyAddress"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["companyPhoneNumber"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["position"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["monthly_earnings"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                                echo "<td><button onclick=\"viewDocument('proofOfBilling-$row[user_id]')\">View</button></td>";
                                echo "<td><button onclick=\"viewDocument('validIdPrimary-$row[user_id]')\">View</button></td>";
                                echo "<td><button onclick=\"viewDocument('coe-$row[user_id]')\">View</button></td>";
                                echo "<td><button style='background-color: blue; color: white;' onclick=\"location.href='../../controller/approve.php?user_id=" . $row["user_id"] . "'\">Approve</button></td>";
                                echo "<td><button style='background-color: red; color: white;' onclick=\"location.href='../../controller/delete.php?id=" . $row["user_id"] . "'\">Reject</button></td>";
                                echo "</tr>";

                                // Hidden modals for viewing documents
                                echo "<div id='proofOfBilling-$row[user_id]' class='modal'><div class='modal-content'><span class='close' onclick=\"closeModal('proofOfBilling-$row[user_id]')\">&times;</span><img src='data:image/jpeg;base64,$proofOfBilling'></div></div>";
                                echo "<div id='validIdPrimary-$row[user_id]' class='modal'><div class='modal-content'><span class='close' onclick=\"closeModal('validIdPrimary-$row[user_id]')\">&times;</span><img src='data:image/jpeg;base64,$validIdPrimary'></div></div>";
                                echo "<div id='coe-$row[user_id]' class='modal'><div class='modal-content'><span class='close' onclick=\"closeModal('coe-$row[user_id]')\">&times;</span><img src='data:image/jpeg;base64,$coe'></div></div>";
                            }
                        } else {
                            echo "<tr><td colspan='23'>No requests found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function viewDocument(id) {
        var modal = document.getElementById(id);
        modal.style.display = "block";
    }

    function closeModal(id) {
        var modal = document.getElementById(id);
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
            var modal = modals[i];
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }
</script>
</body>
</html>
