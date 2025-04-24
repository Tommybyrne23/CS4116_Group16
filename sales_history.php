<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$transactions = getTransactionsForBusiness($conn);
$businessID = getBusinessID($conn, $_SESSION['username']); 
$services = getAllServices($conn, $businessID);
$subservices = getAllSubservicesForBusiness($conn, $_SESSION['username']);
$businessName = getBusinessNameFromUsername($conn, $_SESSION['username']);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Sales History</title>
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

        .sales-history {
            flex-grow: 1;
        }

        .sales-history h2 {
            margin-bottom: 10px;
        }

        .sales-list {
            width: 100%;
            border-collapse: collapse;
        }

        .sales-list th,
        .sales-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .sales-list th {
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

        <div class="sales-history">
            <h2><?php echo $businessName . "'s Sales History"?></h2>

            <table class="sales-list">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Subservice</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <?php foreach ($transactions as $transaction) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getUsernameFromUserID($conn, $transaction['userID'])); ?></td>
                        <td><?php echo htmlspecialchars(getServiceName($conn, $transaction['serviceID'])); ?></td>
                        <td><?php echo htmlspecialchars(getSubserviceName($conn, $transaction['subserviceID'])); ?></td>
                        <td><?php echo "$" . htmlspecialchars(getCost($conn, $transaction['subserviceID'])); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <div class="main-content">
        <div class="sales-history">
            <h2><?php echo "Total Service Sales"?></h2>

            <table class="sales-list">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Sales Count</th>
                        <th>Total Income</th>
                    </tr>
                </thead>
                <?php foreach ($services as $service) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getServiceName($conn, $service['serviceID'])); ?></td>
                        <td><?php echo htmlspecialchars(getSoldServicesCount($conn, $service['serviceID'])); ?></td>
                        <td><?php echo "$" . (getServiceRevenue($conn, $service['serviceID']) ?? 0); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <div class="main-content">
        <div class="sales-history">
            <h2><?php echo "Total Subservice Sales"?></h2>

            <table class="sales-list">
                <thead>
                    <tr>
                        <th>Subservice</th>
                        <th>Sales Count</th>
                        <th>Total Income</th>
                    </tr>
                </thead>
                <?php foreach ($subservices as $subservice) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getSubserviceName($conn, $subservice['subserviceID'])); ?></td>
                        <td><?php echo htmlspecialchars(getSoldSubservicesCount($conn, $subservice['subserviceID'])); ?></td>
                        <td><?php echo "$" . (getSubserviceRevenue($conn, $subservice['subserviceID']) ?? 0); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>