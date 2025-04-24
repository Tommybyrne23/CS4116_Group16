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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $compName = $_POST["compName"];
    $sport = $_POST["sport"];
    $type = $_POST["type"]; //make drop down menu
    $date = $_POST["date"]; //make sure you can't put past dates
    $location = $_POST["location"];

    header("Location: add_competition.php?submitted=true");
    
    // Validate input (add more validation as needed)
    if (empty($compName) || empty($sport) || empty($type) || empty($date) || empty($location)) {
        $errorMessage = "All fields are required.";
    } else {
        if(addCompetition($conn, $compName, $sport, $type, $date, $location)){
            $successMessage = "Competition successfully added.";
        } else{
            $errorMessage = "Failed to add competition. Please try again.";
        }
    }
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Add Competition</title>
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

        h1.add-competition-page-heading {
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
        .user-form select,
        .user-form input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .user-form input[type="number"] {
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
            <button onclick="location.href='homepage.php'">Logout</button>
            <button onclick="location.href='admin_home.php'">Home</button>
            <button onclick="location.href='competition_list.php'">Competition Management</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="add-competition-page-heading" id="formTitle">Add a Competition</h1>

        <form class="user-form" id="userForm" method="post">
            <div class="error"><?php echo $errorMessage; ?></div>
            <label for="compName">Competition Name:</label>
            <input type="text" id="compName" name="compName" required>

            <label for="sport">Sport:</label>
            <input type="text" id="sport" name="sport" required>

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="team">Team</option>
                <option value="individual">Individual</option>
            </select>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <button type="submit" name="add_subservice">Add Competition</button>

        </form>
    </div>
</body>
</html>