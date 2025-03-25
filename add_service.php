<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $businessID = $_POST['business_id'];
    $serviceName = $_POST['service_name'];
    $description = $_POST['description'];

    // Validate input lengths
    if (strlen($serviceName) > 30 || strlen($description) > 200) {
        die("Error: Input exceeds allowed length.");
    }

    // Insert new service into the database
    $sql = "INSERT INTO services (serviceName, description, businessID) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $serviceName, $description, $businessID);

        if ($stmt->execute()) {
            // Service added successfully
            // Redirect back to the business main page
            header("Location: business_main_page.php?business_id=$businessID");
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



