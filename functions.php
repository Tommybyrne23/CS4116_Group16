<?php

/*
Creates a connection to the server.
*/
function connectToDatabase() {
    $servername = "sql107.infinityfree.com";
    $dbusername = "if0_38519522";
    $dbpassword = "qAsQuSfBci";
    $dbname = "if0_38519522_SportzMarketplace";

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
    $usernameCheck = $conn->prepare("SELECT username FROM Users WHERE username = ?");
    $usernameCheck->bind_param("s", $username);
    $usernameCheck->execute();
    $usernameCheck->store_result();

    $exists = ($usernameCheck->num_rows > 0);

    $usernameCheck->close();
    $firstName = "";
    $lastName = "";
    $username = "";
    $password = "";

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
function registerBusiness($conn, $username, $password, $name, $description, $contactInfo){
    $stmt = $conn->prepare("INSERT INTO Businesses (username, password, name, description, contactInfo) VALUES (?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sssss", $username, $password, $name, $description, $contactInfo);
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

    header("Location: login.php"); // Redirect to login page
    exit;
}

// /*
// */
// function login($conn, $username){
//     $_SESSION['username'] = $username;
//     $stmt = $conn->prepare("SELECT password, username FROM Users WHERE username = ?");
//     if ($stmt) {
//             $stmt->bind_param("s", $username);
//             if ($stmt->execute()) {
//                 echo //what do i put here?
//                 $stmt->close();
//                 exit; // Stop further execution after redirect
// }

/*
Gets the firstname variable from the session.
*/
function getFirstName($conn) {
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

    if (isset($_SESSION['firstName'])) {
        return $_SESSION['firstName'];
    } else {
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
}

/*
*/
function getLastName($conn){
    if (isset($_SESSION['lastName'])) {
        return $_SESSION['lastName'];
    } else {
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
}

/*
*/
function getUsername($conn){
    
}

/*
*/
function getPassword($conn){
    return $_SESSION['password'];
}

/*
*/
function getChangePassword($conn, $username, $newpassword){
    
}

// /*
// */
// function getUserCredentials($conn, ???){

// }
?>
