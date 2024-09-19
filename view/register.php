<?php
session_start();

// Include your database connection file
include 'db_connection.php';

// Initialize an array to store errors
$errors = [];

// Validate and sanitize input data
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Calculate age based on birthdate
function calculate_age($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan = validate_input($_POST["plan"]);
    $name = validate_input($_POST["name"]);
    $password = validate_input($_POST["password"]);
    $address = validate_input($_POST["address"]);
    $gender = validate_input($_POST["gender"]);
    $birthdate = validate_input($_POST["Birthdate"]);
    $email = validate_input($_POST["email"]);
    $contact = validate_input($_POST["contact#"]);
    $companyName = validate_input($_POST["companyName"]);
    $companyAddress = validate_input($_POST["companyAddress"]);
    $companyPhoneNumber = validate_input($_POST["companyPhoneNumber"]);
    $position = validate_input($_POST["position"]);
    $monthly_earnings = validate_input($_POST["monthly_earnings"]);

    // Only get bank details if the plan is premium
    $bankname = "";
    $bankAccount = "";
    $cardHolder = "";
    $tin = "";

    if ($plan == 'premium') {
        $bankname = validate_input($_POST["bankname"]);
        $bankAccount = validate_input($_POST["bankAccount#"]);
        $cardHolder = validate_input($_POST["cardHolder"]);
        $tin = validate_input($_POST["tin#"]);
    }

    // File uploads
    $proof_billing = $_FILES["proof_billing"]["name"];
    $valid_id_primary = $_FILES["valid_id_primary"]["name"];
    $coe = $_FILES["coe"]["name"];

    // Move uploaded files to a designated folder
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    move_uploaded_file($_FILES["proof_billing"]["tmp_name"], $upload_dir . basename($proof_billing));
    move_uploaded_file($_FILES["valid_id_primary"]["tmp_name"], $upload_dir . basename($valid_id_primary));
    move_uploaded_file($_FILES["coe"]["tmp_name"], $upload_dir . basename($coe));

    // Validate required fields
    $errors = [];
    if (empty($plan)) {
        $errors['plan'] = "Plan is required.";
    }
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }
    if (empty($gender)) {
        $errors['gender'] = "Gender is required.";
    }
    if (empty($birthdate)) {
        $errors['birthdate'] = "Birthdate is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }
    if (empty($contact)) {
        $errors['contact'] = "Contact number is required.";
    }
    if (empty($companyName)) {
        $errors['companyName'] = "Company name is required.";
    }
    if (empty($companyAddress)) {
        $errors['companyAddress'] = "Company address is required.";
    }
    if (empty($companyPhoneNumber)) {
        $errors['companyPhoneNumber'] = "Company phone number is required.";
    }
    if (empty($position)) {
        $errors['position'] = "Position is required.";
    }
    if (empty($monthly_earnings)) {
        $errors['monthly_earnings'] = "Monthly earnings are required.";
    }

    // Check if email already exists in the database
    $conn = openDbConnection();
    $email_check_query = "SELECT * FROM user_tbl WHERE email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors['email'] = "Email already exists.";
    }

    $stmt->close();

    // Check if there are any errors
    if (empty($errors)) {
        // Calculate age
        $age = calculate_age($birthdate);

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into database
        $sql = "INSERT INTO user_tbl (
            plan, name, password, address, gender, birthdate, age, email, contact, companyName, 
            companyAddress, companyPhoneNumber, position, monthly_earnings, proof_of_billing, 
            valid_id_primary, coe, bankname, bankAccount, cardHolder, tin
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Bind parameters for both basic and premium plans
        $stmt->bind_param(
            "sssssssssssssssssssss",
            $plan, $name, $hashed_password, $address, $gender, $birthdate, $age, $email, $contact,
            $companyName, $companyAddress, $companyPhoneNumber, $position, $monthly_earnings,
            $proof_billing, $valid_id_primary, $coe, $bankname, $bankAccount, $cardHolder, $tin
        );

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/registration.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .invalid-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .button{
            color: black;
        }
    </style>
</head>
<body>
<?php include '../controller/header.php'; ?>
<div class="register-container">
    <div class="acctype">  
        <form id="regId" action="register.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="page" id="page1">
                <p>REGISTER YOUR ACCOUNT</p>
                <br>
                <label for="acctype">CHOOSE PLAN:</label>
                <select name="plan" id="plan">
                    <option value="basic" <?php echo isset($plan) && $plan == 'basic' ? 'selected' : ''; ?>>Basic</option>
                    <option value="premium" <?php echo isset($plan) && $plan == 'premium' ? 'selected' : ''; ?>>Premium</option>
                </select>
                <?php if (isset($errors['plan'])): ?>
                    <div class="invalid-message"><?php echo $errors['plan']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-message"><?php echo $errors['name']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password">
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-message"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo isset($address) ? $address : ''; ?>">
                <?php if (isset($errors['address'])): ?>
                    <div class="invalid-message"><?php echo $errors['address']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="gender">Gender:</label>
                <input type="text" id="gender" name="gender" value="<?php echo isset($gender) ? $gender : ''; ?>">
                <?php if (isset($errors['gender'])): ?>
                    <div class="invalid-message"><?php echo $errors['gender']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="Birthdate">Birthdate:</label>
                <input type="date" id="Birthdate" name="Birthdate" value="<?php echo isset($birthdate) ? $birthdate : ''; ?>">
                <?php if (isset($errors['birthdate'])): ?>
                    <div class="invalid-message"><?php echo $errors['birthdate']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-message"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="contact#">Contact Number:</label>
                <input type="text" id="contact#" name="contact#" value="<?php echo isset($contact) ? $contact : ''; ?>">
                <?php if (isset($errors['contact'])): ?>
                    <div class="invalid-message"><?php echo $errors['contact']; ?></div>
                <?php endif; ?>
                <br><br>
                <button type="button" class="button" id="nextBtn">Next</button>
            </div>
            <div class="page" id="page2" style="display: none;">
                <label for="companyName">Company Name:</label>
                <input type="text" id="companyName" name="companyName" value="<?php echo isset($companyName) ? $companyName : ''; ?>">
                <?php if (isset($errors['companyName'])): ?>
                    <div class="invalid-message"><?php echo $errors['companyName']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="companyAddress">Company Address:</label>
                <input type="text" id="companyAddress" name="companyAddress" value="<?php echo isset($companyAddress) ? $companyAddress : ''; ?>">
                <?php if (isset($errors['companyAddress'])): ?>
                    <div class="invalid-message"><?php echo $errors['companyAddress']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="companyPhoneNumber">Company Phone Number:</label>
                <input type="text" id="companyPhoneNumber" name="companyPhoneNumber" value="<?php echo isset($companyPhoneNumber) ? $companyPhoneNumber : ''; ?>">
                <?php if (isset($errors['companyPhoneNumber'])): ?>
                    <div class="invalid-message"><?php echo $errors['companyPhoneNumber']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" value="<?php echo isset($position) ? $position : ''; ?>">
                <?php if (isset($errors['position'])): ?>
                    <div class="invalid-message"><?php echo $errors['position']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="monthly_earnings">Monthly Earnings:</label>
                <input type="text" id="monthly_earnings" name="monthly_earnings" value="<?php echo isset($monthly_earnings) ? $monthly_earnings : ''; ?>">
                <?php if (isset($errors['monthly_earnings'])): ?>
                    <div class="invalid-message"><?php echo $errors['monthly_earnings']; ?></div>
                <?php endif; ?>
                <br><br>
                <label for="proof_billing">Proof of Billing:</label>
                <input type="file" id="proof_billing" name="proof_billing">
                <br><br>
                <label for="valid_id_primary">Primary Valid ID:</label>
                <input type="file" id="valid_id_primary" name="valid_id_primary">
                <br><br>
                <label for="coe">Certificate of Employment:</label>
                <input type="file" id="coe" name="coe">
                <br><br>
                <div id="premiumFields" style="display: <?php echo isset($plan) && $plan == 'premium' ? 'block' : 'none'; ?>;">
                    <label for="bankname">Bank Name:</label>
                    <input type="text" id="bankname" name="bankname" value="<?php echo isset($bankname) ? $bankname : ''; ?>">
                    <?php if (isset($errors['bankname'])): ?>
                        <div class="invalid-message"><?php echo $errors['bankname']; ?></div>
                    <?php endif; ?>
                    <br><br>
                    <label for="bankAccount#">Bank Account Number:</label>
                    <input type="text" id="bankAccount#" name="bankAccount#" value="<?php echo isset($bankAccount) ? $bankAccount : ''; ?>">
                    <?php if (isset($errors['bankAccount'])): ?>
                        <div class="invalid-message"><?php echo $errors['bankAccount']; ?></div>
                    <?php endif; ?>
                    <br><br>
                    <label for="cardHolder">Card Holder:</label>
                    <input type="text" id="cardHolder" name="cardHolder" value="<?php echo isset($cardHolder) ? $cardHolder : ''; ?>">
                    <?php if (isset($errors['cardHolder'])): ?>
                        <div class="invalid-message"><?php echo $errors['cardHolder']; ?></div>
                    <?php endif; ?>
                    <br><br>
                    <label for="tin#">TIN Number:</label>
                    <input type="text" id="tin#" name="tin#" value="<?php echo isset($tin) ? $tin : ''; ?>">
                    <?php if (isset($errors['tin'])): ?>
                        <div class="invalid-message"><?php echo $errors['tin']; ?></div>
                    <?php endif; ?>
                    <br><br>
                </div>
                <button type="button" class="button" id="prevBtn">Previous</button>
                <button type="submit" class="button">Submit</button>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#nextBtn").click(function() {
            $("#page1").hide();
            $("#page2").show();
        });

        $("#prevBtn").click(function() {
            $("#page2").hide();
            $("#page1").show();
        });

        $("#plan").change(function() {
            if ($(this).val() === "premium") {
                $("#premiumFields").show();
            } else {
                $("#premiumFields").hide();
            }
        });
    });
</script>
</body>
</html>
