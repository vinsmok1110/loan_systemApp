<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan</title>
    <link rel="stylesheet" href="../css/design.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <!-- Header content here -->
            <?php include 'header.php'; ?>

            <h2>Loan & Transaction:</h2>

            <!-- Display loan transactions here -->
            <?php

            
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            include 'get_loan.php';

            $totalLoanAmount = 0;

            if (isset($_SESSION['user_id'])) {
                $result = getLoanData($_SESSION['user_id']);

                if ($result->num_rows > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>No.</th><th>Transaction ID</th><th>Loan Amount</th><th>Date</th><th>Status</th><th>Note</th><th>Payment Status</th></tr>";
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        $date = new DateTime($row['date']);
                        $formattedDate = $date->format('m/d/y');
                        
                        echo "<tr><td>" . $count . "</td><td>" . htmlspecialchars($row['transaction_id']) . "</td><td>$" . number_format($row['loan_amount'], 2) . "</td><td>" . htmlspecialchars($formattedDate) . "</td><td>" . htmlspecialchars($row['status']) . "</td><td>" . htmlspecialchars($row['note']) . "</td><td>" . htmlspecialchars($row['payment_status']) . "</td></tr>";
                        $totalLoanAmount += $row['loan_amount'];
                        $count++;
                    }
                    echo "</table>";
                    echo "<p>Total Loan Amount: $" . number_format($totalLoanAmount, 2) . "</p>";
                } else {
                    echo "<p>No loan transactions found.</p>";
                }
            } else {
                echo "<p>Please log in to view loan transactions.</p>";
            }

            
            ?>

            <br>
            <button id="openLoanForm">Apply for a Loan</button>
        </div>
    </div>

    <div class="loan-form-container" id="loanFormContainer">
        <div class="loan-form">
            <h2>Apply for a Loan</h2>
            <form action="../../controller/loan_process.php" method="POST">
                <label for="amount">Loan Amount:</label>
                <input type="number" id="amount" name="loan_amount" required><br><br>
                
                <label for="note">Note:</label>
                <textarea id="note" name="note" rows="4" required></textarea><br><br>
                
                <label for="payable_months">Payable Months:</label>
                <input type="number" id="payable_months" name="payable_months" min="3" max="32" required><br><br>
                
                <input type="submit" value="Apply">
                <button type="button" id="closeLoanForm">Close</button>
            </form>
        </div>
    </div>

    <script>
        const openLoanFormBtn = document.getElementById('openLoanForm');
        const closeLoanFormBtn = document.getElementById('closeLoanForm');
        const loanFormContainer = document.getElementById('loanFormContainer');

        function openLoanForm() {
            loanFormContainer.style.display = 'block';
        }

        function closeLoanForm() {
            loanFormContainer.style.display = 'none';
        }

        openLoanFormBtn.addEventListener('click', openLoanForm);
        closeLoanFormBtn.addEventListener('click', closeLoanForm);
    </script>
</body>
</html>
