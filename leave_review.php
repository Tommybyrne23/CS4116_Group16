<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$errorMessage = "";
$successMessage = "";

$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = getUserID($conn, $username);
    $businessID = getBusinessID($conn, getBusinessUsername($conn, trim($_POST["businessName"])));
    $serviceID = getServiceID($conn, $businessID, trim($_POST["serviceName"]));
    $subserviceID = getSubserviceID($conn, $serviceID, trim($_POST["subserviceName"]));
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    // Validate input (add more validation as needed)
    if (empty($userID) || empty($businessID) || empty($serviceID) || empty($subserviceID) || empty($rating) || empty($comment)) {
        $errorMessage = "All fields are required.";
    } else {
        if(leaveReview($conn, $userID, $businessID, $serviceID, $subserviceID, $rating, $comment)){
            $successMessage = "Review successfully submitted.";
        } else{
            $errorMessage = "Failed to leave review. Please try again.";
        }
    }
}
//$conn->close();
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Product Review</title>
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
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1.review-page-heading {
            text-align: center;
        }

        .user-form {
            width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .user-form label {
            display: block;
            margin-bottom: 5px;
        }

        .user-form input[type="text"],
        .user-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .user-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .user-form button:hover {
            background-color: #0056b3;
        }

        .form-toggle {
            margin-top: 10px;
            text-align: center;
        }

        .form-toggle a {
            text-decoration: none;
            color: #007bff;
        }
        .error {
            color: red;
            margin-bottom: 10px; /* Adjust margin for spacing */
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
        <h1 class="review-page-heading" id="formTitle">Leave a Review</h1>

        <form class="user-form" id="userForm" method="post">
            <div class="error"><?php echo $errorMessage; ?></div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="businessName">Business:</label>
            <input type="text" id="businessName" name="businessName" required>

            <label for="serviceName">Service:</label>
            <input type="text" id="serviceName" name="serviceName" required>

            <label for="subserviceName">Subservice:</label>
            <input type="text" id="subserviceName" name="subserviceName" required>

            <label for="rating">Rating (1-5 Stars):</label>
            <input type="number" id="rating" name="rating" required>

            <label for="comment">Comment:</label>
            <input type="text" id="comment" name="comment" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
