<?php
// Include database connection file
include '../model/db.php';

// Define a function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Define a function to create the uploads directory if it doesn't exist
function createUploadsDir($dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Define the uploads directory
$uploadsDir = "../uploads/";
createUploadsDir($uploadsDir);

// Define an array to store error messages
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish database connection
    $conn = connectDB();

    // Sanitize and validate plan
    $plan = sanitizeInput($_POST["plan"]);
    if (empty($plan)) {
        $errors['plan'] = "Plan is required.";
    } else {
        // Check the membership limit for the selected plan
        $sql = "SELECT COUNT(*) FROM user_tbl WHERE plan = :plan";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':plan', $plan);
        $stmt->execute();
        $memberCount = $stmt->fetchColumn();

        if ($memberCount >= 50) {
            $errors['plan'] = ucfirst($plan) . " plan is full. Registration is closed.";
        }
    }

    // Sanitize and validate name
    $name = sanitizeInput($_POST["name"]);
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }

    // Sanitize and validate password
    $password = sanitizeInput($_POST["password"]);
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) != 6) {
        $errors['password'] = "Password must be exactly 6 characters long.";
    }

    // Sanitize and validate address
    $address = sanitizeInput($_POST["address"]);
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    // Sanitize and validate gender
    $gender = sanitizeInput($_POST["gender"]);
    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }

    // Sanitize and validate birthdate
    $birthdate = sanitizeInput($_POST["Birthdate"]);
    if (empty($birthdate)) {
        $errors['Birthdate'] = "Birthdate is required.";
    }

    // Sanitize and validate email
    $email = sanitizeInput($_POST["email"]);
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        // Check if email already exists in the database
        $sql = "SELECT COUNT(*) FROM user_tbl WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emailExists = $stmt->fetchColumn();

        if ($emailExists) {
            $errors['email'] = "Email already exists.";
        }
    }

    // Sanitize and validate contact number
    $contact = sanitizeInput($_POST["contact#"]);
    if (empty($contact)) {
        $errors['contact'] = "Contact number is required.";
    }

    // Sanitize and validate bank details (if plan is premium)
    if ($plan == "premium") {
        $bankname = sanitizeInput($_POST["bankname"]);
        if (empty($bankname)) {
            $errors['bankname'] = "Bank name is required.";
        }

        $bankAccount = sanitizeInput($_POST["bankAccount#"]);
        if (empty($bankAccount)) {
            $errors['bankAccount'] = "Bank account number is required.";
        }

        $cardHolder = sanitizeInput($_POST["cardHolder"]);
        if (empty($cardHolder)) {
            $errors['cardHolder'] = "Card holder's name is required.";
        }

        $tin = sanitizeInput($_POST["tin#"]);
        if (empty($tin)) {
            $errors['tin'] = "TIN number is required.";
        }
    }

    // Sanitize and validate employment details
    $companyName = sanitizeInput($_POST["companyName"]);
    if (empty($companyName)) {
        $errors['companyName'] = "Company name is required.";
    }

    $companyAddress = sanitizeInput($_POST["companyAddress"]);
    if (empty($companyAddress)) {
        $errors['companyAddress'] = "Company address is required.";
    }

    $companyPhoneNumber = sanitizeInput($_POST["companyPhoneNumber"]);
    if (empty($companyPhoneNumber)) {
        $errors['companyPhoneNumber'] = "Company phone number is required.";
    }

    $position = sanitizeInput($_POST["position"]);
    if (empty($position)) {
        $errors['position'] = "Position is required.";
    }

    $monthlyEarnings = sanitizeInput($_POST["monthly_earnings"]);
    if (empty($monthlyEarnings)) {
        $errors['monthly_earnings'] = "Monthly earnings are required.";
    }

    // Validate file uploads
    $proofBilling = $_FILES["proof_billing"];
    $validIdPrimary = $_FILES["valid_id_primary"];
    $coe = $_FILES["coe"];

    $allowedFileTypes = ["image/jpeg", "image/png", "application/pdf"];
    $maxFileSize = 5 * 1024 * 1024; // 5 MB

    if (!in_array($proofBilling["type"], $allowedFileTypes) || $proofBilling["size"] > $maxFileSize) {
        $errors['proof_billing'] = "Invalid proof of billing file.";
    }

    if (!in_array($validIdPrimary["type"], $allowedFileTypes) || $validIdPrimary["size"] > $maxFileSize) {
        $errors['valid_id_primary'] = "Invalid valid ID (primary) file.";
    }

    if (!in_array($coe["type"], $allowedFileTypes) || $coe["size"] > $maxFileSize) {
        $errors['coe'] = "Invalid certificate of employment file.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        // Move uploaded files to a secure location
        if (!move_uploaded_file($proofBilling["tmp_name"], $uploadsDir . basename($proofBilling["name"]))) {
            $errors['proof_billing'] = "Failed to upload proof of billing.";
        }
        if (!move_uploaded_file($validIdPrimary["tmp_name"], $uploadsDir . basename($validIdPrimary["name"]))) {
            $errors['valid_id_primary'] = "Failed to upload valid ID (primary).";
        }
        if (!move_uploaded_file($coe["tmp_name"], $uploadsDir . basename($coe["name"]))) {
            $errors['coe'] = "Failed to upload certificate of employment.";
        }

        if (empty($errors)) {
            // Prepare SQL query to insert user data into the database
            $sql = "INSERT INTO user_tbl (plan, name, password, address, gender, birthdate, email, contact, bankname, bankAccount, cardHolder, tin, companyName, companyAddress, companyPhoneNumber, position, monthly_earnings, proof_of_billing, valid_id_primary, coe)
                    VALUES (:plan, :name, :password, :address, :gender, :birthdate, :email, :contact, :bankname, :bankAccount, :cardHolder, :tin, :companyName, :companyAddress, :companyPhoneNumber, :position, :monthly_earnings, :proof_of_billing, :valid_id_primary, :coe)";

            // Prepare the statement
            $stmt = $conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':plan', $plan);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact', $contact);
            $stmt->bindParam(':bankname', $bankname);
            $stmt->bindParam(':bankAccount', $bankAccount);
            $stmt->bindParam(':cardHolder', $cardHolder);
            $stmt->bindParam(':tin', $tin);
            $stmt->bindParam(':companyName', $companyName);
            $stmt->bindParam(':companyAddress', $companyAddress);
            $stmt->bindParam(':companyPhoneNumber', $companyPhoneNumber);
            $stmt->bindParam(':position', $position);
            $stmt->bindParam(':monthly_earnings', $monthlyEarnings);
            $stmt->bindParam(':proof_of_billing', $proofBilling["name"]);
            $stmt->bindParam(':valid_id_primary', $validIdPrimary["name"]);
            $stmt->bindParam(':coe', $coe["name"]);

            // Execute the query and check for errors
            if ($stmt->execute()) {
                echo "Registration successful!";
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }

            // Close the statement and connection
            $stmt = null;
            $conn = null;
        }
    } else {
        // Display error messages
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>
