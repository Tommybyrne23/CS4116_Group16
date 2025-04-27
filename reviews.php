<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$userID = getUserID($conn);
$businesses = getAllBusinesses($conn); 

// Handle message request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiverID'])) {
    $receiverID = intval($_POST['receiverID']);
    //echo "<!-- receiverID:" . $receiverID . " | currentUser: " . $userID . "-->";
    if (sendMessageRequest($conn, $userID, $receiverID)) {
        $success = "Message request sent.";
    } else {
        $error = "Failed to send message request.";
    }
}
?>
<html lang="en">
<head>
    <title>Reviews</title>
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

        h2 {
            margin-top: 30px;
            background: #f0f0f0;
            padding: 10px;
            background: rgb(17, 130, 235);
            //text-align: center;
            margin-left: 50px;
            color: white;
        }

        .main-content {
            display: flex;
            padding: 20px;
        }

        .review {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }
        .review-list {
            width: 90%;
            margin: 0 auto 20px auto; /* top: 0, sides: auto, bottom: 20px */
            border-collapse: separate; /* SUPER important! */
            border-spacing: 0; /* No gaps */
            border-radius: 10px; /* Round corners */
            overflow: hidden; /* Clip inner stuff */
            background-color: white; /* Helps in some cases */
        }

        .review-list th, .review-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .review-list th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .review-list td {
            background-color: white;
        }

        .review-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .review p {
            margin: 5px 0;
        }

        .request-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .request-button:hover {
            background-color: #0056b3;
        }

        .status {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .status.success {
            background-color: #d4edda;
            color: #155724;
        }

        .status.error {
            background-color: #f8d7da;
            color: #721c24;
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

        <h1 style="text-align: center; color: white;">Business Reviews & Message Requests</h1>

        <?php if (isset($success)): ?>
            <div class="status success"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif (isset($error)): ?>
            <div class="status error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php foreach ($businesses as $business): ?>
        <div>
            <h2><?php echo htmlspecialchars($business['name']); ?></h2>
            <?php 
                $reviews = getReviewsForBusiness($conn, $business['businessID']); 
                if (empty($reviews)) {
                    echo "<p style='text-align: center; color:white;'>No reviews yet.</p>";
                }
            ?>
            <table class="review-list">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Service</th>
                        <th>Subservice</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                            <td><?php echo htmlspecialchars($review['serviceName']); ?></td>
                            <td><?php echo htmlspecialchars($review['subserviceName']); ?></td>
                            <td><?php echo htmlspecialchars($review['rating']); ?></td>
                            <td><?php echo htmlspecialchars($review['comment']); ?></td>
                            <?php 
                                //echo "<!-- DEBUG: Username in review is '{$review['username']}' -->";
                                $username = $review['username'];
                                $receiverID = getUserIDFromUsername($conn, $username);
                            ?>
                            <td>
                            <?php if ($receiverID && $receiverID != $userID): ?>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="receiverID" value="<?php echo $receiverID; ?>">
                                    <button class="request-button" type="submit">Request to Message</button>
                                </form>
                            <?php else: ?>
                                <span style="color: red;">Cannot message yourself.</span>
                            <?php endif; ?>
                            </td>
                        </tr>        
        <?php endforeach; ?>
        </tbody>
        </table>
        </div>
    <?php endforeach; ?>
</body>
</html>