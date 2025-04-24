<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$serviceID = $_SESSION['selectedService']; //was set in the service page (also figure out how to reset each time you navigate back to home page)

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
$minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : null;
$maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : null;

if ($keyword || $minPrice || $maxPrice) {
    $subservices = searchSubservices($conn, $serviceID, $keyword, $minPrice, $maxPrice);
} else {
    $subservices = getAllSubservices($conn, $serviceID);
}

if (is_array($subservices) && !empty($subservices)) {
    // subservices found, proceed to display them
} else {
    //echo "<p>No subservices found for this business.</p>";  //businesses have to exist to pop up, but they don't necessarily have to have services to exist
    $subservices = []; // Ensure $subservices is an array
}

// Handle form submission
if (isset($_POST['selectedSubservice'])) {
    $_SESSION['selectedSubservice'] = $_POST['selectedSubservice'];

    if (isset($_POST['purchase'])) {
        header("Location: transaction.php");
        exit;
    }

    if (isset($_POST['inquiry'])) {
        header("Location: make_inquiry.php");
        exit;
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld Marketplace</title>
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

        .search-bar {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f0f0f0; /* Optional: Add background color */
        }

        .search-bar input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            flex-grow: 1; /* Make input expand to fill available space */
            margin-right: 10px;
        }

        .search-bar input[type="number"] {
            padding: 8px;
            border: 1px solid #ddd;
            flex-grow: 1; /* Make input expand to fill available space */
            margin-right: 10px;
        }

        .search-bar button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }

        .subservice_listings {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5 columns for 10 businesses */
            gap: 20px;
        }

        .subservice-container {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            cursor: pointer;
        }

        .subservice-container button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='homepage.php'">Logout</button>
            <button onclick="location.href='user_home.php'">Home</button>
            <button onclick="location.href='user_messages.php'">Messages</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="search-bar">
        <form method = "GET">
            <input type="text" id="searchInput" name="keyword" placeholder="Search subservices..." value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
            <input type="number" name="minPrice" name="minPrice" placeholder="Minimum Price" value="<?php echo htmlspecialchars($_GET[$minPrice] ?? ""); ?>" style="margin-left:10px;">
            <input type="number" name="maxPrice" name="maxPrice" placeholder="Maximum Price" value="<?php echo htmlspecialchars($_GET[$maxPrice] ?? ""); ?>" style="margin-left:10px;">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="subservice_listings">
        <?php
            if (is_array($subservices) && !empty($subservices)) {
                foreach ($subservices as $subservice) {
                    if (is_array($subservice)) {
                        $reviews = getReviewsForSubservice($conn, $subservice['subserviceID']);
                        ?>
                        <form method="post">
                            <div class="subservice-container">
                                <h2><?php echo htmlspecialchars($subservice['subserviceName']); ?></h2>
                                <p><?php echo htmlspecialchars($subservice['description']); ?></p>
                                <p><?php echo '$' . htmlspecialchars($subservice['cost']); ?></p>
                                <input type="hidden" name="selectedSubservice" value="<?php echo $subservice['subserviceID']; ?>">
                                <button type="submit" name="purchase" class="go-to-subservice-button">Purchase Subservice</button>
                                <p></p>
                                <button type="submit" name="inquiry" class="go-to-subservice-button">Make Inquiry</button>

                                <h3>Reviews:</h3>
                                <div class="reviews">
                                    <?php 
                                    if (!empty($reviews)) {
                                        foreach ($reviews as $review) {
                                            echo "<p><strong>" . htmlspecialchars(getUsernameFromUserID($conn, $review['userID'])) . ":</strong> \"" . htmlspecialchars($review['comment']) . "\" " . $review['rating'] . "/5 stars";
                                        }
                                    } else {
                                        echo "<p>No reviews yet.</p>";
                                    }
                                    ?>
                                </div>

                            </div>
                        </form>
                        <?php
                    } else {
                        echo "<p>Invalid subservice data.</p>";
                    }
                }
            } else {
                echo "<p>No subservices found.</p>";
            }
        ?>
    </div>   
</body>
</html>