<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Signup Form</title>
    <!-- CSS Link -->
    <link rel="stylesheet" href="css/registration.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include 'controller/header.php'; ?>

    <!-- Include the form class definitions -->
    <?php include 'Form.php'; ?>

    <!-- Create form instances -->
    <!-- <?php
    $loginForm = new Form("controller/login_connect.php");
    $loginForm->addElement(new Input("email", "email", "Enter your email", "uil uil-envelope-alt email"));
    $loginForm->addElement(new Input("password", "password", "Enter your password", "uil uil-lock password"));
    $loginForm->addElement(new Button("Login Now"));

    $signupForm = new Form("#");
    $signupForm->addElement(new Input("email", "", "Enter your email", "uil uil-envelope-alt email"));
    $signupForm->addElement(new Input("password", "", "Create password", "uil uil-lock password"));
    $signupForm->addElement(new Input("password", "", "Confirm password", "uil uil-lock password"));
    $signupForm->addElement(new Button("Signup Now"));
    ?> -->

    <!-- Home -->
    <section class="home">
        
        <div class="form_container">
            <i class="uil uil-times form_close"></i>
           
            <!-- Login Form -->
            <div class="form login_form">
              
                <?php
                // Render the login form
                $loginForm->render();
                ?>
            </div>

            <!-- Signup Form
            <div class="form signup_form" >
                <?php
                // Render the signup form
                $signupForm->render();
                ?>
            </div> -->
        </div>
    </section>

    <script src="view/index/script.js"></script>
</body>
</html>
