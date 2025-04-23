<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
session_start();

// $businessU = 'soccerstarz';
// $businessN = 'Soccer Starz';
// $serviceName = 'Lane Rentals'; //will be set on page using post method

$conn = connectToDatabase();

//echo getUserID($conn);

//test with get businessID function (when given a business name)

//$businessID = getBusinessID($conn, $business);

//echo getBusinessName($conn, $businessID);

// echo getBusinessUsername($conn, $businessN);

// var_dump($businessID);

// $serviceID = getServiceID($conn, getBusinessID($conn, $business), $serviceName);

//echo $serviceID;

// $subserviceName = '2 hr Lane Rental'; //will get by user input

// $subserviceID = getSubserviceID($conn, $serviceID, $subserviceName);

// echo $subserviceID;

// $price = getCost($conn, $subserviceID);

// echo $price;

// $services = getAllServices($conn, $businessID);

// $subservices = getAllSubservices($conn, $serviceID);

// $keyword = 'Rental';

// $searchResults = searchSubservices($conn, $serviceID, $keyword);

// $_SESSION['username'] = 'ecole12';
//$username = $_SESSION['username'];

// $comps = getCompsForUser($conn, getUserID($conn));

// if (is_array($searchResults)) {
//     foreach ($searchResults as $result) {
//         if (is_array($result)) { // Check if $business is an array
//             foreach ($result as $key => $value) {
//                 echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
//             }
//             echo "<br>"; // Add a line break between businesses
//         } else {
//             echo "service data is not an array.<br>";
//         }
//     }
// } else {
//     echo "services array is not an array.<br>";
// }
// $username = 'soccerstarz';
// echo getBusinessID($conn, $username);

// echo getBusinessName($conn, $businessID);

// $userID = 1;
// $businessID = 3;
// $serviceID = 9;
// $subserviceID = 22;
// $rating = 4;
// $comment = 'had a great experience!';

//leaveReview($conn, $userID, $businessID, $serviceID, $subserviceID, $rating, $comment);

// $reviews = getReviewsForSubservice($conn, $subserviceID);

// if (is_array($reviews)) {
//     foreach ($reviews as $review) {
//         if (is_array($review)) {
//             foreach ($review as $key => $value) {
//                 echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
//             }
//             echo "<br>"; // Add a line break between businesses
//         } else {
//             echo "service data is not an array.<br>";
//         }
//     }
// } else {
//     echo "reviews array is not an array.<br>";
// }

// $subserviceID = 83;

// deleteSubservice($conn, $subserviceID);

// $_SESSION['username'] = 'amazingathletics';
// $transactions = getTransactionsForBusiness($conn);

// if (is_array($transactions)) {
//     foreach ($transactions as $transaction) {
//         if (is_array($transaction)) {
//             foreach ($transaction as $key => $value) {
//                 echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
//             }
//             echo "<br>"; // Add a line break between businesses
//         } else {
//             echo "service data is not an array.<br>";
//         }
//     }
// } else {
//     echo "reviews array is not an array.<br>";
// }

//$subserviceID = 9;
//echo getServiceRevenue($conn, $serviceID);
//echo getSubserviceRevenue($conn, $subserviceID);

//$userID = 1;
//$businessID = 1;
//$message = "Do you have any questions about our business?";
//$sender = "business";
//sendMessage($conn, $userID, $businessID, $message, $sender);
//$messages = getMessages($conn, $userID, $businessID);
//$inquiries = getInquiriesForBusiness($conn, $businessID);
$bannedUsers = getAllBannedUsers($conn);

if (is_array($bannedUsers)) {
    foreach ($bannedUsers as $user) {
        if (is_array($user)) {
            foreach ($user as $key => $value) {
                echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
            }
            echo "<br>"; // Add a line break between businesses
        } else {
            echo "customer data is not an array.<br>";
        }
    }
} else {
    echo "customers array is not an array.<br>";
}

//$inquiryID = 10;
//echo $inquiryID;
//deleteInquiry($conn, $inquiryID);
//unbanUser($conn, "Test3");
//echo ":)";

?>