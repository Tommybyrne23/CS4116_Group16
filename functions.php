<?php

/*
Creates a connection to the server.
*/
function connectToDatabase() {
    $servername = "localhost"; //server name
    $dbusername = "root";        //database username
    $dbpassword = "";            //database password
    $dbname = "sportzworldmarketplace";  //database name

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

/*
Checks if a user already exists for the given username.
*/
function usernameExists($conn, $username) {
    $usernameCheck1 = $conn->prepare("SELECT username FROM Users WHERE username = ?");
    $usernameCheck1->bind_param("s", $username);
    $usernameCheck1->execute();
    $usernameCheck1->store_result();

    $usernameCheck2 = $conn->prepare("SELECT username FROM BannedUsers WHERE username = ?");
    $usernameCheck2->bind_param("s", $username);
    $usernameCheck2->execute();
    $usernameCheck2->store_result();

    $exists = ($usernameCheck1->num_rows > 0 || $usernameCheck2->num_rows > 0);

    $usernameCheck1->close();
    $usernameCheck2->close();

    return $exists;
}

/*
Adds a new entry into the Users table & redirects the user to the user home page.
*/
function registerUser($conn, $firstName, $lastName, $username, $password) {
    $stmt = $conn->prepare("INSERT INTO Users (firstName, lastName, username, password) VALUES (?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("ssss", $firstName, $lastName, $username, $password);
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            header("Location: user_home.php");
            $stmt->close();
            exit; // Stop further execution after redirect
        } else {
            error_log("Error executing registration: " . $stmt->error);
            $stmt->close();
            return false;
        }
    } else {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }
}

/*
Checks to see if a username exists (after a login submission) in the Users/Businesses/Admins table & if so stores the username to the session & redirects the user to the proper page.
*/
function checkUserCredentials($conn, $username, $password, $table, $redirect){
    $stmt = $conn->prepare("SELECT username FROM $table WHERE username = ? AND password = ?");

    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $_SESSION['username'] = $username;
            header("Location: $redirect");
            $stmt->close();
            return true; // Indicate success
        } else {
            $stmt->close();
            return false; // Indicate failure
        }
    } else {
        error_log("Error preparing statement: " . $conn->error);
        return false; // Indicate failure
    }
}

/*
Adds a new entry to the Businesses table & redirects the user to the business home page.
*/
function registerBusiness($conn, $username, $password, $name, $description, $contactInfo, $logoURL){
    $stmt = $conn->prepare("INSERT INTO Businesses (username, password, name, description, contactInfo, image_url) VALUES (?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("ssssss", $username, $password, $name, $description, $contactInfo, $logoURL);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: business_home.php");
                $stmt->close();
                exit; // Stop further execution after redirect
            } else {
                error_log("Error executing registration: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error preparing statement: " . $conn->error);
            return false;
        }
}

/*
Deletes an entry from the Users table.
*/
function banUser($conn, $username){
    // Step 1: Select the user's data
    $selectStmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    if (!$selectStmt) {
        error_log("Error preparing SELECT statement: " . $conn->error);
        return false;
    }

    $selectStmt->bind_param("s", $username);
    if (!$selectStmt->execute()) {
        error_log("Error executing SELECT statement: " . $selectStmt->error);
        $selectStmt->close();
        return false;
    }

    $result = $selectStmt->get_result();
    if ($result->num_rows === 0) {
        $selectStmt->close();
        return false; // User not found
    }

    $userData = $result->fetch_assoc();
    $selectStmt->close();

    // Step 2: Insert user data into bannedUsers
    $insertStmt = $conn->prepare("INSERT INTO BannedUsers (userID, firstName, lastName, username, password) VALUES (?, ?, ?, ?, ?)");
    if (!$insertStmt) {
        error_log("Error preparing INSERT statement: " . $conn->error);
        return false;
    }

    $insertStmt->bind_param(
        "issss",
        $userData['userID'],
        $userData['firstName'],
        $userData['lastName'],
        $userData['username'],
        $userData['password']
    );

    if (!$insertStmt->execute()) {
        error_log("Error inserting into BannedUsers: " . $insertStmt->error);
        $insertStmt->close();
        return false;
    }
    $insertStmt->close();
    
    $stmt = $conn->prepare("DELETE FROM Users WHERE username = ?");

    if ($stmt) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $stmt->close();
            return true; // User banned successfully
        } else {
            error_log("Error banning user: " . $stmt->error);
            $stmt->close();
            return false; // Error during execution
        }
    } else {
        error_log("Error preparing ban user statement: " . $conn->error);
        return false; // Error preparing the statement
    }
}

