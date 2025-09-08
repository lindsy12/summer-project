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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}


// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);


// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
    $dermatologist_id = $data['dermatologist_id'];
    $patient_name = $data['patient_name'];
    $patient_email = $data['patient_email'];
    $patient_phone = $data['patient_phone'];
    $appointment_date = $data['appointment_date'];
    $appointment_time = $data['appointment_time'];
    $concern = $data['concern'];
    $status = 'pending'; // Default status
   
    // Check if appointments table exists, create if not
    $checkTable = "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dermatologist_id INT NOT NULL,
        patient_name VARCHAR(100) NOT NULL,
        patient_email VARCHAR(100) NOT NULL,
        patient_phone VARCHAR(20) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        concern TEXT NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (dermatologist_id) REFERENCES users(id) ON DELETE CASCADE
    )";
   
    if (!$conn->query($checkTable)) {
        echo json_encode(['success' => false, 'message' => 'Error creating table: ' . $conn->error]);
        exit;
    }
   
    // Insert appointment
    $stmt = $conn->prepare("INSERT INTO appointments (dermatologist_id, patient_name, patient_email, patient_phone, appointment_date, appointment_time, concern, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $dermatologist_id, $patient_name, $patient_email, $patient_phone, $appointment_date, $appointment_time, $concern, $status);
   
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error booking appointment: ' . $conn->error]);
    }
   
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method or data']);
}


$conn->close();
?>  


