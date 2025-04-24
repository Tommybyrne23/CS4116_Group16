<?php
//maybe make the first business in the list the default page, then send all others to same page w/business assigned
//sender will always be user on this page
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$sender = 'user';
$businesses = getAllBusinesses($conn); // Assumes this returns array of businesses

$defaultBusiness = isset($_GET['business']) 
    ? $_GET['business'] 
    : (!empty($businesses) ? $businesses[0]['username'] : null);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Message Businesses</title>
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
            <h2>Businesses</h2>
            <ul>
                <?php if (!empty($businesses)): ?>
                    <?php foreach ($businesses as $business): ?>
                        <li>
                            <a 
                                href="?business=<?php echo urlencode($business['username']); ?>"
                                class="<?php echo ($business['username'] === $defaultBusiness) ? 'active' : ''; ?>"
                            >
                                <?php echo htmlspecialchars($business['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No businesses found.</li>
                <?php endif; ?>
            </ul>
        </div>
        <div style="flex-grow: 1;">
            <iframe class="message-view" src="message_business.php?business=<?php echo urlencode($defaultBusiness); ?>" onload="resizeIframe()"></iframe>
        </div>
    </div>
</body>
</html>