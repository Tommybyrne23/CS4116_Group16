<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

//This is the login page.
session_start();
require_once 'functions.php';

$conn = connectToDatabase();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validate input
    if (empty($username) || empty($password)) {
        $errorMessage = "Username and password are required.";
    } else {
        if (checkUserCredentials($conn, $username, $password, 'Users', 'user_home.php')) {
            exit; // Redirect handled in checkUserCredentials
        } elseif (checkUserCredentials($conn, $username, $password, 'Businesses', 'business_home.php')) {
            exit; // Redirect handled in checkUserCredentials
        } elseif (checkUserCredentials($conn, $username, $password, 'Admins', 'admin_home.php')) {
            exit; // Redirect handled in checkUserCredentials
        } else {
            $errorMessage = "Invalid username or password.";
        }
    }
}
$conn->close();
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - User Login</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background:rgb(255, 117, 37);
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
            background: white;
            margin: 60px auto 0 auto;
            max-width: 500px;
            border-radius: 18px;
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

        .user-form input[type="text"], .user-form input[type="password"] {
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
            margin-bottom: 10px;
        }
</style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav></nav>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading" id="formTitle">Login</h1>

        <form class="user-form" id="userForm" method="post" action="login.php">
            <div class="error"><?php echo $errorMessage; ?></div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div class="form-toggle">
            <a href="homepage.php">Don't have an account? Sign up!</a>
        </div>
    </div>
</body>
</html>
