<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$transactions = getTransactionsForUser($conn); 

$firstName = getFirstName($conn);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Transaction History</title>
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

        .transaction-history {
            flex-grow: 1;
        }

        .transaction-history h2 {
            margin-bottom: 10px;
        }

        .transaction-list {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-list th,
        .transaction-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .transaction-list th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='login.php'">Logout</button>
            <button onclick="location.href='user_home.php'">Home</button>
            <button onclick="location.href='user_messages.php'">Messages</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="main-content">
        <div class="sidebar">
            <ul>
                <li><a href="user_info.php">User Info</a></li>
                <li><a href="transaction_history.php">Transaction History</a></li>
                <li><a href="competition_history.php">Competition History</a></li>
            </ul>
        </div>

        <div class="transaction-history">
            <h2><?php echo $firstName . "'s Transaction History"?></h2>

            <table class="transaction-list">
                <thead>
                    <tr>
                        <th>Business</th>
                        <th>Service</th>
                        <th>Subservice</th>
                        <th>Cost</th>
                        <th>Reviews</th>
                    </tr>
                </thead>
                <?php foreach ($transactions as $transaction) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getBusinessName($conn, $transaction['businessID'])); ?></td>
                        <td><?php echo htmlspecialchars(getServiceName($conn, $transaction['serviceID'])); ?></td>
                        <td><?php echo htmlspecialchars(getSubserviceName($conn, $transaction['subserviceID'])); ?></td>
                        <td><?php echo "$" . htmlspecialchars($transaction['total']); ?></td>
                        <td>
                            <a href="leave_review.php" class="review-link">Leave Review</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>