<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$transactions = getTransactionsForBusiness($conn);
$businessID = getBusinessID($conn, $_SESSION['username']); 
$businessName = getBusinessNameFromUsername($conn, $_SESSION['username']);
//echo "Business ID: " . $businessID . "<br>";
$customers = getCustomers($conn, $businessID);
//may need to display something to show banned user was someone who made a transaction
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Verified Customers</title>
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
            display: flex;
            padding: 20px;
        }

        .sidebar {
            width: 200px;
            margin-right: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            background-color: #555;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #333;
        }

        .verified-customers {
            flex-grow: 1;
        }

        .verified-customers h2 {
            margin-bottom: 10px;
        }

        .customers-list {
            width: 100%;
            border-collapse: collapse;
        }

        .customers-list th,
        .customers-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .customers-list th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='homepage.php'">Logout</button>
            <button onclick="location.href='business_home.php'">Home</button>
            <button onclick="location.href='business_messages.php'">Messages</button>
        </nav>
    </header>

    <div class="main-content">
        <div class="sidebar">
            <ul>
                <li><a href="business_profile.php">Business Info</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="verified_customers.php">Verified Customers</a></li>
                <li><a href="business_reviews.php">Reviews & Ratings</a></li>
            </ul>
        </div>

        <div class="verified-customers">
            <h2><?php echo $businessName . "'s Verified Customers"?></h2>

            <table class="customers-list">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <?php foreach ($customers as $customer) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getUsernameFromUserID($conn, $customer['userID'])); ?></td>
                        <td><?php echo htmlspecialchars(getFirstNameFromUserID($conn, $customer['userID'])); ?></td>
                        <td><?php echo htmlspecialchars(getLastNameFromUserID($conn, $customer['userID'])); ?></td>
                        <td><?php echo "$" . htmlspecialchars(getTotalSpent($conn, $customer['userID'])); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    
</body>
</html>