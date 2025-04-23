<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$businessName = getBusinessNameFromUsername($conn, $_SESSION['username']);
$businessID = getBusinessID($conn, $_SESSION['username']);
$services = getAllServices($conn, $businessID);
$subservices = getAllSubservicesForBusiness($conn, $_SESSION['username']);

// Handle delete service action
if (isset($_POST['delete_service'])) {
    $serviceID = $_POST['delete_service'];
    if (deleteService($conn, $serviceID)) {
        // Service deleted successfully, refresh the servive list
        $services = getAllServices($conn, $businessID);
    } else {
        echo "<p style='color:red;'>Failed to delete service.</p>";
    }
}

// Handle delete subservice action
if (isset($_POST['delete_subservice'])) {
    $subserviceID = $_POST['delete_subservice'];
    if (deleteSubservice($conn, $subserviceID)) {
        // Subservice deleted successfully, refresh the subservice list
        $subservices = getAllSubservicesForBusiness($conn, $_SESSION['username']);
    } else {
        echo "<p style='color:red;'>Failed to delete subservice.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Business Home</title>
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
        }

        .main-content h1 {
            text-align: center;
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
            <button onclick="location.href='business_messages.php'">Messages</button>
            <button onclick="location.href='inquiry_approval.php'">Inquiries</button>
            <button onclick="location.href='business_profile.php'">Business Profile</button> 
        </nav>
    </header>

    <div class="main-content">
        <h1 class="business-page-heading"><?php echo $businessName?> Home</h1>

        <h2>Services</h2>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service['serviceName']); ?></td>
                            <td><?php echo htmlspecialchars($service['description']); ?></td>
                            <td>
                                <form method="post">
                                    <button class="delete-button" type="submit" name="delete_service" value="<?php echo htmlspecialchars($service['serviceID']); ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <button class="add-button" onclick="location.href='add_service.php'">Add Service</button>

        <h2>Subservices</h2>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Subservice Name</th>
                        <th>Service</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subservices as $subservice) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subservice['subserviceName']); ?></td>
                            <td><?php echo htmlspecialchars(getServiceName($conn, $subservice['serviceID'])); ?></td>
                            <td><?php echo htmlspecialchars($subservice['description']); ?></td>
                            <td><?php echo htmlspecialchars($subservice['cost']); ?></td>
                            <td>
                                <form method="post">
                                    <button class="delete-button" type="submit" name="delete_subservice" value="<?php echo getSubserviceID($conn, $subservice['serviceID'], $subservice['subserviceName']); ?>">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <button class="add-button" onclick="location.href='add_subservice.php'">Add Subservice</button>
    </div>
</body>
</html>