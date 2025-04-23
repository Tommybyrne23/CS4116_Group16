<?php
//echo "message user page"
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

//$businessID = getUserID($conn);
//$sender = "user";
$businessUsername = $_SESSION['username'];
//echo $_SESSION['username'];
$businessID = getBusinessID($conn, $businessUsername);
$userUsername = isset($_GET['user']) ? $_GET['user'] : null;
$userID = getUserIDFromUsername($conn, $userUsername);

if (!$userID) {
    echo "No users selected.";
    exit;
}

// Handle sending message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        sendMessage($conn, $userID, $businessID, $message, "business");
    }
}

// Fetch all messages between the user and business
$messages = getMessages($conn, $userID, $businessID);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages with <?php getFirstNameFromUserID($conn, $userID)?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: sans-serif;
            background-color: #f5f5f5;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 15px;
            background-color: #e0e0e0;
            position: relative;
            clear: both;
        }

        .message.user {
            align-self: flex-end;
            background-color: #007bff;
            color: white;
            text-align: right;
        }

        .message.business {
            align-self: flex-start;
            background-color: #ffffff;
            border: 1px solid #ccc;
        }

        .message-time {
            font-size: 0.75em;
            color: #666;
            margin-top: 5px;
        }

        .message-form {
            border-top: 1px solid #ccc;
            padding: 10px 20px;
            background-color: #fff;
        }

        .message-form form {
            display: flex;
            gap: 10px;
        }

        .message-form input[type="text"] {
            flex-grow: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .message-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .message-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="messages">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message <?php echo $msg['sender'] === 'business' ? 'user' : 'business'; ?>">
                    <div><?php echo htmlspecialchars($msg['message']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages yet.</p>
        <?php endif; ?>
    </div>

    <div class="message-form">
        <form method="POST">
            <input type="text" name="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </div>
</div>
</body>
</html>