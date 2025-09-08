<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'client') {
    header("Location: ../login.php");
    exit;
}


require_once('../config.php');


// Handle file upload
$picture_path = '';
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
   
    $file_extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $filename;
   
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
        $picture_path = $target_file;
    }
}


// Check if profile already exists
$stmt = $conn->prepare("SELECT id FROM client_profiles WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


if ($result->num_rows > 0) {
    // Update existing profile
    if (!empty($picture_path)) {
        $stmt = $conn->prepare("UPDATE client_profiles SET name=?, age=?, skintone=?, picture=?, skin_condition=?, suffering_from=?, goals=?, gender=?, condition_start=?, taken_meds=?, products_used=?, blood_group=? WHERE user_id=?");
        $stmt->bind_param("sissssssssssi", $_POST['name'], $_POST['age'], $_POST['skintone'], $picture_path, $_POST['skin_condition'], $_POST['suffering_from'], $_POST['goals'], $_POST['gender'], $_POST['condition_start'], $_POST['taken_meds'], $_POST['products_used'], $_POST['blood_group'], $_SESSION['id']);
    } else {
        $stmt = $conn->prepare("UPDATE client_profiles SET name=?, age=?, skintone=?, skin_condition=?, suffering_from=?, goals=?, gender=?, condition_start=?, taken_meds=?, products_used=?, blood_group=? WHERE user_id=?");
        $stmt->bind_param("issssssssssi", $_POST['name'], $_POST['age'], $_POST['skintone'], $_POST['skin_condition'], $_POST['suffering_from'], $_POST['goals'], $_POST['gender'], $_POST['condition_start'], $_POST['taken_meds'], $_POST['products_used'], $_POST['blood_group'], $_SESSION['id']);
    }
} else {
    // Insert new profile
    $stmt = $conn->prepare("INSERT INTO client_profiles (user_id, name, age, skintone, picture, skin_condition, suffering_from, goals, gender, condition_start, taken_meds, products_used, blood_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssssss", $_SESSION['id'], $_POST['name'], $_POST['age'], $_POST['skintone'], $picture_path, $_POST['skin_condition'], $_POST['suffering_from'], $_POST['goals'], $_POST['gender'], $_POST['condition_start'], $_POST['taken_meds'], $_POST['products_used'], $_POST['blood_group']);
}


if ($stmt->execute()) {
    header("Location: client-dashboard.php?success=1");
} else {
    header("Location: client-dashboard.php?error=1");
}
$stmt->close();
$conn->close();
?>

