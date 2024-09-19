<?php
// Start the session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the file containing the function
include 'get_loan.php';

// Initialize loan count variable
$loan_count = 0;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Get loan data for the logged-in user
    $result = getLoanData($_SESSION['user_id']);

    // Calculate loan count
    if ($result->num_rows > 0) {
        $loan_count = $result->num_rows;
    }
} else {
    echo "<p>Please log in to view loan transactions.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/design.css">
</head>
<body>
<?php include 'basic_sidebar.php'; ?>
<?php include 'header.php'; ?>
<main class="mainContent">
    <div class="cardsContainer">
        <div class="cardsColumn">
            <div class="card">
                <div class="cardBody">
                    <h2>Welcome Back Admin!</h2>
                    <!-- php echo welcome back then put the log in churva2 -->
                </div>
                <hr>
                <div class="colCard">
                    <div class="card1">
                        <div class="cardBody">
                            <div class="loan">
                                <div class="loan1">
                                    <span class="text">Loans</span>
                                    <div class="no_loan">
                                        <?php echo $loan_count; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cardFooter">
                            <span class="text">View Loan List </span>
                            <div class="right">
                                <a href="loan.php">
                                    <img src="icons/right.png" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
