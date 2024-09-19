<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User session not found. Please log in.";
    exit();
}

// Include the Database and Loan classes
include '../model/database.php';
include 'loan_function.php';

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "go_loan";

// Create a new Database instance
$db = new Database($servername, $username, $password, $dbname);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_SESSION['user_id'];
    $loan_amount = $_POST['loan_amount'];
    $note = $_POST['note'];
    $payable_months = $_POST['payable_months'];

    // Create a new Loan instance
    $loan = new Loan($db, $user_id, $loan_amount, $note, $payable_months);

    // Process the loan
    $loan->processLoan();
}
?>
