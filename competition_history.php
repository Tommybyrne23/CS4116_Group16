<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$competitions = getCompsForUser($conn, getUserID($conn)); //make a get userID function

$firstName = getFirstName($conn);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Competition History</title>
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

        .competition-history {
            flex-grow: 1;
        }

        .competition-history h2 {
            margin-bottom: 10px;
        }

        .competition-list {
            width: 100%;
            border-collapse: collapse;
        }

        .competition-list th,
        .competition-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .competition-list th {
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

        <div class="competition-history">
            <h2><?php echo $firstName . "'s Competition History"?></h2>

            <table class="competition-list">
                <thead>
                    <tr>
                        <th>Competition</th>
                        <th>Date</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <?php foreach ($competitions as $competition) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($competition['compName']); ?></td>
                        <td><?php echo htmlspecialchars($competition['date']); ?></td>
                        <td><?php echo htmlspecialchars($competition['location']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>