<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$businessUsername = $_SESSION['selectedBusiness']; //was set in the user home page (also figure out how to reset each time you navigate back to home page)
$businessID = getBusinessID($conn, $businessUsername);

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($keyword || $type) {
    $services = searchServices($conn, $businessID, $keyword, $type);
} else {
    $services = getAllServices($conn, $businessID);
}

if (is_array($services) && !empty($services)) {
    // Services found, proceed to display them
} else {
    $services = []; // Ensure $services is an array
}

// Handle form submission
if (isset($_POST['selectedService'])) {
    $_SESSION['selectedService'] = $_POST['selectedService']; //should be an id number, not an array
    header("Location: subservices.php"); // Redirect to the service profile
    exit;
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
            background: rgb(17, 130, 235);
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

        .search-bar input[type="text"], select {
            padding: 8px;
            border: 1px solid #ddd;
            flex-grow: 1; /* Make input expand to fill available space */
            margin-right: 10px;
            color: gray;
        }

        .search-bar button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }

        .service_listings {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0;
            grid-auto-rows: 1fr;
        }

        .service-container {
            background: white;
            border-radius: 12px;
            text-align: center;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            height: 100%;           /* This is critical: fills the grid row */
            min-height: 300px;      /* Optional: ensures a minimum size */
            box-sizing: border-box;
        }


        .service-container button {
            background-color: rgb(17, 130, 235);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top:auto;
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
            <input type="text" id="searchInput" name="keyword" placeholder="Search services..." value="<?php echo htmlspecialchars($keyword ?? ''); ?>">

            <select id="searchInput" name="type">
                <option value="">Select type...</option>
                <option value="Rental" <?php echo (isset($type) && $type === 'Rental') ? 'selected' : ''; ?>>Rental</option>
            </select>

            <button type="submit">Search</button>
        </form>
    </div>

    <h1 style="text-align:center; color:white; margin-top:30px; margin-bottom:10px;">
        <?php echo htmlspecialchars($businessUsername); ?>'s Services
    </h1>
    <div class="service_listings">
        <?php
            if (is_array($services) && !empty($services)) {
                foreach ($services as $service) {
                    if (is_array($service)) {
                        ?>
                        <form method="post">
                            <div class="service-container">
                                <h2><?php echo htmlspecialchars($service['serviceName']); ?></h2>
                                <p><?php echo htmlspecialchars($service['description']); ?></p>
                                <input type="hidden" name="selectedService" value="<?php echo $service['serviceID']; ?>">
                                <button type="submit" class="go-to-service-button">Go to Service</button>
                            </div>
                        </form>
                        <?php
                    } else {
                        echo "<p>Invalid service data.</p>";
                    }
                }
            } else {
                echo "<p>No services found.</p>";
            }
        ?>
    </div>   
</body>
</html>