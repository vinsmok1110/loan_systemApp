<?php
// Include the DatabaseCleaner class
include 'DatabaseCleaner.php';

// Usage
$cleaner = new DatabaseCleaner("localhost", "root", "", "go_loan");
$cleaner->cleanOldRecords();
$cleaner->closeConnection();
?>
