<?php
// Include the UserActions class
include 'UserActions.php';

// Usage
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $userActions = new UserActions("localhost", "root", "", "go_loan");
    $userActions->approveUser($userId);
    $userActions->closeConnection();
} else {
    echo "No ID provided for approval.";
}
?>