/*
*/
function unbanUser($conn, $username){
    // Step 1: Select the user's data
    $selectStmt = $conn->prepare("SELECT * FROM BannedUsers WHERE username = ?");
    if (!$selectStmt) {
        error_log("Error preparing SELECT statement: " . $conn->error);
        return false;
    }

    $selectStmt->bind_param("s", $username);
    if (!$selectStmt->execute()) {
        error_log("Error executing SELECT statement: " . $selectStmt->error);
        $selectStmt->close();
        return false;
    }

    $result = $selectStmt->get_result();
    if ($result->num_rows === 0) {
        $selectStmt->close();
        return false; // User not found
    }

    $userData = $result->fetch_assoc();
    $selectStmt->close();

    // Step 2: Insert user data into Users
    $insertStmt = $conn->prepare("INSERT INTO Users (firstName, lastName, username, password) VALUES (?, ?, ?, ?)");
    if (!$insertStmt) {
        error_log("Error preparing INSERT statement: " . $conn->error);
        return false;
    }

    $insertStmt->bind_param(
        "ssss",
        $userData['firstName'],
        $userData['lastName'],
        $userData['username'],
        $userData['password']
    );

    if (!$insertStmt->execute()) {
        error_log("Error inserting into Users: " . $insertStmt->error);
        $insertStmt->close();
        return false;
    }
    $insertStmt->close();
    
    $stmt = $conn->prepare("DELETE FROM BannedUsers WHERE username = ?");

    if ($stmt) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $stmt->close();
            return true; // User unbanned successfully
        } else {
            error_log("Error unbanning user: " . $stmt->error);
            $stmt->close();
            return false; // Error during execution
        }
    } else {
        error_log("Error preparing unban user statement: " . $conn->error);
        return false; // Error preparing the statement
    }
}

/*
Deletes an entry from the Reviews table.
*/
function deleteReview($conn, $comment){
    $stmt = $conn->prepare("DELETE FROM Reviews WHERE comment = ?");

        if ($stmt) {
            $stmt->bind_param("s", $comment);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // Review deleted successfully
            } else {
                error_log("Error banning user: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing delete review statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
Gets a list of all the users to display all the usernames on the admins home page.
*/
function getAllUsers($conn){
    $users = [];
        $stmt = $conn->prepare("SELECT username FROM Users");
        if($stmt){
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $users[] = $row;
            }
            $stmt->close();
        }
        return $users;
}

/*
Gets a list of all the banned users to display all the usernames on the admins home page.
*/
function getAllBannedUsers($conn){
    $users = [];
        $stmt = $conn->prepare("SELECT username FROM BannedUsers");
        if($stmt){
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $users[] = $row;
            }
            $stmt->close();
        }
        return $users;
}

/*
Gets a list of all the reviews to display the usernames & corresponding reviews on the admins home page.
*/
function getAllReviews($conn){
    $reviews = [];
        $stmt = $conn->prepare("SELECT Reviews.comment, Users.username FROM Reviews JOIN Users ON Reviews.userID = Users.userID");
        if($stmt){
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $reviews[] = $row;
            }
            $stmt->close();
        }
        return $reviews;
}

/*
Clears session & sends user back to login page.
*/
function logout(){
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy the session

    header("Location: homepage.php"); // Redirect to  homepage
    exit;
}

/*
Gets the firstname variable from the session.
*/
function getFirstName($conn) {
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $conn->prepare("SELECT firstName FROM Users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) {
                    $stmt->bind_result($firstName);
                    if ($stmt->fetch()) {
                        $_SESSION['firstName'] = $firstName; // Store in session for future use
                        $stmt->close();
                        return $firstName;
                    } else {
                        $stmt->close();
                        return null; // Username found, but no first name
                    }
                } else {
                    $stmt->close();
                    return null; // Execution failed
                }
            } else {
                return null; // Prepare failed
            }
        } else {
            return null; // Username not set in session
        }
    }

/*
Gets the last name variable in a session for the given connection.
*/
function getLastName($conn){
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $conn->prepare("SELECT lastName FROM Users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) {
                    $stmt->bind_result($lastName);
                    if ($stmt->fetch()) {
                        $_SESSION['lastName'] = $lastName; // Store in session for future use
                        $stmt->close();
                        return $lastName;
                    } else {
                        $stmt->close();
                        return null; // Username found, but no last name
                    }
                } else {
                    $stmt->close();
                    return null; // Execution failed
                }
            } else {
                return null; // Prepare failed
            }
        } else {
            return null; // Username not set in session
        }
    }

