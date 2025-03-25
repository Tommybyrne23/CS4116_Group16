<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the subservice ID from the form
    $subserviceID = $_POST['subservice_id'];

    // Delete the subservice from the database
    $sql = "DELETE FROM subservices WHERE subserviceID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $subserviceID);

        if ($stmt->execute()) {
            // Redirect back to the main page with a success message
            header("Location: business_main_page.php?success=subservice_removed");
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