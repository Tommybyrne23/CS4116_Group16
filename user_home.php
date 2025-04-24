<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

// Handle form submission
if (isset($_POST['selectedBusiness'])) {
    $_SESSION['selectedBusiness'] = $_POST['selectedBusiness'];
    header("Location: services.php"); // Redirect to the business profile
    exit;
}

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;

if ($keyword) {
    $businesses = searchBusinesses($conn, $keyword); // Use search function
} else {
    $businesses = getAllBusinesses($conn); // Get all businesses if no keyword
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
            background-color: #f0f0f0;
        }

        .search-bar input[type="text"] {
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

        .business_listings {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .business-container {
            text-align: center;
            border: 1px solid #ddd;
            padding: 10px;
            cursor: pointer;
            background: rgb(255,255,255);
            border-radius: 12px;
            max-width: 270px;
        }

        .business-container img {
            width: 100%;    
            max-width: 250px;     
            height: 180px;      
            object-fit: cover;    /* Crop image to fill box, keeping aspect ratio */
            border-radius: 8px;   
            background: #eee;     /* Optional: fallback bg for transparent images */
        }

        .business-container button {
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
            <button onclick="location.href='user_messages.php'">Messages</button>
            <button onclick="location.href='competitions.php'">Competitions</button>
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="search-bar">
        <form method = "GET">
            <input type="text" id="searchInput" name="keyword" placeholder="Search businesses..." value="<?php echo htmlspecialchars($keyword ?? ""); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="business_listings">
        <?php
            if (is_array($businesses) && !empty($businesses)) {
                foreach ($businesses as $business) {
                    if (is_array($business)) {
                        ?>
                        <form method="post">
                            <div class="business-container">
                                <img src="<?php echo htmlspecialchars($business['image_url']); ?>" alt="<?php echo htmlspecialchars($business['name']); ?> logo">
                                <h2><?php echo htmlspecialchars($business['name']); ?></h2>
                                <input type="hidden" name="selectedBusiness" value="<?php echo htmlspecialchars($business['username']); ?>">
                                <button type="submit" class="go-to-business-button">Go to Business</button>
                            </div>
                        </form>
                        <?php
                    } else {
                        echo "<p>Invalid business data.</p>";
                    }
                }
            } else {
                echo "<p>No businesses found.</p>";
            }
        ?>
    </div>   
</body>
</html>