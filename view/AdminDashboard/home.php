<?php
// Include or define necessary functions
include_once 'function.php';

$user_id = 123; 

// Count the number of saving transactions
$saving_count = count($savingTransactions);

// Get the total number of unique user IDs

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/design.css">
    <title>Home</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>
<main class="mainContent">
    <div class="cardsContainer">
        <div class="cardsColumn">
            <div class="card">
                <div class="cardBody">
                    <h2>Welcome Back Admin!</h2>
                    <!-- php echo welcome back then put the log in churva2 -->
                </div>

                <hr>

                <div class="row1">
                    <div class="colCard">
                        <div class="card1">
                            <div class="cardBody">
                                <div class="payments">
                                    <div class="payments1">
                                        <span class="text">Payments</span>
                                        <div class="totalPayment">
                                            "0"
                                        </div>
                                    </div>
                                    <img src="icons/calendar.png" alt="">
                                </div>
                            </div>
                            <div class="cardFooter ">
                                <!-- <a class="view" href=" "> View Payments</a> -->
                                <span class="text">View Payments </span>
                                <div class="right">
                                    <a href="payments.php">
                                        <img src="icons/right.png" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="colCard">
                        <div class="card1">
                            <div class="cardBody">
                                <div class="payments">
                                    <div class="payments1">
                                        <span class="text">Borrowers</span>
                                        <div class="totalPayment">
                                            <?php echo $totalUniqueUserIDs; ?>
                                        </div>
                                    </div>
                                    <img src="icons/borrower.png" alt="">
                                </div>
                            </div>
                            <div class="cardFooter ">
                                <!-- <a class="view" href=" "> View Payments</a> -->
                                <span class="text">View Borrowers </span>
                                <div class="right">
                                    <a href="borrowers.php">
                                        <img src="icons/right.png" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="colCard">
                        <div class="card1">
                            <div class="cardBody">
                                <div class="payments">
                                    <div class="payments1">
                                        <span class="text">Loans</span>
                                        <div class="totalPayment">
                                            <?php echo $totalLoanIDs; ?>
                                        </div>
                                    </div>
                                    <img src="icons/borrower.png" alt="">
                                </div>
                            </div>
                            <div class="cardFooter ">
                                <!-- <a class="view" href=" "> View Payments</a> -->
                                <span class="text">View Loan List </span>
                                <div class="right">
                                    <a href="loans.php">
                                        <img src="icons/right.png" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <img src="icons/calendar.png" alt="">
                                </div>
                            </div>
                            <div class="cardFooter ">
                                <!-- <a class="view" href=" "> View Payments</a> -->
                                <span class="text">View Saving </span>
                                <div class="right">
                                    <a href="savings.php">
                                        <img src="icons/right.png" alt="">
                                    </a>
                                </div>
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
