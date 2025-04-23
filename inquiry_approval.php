<?php
//echo "inquiry approval page-- have an approve & deny button (if approved add to transactions & if denied delete from inquiries)";

//This is the admin home page.
session_start();
require_once 'functions.php';
isLoggedIn();
$conn = connectToDatabase();

$businessID = getBusinessID($conn, $_SESSION['username']);

// Get all inquiries
$inquiries = getInquiriesForBusiness($conn, $businessID);

// echo "<pre>";
// print_r($inquiries);
// echo "</pre>";

// Handle deny inquiry action
if (isset($_POST['deny_inquiry'])) {
    $inquiryID = $_POST['deny_inquiry'];
    if (deleteInquiry($conn, $inquiryID)) {
        // User banned successfully, refresh the user list
        $inquiries = getInquiriesForBusiness($conn, $businessID);
    } else {
        echo "<p style='color:red;'>Failed to delete inquiry.</p>";
    }
}

// Handle approve inquiry action
if (isset($_POST['approve_inquiry'])) {
    $inquiryID = $_POST['approve_inquiry'];
    if (approveInquiry($conn, $inquiryID)) {
        // inquiry approved & transaction added to transaction table & inquiry deleted from inquiries table
        $inquiries = getAllInquiriesForBusiness($businessID);
    } else {
        echo "<p style='color:green;'>Failed to approve inquiry.</p>";
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Inquiries</title>
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

        .section {
            flex: 1;
            margin-right: 20px;
        }

        h1.inquiry-page-heading {
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

        .deny-button {
            background-color: red;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .approve-button {
            background-color: green;
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

        .deny-button:hover, .approve-button:hover {
            background-color: #c82333;
        }

    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='login.php'">Logout</button> 
            <button onclick="location.href='business_messages.php'">Messages</button>
            <button onclick="location.href='business_profile.php'">Business Profile</button> 
        </nav>
    </header>

    <div class="main-content">
        <div class="section">
            <h1 class="inquiry-page-heading">Inquiry Management</h1>

            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Service</th>
                        <th>Subservice</th>
                        <th>Inquiry</th>
                        <th>Normal Cost</th>
                        <th>Proposed Price</th>
                        <th>Approve</th>
                        <th>Deny</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <?php
                            $conn = connectToDatabase();
                            $username = getUsernameFromUserID($conn, $inquiry['userID']);
                            $serviceName = getServiceName($conn, $inquiry['serviceID']);
                            $subserviceName = getSubserviceName($conn, $inquiry['subserviceID']);
                            $normalCost = getCost($conn, $inquiry['subserviceID']);
                        ?>
                     <tr>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><?php echo htmlspecialchars($serviceName); ?></td>
                            <td><?php echo htmlspecialchars($subserviceName); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['message']); ?></td>
                            <td><?php echo '$' . htmlspecialchars($normalCost); ?></td>
                            <td><?php echo '$' . htmlspecialchars($inquiry['proposed_price']); ?></td>
                            <td>
                                <form method="post">
                                    <button class="approve-button" type="submit" name="approve_inquiry" value="<?php echo htmlspecialchars($inquiry['inquiryID']); ?>">Approve</button>
                                </form>
                            </td>
                            <td>
                                <form method="post">
                                    <button class="deny-button" type="submit" name="deny_inquiry" value="<?php echo htmlspecialchars($inquiry['inquiryID']); ?>">Deny</button>
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
