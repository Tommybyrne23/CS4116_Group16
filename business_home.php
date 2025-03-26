<?php
session_start();
include "functions.php";
$conn = connectToDatabase();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
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

$serviceMessage = '';
$subserviceMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_service'])) {
        $serviceName = $_POST['service_name'];
        $description = $_POST['description'];
        $businessID = $_POST['business_id'];

        if (addService($conn, $serviceName, $description, $businessID)) {
            $serviceMessage = "Service added successfully!";
        } else {
            $serviceMessage = "Error adding service. Please try again.";
        }
    } elseif (isset($_POST['remove_service'])) {
        $serviceID = $_POST['service_id'];
        if (removeService($conn, $serviceID)) {
            $serviceMessage = "Service removed successfully!";
        } else {
            $serviceMessage = "Error removing service. Please try again.";
        }
    } elseif (isset($_POST['add_subservice'])) {
        $subserviceName = $_POST['subservice_name'];
        $description = $_POST['subservice_description'];
        $cost = $_POST['subservice_cost'];
        $serviceID = $_POST['service_id'];
    
        if (addSubservice($conn, $subserviceName, $description, $cost, $serviceID)) {
            $subserviceMessage = "Subservice added successfully!";
        } else {
            $subserviceMessage = "Error adding subservice. Please try again.";
        }
    } elseif (isset($_POST['remove_subservice'])) {
        $subserviceID = $_POST['subservice_id'];
        if (removeSubservice($conn, $subserviceID)) {
            $subserviceMessage = "Subservice removed successfully!";
        } else {
            $subserviceMessage = "Error removing subservice. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $businessName; ?> Home</title>
    <link rel="stylesheet" href="business_home.css">
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='business_home.php'">Home</button>
            <button onclick="location.href='business_messaging.php'">Messages</button>
            <button onclick="location.href='sales_history.php'">Sales History</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading"><?php echo $businessName; ?> Home</h1>

        <h2>Business Services</h2>
        <table border='1'>
            <tr>
                <th>Service Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php
            $services = getBusinessServices($conn, $businessID);
            if (!empty($services)) {
                foreach ($services as $service) {
                    echo "<tr>
                            <td>" . htmlspecialchars($service['serviceName']) . "</td>
                            <td>" . htmlspecialchars($service['description']) . "</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='service_id' value='" . $service['serviceID'] . "'>
                                    <button type='submit' name='remove_service' class='delete-button'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>You have no services on offer! Use the form below to add new services.</td></tr>";
            }
            ?>
        </table>

        <h2>Add a New Service</h2>
        <form method="POST" action="">
            <input type="hidden" name="business_id" value="<?php echo $businessID; ?>">
            <label for="service_name">Service Name:</label>
            <input type="text" id="service_name" name="service_name" maxlength="30" required><br><br>
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="4" cols="50" maxlength="200" required></textarea><br><br>
            <button type="submit" name="add_service" class="add-button">Add Service</button>
        </form>
        <p class="message"><?php echo $serviceMessage; ?></p>


        <h2>Subservices</h2>
        <table border='1'>
            <tr>
                <th>Subservice Name</th>
                <th>Description</th>
                <th>Cost (€)</th>
                <th>Action</th>
            </tr>
            <?php
            $subservices = getBusinessSubservices($conn, $businessID);
            if (!empty($subservices)) {
                foreach ($subservices as $subservice) {
                    echo "<tr>
                            <td>" . htmlspecialchars($subservice['subserviceName']) . "</td>
                            <td>" . htmlspecialchars($subservice['description']) . "</td>
                            <td>" . htmlspecialchars($subservice['cost']) . "</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='subservice_id' value='" . $subservice['subserviceID'] . "'>
                                    <button type='submit' name='remove_subservice' class='delete-button'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>You have no subservices listed! Use the form below to add subservices.</td></tr>";
            }
            ?>
        </table>

        <h2>Add a New Subservice</h2>
        <form method="POST" action="">
            <label for="subservice_name">Subservice Name:</label>
            <input type="text" id="subservice_name" name="subservice_name" maxlength="30" required><br><br>

            <label for="subservice_description">Description:</label><br>
            <textarea id="subservice_description" name="subservice_description" rows="4" cols="50" maxlength="200" required></textarea><br><br>

            <label for="subservice_cost">Cost (€):</label>
            <input type="number" id="subservice_cost" name="subservice_cost" step="0.01" required><br><br>

            <label for="service_id">Main Service:</label>
            <select id="service_id" name="service_id" required>
                <?php
                $services = getBusinessServices($conn, $businessID);
                foreach ($services as $service) {
                    echo "<option value='" . $service['serviceID'] . "'>" . htmlspecialchars($service['serviceName']) . "</option>";
                }
                ?>
            </select><br><br>

            <button type="submit" name="add_subservice" class="add-button">Add Subservice</button>
        </form>
        <p class="message"><?php echo $subserviceMessage; ?></p>
    </div>
</body>
</html>
