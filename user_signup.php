<?php
session_start();
require_once 'functions.php';

$conn = connectToDatabase();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validate input (add more validation as needed)
    if (empty($firstName) || empty($lastName) || empty($username) || empty($password)) {
        $errorMessage = "All fields are required.";;
    } else {
        /*
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        */
        
        // Check for duplicate username BEFORE inserting
        
        if (usernameExists($conn, $username)) {
            $errorMessage = "Username already exists.";

        } else{

            if(registerUser($conn, $firstName, $lastName, $username, $password)){
                //do nothing
            } else{
                $errorMessage = "Registration failed. Please try again.";
            }
        }
        $usernameCheck->close();
    }
}

$conn->close();
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - User Sign Up</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        .main-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1.service-page-heading {
            text-align: center;
        }

        .user-form {
            width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .user-form label {
            display: block;
            margin-bottom: 5px;
        }

        .user-form input[type="text"],
        .user-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .user-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .user-form button:hover {
            background-color: #0056b3;
        }

        .form-toggle {
            margin-top: 10px;
            text-align: center;
        }

        .form-toggle a {
            text-decoration: none;
            color: #007bff;
        }
        .error {
            color: red;
            margin-bottom: 10px; /* Adjust margin for spacing */
        }
    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading" id="formTitle">Sign Up</h1>

        <form class="user-form" id="userForm" method="post" action="user_signup.php">
            <div class="error"><?php echo $errorMessage; ?></div>
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Submit</button>
        </form>

        <div class="form-toggle">
            <a href="login.php" id="toggleLink" onclick="toggleForm()">Already have an account? Log in<br><br></a>
            <a href="business_signup.php">Are you a business? Sign up here.</a>
        </div>
    </div>

</body>
</html>
