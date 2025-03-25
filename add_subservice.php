<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceID = $_POST['service_id'];
    $subserviceName = $_POST['subservice_name'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];

    // Validate input lengths
    if (strlen($subserviceName) > 30 || strlen($description) > 200) {
        die("Error: Input exceeds allowed length.");
    }

    // Insert new subservice into the database
    $sql = "INSERT INTO subservices (subserviceName, description, cost, serviceID) VALUES (?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssdi", $subserviceName, $description, $cost, $serviceID);

        if ($stmt->execute()) {
            // Redirect back to the main page with a success message
            header("Location: business_main_page.php?success=subservice_added");
            exit();
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
}
?>
