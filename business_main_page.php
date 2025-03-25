<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="business_main_page_styles.css">

    <title>Business Services</title>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='business_home.html'">Home</button> 
            <button onclick="location.href='business_messaging.html'">Messages</button>
            <button onclick="location.href='sales_history.html'">Sales History</button> 
        </nav>
    </header>

    <div class="main-content">

    <h1 class="service-page-heading">Soccer Starz Home</h1>

    <h2>Business Services</h2>

    <form method="GET" action="">
        <label for="business_id">Enter Business ID:</label>
        <input type="number" id="business_id" name="business_id" required>
        <button type="submit">Display Services</button>
    </form>

    <?php
    if (isset($_GET['business_id'])) {
        include "config.php";
        $businessID = $_GET['business_id'];

        $sql = "SELECT serviceID, serviceName, description FROM Services WHERE businessID = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $businessID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>Services for Business ID: $businessID</h2>";
                echo "<table border='1'>
                        <tr>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['serviceName']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>
                                <form action='remove_service.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='service_id' value='" . $row['serviceID'] . "'>
                                    <button type='submit' class='delete-button'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No services found for Business ID: $businessID</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
        }
        $conn->close();
    }
    ?>

    <h2>Add a New Service</h2>
    <form action="add_service.php" method="POST">
        <input type="hidden" name="business_id" value="<?php echo isset($_GET['business_id']) ? $_GET['business_id'] : ''; ?>">
        
        <label for="service_name">Service Name:</label>
        <input type="text" id="service_name" name="service_name" maxlength="30" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4" cols="50" maxlength="200" required></textarea><br><br>

        <button type="submit" class="add-button">Add Service</button>
    </form>



    <h2>Subservices</h2>
<table border='1'>
    <tr>
        <th>Subservice Name</th>
        <th>Description</th>
        <th>Cost (€)</th>
        <th>Action</th>
    </tr>
    <?php
    if (isset($_GET['business_id'])) {
        include "config.php";
        $businessID = $_GET['business_id'];

        // Fetch subservices for this business's services
        $sql = "SELECT ss.subserviceID, ss.subserviceName, ss.description, ss.cost 
                FROM subservices ss
                INNER JOIN services s ON ss.serviceID = s.serviceID
                WHERE s.businessID = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $businessID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['subserviceName']) . "</td>
                            <td>" . htmlspecialchars($row['description']) . "</td>
                            <td>" . htmlspecialchars($row['cost']) . "</td>
                            <td>
                                <form action='remove_subservice.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='subservice_id' value='" . $row['subserviceID'] . "'>
                                    <button type='submit' class='delete-button'>Remove</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No subservices found.</td></tr>";
            }

            $stmt->close();
        } else {
            echo "<p>Error fetching subservices: " . $conn->error . "</p>";
        }
        
        $conn->close();
    }
    ?>
</table>

    <h2>Add a New Subservice</h2>
<form action="add_subservice.php" method="POST">
    <label for="service_id">Business ID:</label>
    <input type="number" id="service_id" name="service_id" required><br><br>

    <label for="subservice_name">Subservice Name:</label>
    <input type="text" id="subservice_name" name="subservice_name" maxlength="30" required><br><br>

    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="50" maxlength="200" required></textarea><br><br>

    <label for="cost">Cost:</label>
    <input type="number" id="cost" name="cost" step="0.01" required><br><br>

    <button type="submit" class="add-button">Add Subservice</button>
</form>



    </div>


</body>
</html>
