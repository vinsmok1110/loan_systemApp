<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Billing Summary</title>
    <link rel="stylesheet" href="../css/design.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <?php include 'basic_sidebar.php'; ?>
        </div>
        <div class="content">
            <?php include 'header.php'; ?>

            <?php
            session_start();

            if (!isset($_SESSION['user_id'])) {
                die("You must be logged in to view this page.");
            }

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "go_loan";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . htmlspecialchars($conn->connect_error));
            }

            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM loans WHERE status = 'approved' AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>Billing Summary</h2>";

                while ($row = $result->fetch_assoc()) {
                    $loan_id = htmlspecialchars($row['loan_id']);
                    $loan_amount = htmlspecialchars($row['loan_amount']);
                    $date = htmlspecialchars($row['date']);
                    $payable_months = htmlspecialchars($row['payable_months']);
                    $interest_rate = 0.03;
                    $penalty_rate = 0.02;

                    $total_interest = $loan_amount * $interest_rate;
                    $total_amount = $loan_amount - $total_interest;
                    $monthly_payment = $loan_amount / $payable_months;
                    $total_penalty = $loan_amount * $penalty_rate;

                    $total_amount_due = 0;
                    $total_paid = 0;

                    echo "<table border='1'>
                            <tr>
                                <td>Loan Amount</td>
                                <td>" . number_format($loan_amount, 2) . "</td>
                            </tr>
                            <tr>
                                <td>Interest Rate (3%)</td>
                                <td>" . number_format($total_interest, 2) . "</td>
                            </tr>
                            <tr>
                                <td>Total Amount on hand</td>
                                <td>" . number_format($total_amount, 2) . "</td>
                            </tr>
                            <tr>
                                <td>Penalty Rate (2%)</td>
                                <td>" . number_format($total_penalty, 2) . "</td>
                            </tr>
                        </table>";

                    echo "<h3>Monthly Payments [$payable_months Months]</h3>";
                    echo "<table border='1'>
                            <tr>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Total Paid</th>
                                <th>Action</th>
                            </tr>";

                    for ($i = 1; $i <= $payable_months; $i++) {
                        $due_date = date('Y-m-d', strtotime("+$i months", strtotime($date)));

                        $billing_sql = "SELECT * FROM billing WHERE loan_id = ? AND due_date = ?";
                        $billing_stmt = $conn->prepare($billing_sql);
                        $billing_stmt->bind_param("is", $loan_id, $due_date);
                        $billing_stmt->execute();
                        $billing_result = $billing_stmt->get_result();

                        if ($billing_result->num_rows == 0) {
                            $billing_sql = "INSERT INTO billing (loan_id, billing_amount, due_date, payment_status, date_generated, interest)
                                            VALUES (?, ?, ?, 'Pending', NOW(), ?)";
                            $billing_stmt = $conn->prepare($billing_sql);
                            $billing_stmt->bind_param("iisd", $loan_id, $monthly_payment, $due_date, $interest_rate);
                            $billing_stmt->execute();
                        } else {
                            $billing_row = $billing_result->fetch_assoc();
                            $billing_id = htmlspecialchars($billing_row['billing_id']);
                            $payment_status = htmlspecialchars($billing_row['payment_status']);
                            $billing_amount = htmlspecialchars($billing_row['billing_amount']);

                            $payment_sql = "SELECT SUM(payment_amount) as total_paid FROM payment WHERE billing_id = ?";
                            $payment_stmt = $conn->prepare($payment_sql);
                            $payment_stmt->bind_param("i", $billing_id);
                            $payment_stmt->execute();
                            $payment_result = $payment_stmt->get_result();
                            $payment_row = $payment_result->fetch_assoc();
                            $billing_total_paid = $payment_row['total_paid'] ? $payment_row['total_paid'] : 0;

                            echo "<tr>
                                    <td>$due_date</td>
                                    <td>" . number_format($billing_amount, 2) . "</td>
                                    <td>$payment_status</td>
                                    <td>" . number_format($billing_total_paid, 2) . "</td>
                                    <td>";

                            if ($payment_status == 'Pending' || $payment_status == 'Overdue' || $payment_status == 'Partial Payment') {
                                echo "<button class='openPaymentForm' data-loan-id='$loan_id' data-due-date='$due_date' data-billing-id='$billing_id' data-billing-amount='$billing_amount'>Pay Now</button>";
                            }

                            echo "</td>
                                </tr>";

                            $total_amount_due += $billing_amount;
                            $total_paid += $billing_total_paid;
                        }
                    }

                    $balance = $loan_amount - $total_paid;

                    echo "</table>";
                    echo "<h3>Total Amount Paid: <span id='totalPaid_" . $loan_id . "'>" . number_format($total_paid, 2) . "</span></h3>";
                    echo "<h3>Balance: <span id='balance_" . $loan_id . "'>" . number_format($balance, 2) . "</span></h3>";
                }

            } else {
                echo "No Loan to Pay.";
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
                $loan_id = filter_input(INPUT_POST, 'loan_id', FILTER_SANITIZE_NUMBER_INT);
                $due_date = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_STRING);
                $billing_id = filter_input(INPUT_POST, 'billing_id', FILTER_SANITIZE_NUMBER_INT);
                $billing_amount = filter_input(INPUT_POST, 'billing_amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $payment_amount = filter_input(INPUT_POST, 'paymentAmount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                if ($payment_amount <= 0 || $payment_amount > $billing_amount) {
                    die("Invalid payment amount.");
                }

                $payment_date = date('Y-m-d');

                $billing_sql = "SELECT * FROM billing WHERE loan_id = ? AND due_date = ?";
                $billing_stmt = $conn->prepare($billing_sql);
                $billing_stmt->bind_param("is", $loan_id, $due_date);
                $billing_stmt->execute();
                $billing_result = $billing_stmt->get_result();

                if ($billing_result->num_rows > 0) {
                    $billing_row = $billing_result->fetch_assoc();
                    $current_billing_amount = $billing_row['billing_amount'];

                    $new_billing_amount = $current_billing_amount - $payment_amount;

                    $payment_status = $new_billing_amount > 0 ? 'Partial Payment' : 'Completed';
                    $new_billing_amount = max($new_billing_amount, 0);

                    $update_sql = "UPDATE billing SET billing_amount = ?, payment_status = ? WHERE loan_id = ? AND due_date = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("dsis", $new_billing_amount, $payment_status, $loan_id, $due_date);

                    if ($update_stmt->execute() === TRUE) {
                        $insert_payment_sql = "INSERT INTO payment (billing_id, payment_amount, payment_date, payment_method, payment_status)
                                               VALUES (?, ?, ?, 'Online', ?)";
                        $insert_payment_stmt = $conn->prepare($insert_payment_sql);
                        $insert_payment_stmt->bind_param("idss", $billing_id, $payment_amount, $payment_date, $payment_status);

                        if ($insert_payment_stmt->execute() === TRUE) {
                            echo "Payment successful.";
                            $total_paid += $payment_amount;
                            $balance = $loan_amount - $total_paid;

                            echo "<script>
                                    document.getElementById('totalPaid_" . $loan_id . "').innerText = '" . number_format($total_paid, 2) . "';
                                    document.getElementById('balance_" . $loan_id . "').innerText = '" . number_format($balance, 2) . "';
                                  </script>";
                        } else {
                            echo "Error: " . htmlspecialchars($insert_payment_stmt->error);
                        }
                    } else {
                        echo "Error: " . htmlspecialchars($update_stmt->error);
                    }
                }
            }

            $conn->close();
            ?>

            <div id="paymentPopup" class="popup">
                <form id="paymentForm" method="post" action="">
                    <label for="paymentAmount">Enter Payment Amount:</label><br>
                    <input type="number" id="paymentAmount" name="paymentAmount" step="0.01" min="0.01" required><br><br>
                    <input type="hidden" id="loan_id" name="loan_id">
                    <input type="hidden" id="due_date" name="due_date">
                    <input type="hidden" id="billing_id" name="billing_id">
                    <input type="hidden" id="billing_amount" name="billing_amount">
                    <input type="submit" name="pay" value="Confirm Payment">
                    <button type="button" id="closePaymentForm">Close</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (event) {
            if (event.target.matches('.openPaymentForm')) {
                const loan_id = event.target.getAttribute('data-loan-id');
                const due_date = event.target.getAttribute('data-due-date');
                const billing_id = event.target.getAttribute('data-billing-id');
                const billing_amount = event.target.getAttribute('data-billing-amount');
                openPaymentForm(loan_id, due_date, billing_id, billing_amount);
            }

            if (event.target.matches('#closePaymentForm')) {
                closePaymentForm();
            }
        });

        function openPaymentForm(loan_id, due_date, billing_id, billing_amount) {
            const paymentPopup = document.getElementById('paymentPopup');
            paymentPopup.style.display = 'block';
            document.getElementById('loan_id').value = loan_id;
            document.getElementById('due_date').value = due_date;
            document.getElementById('billing_id').value = billing_id;
            document.getElementById('billing_amount').value = billing_amount;
            document.getElementById('paymentAmount').value = billing_amount;
        }

        function closePaymentForm() {
            document.getElementById('paymentPopup').style.display = 'none';
        }
    </script>
</body>
</html>