/*
Gets the username variable that is set for a session (to be used after login --> might want to add in if username is not set just in case.)
*/
function getUsername($conn){
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    } else{
        return null;  //there should always be a username assigned to the session cuz you can't get past login (or into user info) page without one
    }
}

/*
Gets the password variable for a session for the given connection.
*/
function getPasswordForUser($conn){
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $password = '';
            $stmt = $conn->prepare("SELECT password FROM Users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) {
                    $stmt->bind_result($password);
                    if ($stmt->fetch()) {
                        //$_SESSION['password'] = $password; // Store in session for future use
                        $stmt->close();
                        return $password;
                    } else {
                        $stmt->close();
                        return null; // Username found, but no password
                    }
                } else {
                    $stmt->close();
                    return null; // Execution failed
                }
            } else {
                return null; // Prepare failed
            }
        } else {
            return null; // Username not set in session
        }  
}

/*
Changes the password field in the database for a given username & resets the session password.
*/
function changePasswordForUser($conn, $newPassword){
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE username = ?");

    if (!$stmt) {
        return "Database error: " . $conn->error; //do we need this if clause??
    }

    $stmt->bind_param("ss", $newPassword, $username);
    if (!$stmt->execute()) {
        $stmt->close();
        return "Database error: " . $stmt->error;
    }

    $stmt->close();

    $_SESSION['password'] = $newPassword;
    return "Password changed successfully.";
}

/*
Returns an arraylist of info [username, pass, fname, lname]
*/
function getUserInfo($conn){
    $username = getUsername($conn); // Assuming getUsername() exists

    if ($username === null) {
        return null; // Or throw an exception: throw new Exception("Username not found.");
    }

    $firstName = getFirstName($conn);
    $lastName = getLastName($conn);    //should i create a session variable as well?? --> yes probably? maybe not in here tho.
    $password = getPassword($conn);

    if ($firstName === null || $lastName === null || $password === null) {
        return null; // Or handle the error appropriately
    }

    return array(
        'firstName' => $firstName,
        'lastName' => $lastName,
        'username' => $username,
        'password' => $password,
    );
}

/*
might not work but i am unsure
*/
function isLoggedIn(){
    if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
    }
}

/*
*/
function getAllBusinesses($conn){
    $businesses = [];
    $stmt = $conn->prepare("SELECT * FROM Businesses");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $businesses[] = $row;
        }
        $stmt->close();
    }
    return $businesses;
}

/*
*/
function searchBusinesses($conn, $keyword){
    $businesses = [];
    $stmt = $conn->prepare("SELECT * FROM Businesses WHERE name LIKE ?");

    $keyword = "%" . $keyword . "%";

    $stmt->bind_param("s", $keyword);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $businesses[] = $row;
        }
        $stmt->close();
    }
    return $businesses;
}

/*
*/
function getAllCompetitions($conn){
    $competitions = [];
    $stmt = $conn->prepare("SELECT * FROM Competitions");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $competitions[] = $row;
        }
        $stmt->close();
    }
    return $competitions;
}

/*
*/
function getUpcomingCompetitions($conn){
    $upcomingComps = [];
    $currDate = date('Y-m-d');

    $stmt = $conn->prepare("SELECT * FROM Competitions WHERE date >= ? ORDER BY date ASC");

    if ($stmt) {
        $stmt->bind_param("s", $currDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $upcomingComps[] = $row;
        }

        $stmt->close();
    }

    return $upcomingComps;
}

/*
*/
function getPastCompetitions($conn){
    $pastComps = [];
    $currDate = date('Y-m-d');

    $stmt = $conn->prepare("SELECT * FROM Competitions WHERE date <= ? ORDER BY date DESC");

    if ($stmt) {
        $stmt->bind_param("s", $currDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $pastComps[] = $row;
        }

        $stmt->close();
    }

    return $pastComps;
}

/*
make a search competitions function to search for competitions of a certain sport or date
*/
function searchUpcomingCompetitions($conn, $keyword){
    $competitions = [];
    $currDate = date('Y-m-d');

    $stmt = $conn->prepare("SELECT * FROM Competitions WHERE compName LIKE ? AND date >= ?");

    $keyword = "%" . $keyword . "%";

    $stmt->bind_param("ss", $keyword, $currDate);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $competitions[] = $row;
        }
        $stmt->close();
    }
    return $competitions;
}

