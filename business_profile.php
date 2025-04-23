<?php
//echo 'business profile page, put sales history in here';
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$username = getUsername($conn);
$businessID = getBusinessID($conn, $username);
$name = getBusinessName($conn, $businessID);
$password = getPasswordForBusiness($conn);

// Handle changing password
if (isset($_POST['newPassword'])) {
    $newPassword = $_POST['newPassword'];
    $changeResult = changePasswordForBusiness($conn, $newPassword);
    $password = getPasswordForBusiness($conn); // Update password variable after change
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

        .profile-info {
            flex-grow: 1;
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
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .profile-info button:hover {
            background-color: #0056b3;
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
            <button onclick="location.href='inquiry_approval.php'">Inquiries</button>
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

        <div class="profile-info">
            <form method="post">

                <div>
                    <label for="businessName">Business Name:</label>
                    <input type="text" id="businessName" name="businessName" value="<?php echo $name; ?>" readonly>
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