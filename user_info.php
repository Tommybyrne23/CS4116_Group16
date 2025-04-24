<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$firstName = getFirstName($conn);
$lastName = getLastName($conn);
$username = getUsername($conn);
$password = getPasswordForUser($conn);
$changeMessage = "";

// Handle changing password
if (isset($_POST['newPassword'])) {
    $newPassword = $_POST['newPassword'];
    $changeResult = changePasswordForUser($conn, $newPassword);
    echo "<p>" . htmlspecialchars($changeResult) . "</p>"; // Display change result
    $password = getPasswordForUser($conn); // Update password variable after change
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - User Profile</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background: rgb(17, 130, 235);
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
            background-color: #333;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #333;
        }

        .profile-info {
            padding: 20px;
            border-radius: 10px;
            flex-grow: 1;
            background: white;
        }

        .profile-info div {
            margin-bottom: 10px;
        }

        .profile-info label {
            display: block;
            margin-bottom: 5px;
        }

        .profile-info input[type="text"],
        .profile-info input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .profile-info button {
            background-color: #333;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .profile-info button:hover {
            background-color: #333;
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
        <div class="sidebar">
            <ul>
                <li><a href="user_info.php">User Info</a></li>
                <li><a href="transaction_history.php">Transaction History</a></li>
                <li><a href="competition_history.php">Competition History</a></li>
            </ul>
        </div>

        <div class="profile-info">
            <form method="post">

                <div>
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" value="<?php echo $firstName; ?>" readonly>
                </div>
                <div>
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" value="<?php echo $lastName; ?>" readonly>
                </div>
                <div>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="text" id="password" name="password" value="<?php echo $password; ?>" readonly>
                </div>
                <div>
                    <label for="newPassword">New Password:</label>
                    <input type="text" id="newPassword" name="newPassword">
                </div>
                <div>
                    <button type="submit">Change Password</button>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>