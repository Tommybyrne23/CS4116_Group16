<?php
//maybe make the first business in the list the default page, then send all others to same page w/business assigned
//sender will always be business on this page
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$sender = 'business';
$users = getAllUsers($conn); // Assumes this returns array of businesses

$defaultUser = isset($_GET['user']) 
    ? $_GET['user'] 
    : (!empty($users) ? $users[0]['username'] : null);
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Message Users</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background: #2e8b57;
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
            max-width: 1200px;         
            margin: 40px auto;        
            background: white;         
            border-radius: 10px;       
            padding: 32px 24px 32px 24px;
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
            <button onclick="location.href='business_messages.php'">Messages</button>
            <button onclick="location.href='inquiry_approval.php'">Inquiries</button>
            <button onclick="location.href='business_profile.php'">Business Profile</button>
        </nav>
    </header>

    <div class="main-content">
        <div class="sidebar">
            <h2>Users</h2>
            <ul>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <li>
                            <a 
                                href="?user=<?php echo urlencode($user['username']); ?>"
                                class="<?php echo ($user['username'] === $defaultUser) ? 'active' : ''; ?>"
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
            <iframe class="message-view" src="message_user.php?user=<?php echo urlencode($defaultUser); ?>" onload="resizeIframe()"></iframe>
        </div>
    </div>
</body>
</html>