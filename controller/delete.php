<?php
require_once '../model/DatabaseManager.php'; 

class UserManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function deleteUser($user_id) {
        $conn = $this->db->conn;

        // Start transaction
        $conn->begin_transaction();

        try {
            // Get the user data
            $sql_select = "SELECT * FROM user_tbl WHERE user_id = ?";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bind_param("i", $user_id);
            $stmt_select->execute();
            $result = $stmt_select->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Insert into user_delete table
                $sql_insert = "INSERT INTO user_delete (user_id, plan, name, email, status) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("issss", $row['user_id'], $row['plan'], $row['name'], $row['email'], $row['status']);
                $stmt_insert->execute();
                $stmt_insert->close();

                // Delete from user_tbl table
                $sql_delete = "DELETE FROM user_tbl WHERE user_id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $user_id);
                $stmt_delete->execute();
                $stmt_delete->close();

                // Commit transaction
                $conn->commit();

                echo "User deleted successfully.";
            } else {
                echo "User not found.";
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }

        $stmt_select->close();
    }
}

// Usage
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $database = new DatabaseManager("localhost", "root", "", "go_loan"); // Create database connection
    $userManager = new UserManager($database);
    $userManager->deleteUser($user_id);
    $database->closeConnection();
} else {
    echo "No user ID specified.";
}
?>
