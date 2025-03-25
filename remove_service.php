<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the service ID from the form
    $serviceID = $_POST['service_id'];

    // Delete the service from the database
    $sql = "DELETE FROM services WHERE serviceID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $serviceID);

        if ($stmt->execute()) {
            // Service deleted successfully!
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


