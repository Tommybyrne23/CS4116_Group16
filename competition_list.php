<?php

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

if (isset($_POST['delete_competition'])) {
    $compID = $_POST['delete_competition'];
    if (deleteCompetition($conn, $compID)) {
        // competition deleted successfully, refresh the competition
        $upcomingComps = getUpcomingCompetitions($conn);
        $pastComps = getPastCompetitions($conn);
    } else {
        echo "<p style='color:red;'>Failed to delete competition.</p>";
    }
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

        h1.admin-page-heading {
            text-align: center;
        }

        .upcoming-competitions, .past-competitions {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            flex: 1;
            margin-right: 20px;
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

        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .add-button {
            background-color: green;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <header>
        <h1>SportzWorld Marketplace</h1>
        <nav>
            <button onclick="location.href='login.php'">Logout</button>
            <button onclick="location.href='admin_home.php'">Home</button>
        </nav>
    </header>

    <div class="search-bar">
        <form method = "GET">
            <input type="text" id="searchInput" name="keyword" placeholder="Search competitions..." value="<?php if($keyword){echo htmlspecialchars($keyword);} ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="main-content">
        <h1 class="competition-page-heading">Competitions</h1>

        <div class="upcoming-competitions">
            <h2>Upcoming Competitions</h2>
            <table>
                <tr>
                    <th>Competition</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Sport</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
                <?php if (is_array($upcomingComps) && !empty($upcomingComps)) {
                    foreach ($upcomingComps as $upComp) {
                        if (is_array($upComp)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($upComp['compName']); ?></td>
                                <td><?php echo htmlspecialchars($upComp['date']); ?></td>
                                <td><?php echo htmlspecialchars($upComp['location']); ?></td>
                                <td><?php echo htmlspecialchars($upComp['sport']); ?></td>
                                <td><?php echo htmlspecialchars($upComp['type']); ?></td>
                                <td>
                                    <form method="post">
                                        <button class="delete-button" type="submit" name="delete_competition" value="<?php echo htmlspecialchars($upComp['compID']); ?>">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    }
                } else { ?>
                    <tr>
                        <td colspan="6">No upcoming competitions found.</td>
                    </tr>
                <?php } ?>
            </table>
            <button class="add-button" onclick="location.href='add_competition.php'">Add Competition</button>
        </div>

        <div class="past-competitions">
            <h2>Past Competitions</h2>
            <table>
                <tr>
                    <th>Competition</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Sport</th>
                    <th>Type</th>
                </tr>
                <?php if (is_array($pastComps) && !empty($pastComps)) {
                    foreach ($pastComps as $pastComp) {
                        if (is_array($pastComp)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pastComp['compName']); ?></td>
                                <td><?php echo htmlspecialchars($pastComp['date']); ?></td>
                                <td><?php echo htmlspecialchars($pastComp['location']); ?></td>
                                <td><?php echo htmlspecialchars($pastComp['sport']); ?></td>
                                <td><?php echo htmlspecialchars($pastComp['type']); ?></td>
                            </tr>
                        <?php }
                    }
                } else { ?>
                    <tr>
                        <td colspan="5">No past competitions found.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
</body>
</html>
