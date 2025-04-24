<?php
//make inquiry page
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$username = $_SESSION['username'];
$userID = getUserID($conn, $username);
$businessID = getBusinessID($conn, $_SESSION['selectedBusiness']);
$business = getBusinessName($conn, $businessID);
$serviceID = $_SESSION['selectedService'];
$service = getServiceName($conn, $serviceID);
$subserviceID = $_SESSION['selectedSubservice'];
$subservice = getSubserviceName($conn, $subserviceID);
$cost = getCost($conn, $subserviceID);

$errorMessage = "";
$successMessage = "";

if (isset($_POST['submitInquiry'])) {
    header("Location: make_inquiry.php?submitted=true"); // Redirect
    $message = $_POST['message'];
    $proposedPrice = $_POST['proposedPrice'];
    if (sendInquiry($conn, $userID, $businessID, $serviceID, $subserviceID, $message, $proposedPrice)) { //. CREATE sendInquiry() function
        $successMessage = "Inquiry successfully submitted.";
        $_POST['message'] = '';
        $_POST['proposedPrice'] = '';
    } else {
        $errorMessage = "Inquiry submission failed.";
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Inquiry</title>
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
        }

        h1.inquiry-page-heading {
            text-align: center;
        }

        #totalCost {
            text-align: center;
        }

        .inquiry-form {
            width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .inquiry-form label {
            display: block;
            margin-bottom: 5px;
        }

        .inquiry-form input[type="text"],
        .inquiry-form input[type="number"],
        .inquiry-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .inquiry-form .button-container {
            text-align: center;
        }

        .inquiry-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .inquiry-form button:hover {
            background-color: #0056b3;
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
            <button onclick="location.href='user_inquiries.php'">Inquiries</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="inquiry-page-heading">Make Inquiry</h1>

        <form class="inquiry-form" id="inquiryForm" method="post">
            <div class="error"><?php echo $errorMessage; ?></div>
            <div class="success"><?php echo $successMessage; ?></div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label for="cardNumber">Card Number:</label>
            <input type="number" id="cardNumber" name="cardNumber" required> 

            <label for="business">Business:</label>
            <input type="text" id="business" name="business" value="<?php echo htmlspecialchars($business); ?>" readonly>

            <label for="service">Service:</label>
            <input type="text" id="service" name="service" value="<?php echo htmlspecialchars($service); ?>" readonly>

            <label for="subservice">Subservice:</label>
            <input type="text" id="subservice" name="subservice" value="<?php echo htmlspecialchars($subservice); ?>" readonly>

            <label for="normalPrice">Normal Price:</label>
            <input type="number" id="normalPrice" name="normalPrice" value="<?php echo $cost; ?>" readonly>

            <label for="proposedPrice">Proposed Price:</label>
            <input type="number" step="0.01" id="proposedPrice" name="proposedPrice" required>

            <label for="message">Message:</label>
            <input type="text" id="message" name="message" required>

            <div class="button-container">
                <button type="submit" name="submitInquiry">Submit Inquiry</button>
            </div>
        </form>
    </div>
</body>
</html>