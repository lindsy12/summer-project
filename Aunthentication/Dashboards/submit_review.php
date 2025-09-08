<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'client') {
    http_response_code(403);
    exit;
}


require_once('../config.php');


$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];


// Check if user already reviewed this product
$stmt = $conn->prepare("SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $_SESSION['id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    // Update existing review
    $stmt = $conn->prepare("UPDATE product_reviews SET rating = ?, review = ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("isii", $rating, $review, $_SESSION['id'], $product_id);
} else {
    // Insert new review
    $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_id, $_SESSION['id'], $rating, $review);
}


if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}


$stmt->close();
$conn->close();
?>

