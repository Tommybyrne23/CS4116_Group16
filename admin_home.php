<?php
//This is the admin home page.
session_start();
require_once 'functions.php';

$conn = connectToDatabase();

// Handle Logout
if (isset($_POST['logout'])) {
    logout(); // Call the logout function
}

// Get all users
$users = getAllUsers($conn);

// Get all reviews
$reviews = getAllReviews($conn);

// Handle ban user action
if (isset($_POST['ban_user'])) {
    $username = $_POST['ban_user'];
    if (banUser($conn, $username)) {
        // User banned successfully, refresh the user list
        $users = getAllUsers($conn);
    } else {
        echo "<p style='color:red;'>Failed to ban user.</p>";
    }
}

// Handle delete review action
if (isset($_POST['delete_review'])) {
    $comment = $_POST['delete_review'];
    if (deleteReview($conn, $comment)) {
        // Review deleted successfully, refresh the review list
        $reviews = getAllReviews($conn);
    } else {
        echo "<p style='color:red;'>Failed to delete review.</p>";
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Admin Home</title>
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

        .main-content {
            display: flex;
            padding: 20px;
        }

        .section {
            flex: 1;
            margin-right: 20px;
        }

        h1.service-page-heading {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .ban-button, .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-container{
            display: flex;
            justify-content: center; /* Centers horizontally */
            align-items: center; /* Centers vertically (if needed) */
        }

        .logout-button{
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-left: 10px;
            cursor: pointer;
        }

        .ban-button:hover, .delete-button:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace- Admin</h1>
            <div class="logout-container">
                <form method="post" action="admin_home.php">
                    <button class="logout-button" type="submit" name="logout">Logout</button>
                </form>
            </div>
    </header>

    <div class="main-content">
        <div class="section">
            <h1 class="service-page-heading">User Management</h1>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <form method="post">
                                    <button class="ban-button" type="submit" name="ban_user" value="<?php echo htmlspecialchars($user['username']); ?>">Ban</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h1 class="service-page-heading">Review Management</h1>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Review</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                            <td><?php echo htmlspecialchars($review['comment']); ?></td>
                            <td>
                                <form method="post">
                                    <button class="delete-button" type="submit" name="delete_review" value="<?php echo htmlspecialchars($review['comment']); ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
