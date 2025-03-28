<?php

/*
Creates a connection to the server.
*/
function connectToDatabase() {
    $servername = "localhost"; //server name
    $dbusername = "root";        //database username
    $dbpassword = "";            //database password
    $dbname = "sportzmarketplace";  //database name

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








//Additions for business_home.php below this

function addService($conn, $serviceName, $description, $businessID) {
    $stmt = $conn->prepare("INSERT INTO services (serviceName, description, businessID) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssi", $serviceName, $description, $businessID);
        if ($stmt->execute()) {
            return true; // Service added successfully
        } else {
            error_log("Error executing addService: " . $stmt->error);
            return false;
        }
    } else {
        error_log("Error preparing addService statement: " . $conn->error);
        return false;
    }
}

function removeService($conn, $serviceID) {
    $stmt = $conn->prepare("DELETE FROM Services WHERE serviceID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $serviceID);
        if ($stmt->execute()) {
            return true; // Service removed successfully
        } else {
            error_log("Error executing removeService: " . $stmt->error);
            return false;
        }
    } else {
        error_log("Error preparing removeService statement: " . $conn->error);
        return false;
    }
}

function addSubservice($conn, $subserviceName, $description, $cost, $serviceID) {
    $stmt = $conn->prepare("INSERT INTO SubServices (subserviceName, description, cost, serviceID) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssdi", $subserviceName, $description, $cost, $serviceID);
        if ($stmt->execute()) {
            return true; // Subservice added successfully
        } else {
            error_log("Error executing addSubservice: " . $stmt->error);
            return false;
        }
    } else {
        error_log("Error preparing addSubservice statement: " . $conn->error);
        return false;
    }
}

function removeSubservice($conn, $subserviceID) {
    $stmt = $conn->prepare("DELETE FROM SubServices WHERE subserviceID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $subserviceID);
        if ($stmt->execute()) {
            return true; // Subservice removed successfully
        } else {
            error_log("Error executing removeSubservice: " . $stmt->error);
            return false;
        }
    } else {
        error_log("Error preparing removeSubservice statement: " . $conn->error);
        return false;
    }
}

function getBusinessServices($conn, $businessID) {
    $services = [];
    $stmt = $conn->prepare("SELECT serviceID, serviceName, description FROM Services WHERE businessID = ?");
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

function getBusinessSubservices($conn, $businessID) {
    $subservices = [];
    $stmt = $conn->prepare("SELECT ss.subserviceID, ss.subserviceName, ss.description, ss.cost 
                            FROM SubServices ss 
                            INNER JOIN Services s ON ss.serviceID = s.serviceID 
                            WHERE s.businessID = ?");
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

//Sales history function below
function getSalesHistory($conn, $businessID) {
    $sql = "SELECT u.firstName AS customerName, s.serviceName, ss.subserviceName, ss.cost
            FROM Transactions t
            JOIN Users u ON t.userID = u.userID
            JOIN Services s ON t.serviceID = s.serviceID
            LEFT JOIN SubServices ss ON t.subserviceID = ss.subserviceID
            WHERE t.businessID = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();
        $salesHistory = [];
        while ($row = $result->fetch_assoc()) {
            $salesHistory[] = $row;
        }
        $stmt->close();
        return $salesHistory;
    } else {
        error_log("Error preparing sales history statement: " . $conn->error);
        return [];
    }
}

//functions for analytics table in sales_history.php
function getTopCustomers($conn, $businessID) {
    $sql = "SELECT u.firstName, u.lastName, u.userID, 
                   SUM(ss.cost) AS total_spent, 
                   COUNT(t.transactionID) AS num_purchases
            FROM Transactions t
            JOIN Users u ON t.userID = u.userID
            LEFT JOIN SubServices ss ON t.subserviceID = ss.subserviceID
            WHERE t.businessID = ?
            GROUP BY u.userID
            ORDER BY total_spent DESC, num_purchases DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();
        $topCustomers = [];
        while ($row = $result->fetch_assoc()) {
            $topCustomers[] = $row;
        }
        $stmt->close();
        return $topCustomers;
    } else {
        error_log("Error preparing top customers query: " . $conn->error);
        return [];
    }
}

function getTopSellingServices($conn, $businessID) {
    $sql = "SELECT s.serviceName, s.serviceID, 
                   COUNT(t.transactionID) AS num_sold
            FROM Transactions t
            JOIN Services s ON t.serviceID = s.serviceID
            WHERE t.businessID = ?
            GROUP BY s.serviceID
            ORDER BY num_sold DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();
        $topServices = [];
        while ($row = $result->fetch_assoc()) {
            $topServices[] = $row;
        }
        $stmt->close();
        return $topServices;
    } else {
        error_log("Error preparing top services query: " . $conn->error);
        return [];
    }
}

function getTopSellingSubservices($conn, $businessID) {
    $sql = "SELECT ss.subserviceName, ss.subserviceID, 
                   COUNT(t.transactionID) AS num_sold
            FROM Transactions t
            JOIN SubServices ss ON t.subserviceID = ss.subserviceID
            WHERE t.businessID = ?
            GROUP BY ss.subserviceID
            ORDER BY num_sold DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $businessID);
        $stmt->execute();
        $result = $stmt->get_result();
        $topSubservices = [];
        while ($row = $result->fetch_assoc()) {
            $topSubservices[] = $row;
        }
        $stmt->close();
        return $topSubservices;
    } else {
        error_log("Error preparing top subservices query: " . $conn->error);
        return [];
    }
}

?>