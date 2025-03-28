<?php
session_start();
include "functions.php";
$conn = connectToDatabase();

if (!isset($_SESSION['username'])) {
    die("Error: User not logged in.");
}

$businessUsername = $_SESSION['username'];
$stmt = $conn->prepare("SELECT businessID, name FROM Businesses WHERE username = ?");
$stmt->bind_param("s", $businessUsername);
$stmt->execute();
$result = $stmt->get_result();
$businessData = $result->fetch_assoc();
$businessID = $businessData['businessID'];
$businessName = $businessData['name'];
$stmt->close();

$salesHistory = getSalesHistory($conn, $businessID);
$topCustomers = getTopCustomers($conn, $businessID);
$topServices = getTopSellingServices($conn, $businessID);
$topSubservices = getTopSellingSubservices($conn, $businessID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel=stylesheet href="sales_history.css">
    <title>Sales History</title>

</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='business_home.php'">Home</button> 
            <button onclick="location.href='business_messaging.php'">Messages</button> 
        </nav>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading"><?php echo $businessName  ?> Sales History</h1>

        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Service</th>
                    <th>Subservice</th>
                    <th>Total (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($salesHistory)): ?>
                    <?php foreach ($salesHistory as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['customerName']); ?></td>
                            <td><?php echo htmlspecialchars($sale['serviceName']); ?></td>
                            <td><?php echo htmlspecialchars($sale['subserviceName'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($sale['cost'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No sales history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Top Customers</h2>
<table>
    <thead>
        <tr>
            <th>Customer Name</th>
            <th>Total Spent (€)</th>
            <th>Number of Purchases</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($topCustomers)): ?>
            <?php foreach ($topCustomers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['firstName'] . ' ' . $customer['lastName']); ?></td>
                    <td><?php echo htmlspecialchars($customer['total_spent']); ?></td>
                    <td><?php echo htmlspecialchars($customer['num_purchases']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">No customer data available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Top Selling Services</h2>
<table>
    <thead>
        <tr>
            <th>Service Name</th>
            <th>Number Sold</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($topServices)): ?>
            <?php foreach ($topServices as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['serviceName']); ?></td>
                    <td><?php echo htmlspecialchars($service['num_sold']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No service data available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Top Selling Subservices</h2>
<table>
    <thead>
        <tr>
            <th>Subservice Name</th>
            <th>Number Sold</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($topSubservices)): ?>
            <?php foreach ($topSubservices as $subservice): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subservice['subserviceName']); ?></td>
                    <td><?php echo htmlspecialchars($subservice['num_sold']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No subservice data available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

    </div>

    

</body>
</html>

