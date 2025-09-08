<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'client') {
    http_response_code(403);
    exit;
}


require_once('../config.php');


// Get all clients except the current user
$sql = "SELECT * FROM client_profiles WHERE user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();


$clients = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
}


header('Content-Type: application/json');
echo json_encode($clients);


$stmt->close();
$conn->close();
?>

