<?php
// Start the session
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "go_loan";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id']; // Get the logged-in user ID from the session

// Function to get savings transactions and withdrawal requests
function getSavingsTransactions($user_id, $search = '') {
    global $conn;

    // Sanitize the search input
    $search = mysqli_real_escape_string($conn, $search);

    // Prepare the query to fetch savings transactions
    $savings_sql = "SELECT transaction_id, user_id, 'Deposit' as transaction_type, amount, current_amount, status, created_at FROM savingstransactions WHERE user_id = '$user_id'";
    if (!empty($search)) {
        $savings_sql .= " AND (transaction_id LIKE '%$search%' OR transaction_type LIKE '%$search%' OR amount LIKE '%$search%')";
    }

    // Prepare the query to fetch withdrawal requests
    $withdrawal_sql = "SELECT request_id as transaction_id, user_id, 'Withdrawal' as transaction_type, amount, NULL as current_amount, status, created_at FROM withdrawalrequests WHERE user_id = '$user_id'";
    if (!empty($search)) {
        $withdrawal_sql .= " AND (transaction_id LIKE '%$search%' OR transaction_type LIKE '%$search%' OR amount LIKE '%$search%')";
    }

    // Combine the two queries using UNION ALL
    $sql = "($savings_sql) UNION ALL ($withdrawal_sql) ORDER BY created_at DESC";

    // Execute the query
    $result = $conn->query($sql);

    // Check for errors
    if (!$result) {
        die("Error: " . $conn->error);
    }

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    // Fetch the latest current amount
    $latest_current_amount = getCurrentAmount($user_id);

    return ['transactions' => $transactions, 'latest_current_amount' => $latest_current_amount];
}

// Function to get the current amount for the user
function getCurrentAmount($user_id) {
    global $conn;

    // Prepare the query to fetch the latest completed transaction
    $sql = "SELECT current_amount FROM savingstransactions WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 1";

    // Execute the query
    $result = $conn->query($sql);

    // Check for errors
    if (!$result) {
        die("Error: " . $conn->error);
    }

    $current_amount = 0;
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_amount = $row['current_amount'];
    }

    return $current_amount;
}

// Fetch savings transactions for the user, including the latest current amount
$search = isset($_GET['search']) ? $_GET['search'] : '';
$savingsData = getSavingsTransactions($user_id, $search);
$savingsTransactions = $savingsData['transactions'];
$latest_current_amount = $savingsData['latest_current_amount'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deposit'])) {
        $amount = $_POST['deposit_amount'];
        $message = deposit($user_id, $amount);
        echo "<p>$message</p>";
    }

    if (isset($_POST['withdraw'])) {
        $amount = $_POST['withdraw_amount'];
        $message = withdraw($user_id, $amount);
        echo "<p>$message</p>";
    }
}

