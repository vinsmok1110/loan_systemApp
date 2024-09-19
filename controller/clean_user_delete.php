<?php
// Include the DatabaseCleaner class
include 'DatabaseCleaner.php';

// Usage
$cleaner = new DatabaseCleaner();
$cleaner->cleanOldRecords();
$cleaner->closeConnection();
?>
