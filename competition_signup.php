<?php
require_once 'functions.php';
session_start();
isLoggedIn();
$conn = connectToDatabase();

$username = getUsername($conn);
$firstName = getFirstName($conn);
$lastName = getLastName($conn);

$upcomingComps = getUpcomingCompetitions($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") { //the name/username will already be listed in the box for signup cuz user is logged in... only submits the compname
    $compName = $_POST["compName"];
    signupForComp($conn, $compName);
    echo "Successfully registered for " . htmlspecialchars($compName);
}


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportzWorld - Competition Sign Up</title>
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

        .main-content {
            padding: 20px;
        }

        h1.service-page-heading {
            text-align: center;
        }

        .signup-form {
            width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .signup-form label {
            display: block;
            margin-bottom: 5px;
        }

        .signup-form input[type="text"],
        .signup-form input[type="email"],
        .signup-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .signup-form .button-container {
            text-align: center;
        }

        .signup-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .signup-form button:hover {
            background-color: #0056b3;
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
            <button onclick="location.href='user_info.php'">User Info</button>
        </nav>
    </header>

    <div class="main-content">
        <h1 class="service-page-heading">Competition Sign Up</h1>

        <form class="signup-form" id="signupForm" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
            
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstName); ?>" readonly>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastName); ?>" readonly>

            <label for="compName">Select Competition:</label>
            <select id="compName" name="compName" required>
                <option value="">--Please choose a competition--</option>
                <?php
                if (is_array($upcomingComps) && !empty($upcomingComps)) {
                    foreach ($upcomingComps as $comp) {
                        if (is_array($comp)) {
                            echo '<option value="' . htmlspecialchars($comp['compName']) . '">' . htmlspecialchars($comp['compName']) . ' - ' . htmlspecialchars($comp['date']) . '</option>';
                        }
                    }
                } else {
                    echo '<option value="" disabled>No upcoming competitions</option>';
                }
                ?>
                </select>

            <div class="button-container">
                <button type="submit">Sign Up</button>
            </div>
        </form>
    </div>
</body>
</html>