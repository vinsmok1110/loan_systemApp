<?php

class UserAuth {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function loginUser($username, $password) {
        $errorMessages = [];

        try {
            // Prepare and execute the SQL query for user login
            $sql_user = "SELECT user_id, password, plan, status FROM user_tbl WHERE email = :email";
            $stmt_user = $this->conn->prepare($sql_user);
            $stmt_user->bindParam(':email', $username);
            $stmt_user->execute();

            // Check if a row is found with the given username in users table
            if ($stmt_user->rowCount() == 1) {
                $row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

                // Check if user status is approved
                if ($row_user['status'] == 'approved') {
                    // Verify the password using password_verify function
                    if (password_verify($password, $row_user['password'])) {
                        // Login successful for user
                        $_SESSION['user_id'] = $row_user['user_id']; // Store user ID in session
                        $_SESSION['plan'] = $row_user['plan']; // Store user role in session
                    } else {
                        // Incorrect password for user
                        $errorMessages['password'] = "Invalid password for user.";
                    }
                } else {
                    // User status is not approved
                    $errorMessages['status'] = "User status is pending. Approval required.";
                }
            } else {
                // Username not found in users table
                $errorMessages['email'] = "Username not found.";
            }
        } catch (PDOException $e) {
            // Handle database connection errors
            $errorMessages['database'] = "Database error: " . $e->getMessage();
        }

        return $errorMessages;
    }
}
?>
