<?php
// Include the DatabaseManager class
require_once '../model/DatabaseManager.php'; 

class UserActions extends DatabaseManager {
    public function approveUser($userId) {
        $id = $this->conn->real_escape_string($userId);

        // Update the status of the user to "approved"
        $sql_update = "UPDATE user_tbl SET status = 'approved' WHERE user_id = $id";

        if ($this->conn->query($sql_update) === TRUE) {
            echo "User status updated to approved.";
        } else {
            echo "Error updating user status: " . $this->conn->error;
        }
    }
}
?>
