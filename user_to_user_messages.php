<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$sender = getUserID($conn);
$users = getAllUsers($conn);
//$verifiedUsers = [];

for ($i = 0; $i < count($users); $i++) {
    // Get the current user
    $currentUser = $users[$i];

    // Check if the user is verified using the isVerified() function
    if (isVerified($conn, $currentUser['username'])) {
        // Add the user to the $verifiedUsers array
        $verifiedUsers[] = $currentUser;
    }
}

if (is_array($verifiedUsers)) {
    foreach ($verifiedUsers as $user) {
        if (is_array($user)) {
            foreach ($user as $key => $value) {
                echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
            }
            echo "<br>"; // Add a line break between businesses
        } else {
            echo "customer data is not an array.<br>";
        }
    }
} else {
    echo "customers array is not an array.<br>";
}

$receiver = isset($_GET['receiver']) 
    ? $_GET['receiver'] 
    : (!empty($users) ? $users[0]['username'] : null);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Message Verified Users</title>
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
            width: 250px;
            margin-right: 20px;
        }

        .sidebar h2 {
            margin-top: 0;
            margin-bottom: 10px;
            text-align: center;
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

        .profile-info h2 {
            margin-top: 0;
        }

        .message-view {
            flex: 1;
            border: none;
            width: 100%;
            height: 100%;
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
            <h2>Verified Users</h2>
            <ul>
                <?php if (!empty($users)): ?>
                    <?php foreach ($verifiedUsers as $user): ?>
                        <li>
                            <a 
                                href="?receiver=<?php echo urlencode($user['username']); ?>"
                                class="<?php echo ($user['username'] === $receiver) ? 'active' : ''; ?>"
                            >
                                <?php echo htmlspecialchars($user['username']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No users found.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div style="flex-grow: 1;">
            <iframe class="message-view" src="user_message_user.php?receiver=<?php echo urlencode($receiver); ?>" onload="resizeIframe()"></iframe>
        </div>
    </div>
</body>
</html>