/*
make a search competitions function to search for competitions of a certain sport or date
*/
function searchPastCompetitions($conn, $keyword){
    $competitions = [];
    $currDate = date('Y-m-d');

    $stmt = $conn->prepare("SELECT * FROM Competitions WHERE compName LIKE ? AND date <= ?");

    $keyword = "%" . $keyword . "%";

    $stmt->bind_param("ss", $keyword, $currDate);

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $competitions[] = $row;
        }
        $stmt->close();
    }
    return $competitions;
}

/*
*/
function signupForComp($conn, $compName){
    $username = $_SESSION['username'];

    // want to insert into participants compID & userID. get userID using username & compID using compName
    $getUserStmt = $conn->prepare("SELECT userID FROM Users WHERE username = ?");
    $getUserStmt->bind_param("s", $username);
    $getUserStmt->execute();
    $userResult = $getUserStmt->get_result();
    $userRow = $userResult->fetch_assoc();
    $userID = $userRow['userID'];
    // $userID = $getUserStmt->get_result();
    $getUserStmt->close();

    $getCompStmt = $conn->prepare("SELECT compID FROM Competitions WHERE compName = ?");
    $getCompStmt->bind_param("s", $compName);
    $getCompStmt->execute();
    $compResult = $getCompStmt->get_result();
    $compRow = $compResult->fetch_assoc();
    $compID = $compRow['compID'];
    // $compID = $getCompStmt->get_result();
    $getCompStmt->close();

    $stmt = $conn->prepare("INSERT INTO Participants (userID, compID) VALUES (?, ?);");
    $stmt->bind_param("ii", $userID, $compID);
    $stmt->execute();
    $stmt->close();
    //$stmt->get_result();
}

/*
Still getting errors
*/
function getCompsForUser($conn, $userID) {
    $competitions = [];
    $stmt = $conn->prepare("SELECT c.compName, c.`date`, c.location FROM Competitions c INNER JOIN Participants p ON c.compID = p.compID INNER JOIN Users u ON u.userID = p.userID WHERE u.userID = ?");

    if ($stmt) {
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $competitions[] = $row;
        }

        $stmt->close();
    }

    return $competitions;
}

