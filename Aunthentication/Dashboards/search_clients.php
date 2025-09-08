<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'client') {
    http_response_code(403);
    exit;
}


require_once('../config.php');


$condition = '%' . $_POST['condition'] . '%';


$sql = "SELECT * FROM client_profiles WHERE skin_condition LIKE ? AND user_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $condition, $_SESSION['id']);
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

