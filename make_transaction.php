<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$username = $_SESSION['username'];
$businesses = getAllBusinesses($conn);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['populateServices'])) {
        $business = $_POST['name'];
        $businessID = getBusinessID($conn, $business);
        $serviceOptions = getAllServices($conn, $businessID);
    }
}

    if (isset($_POST['populateSubservices'])) {
        $service = $_POST['service'];
        $serviceID = getServiceID($conn, $businessID, $service);
        $subserviceOptions = getAllSubservices($conn, $serviceID);
    }

    if (isset($_POST['subservice'])) {
        $subservice = $_POST['subservice'];
        $subserviceID = getSubserviceID($conn, $serviceID, $subservice);
        $cost = getCost($conn, $subserviceID);
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

        h1.service-page-heading {
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

        #totalPrice {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 15px;
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
            <button onclick="location.href='make_transaction.php'">Transactions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading">Purchase Service</h1>

        <div id="totalPrice">Total: €<?php echo $cost; ?></div>

        <form class="transaction-form" id="transactionForm">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="cardNumber">Card Number:</label>
            <input type="number" id="cardNumber" name="cardNumber" required> 

            <label for="business">Select Business:</label>
            <select id="name" name="name" required>
                <option value="">--Please choose a business--</option>
                <?php
                if (is_array($businesses) && !empty($businesses)) {
                    foreach ($businesses as $business) {
                        $selected = (isset($_POST['name']) && $business['name'] == $_POST['name']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($business['name']) . '" ' . $selected . '>' . htmlspecialchars($business['name']) . '</option>';
                    }
                } else {
                    echo '<option value="" disabled>No businesses</option>';
                }
                ?>
            </select>
            <button type="submit" name="populateServices">Next</button>


            <label for="service">Select Service:</label>
            <select id="service" name="service" required>
                <option value="">--Please choose a service--</option>
                <?php
                if (is_array($serviceOptions) && !empty($serviceOptions)) {
                    foreach ($serviceOptions as $service) {
                        $selected = (isset($_POST['service']) && $service['serviceName'] == $_POST['service']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($service['serviceName']) . '" ' . $selected . '>' . htmlspecialchars($service['serviceName']) . '</option>';
                    }
                } else {
                    echo '<option value="" disabled>No services</option>';
                }
                ?>
            </select>
            <button type="submit" name="populateSubservices">Next</button>


            <label for="subservice">Select Subservice:</label>
            <select id="subservice" name="subservice" required>
                <option value="">--Please choose a subservice--</option>
                <?php
                if (is_array($subserviceOptions) && !empty($subserviceOptions)) {
                    foreach ($subserviceOptions as $subservice) {
                        $selected = (isset($_POST['subservice']) && $subservice['subserviceName'] == $_POST['subservice']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($subservice['subserviceName']) . '" ' . $selected . '>' . htmlspecialchars($subservice['subserviceName']) . '</option>';
                    }
                } else {
                    echo '<option value="" disabled>No subservices</option>';
                }
                ?>
            </select>
            <button type="submit" name="calculateCost">Calculate Cost</button>


            <div class="button-container">
                <button type="submit">Complete Transaction</button>
            </div>
        </form>

        <button class="review-button" id="reviewButton" onclick="location.href='review.php'">Leave a Review</button>
    </div>
</body>
</html>