/*
Retrieves the userID for the current session.
*/
function getUserID($conn) {
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT userID FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['userID'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
*/
function getBusinessID($conn, $username) {
    $stmt = $conn->prepare("SELECT businessID FROM Businesses WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['businessID'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
*/
function getServiceID($conn, $businessID, $serviceName){
    $stmt = $conn->prepare("SELECT serviceID FROM Services WHERE businessID = ? AND serviceName = ?");
    $stmt->bind_param("is", $businessID, $serviceName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['serviceID'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
*/
function getSubserviceID($conn, $serviceID, $subserviceName){
    $stmt = $conn->prepare("SELECT subserviceID FROM SubServices WHERE serviceID = ? AND subserviceName = ?");
    $stmt->bind_param("is", $serviceID, $subserviceName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['subserviceID'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
*/
function getCost($conn, $subserviceID){
    $stmt = $conn->prepare("SELECT cost FROM SubServices WHERE subserviceID = ?");
    $stmt->bind_param("i", $subserviceID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['cost'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
Gets all the services for a given business.
*/
function getAllServices($conn, $businessID){
    $services = [];

    $stmt = $conn->prepare("SELECT * FROM Services WHERE businessID = ?");

    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }

        $stmt->close();
    }

    return $services;
}

/*
Gets all the subservices for the given serviceID.
*/
function getAllSubservices($conn, $serviceID){
    $subservices = [];

    $stmt = $conn->prepare("SELECT * FROM SubServices WHERE serviceID = ?");

    if ($stmt) {
        $stmt->bind_param("i", $serviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $subservices[] = $row;
        }

        $stmt->close();
    }

    return $subservices;
}

/*
Generates an array of services given the business & the keyword
*/
function searchServices($conn, $businessID, $keyword, $type){
    $services = [];
    $query = "SELECT * FROM Services WHERE businessID = ?";
    $types = "i";
    $params = [$businessID];

    if (!empty($keyword) && trim($keyword) !== ''){
        $query .= " AND serviceName LIKE ?";
        $types .= "s";
        $params[] = "%" . $keyword . "%";
    }

    if (!empty($type)){
        $query .= " AND serviceName LIKE ?"; //only gonna be 2 options
        $types .= "s";
        $params[] = "%" . $type . "%";
    }

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }

        $stmt->close();
    }
    return $services;
    // $stmt = $conn->prepare("SELECT * FROM Services WHERE serviceName LIKE ? AND businessID = ?");

    // $keyword = "%" . $keyword . "%";

    // $stmt->bind_param("si", $keyword, $businessID);

    // if ($stmt) {
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     while ($row = $result->fetch_assoc()) {
    //         $services[] = $row;
    //     }
    //     $stmt->close();
    // }
    // return $services;
}

/*
Generates an array of subservices given the service & the keyword
*/
function searchSubservices($conn, $serviceID, $keyword, $minPrice, $maxPrice){
    $subservices = [];

    $query = "SELECT * FROM SubServices WHERE serviceID = ?";
    $types = "i";
    $params = [$serviceID];

    if (!empty($keyword) && trim($keyword) !== '') {
        $query .= " AND subserviceName LIKE ?";
        $types .= "s";
        $params[] = "%" . $keyword . "%";
    }

    if (!empty($minPrice)) {
        $query .= " AND cost >= ?";
        $types .= "d";
        $params[] = $minPrice;
    }

    if (!empty($maxPrice)) {
        $query .= " AND cost <= ?";
        $types .= "d";
        $params[] = $maxPrice;
    }

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $subservices[] = $row;
        }

        $stmt->close();
    }

    return $subservices;

    //$stmt = $conn->prepare("SELECT * FROM SubServices WHERE subserviceName LIKE ? AND serviceID = ?");

    //$keyword = "%" . $keyword . "%";

    //$stmt->bind_param("si", $keyword, $serviceID);

    // if ($stmt) {
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     while ($row = $result->fetch_assoc()) {
    //         $subservices[] = $row;
    //     }
    //     $stmt->close();
    // }
    // return $subservices;
}
/*
*/
function getBusinessName($conn, $businessID){
    $stmt = $conn->prepare("SELECT name FROM Businesses WHERE businessID = ?");
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['name'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getServiceName($conn, $serviceID){
    $stmt = $conn->prepare("SELECT serviceName FROM Services WHERE serviceID = ?");
        $stmt->bind_param("i", $serviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['serviceName'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getSubserviceName($conn, $subserviceID){
    $stmt = $conn->prepare("SELECT subserviceName FROM SubServices WHERE subserviceID = ?");
        $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['subserviceName'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function completeTransaction($conn, $userID, $businessID, $serviceID, $subserviceID, $total){
    $stmt = $conn->prepare("INSERT INTO Transactions (userID, businessID, serviceID, subserviceID, total) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiiid", $userID, $businessID, $serviceID, $subserviceID, $total);
            if ($stmt->execute()) {
                $stmt->close();
                exit;
            } else {
                error_log("Error executing transaction: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error preparing statement: " . $conn->error);
            return false;
        }
}

/*
*/
function getTransactionsForUser($conn){
    $transactions = [];
    $username = $_SESSION['username'];
    $userID = getUserID($conn, $username);
    $stmt = $conn->prepare("SELECT * FROM Transactions WHERE userID = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        $stmt->close();
    }

    return $transactions;
}

/*
*/
function leaveReview($conn, $userID, $businessID, $serviceID, $subserviceID, $rating, $comment){
    $count = 0;
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM Transactions WHERE userID = ? AND subserviceID = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("ii", $userID, $subserviceID);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {    
        $stmt = $conn->prepare("INSERT INTO Reviews (userID, businessID, serviceID, subserviceID, rating, comment) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiiiis", $userID, $businessID, $serviceID, $subserviceID, $rating, $comment);
                if ($stmt->execute()) {
                    $stmt->close();
                    return true;
                } else {
                    error_log("Error executing review insertion: " . $stmt->error);
                    $stmt->close();
                    return false;
                }
            } else {
                error_log("Error preparing review statement: " . $conn->error);
                return false;
            }
        } else {
            // User does not have a valid transaction for this subservice
            error_log("Review attempt failed: User has no transaction history for the given subservice");
            return false;
        }
    } else {
        error_log("Error preparing transaction check statement: " . $conn->error);
        return false;
    }
}

/*
*/
function getBusinessUsername($conn, $name){
    $stmt = $conn->prepare("SELECT username FROM Businesses WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['username'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getReviewsForSubservice($conn, $subserviceID){
    $reviews = [];
    $stmt = $conn->prepare("SELECT * FROM Reviews WHERE subserviceID = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }

        $stmt->close();
    }

    return $reviews;
}

/*
*/
function getBusinessNameFromUsername($conn, $username){
    $stmt = $conn->prepare("SELECT name FROM Businesses WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['name'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
Gets all of the subservices a business offers. Takes the connection to the database & the business username as arguments.
*/
function getAllSubservicesForBusiness($conn, $businessUsername){
    $subservices = [];
    $businessID = getBusinessID($conn, $businessUsername);
    $stmt = $conn->prepare("SELECT a.subserviceName, a.description, a.cost, a.subserviceID, b.serviceID FROM SubServices a INNER JOIN Services b ON a.serviceID = b.serviceID WHERE businessID = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $subservices[] = $row;
        }

        $stmt->close();
    }

    return $subservices;
}

/*
*/
function deleteService($conn, $serviceID){
    $stmt = $conn->prepare("DELETE FROM Services WHERE serviceID = ?");

        if ($stmt) {
            $stmt->bind_param("i", $serviceID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // service deleted successfully
            } else {
                error_log("Error deleting service: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing delete service statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function deleteSubservice($conn, $subserviceID){
    $stmt = $conn->prepare("DELETE FROM SubServices WHERE subserviceID = ?");

        if ($stmt) {
            $stmt->bind_param("i", $subserviceID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // subservice deleted successfully
            } else {
                error_log("Error deleting subservice: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing delete subservice statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function getPasswordForBusiness($conn){
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $password = '';
            $stmt = $conn->prepare("SELECT password FROM Businesses WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) {
                    $stmt->bind_result($password);
                    if ($stmt->fetch()) {
                        $stmt->close();
                        return $password;
                    } else {
                        $stmt->close();
                        return null; // Username found, but no password
                    }
                } else {
                    $stmt->close();
                    return null; // Execution failed
                }
            } else {
                return null; // Prepare failed
            }
        } else {
            return null; // Username not set in session
        }  
}

/*
Changes the password field in the database for a given username & resets the session password.
*/
function changePasswordForBusiness($conn, $newPassword){
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("UPDATE Businesses SET password = ? WHERE username = ?");

    if (!$stmt) {
        return "Database error: " . $conn->error; //do we need this if clause??
    }

    $stmt->bind_param("ss", $newPassword, $username);
    if (!$stmt->execute()) {
        $stmt->close();
        return "Database error: " . $stmt->error;
    }

    $stmt->close();

    //return "Password changed successfully.";
}

/*
*/
function getTransactionsForBusiness($conn){
    $transactions = [];
    $username = $_SESSION['username'];
    $businessID = getBusinessID($conn, $username);
    $stmt = $conn->prepare("SELECT * FROM Transactions WHERE businessID = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }

        $stmt->close();
    }

    return $transactions;
}

/*
*/
function getUsernameFromUserID($conn, $userID){
    $stmt = $conn->prepare("SELECT username FROM Users WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['username'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getSoldServicesCount($conn, $serviceID){
    $stmt = $conn->prepare("SELECT COUNT(*) as service_count FROM Transactions WHERE serviceID = ?");
    $stmt->bind_param("i", $serviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['service_count'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getServiceRevenue($conn, $serviceID){
    $stmt = $conn->prepare("SELECT t.serviceID, SUM(s.cost) AS total_revenue FROM Transactions t JOIN SubServices s ON t.subserviceID = s.subserviceID WHERE t.serviceID = ?");
    $stmt->bind_param("i", $serviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_revenue'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getSoldSubservicesCount($conn, $subserviceID){
    $stmt = $conn->prepare("SELECT COUNT(*) as subservice_count FROM Transactions WHERE subserviceID = ?");
    $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['subservice_count'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getSubserviceRevenue($conn, $subserviceID){
    $stmt = $conn->prepare("SELECT t.subserviceID, SUM(s.cost) AS total_revenue FROM Transactions t JOIN SubServices s ON t.subserviceID = s.subserviceID WHERE t.subserviceID = ?");
    $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_revenue'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getTotalSpent($conn, $userID){
    $stmt = $conn->prepare("SELECT SUM(s.cost) AS total_spent FROM SubServices s JOIN Transactions t ON s.subserviceID = t.subserviceID WHERE userID = ?");
    $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_spent'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getCustomers($conn, $businessID){
    $customers = [];
    $stmt = $conn->prepare("SELECT * FROM Users u JOIN Transactions t ON u.userID = t.userID WHERE t.businessID = ? GROUP BY u.userID, u.username, u.firstName, u.lastName");
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }

        $stmt->close();
    }

    return $customers;
}

/*
*/
function getFirstNameFromUserID($conn, $userID){
    $stmt = $conn->prepare("SELECT firstName FROM Users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['firstName'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function getLastNameFromUserID($conn, $userID){
    $stmt = $conn->prepare("SELECT lastName FROM Users WHERE userID = ?");
    $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['lastName'];
        } else {
            return null; // Or handle the case where the business is not found
        }
}

/*
*/
function addService($conn, $serviceName, $description, $businessID){
    $stmt = $conn->prepare("INSERT INTO Services(serviceName, description, businessID) VALUES(?, ?, ?)");
    if ($stmt) {
            $stmt->bind_param("ssi", $serviceName, $description, $businessID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // service deleted successfully
            } else {
                error_log("Error adding service: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing add service statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function addSubservice($conn, $serviceName, $subserviceName, $description, $cost, $businessID){
    $serviceID = getServiceID($conn, $businessID, $serviceName);
    
    $stmt = $conn->prepare("INSERT INTO SubServices(subserviceName, description, cost, serviceID) VALUES(?, ?, ?, ?)");
    if ($stmt) {
            $stmt->bind_param("ssdi", $subserviceName, $description, $cost, $serviceID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // subservice added successfully
            } else {
                error_log("Error adding subservice: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing add subservice statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
Gets a list of reviews for a given subservice.
*/
function getReviews($conn, $subserviceID){
    $reviews = [];
    $stmt = $conn->prepare("SELECT * FROM Reviews WHERE subserviceID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $reviews[] = $row;
        }

        $stmt->close();
    }

    return $reviews;
}

/*
Calculates the average rating of a given subservice.
*/
function getAverageRating ($conn, $subserviceID){
    $avg_rating = 0.0;
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM Reviews WHERE subserviceID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $subserviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $avg_rating = $row['avg_rating'] ?? 0.0;
        }

        $stmt->close();
    }

    return $avg_rating;

}

/*
*/
function getSubservicesForService($conn, $serviceID){
    $subservices = [];
    $stmt = $conn->prepare("SELECT * FROM SubServices WHERE serviceID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $serviceID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $subservices[] = $row;
        }

        $stmt->close();
    }

    return $subservices;
}

/*
*/
function sendMessage($conn, $userID, $businessID, $message, $sender){
    //$userID = getUserID($conn); //maybe change to getting userID in the page & just trusting the parameter
    
    $stmt = $conn->prepare("INSERT INTO Messages(userID, businessID, message, sender) VALUES(?, ?, ?, ?)");
    if ($stmt) {
            $stmt->bind_param("iiss", $userID, $businessID, $message, $sender);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // message sent successfully
            } else {
                error_log("Error sending message: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing send message statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function getMessages($conn, $userID, $businessID){
    $messages = [];
    $stmt = $conn->prepare("SELECT * FROM Messages WHERE userID = ? AND businessID = ? ORDER BY messageID ASC");
    if ($stmt) {
        $stmt->bind_param("ii", $userID, $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        $stmt->close();
    }

    return $messages;
}

/*
*/
function getUserIDFromUsername($conn, $username){
    $stmt = $conn->prepare("SELECT userID FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['userID'];
    } else {
        return null; // Or handle the case where the business is not found
    }
}

/*
*/
function sendInquiry($conn, $userID, $businessID, $serviceID, $subserviceID, $message, $proposedPrice){
    $stmt = $conn->prepare("INSERT INTO Inquiries (userID, businessID, serviceID, subserviceID, message, proposed_price) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iiiisd", $userID, $businessID, $serviceID, $subserviceID, $message, $proposedPrice);
            if ($stmt->execute()) {
                $stmt->close();
                exit;
            } else {
                error_log("Error executing transaction: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error preparing statement: " . $conn->error);
            return false;
        }
}

/*
*/
function getInquiriesForBusiness($conn, $businessID){
    $inquiries = [];
    $stmt = $conn->prepare("SELECT * FROM Inquiries WHERE businessID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $inquiries[] = $row;
        }

        $stmt->close();
    }

    return $inquiries;
}

/*
*/
function deleteInquiry($conn, $inquiryID){
    $stmt = $conn->prepare("DELETE FROM Inquiries WHERE inquiryID = ?");
    if ($stmt) {
            $stmt->bind_param("i", $inquiryID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // inquiry deleted successfully
            } else {
                error_log("Error deleting inquiry: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing delete inquiry statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function approveInquiry($conn, $inquiryID){
    $selectStmt = $conn->prepare("SELECT userID, businessID, serviceID, subserviceID, proposed_price FROM Inquiries WHERE inquiryID = ?");
    if (!$selectStmt) {
        error_log("Error preparing SELECT: " . $conn->error);
        return false;
    }

    $selectStmt->bind_param("i", $inquiryID);
    $selectStmt->execute();
    $selectStmt->bind_result($userID, $businessID, $serviceID, $subserviceID, $proposed_price);

    if ($selectStmt->fetch()) {
        $selectStmt->close();

        // Step 2: Insert into Transactions
        $insertStmt = $conn->prepare("INSERT INTO Transactions (userID, businessID, serviceID, subserviceID, total) VALUES (?, ?, ?, ?, ?)");
        if (!$insertStmt) {
            error_log("Error preparing INSERT: " . $conn->error);
            return false;
        }

        $insertStmt->bind_param("iiiid", $userID, $businessID, $serviceID, $subserviceID, $proposed_price);

        if ($insertStmt->execute()) {
            $insertStmt->close();

            // Step 3: Delete from Inquiries
            $deleteStmt = $conn->prepare("DELETE FROM Inquiries WHERE inquiryID = ?");
            if (!$deleteStmt) {
                error_log("Error preparing DELETE: " . $conn->error);
                return false;
            }

            $deleteStmt->bind_param("i", $inquiryID);
            if ($deleteStmt->execute()) {
                $deleteStmt->close();
                return true; // Successfully approved and removed
            } else {
                error_log("Error executing DELETE: " . $deleteStmt->error);
                $deleteStmt->close();
                return false;
            }

        } else {
            error_log("Error executing INSERT: " . $insertStmt->error);
            $insertStmt->close();
            return false;
        }

    } else {
        error_log("No inquiry found with ID $inquiryID.");
        $selectStmt->close();
        return false;
    }
}

/*
*/
function deleteCompetition($conn, $compID){
    $stmt = $conn->prepare("DELETE FROM Competitions WHERE compID = ?");
    if ($stmt) {
            $stmt->bind_param("i", $compID);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // competition deleted successfully
            } else {
                error_log("Error deleting competition: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing delete competition statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function addCompetition($conn, $compName, $sport, $type, $date, $location){
    $stmt = $conn->prepare("INSERT INTO Competitions (compName, sport, type, date, location) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $compName, $sport, $type, $date, $location);
            if ($stmt->execute()) {
                $stmt->close();
                exit;
            } else {
                error_log("Error adding competition: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } else {
            error_log("Error preparing statement: " . $conn->error);
            return false;
        }
}

/*
*/
function isVerified($conn, $username){
    $userID = getUserIDFromUsername($conn, $username);
    $stmt = $conn->prepare("SELECT * FROM Transactions WHERE userID = ?");
    if($stmt){
        $stmt->bind_param("s", $userID);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $hasRows = $result->num_rows > 0;
            $stmt->close();
            return $hasRows;
        } else {
        $stmt->close();
        return false;  // query execution failed
        }
    }
}

//use getAllUsers() in php page to then use this to check if verified for display
//issue might be if im checking if they're verified but im a verified user 

/*
*/
function sendMessageBetweenUsers($conn, $senderID, $receiverID, $message){
    $stmt = $conn->prepare("INSERT INTO UserMessages(senderID, receiverID, message) VALUES(?, ?, ?)");
    if ($stmt) {
            $stmt->bind_param("iis", $senderID, $receiverID, $message);

            if ($stmt->execute()) {
                $stmt->close();
                return true; // message sent successfully
            } else {
                error_log("Error sending message: " . $stmt->error);
                $stmt->close();
                return false; // Error during execution
            }
        } else {
            error_log("Error preparing send message statement: " . $conn->error);
            return false; // Error preparing the statement
        }
}

/*
*/
function getMessagesBetweenUsers($conn, $senderID, $receiverID){
    $messages = [];
    $stmt = $conn->prepare("SELECT * FROM UserMessages WHERE senderID = ? AND receiverID = ? ORDER BY messageID ASC");
    if ($stmt) {
        $stmt->bind_param("ii", $senderID, $receiverID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        $stmt->close();
    }

    return $messages;
}

?>