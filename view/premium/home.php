<?php
// Start the session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the files containing the functions
include 'get_loan.php';
include 'get_saving.php';

// Initialize loan count variable
$loan_count = 0;
$saving_count = 0;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Get loan data for the logged-in user
    $loan_result = getLoanData($_SESSION['user_id']);

    // Calculate loan count
    if ($loan_result->num_rows > 0) {
        $loan_count = $loan_result->num_rows;
    }

    // Get savings data for the logged-in user
    $savings = get_saving_file($_SESSION['user_id']);

    // Calculate savings count
    $saving_count = count($savings);
} else {
    echo "<p>Please log in to view loan and saving transactions.</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan</title>
    <link rel="stylesheet" href="../css/design.css">
</head>
<body>
    
<main class= "mainContent">
            <?php include 'sidebar.php'; ?>
            <?php include 'header.php'; ?>
        <!-- <div class= "container"> 
            <div class="row">
                <div class="rowCol"> </div>
            </div>
        </div> -->

        <div class="cardsContainer">
            <div class="cardsColumn">
                <div class="card">
                    <div class="cardBody">
                        <h2>Welcome Back User!</h2>
                      <!-- php echo welcome back then put the log in churva2 -->
                    </div>

                    <hr>

                    <div class="row1">
                    <div class="colCard">
                        <div class="card1">
                            <div class="cardBody">
                                <div class="payments">
                                    <div class="payments1">
                                        <span class="text">Saving</span>
                                        <div class="totalPayment">
                                            <?php echo $saving_count; ?>
                                        </div>
                                    </div>
                                    <img src="../icons/calendar.png" alt="">
                                </div>
                            </div>
                            <div class="cardFooter ">
                                <!-- <a class="view" href=" "> View Payments</a> -->
                                <span class="text">View Saving </span>
                                <div class="right">
                                    <a href="payments.php">
                                        <img src="../icons/right.png" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
              
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
                                        <!-- <img src="icons/borrower.png" alt=""> -->
                                    </div>
                                </div>
                                <div class="cardFooter ">
                                    <!-- <a class="view" href=" "> View Payments</a> -->
                                    <span class="text">View Loan List </span>
                                    <div class="right">
                                        <a href="loan.php">
                                            <img src="../icons/right.png" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>





        </div>

    </main>
</body>
</html>
