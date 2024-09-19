<?php
session_start(); // Start session

include '../model/db.php'; // Adjust the path if necessary

class UserAuth {
    private $conn;
    private $errorMessages = array();

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function sanitize($data) {
        return htmlspecialchars(strip_tags($data));
    }

    public function loginUser($username, $password) {
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

                        // Redirect based on user role
                        if ($row_user['plan'] == 'basic') {
                            header("Location: ../view/basic/dashboard.php");
                            exit();
                        } elseif ($row_user['plan'] == 'premium') {
                            header("Location: ../view/premium/home.php");
                            exit();
                        } else {
                            // Handle unrecognized roles
                            $this->errorMessages['plan'] = "Unrecognized user role.";
                        }
                    } else {
                        // Incorrect password for user
                        $this->errorMessages['password'] = "Invalid password for user.";
                    }
                } else {
                    // User status is not approved
                    $this->errorMessages['status'] = "User status is pending. Approval required.";
                }
            } else {
                // Username not found in users table
                $this->loginAdmin($username, $password);
            }
        } catch (PDOException $e) {
            // Handle database connection errors
            $this->errorMessages['database'] = "Database error: " . $e->getMessage();
        }

        return $this->errorMessages;
    }

    private function loginAdmin($username, $password) {
        try {
            // Check admin table for login
            $sql_admin = "SELECT * FROM admin WHERE email = :email";
            $stmt_admin = $this->conn->prepare($sql_admin);
            $stmt_admin->bindParam(':email', $username);
            $stmt_admin->execute();

            // Check if a row is found with the given username in admin table
            if ($stmt_admin->rowCount() == 1) {
                $row_admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);
                // Verify the password using password_verify function
                if (password_verify($password, $row_admin['password'])) {
                    // Login successful for admin
                    // Redirect admin to admin dashboard
                    $_SESSION['admin'] = true; // Set admin session flag
                    header("Location: ../view/AdminDashboard");
                    exit();
                } else {
                    // Incorrect password for admin
                    $this->errorMessages['password'] = "Invalid password for admin.";
                }
            } else {
                // Username not found in admin table
                $this->errorMessages['email'] = "Username not found in users or admin table.";
            }
        } catch (PDOException $e) {
            // Handle database connection errors
            $this->errorMessages['database'] = "Database error: " . $e->getMessage();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Use the function to get a PDO connection
        $conn = connectDB();

        // Create an instance of the UserAuth class
        $auth = new UserAuth($conn);

        // Sanitize input
        $username = $auth->sanitize($username);
        $password = $auth->sanitize($password);

        // Attempt to log in the user
        $errorMessages = $auth->loginUser($username, $password);

        // Return error messages
        echo json_encode($errorMessages);
        exit();
    } catch (PDOException $e) {
        echo json_encode(array("database" => "Database error: " . $e->getMessage()));
        exit();
    } finally {
        // Always close the connection
        if ($conn) {
            $conn = null;
        }
    }
}
?>
