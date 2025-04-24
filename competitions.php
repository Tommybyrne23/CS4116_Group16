<?php
//echo "main competition page"
// Enable all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;

if ($keyword) {
    $upcomingComps = searchUpcomingCompetitions($conn, $keyword); // Use search function
    $pastComps = searchPastCompetitions($conn, $keyword);
} else {
    $upcomingComps = getUpcomingCompetitions($conn); // Get all businesses if no keyword
    $pastComps = getPastCompetitions($conn);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Competitions</title>
    <style>
        body {
        font-family: sans-serif;
        margin: 0;
        padding: 0;
        background: rgb(17, 130, 235)
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
            flex-grow: 1;
            margin-right: 10px;
        }

        .search-bar button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
        }

        .main-content {
            padding: 20px;
        }

        h1.service-page-heading {
            text-align: center;
            color: rgb(255,255,255);
        }

        .upcoming-competitions, .past-competitions {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .upcoming-competitions h2, .past-competitions h2 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .competition-container {
            display: flex; /* Use flexbox for layout */
            flex-direction:column;
            //justify-content: space-between; /* Space items evenly */
            //align-items: center; /* Align items vertically in the center */
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: white;
        }

        .competition-container:last-child {
            margin-bottom: 0; /* Remove bottom margin for the last item */
        }

        .competition-details {
            flex-grow: 1; /* Allow details to expand and take up space */
        }

        .competition-details h3 {
            font-size: 1.1em;
            margin-bottom: 5px;
            color: #007bff; /* Example color for competition name */
        }

        .competition-date, .competition-location, .competition-sport, .competition-type {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 3px;
        }

        .signup-comp-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .signup-comp-button:hover {
            background-color: #0056b3;
        }

        .past-competitions .competition-details h3 {
            font-size: 1em; /* Smaller font for past competition names */
        }

        .past-competitions .competition-date, .past-competitions .competition-location, .past-competitions .competition-sport, .past-competitions .competition-type {
            font-size: 0.8em; /* Smaller font for past competition details */
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
            <input type="text" id="searchInput" name="keyword" placeholder="Search competitions..." value="<?php if($keyword){echo htmlspecialchars($keyword);} ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="main-content">
        <h1 class="service-page-heading">Competitions</h1>

        <div class="upcoming-competitions">
            <h2>Upcoming Competitions</h2>
            <?php
            if (is_array($upcomingComps) && !empty($upcomingComps)) {
                foreach ($upcomingComps as $upComp) {
                    if (is_array($upComp)) {
                        ?>
                        <div class="competition-container">
                            <h3><?php echo htmlspecialchars($upComp['compName']); ?></h3>
                            <p>Date: <?php echo htmlspecialchars($upComp['date']); ?></p>
                            <p>Location: <?php echo htmlspecialchars($upComp['location']); ?></p>
                            <p>Sport: <?php echo htmlspecialchars($upComp['sport']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($upComp['type']); ?></p>
                            <button class="signup-comp-button" onclick="location.href='competition_signup.php?compName=<?php echo urlencode($upComp['compName']); ?>'">Signup</button>
                        </div>
                        <?php
                    } else {
                        echo "<p>Invalid comp data.</p>";
                    }
                }
            } else {
                echo "<p>No comps found.</p>";
            }
            ?>
        </div>

        <div class="past-competitions">
            <h2>Past Competitions</h2>
            <?php
            if (is_array($pastComps) && !empty($pastComps)) {
                foreach ($pastComps as $pastComp) {
                    if (is_array($pastComp)) {
                        ?>
                        <div class="competition-container">
                            <h3><?php echo htmlspecialchars($pastComp['compName']); ?></h3>
                            <p>Date: <?php echo htmlspecialchars($pastComp['date']); ?></p>
                            <p>Location: <?php echo htmlspecialchars($pastComp['location']); ?></p>
                            <p>Sport: <?php echo htmlspecialchars($pastComp['sport']); ?></p>
                            <p>Type: <?php echo htmlspecialchars($pastComp['type']); ?></p>
                        </div>
                        <?php
                    } else {
                        echo "<p>Invalid comp data.</p>";
                    }
                }
            } else {
                echo "<p>No comps found.</p>";
            }
            ?>
    </div>

</body>
</html>