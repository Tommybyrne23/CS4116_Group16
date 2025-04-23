<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$errorMessage = "";
$successMessage = "";

$username = $_SESSION['username'];
$businessID = getBusinessID($conn, $username);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceName = $_POST["serviceName"];
    $description = $_POST["description"];

    // Validate input (add more validation as needed)
    if (empty($serviceName) || empty($description) || empty($businessID)) {
        $errorMessage = "All fields are required.";
    } else {
        if(addService($conn, $serviceName, $description, $businessID)){
            $successMessage = "Service successfully added.";
        } else{
            $errorMessage = "Failed to add service. Please try again.";
        }
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Add Service</title>
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

        nav button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-left: 10px;
            cursor: pointer;
        }

        .main-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1.add-service-page-heading {
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

        <nav>
            <button onclick="location.href='login.php'">Logout</button> 
            <button onclick="location.href='business_home.php'">Home</button>
            <button onclick="location.href='business_messages.php'">Messages</button>
            <button onclick="location.href='business_profile.php'">Business Profile</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="add-service-page-heading" id="formTitle">Add a Service</h1>

        <form class="user-form" id="userForm" method="post">
            <div class="error"><?php echo $errorMessage; ?></div>
            <label for="serviceName">Service Name:</label>
            <input type="text" id="serviceName" name="serviceName" required>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" required>

            <button type="submit" name="add_service">Add Service</button>

        </form>
    </div>
</body>
</html>