<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Perform update operation
    // Assuming you have form fields named 'username' and 'email'
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Perform update query
    // Example: UPDATE users SET username = '$username', email = '$email' WHERE id = $_SESSION['user_id']
    // Execute your database update query here
    
    // Redirect to dashboard or any other page after update
    header("Location: dashboard.php");
    exit;
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
<?php include 'premium_sidebar.php'; ?>
<?php include 'header.php'; ?>
<main class= "mainContent">
        <!-- <div class= "container"> 
            <div class="row">
                <div class="rowCol"> </div>
            </div>
        </div> -->

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
                                            <span class="text">Payments Today</span>
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
                                        <a href="saving.php">
                                            <img src="icons/right.png" alt="">
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
                                                "0"          		
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
                                            <img src="icons/right.png" alt="">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>





        </div>
</body>
</html>
