<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$businessName = getBusinessNameFromUsername($conn, $_SESSION['username']);
$businessID = getBusinessID($conn, $_SESSION['username']);
$services = getAllServices($conn, $businessID);
$subservices = getAllSubservicesForBusiness($conn, $_SESSION['username']);

?>
<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Business Ratings</title>
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
            text-align: center;
        }

        nav button {  /* Style for the buttons */
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-left: 10px;
            cursor: pointer;
        }

        nav button:hover {
            background-color: #333;
        }

        .main-content {
            padding: 20px;
            display: flex;
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

        .main-content h1 {
            text-align: center;
        }

        h1.ratings-page-heading {
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

        .add-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .add-button:hover {
            background-color: #218838;
        }

        .delete-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .delete-button:hover {
            background-color: #c82333;
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
            <button onclick="location.href='business_profile.php'">Business Profile</button> 
        </nav>
    </header>

    <h1 class="ratings-page-heading"><?php echo $businessName?> Ratings & Reviews</h1>
    
    <div class="main-content">
        <div class="sidebar">
            <ul>
                <li><a href="business_profile.php">Business Info</a></li>
                <li><a href="sales_history.php">Sales History</a></li>
                <li><a href="verified_customers.php">Verified Customers</a></li>
                <li><a href="business_reviews.php">Reviews & Ratings</a></li>
            </ul>
        </div>

        <div class="rating-reviews">
            <div>
                <h2>Average Ratings</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Service Name</th>
                                <th>Subservice Name</th>
                                <th>Average Rating</th> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($services as $service) { ?>
                            <?php 
                            $subservices2 = getSubservicesForService($conn, $service['serviceID']);
                            $subserviceCount = count($subservices2);
                            $firstSubservice = true;
                            ?>
                            <?php foreach ($subservices2 as $index => $subservice2) { ?>
                                <tr>
                                    <?php if ($index === 0) { // Only print Service Name on first row ?>
                                        <td rowspan="<?php echo $subserviceCount; ?>">
                                            <?php echo htmlspecialchars($service['serviceName']); ?>
                                        </td>
                                    <?php } ?>
                                    <td><?php echo htmlspecialchars($subservice2['subserviceName']); ?></td>
                                    <td><?php echo getAverageRating($conn, $subservice2['subserviceID']);?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
        <div style="width: 40px;"></div>
        <div>
            <div>
                <h2>Subservice Reviews</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Subservice Name</th>
                                <th>Customer</th>
                                <th>Review</th> 
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($subservices as $subservice) { ?>
                            <?php 
                            $reviews = getReviews($conn, $subservice['subserviceID']);
                            $reviewCount = count($reviews);
                            ?>
                            <?php if ($reviewCount > 0) { ?>
                                <?php foreach ($reviews as $index => $review) { ?>
                                    <tr>
                                        <?php if ($index === 0) { // Only print Subservice Name on first row ?>
                                            <td rowspan="<?php echo $reviewCount; ?>">
                                                <?php echo htmlspecialchars($subservice['subserviceName']); ?>
                                            </td>
                                        <?php } ?>
                                        <td><?php echo htmlspecialchars(getUsernameFromUserID($conn, $review['userID'])) ?? "-"; ?></td>
                                        <td><?php echo htmlspecialchars($review['comment']) ?? "-"; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subservice['subserviceName']); ?></td>
                                    <td colspan="2">-</td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</body>
</html>