// Function to deposit amount
function deposit($user_id, $amount) {
    global $conn;

    // Validate the amount
    if ($amount < 100 || $amount > 10000) {
        return "Amount should be between 100 and 10000.";
    }

    // Sanitize the amount input
    $amount = mysqli_real_escape_string($conn, $amount);

    // Fetch the user's current amount
    $current_amount = getCurrentAmount($user_id);

    // Calculate the new amount
    $new_amount = $current_amount + $amount;

    // Generate a unique transaction ID
    $transaction_id = generateUniqueTransactionID();

    $transaction_type = 'Deposit';
    $status = 'Completed';
    $created_at = date('Y-m-d H:i:s');

    // Prepare the query
    $sql = "INSERT INTO savingstransactions (user_id, transaction_id, transaction_type, amount, current_amount, status, created_at) VALUES ('$user_id', '$transaction_id', '$transaction_type', '$amount', '$new_amount', '$status', '$created_at')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        header("Location: saving.php?message=Deposit successful!"); // Redirect to prevent form resubmission
        exit;
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Function to withdraw amount and create a withdrawal request
function withdraw($user_id, $amount) {
    global $conn;

    // Validate the amount
    if ($amount < 500 || $amount > 5000) {
        return "Amount should be between 500 and 5000.";
    }

    // Sanitize the amount input
    $amount = mysqli_real_escape_string($conn, $amount);

    // Insert withdrawal request into withdrawalrequests table
    $status = 'Pending';
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO withdrawalrequests (user_id, amount, status, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return "Error: " . $conn->error;
    }

    $stmt->bind_param('idss', $user_id, $amount, $status, $created_at);
    if ($stmt->execute()) {
        return "Withdrawal request submitted successfully. It will be processed shortly.";
    } else {
        return "Error: " . $stmt->error;
    }
}

// Function to generate a unique 6-digit transaction ID
function generateUniqueTransactionID() {
    global $conn;

    $unique = false;
    $transaction_id = '';

    do {
        $transaction_id = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $sql = "SELECT COUNT(*) as count FROM savingstransactions WHERE transaction_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    } while ($row['count'] > 0);

    return $transaction_id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savings</title>
    <link rel="stylesheet" href="../css/design.css">
</head>

<style>
    /* Add this style for the search box */
    .search-box {
        width: 300px; /* Adjust the width as needed */
        padding: 10px;
        font-size: 16px;
    }

    .search-button {
        padding: 10px 15px;
        font-size: 16px;
    }

    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more or less, depending on screen size */
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
</style>

<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <?php include 'header.php'; ?>
        <div class="content">
            <h2>Savings Transactions</h2>

            <!-- Display the latest current amount -->
            <p>Latest Current Amount: <?php echo htmlspecialchars($latest_current_amount); ?></p>

            <!-- Search form -->
            <form method="GET" action="saving.php">
                <input type="text" name="search" placeholder="Search transactions" value="<?php echo htmlspecialchars($search); ?>" class="search-box">
                <button type="submit" class="search-button">Search</button>
            </form>

            <!-- Display transactions table if transactions exist -->
            <?php if (!empty($savingsTransactions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Current Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savingsTransactions as $transaction): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['current_amount']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No transactions found.</p>
            <?php endif; ?>

            <!-- Trigger/Open The Modal  -->
            <button id="depositBtn">Deposit</button>
            <button id="withdrawBtn">Withdraw</button> 

            <!-- Deposit Modal -->
            <div id="depositModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="depositClose">&times;</span>
                    <form action="saving.php" method="POST">
                        <h3>Deposit</h3>
                        <label for="deposit_amount">Amount:</label>
                        <input type="number" id="deposit_amount" name="deposit_amount" min="100" max="10000" required>
                        <button type="submit" name="deposit">Deposit</button>
                    </form>
                </div>
            </div>

            <!-- Withdrawal Modal -->
            <div id="withdrawModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="withdrawClose">&times;</span>
                    <form action="saving.php" method="POST">
                        <h3>Withdraw</h3>
                        <label for="withdraw_amount">Amount:</label>
                        <input type="number" id="withdraw_amount" name="withdraw_amount" min="500" max="5000" required>
                        <button type="submit" name="withdraw">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Get the modal elements
        var depositModal = document.getElementById('depositModal');
        var withdrawModal = document.getElementById('withdrawModal');

        // Get the button elements
        var depositBtn = document.getElementById('depositBtn');
        var withdrawBtn = document.getElementById('withdrawBtn');

        // Get the <span> elements that close the modals
        var depositClose = document.getElementById('depositClose');
        var withdrawClose = document.getElementById('withdrawClose');

        // When the user clicks the button, open the modal
        depositBtn.onclick = function() {
            depositModal.style.display = "block";
        }
        withdrawBtn.onclick = function() {
            withdrawModal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        depositClose.onclick = function() {
            depositModal.style.display = "none";
        }
        withdrawClose.onclick = function() {
            withdrawModal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == depositModal) {
                depositModal.style.display = "none";
            }
            if (event.target == withdrawModal) {
                withdrawModal.style.display = "none";
            }
        }
    </script>
</body>
</html>
