<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$username = $_SESSION['username'];
$userID = getUserID($conn, $username);
$businessID = getBusinessID($conn, $_SESSION['selectedBusiness']);
$business = getBusinessName($conn, $businessID);
$serviceID = $_SESSION['selectedService'];
$service = getServiceName($conn, $serviceID);
$subserviceID = $_SESSION['selectedSubservice'];
$subservice = getSubserviceName($conn, $subserviceID);
$cost = getCost($conn, $subserviceID);

if (isset($_POST['submitTransaction'])) {
    if (completeTransaction($conn, $userID, $businessID, $serviceID, $subserviceID, $cost)) {
        echo "Transaction completed successfully!";
        // header("Location: transaction_history.php");
    } else {
        echo "Transaction failed.";
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Transaction</title>
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
        }

        h1.subservice-page-heading {
            text-align: center;
        }

        #totalCost {
            text-align: center;
        }

        .transaction-form {
            width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .transaction-form label {
            display: block;
            margin-bottom: 5px;
        }

        .transaction-form input[type="text"],
        .transaction-form input[type="number"],
        .transaction-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .transaction-form .button-container {
            text-align: center;
        }

        .transaction-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .transaction-form button:hover {
            background-color: #0056b3;
        }

        .review-button {
            display: none;
            margin: 20px auto;
            background-color: #28a745;
            display: block;
            width: fit-content;
        }

        .review-button:hover{
            background-color: #218838;
        }

    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='homepage.php'">Logout</button>
            <button onclick="location.href='user_home.php'">Home</button>
            <button onclick="location.href='user_messages.php'">Messages</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="subservice-page-heading">Purchase Subservice</h1>

        <div id="totalCost">Total: €<?php echo $cost; ?></div>

        <form class="transaction-form" id="transactionForm" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="cardNumber">Card Number:</label>
            <input type="number" id="cardNumber" name="cardNumber" required> 

            <label for="business">Business:</label>
            <input type="text" id="business" name="business" value="<?php echo htmlspecialchars($business); ?>" readonly>

            <label for="service">Service:</label>
            <input type="text" id="service" name="service" value="<?php echo htmlspecialchars($service); ?>" readonly>

            <label for="subservice">Subservice:</label>
            <input type="text" id="subservice" name="subservice" value="<?php echo htmlspecialchars($subservice); ?>" readonly>

            <div class="button-container">
                <button type="submit" name="submitTransaction">Complete Transaction</button>
            </div>
        </form>
    </div>
</body>
</html>