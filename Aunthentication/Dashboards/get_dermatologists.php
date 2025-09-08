<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


// Include the database configuration
require_once('../config.php');


// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}


// Get all dermatologists with their profiles
$sql = "SELECT u.id as dermatologist_id, u.name, u.email,
               p.years_experience, p.address, p.experience1,
               p.experience2, p.availability, p.quote, p.profile_image
        FROM users u
        LEFT JOIN dermatologist_profiles p ON u.id = p.dermatologist_id
        WHERE u.role = 'dermatologist'
        ORDER BY p.years_experience DESC, u.name ASC";


$result = $conn->query($sql);
$dermatologists = [];


if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Handle potential NULL values from LEFT JOIN
            if (empty($row['years_experience'])) $row['years_experience'] = 0;
            if (empty($row['experience1'])) $row['experience1'] = 'Dermatology specialist';
            if (empty($row['experience2'])) $row['experience2'] = 'Skin care expert';
            if (empty($row['availability'])) $row['availability'] = 'Schedule varies';
            if (empty($row['quote'])) $row['quote'] = 'Committed to providing the best skin care solutions.';
           
            $dermatologists[] = $row;
        }
    }
    echo json_encode($dermatologists);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
}


$conn->close();
?>

