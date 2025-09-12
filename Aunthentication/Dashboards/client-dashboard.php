<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'client') {
    header("Location: ../login.php");
    exit;
}




// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "skincare_db";




$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Handle profile form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $skintone = $_POST['skintone'];
    $skin_condition = $_POST['skin_condition'];
    $suffering_from = $_POST['suffering_from'];
    $goals = $_POST['goals'];
    $gender = $_POST['gender'];
    $condition_start = $_POST['condition_start'];
    $taken_meds = $_POST['taken_meds'];
    $products_used = $_POST['products_used'];
    $blood_group = $_POST['blood_group'];
   
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
    $check_stmt = $conn->prepare("SELECT id FROM client_profiles WHERE user_id = ?");
    $check_stmt->bind_param("i", $_SESSION['id']);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
   
    if ($result->num_rows > 0) {
        // Update existing profile
        if (!empty($picture_path)) {
            $stmt = $conn->prepare("UPDATE client_profiles SET name=?, age=?, skintone=?, picture=?, skin_condition=?, suffering_from=?, goals=?, gender=?, condition_start=?, taken_meds=?, products_used=?, blood_group=? WHERE user_id=?");
            $stmt->bind_param("sissssssssssi", $name, $age, $skintone, $picture_path, $skin_condition, $suffering_from, $goals, $gender, $condition_start, $taken_meds, $products_used, $blood_group, $_SESSION['id']);
        } else {
            $stmt = $conn->prepare("UPDATE client_profiles SET name=?, age=?, skintone=?, skin_condition=?, suffering_from=?, goals=?, gender=?, condition_start=?, taken_meds=?, products_used=?, blood_group=? WHERE user_id=?");
            $stmt->bind_param("issssssssssi", $name, $age, $skintone, $skin_condition, $suffering_from, $goals, $gender, $condition_start, $taken_meds, $products_used, $blood_group, $_SESSION['id']);
        }
    } else {
        // Insert new profile
        $stmt = $conn->prepare("INSERT INTO client_profiles (user_id, name, age, skintone, picture, skin_condition, suffering_from, goals, gender, condition_start, taken_meds, products_used, blood_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssss", $_SESSION['id'], $name, $age, $skintone, $picture_path, $skin_condition, $suffering_from, $goals, $gender, $condition_start, $taken_meds, $products_used, $blood_group);
    }
   
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
    $stmt->close();
}




// Get user profile if exists
$profile = null;
$profile_stmt = $conn->prepare("SELECT * FROM client_profiles WHERE user_id = ?");
$profile_stmt->bind_param("i", $_SESSION['id']);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();
if ($profile_result->num_rows > 0) {
    $profile = $profile_result->fetch_assoc();
}
$profile_stmt->close();




// Get products
$products = [];
$products_result = $conn->query("SELECT * FROM products");
if ($products_result->num_rows > 0) {
    while ($row = $products_result->fetch_assoc()) {
        $products[] = $row;
    }
}




// Get clients (excluding current user)
$clients = [];
$clients_result = $conn->query("SELECT * FROM client_profiles WHERE user_id != " . $_SESSION['id']);
if ($clients_result->num_rows > 0) {
    while ($row = $clients_result->fetch_assoc()) {
        $clients[] = $row;
    }
}




// Get dermatologists
$dermatologists = [];
$derm_result = $conn->query("SELECT * FROM dermatologist_profiles");
if ($derm_result->num_rows > 0) {
    while ($row = $derm_result->fetch_assoc()) {
        $dermatologists[] = $row;
    }
}




// Handle search
$search_results = [];
if (isset($_GET['search_condition'])) {
    $search_condition = $conn->real_escape_string($_GET['search_condition']);
    $search_query = "SELECT * FROM client_profiles WHERE skin_condition LIKE '%$search_condition%' AND user_id != " . $_SESSION['id'];
    $search_result = $conn->query($search_query);
    if ($search_result->num_rows > 0) {
        while ($row = $search_result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}




// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
   
    $review_stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
    $review_stmt->bind_param("iiis", $product_id, $_SESSION['id'], $rating, $review_text);
   
    if ($review_stmt->execute()) {
        $review_success = "Review submitted successfully!";
    } else {
        $review_error = "Error submitting review: " . $conn->error;
    }
    $review_stmt->close();
}




// Get reviews for products




$reviews = [];
$reviews_result = $conn->query("SELECT pr.*, cp.name FROM product_reviews pr JOIN client_profiles cp ON pr.user_id = cp.user_id");
if ($reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6f42c1; /* Purple */
            --secondary-color: #fd7e14; /* Orange */
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
       
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
       
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            transition: all 0.3s;
        }
       
        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid var(--secondary-color);
        }
       
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
       
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
       
        .card:hover {
            transform: translateY(-5px);
        }
       
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
       
        .btn-primary:hover {
            background-color: #5a32a3;
            border-color: #5a32a3;
        }
       
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
       
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
       
        .badge-orange {
            background-color: var(--secondary-color);
            color: white;
        }
       
        .tab-content {
            display: none;
        }
       
        .tab-content.active {
            display: block;
        }
       
        .star-rating {
            color: var(--secondary-color);
            font-size: 1.2rem;
        }
       
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
        }
       
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
           
            .main-content {
                margin-left: 0;
            }
        }




    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-center mb-4">Skincare Hub</h3>
                <div class="list-group">
                    <a href="#" class="active" data-tab="profile"><i class="fas fa-user me-2"></i> My Profile</a>
                    <a href="#" data-tab="products"><i class="fas fa-capsules me-2"></i> Products</a>
                    <a href="#" data-tab="reviews"><i class="fas fa-star me-2"></i> Reviews</a>
                    <a href="#" data-tab="clients"><i class="fas fa-users me-2"></i> Other Clients</a>
                    <a href="#" data-tab="dermatologists"><i class="fas fa-user-md me-2"></i> Dermatologists</a>
                    <a href="#" data-tab="search"><i class="fas fa-search me-2"></i> Search</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </div>
            </div>




            <!-- Main Content -->




           
            <div class="col-md-9 col-lg-10 main-content">
                <h2 class="mb-4">Welcome, <?php echo $_SESSION['name']; ?>!</h2>




                <!-- Add this button to the card header -->




                <!-- Profile Tab -->








                <div id="profile" class="tab-content active">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">My Profile</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success"><?php echo $success_message; ?></div>
                            <?php endif; ?>
                           
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                           
                            <?php if ($profile): ?>
                                <div class="text-center mb-4">
                                    <?php if (!empty($profile['picture'])): ?>
                                        <img src="<?php echo $profile['picture']; ?>" class="profile-img" alt="Profile Picture">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/150" class="profile-img" alt="Profile Picture">
                                    <?php endif; ?>
                                    <h4 class="mt-3"><?php echo $profile['name']; ?></h4>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">You haven't created your profile yet. Please fill out the form below.</p>
                            <?php endif; ?>




                           
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name"  
                                                value="<?php echo $profile ? $profile['name'] : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="number" class="form-control" id="age" name="age"
                                                value="<?php echo $profile ? $profile['age'] : ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="skintone" class="form-label">Skin Tone</label>
                                            <select class="form-select" id="skintone" name="skintone" required>
                                                <option value="">Select Skin Tone</option>
                                                <option value="Very Fair" <?php echo ($profile && $profile['skintone'] == 'Very Fair') ? 'selected' : ''; ?>>Very Fair</option>
                                                <option value="Fair" <?php echo ($profile && $profile['skintone'] == 'Fair') ? 'selected' : ''; ?>>Fair</option>
                                                <option value="Medium" <?php echo ($profile && $profile['skintone'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                                <option value="Olive" <?php echo ($profile && $profile['skintone'] == 'Olive') ? 'selected' : ''; ?>>Olive</option>
                                                <option value="Tan" <?php echo ($profile && $profile['skintone'] == 'Tan') ? 'selected' : ''; ?>>Tan</option>
                                                <option value="Brown" <?php echo ($profile && $profile['skintone'] == 'Brown') ? 'selected' : ''; ?>>Brown</option>
                                                <option value="Dark Brown" <?php echo ($profile && $profile['skintone'] == 'Dark Brown') ? 'selected' : ''; ?>>Dark Brown</option>
                                                <option value="Black" <?php echo ($profile && $profile['skintone'] == 'Black') ? 'selected' : ''; ?>>Black</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="picture" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control" id="picture" name="picture">
                                            <?php if ($profile && !empty($profile['picture'])): ?>
                                                <small class="text-muted">Current: <?php echo basename($profile['picture']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="skin_condition" class="form-label">Skin Condition</label>
                                    <textarea class="form-control" id="skin_condition" name="skin_condition" rows="2" required><?php echo $profile ? $profile['skin_condition'] : ''; ?></textarea>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="suffering_from" class="form-label">What are you suffering from?</label>
                                    <textarea class="form-control" id="suffering_from" name="suffering_from" rows="2" required><?php echo $profile ? $profile['suffering_from'] : ''; ?></textarea>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="goals" class="form-label">What do you want to achieve?</label>
                                    <textarea class="form-control" id="goals" name="goals" rows="2" required><?php echo $profile ? $profile['goals'] : ''; ?></textarea>
                                </div>
                               
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-select" id="gender" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male" <?php echo ($profile && $profile['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo ($profile && $profile['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="other" <?php echo ($profile && $profile['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="condition_start" class="form-label">When did the skin condition start?</label>
                                            <input type="date" class="form-control" id="condition_start" name="condition_start"
                                                value="<?php echo $profile ? $profile['condition_start'] : ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Have you taken any medications before?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="taken_meds" id="taken_meds_yes" value="yes"
                                                        <?php echo ($profile && $profile['taken_meds'] == 'yes') ? 'checked' : ''; ?> required>
                                                    <label class="form-check-label" for="taken_meds_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="taken_meds" id="taken_meds_no" value="no"
                                                        <?php echo ($profile && $profile['taken_meds'] == 'no') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="taken_meds_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="blood_group" class="form-label">Blood Group</label>
                                            <select class="form-select" id="blood_group" name="blood_group" required>
                                                <option value="">Select Blood Group</option>
                                                <option value="A+" <?php echo ($profile && $profile['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                                <option value="A-" <?php echo ($profile && $profile['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                                <option value="B+" <?php echo ($profile && $profile['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                                <option value="B-" <?php echo ($profile && $profile['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                                <option value="AB+" <?php echo ($profile && $profile['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                                <option value="AB-" <?php echo ($profile && $profile['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                                <option value="O+" <?php echo ($profile && $profile['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                                <option value="O-" <?php echo ($profile && $profile['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="mb-3">
                                    <label for="products_used" class="form-label">Name the products you have used before</label>
                                    <textarea class="form-control" id="products_used" name="products_used" rows="3"><?php echo $profile ? $profile['products_used'] : ''; ?></textarea>
                                </div>
                               
                                <button type="submit" class="btn btn-primary"><?php echo $profile ? 'Update Profile' : 'Create Profile'; ?></button>
                            </form>
                        </div>
                    </div>
                </div>
               
                <!-- Products Tab -->
                <div id="products" class="tab-content">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Products</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (count($products) > 0): ?>
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100">
                                                <img src="<?php echo $product['image'] ? $product['image'] : 'https://via.placeholder.com/300x200'; ?>"
                                                     class="card-img-top" alt="<?php echo $product['name']; ?>"
                                                     style="height: 200px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                                    <p class="card-text"><?php echo substr($product['description'], 0, 100); ?>...</p>
                                                </div>
                                                <div class="card-footer">
                                                    <button class="btn btn-outline-primary btn-sm review-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#reviewModal"
                                                            data-product-id="<?php echo $product['id']; ?>"
                                                            data-product-name="<?php echo $product['name']; ?>">
                                                        Review
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted">No products found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
               
                <!-- Reviews Tab -->
                <div id="reviews" class="tab-content">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Product Reviews</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($review_success)): ?>
                                <div class="alert alert-success"><?php echo $review_success; ?></div>
                            <?php endif; ?>
                           
                            <?php if (isset($review_error)): ?>
                                <div class="alert alert-danger"><?php echo $review_error; ?></div>
                            <?php endif; ?>
                           
                            <?php if (count($reviews) > 0): ?>
                                <div class="row">
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?php echo $review['name']; ?></h5>
                                                    <div class="star-rating">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star<?php echo $i <= $review['rating'] ? '' : '-empty'; ?>"></i>
                                                        <?php endfor; ?>
                                                        <span class="ms-2">(<?php echo $review['rating']; ?>/5)</span>
                                                    </div>
                                                    <p class="text-muted">Posted on: <?php echo date('M j, Y', strtotime($review['created_at'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No reviews found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
               
                <!-- Clients Tab -->
                <div id="clients" class="tab-content">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Other Clients</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (count($clients) > 0): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <?php if (!empty($client['picture'])): ?>
                                                            <img src="<?php echo $client['picture']; ?>" class="rounded-circle me-3" alt="Profile" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <img src="https://via.placeholder.com/60" class="rounded-circle me-3" alt="Profile" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php endif; ?>
                                                        <div>
                                                            <h5 class="card-title mb-0"><?php echo $client['name']; ?></h5>
                                                            <p class="text-muted mb-0">Age: <?php echo $client['age']; ?></p>
                                                        </div>
                                                    </div>
                                                    <p><strong>Skin Tone:</strong> <?php echo $client['skintone']; ?></p>
                                                    <p><strong>Skin Condition:</strong> <?php echo substr($client['skin_condition'], 0, 100); ?>...</p>
                                                    <p><strong>Goals:</strong> <?php echo substr($client['goals'], 0, 100); ?>...</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted">No other clients found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
               
                <!-- Dermatologists Tab -->
                <div id="dermatologists" class="tab-content">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Dermatologists</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (count($dermatologists) > 0): ?>
                                    <?php foreach ($dermatologists as $derm): ?>
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100">
                                                    <div class="card-body">
                                                    <h5 class="card-title"><?php echo $derm['name']; ?></h5>
                                                    <p class="card-text"><?php echo substr($derm['years_experience'], 0, 100); ?>...</p>
                                                    <p><strong>Specialization:</strong> <?php echo $derm['experience1']; ?></p>
                                                    <p><strong>Experience:</strong> <?php echo $derm['experience2']; ?> years</p>
                                                    <p><strong>Availability:</strong> <?php echo $derm['availability']; ?> years</p>
                                                    <p><strong>Personal Statement:</strong> <?php echo $derm['quote']; ?></p>




                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted">No dermatologists found.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
               
                <!-- Search Tab -->
                <div id="search" class="tab-content">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Search Clients by Skin Condition</h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <input type="hidden" name="tab" value="search">
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="search_condition"
                                           placeholder="Enter skin condition to search"
                                           value="<?php echo isset($_GET['search_condition']) ? $_GET['search_condition'] : ''; ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                           
                            <?php if (isset($_GET['search_condition'])): ?>
                                <div class="mt-4">
                                    <h5>Search Results</h5>
                                    <?php if (count($search_results) > 0): ?>
                                        <div class="row">
                                            <?php foreach ($search_results as $client): ?>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center mb-3">
                                                                <?php if (!empty($client['picture'])): ?>
                                                                    <img src="<?php echo $client['picture']; ?>" class="rounded-circle me-3" alt="Profile" style="width: 60px; height: 60px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <img src="https://via.placeholder.com/60" class="rounded-circle me-3" alt="Profile" style="width: 60px; height: 60px; object-fit: cover;">
                                                                <?php endif; ?>
                                                                <div>
                                                                    <h5 class="card-title mb-0"><?php echo $client['name']; ?></h5>
                                                                    <p class="text-muted mb-0">Age: <?php echo $client['age']; ?></p>
                                                                </div>
                                                            </div>
                                                            <p><strong>Skin Tone:</strong> <?php echo $client['skintone']; ?></p>
                                                            <p><strong>Skin Condition:</strong> <?php echo $client['skin_condition']; ?></p>
                                                            <p><strong>Suffering From:</strong> <?php echo $client['suffering_from']; ?></p>
                                                            <p><strong>Products Used:</strong> <?php echo $client['products_used']; ?></p>
                                                            <p><strong>Medicin Taken:</strong> <?php echo $client['taken_meds']; ?></p>




                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No clients found with that skin condition.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Product: <span id="review-product-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="submit_review" value="1">
                        <input type="hidden" name="product_id" id="review-product-id">
                       
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="">Select Rating</option>
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                       
                        <div class="mb-3">
                            <label for="review_text" class="form-label">Review</label>
                            <textarea class="form-control" id="review_text" name="review_text" rows="3" required></textarea>
                        </div>
                       
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tab navigation
        document.querySelectorAll('.sidebar a[data-tab]').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
               
                // Remove active class from all tabs and links
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.querySelectorAll('.sidebar a').forEach(link => {
                    link.classList.remove('active');
                });
               
                // Add active class to current tab and link
                const tabName = this.getAttribute('data-tab');
                document.getElementById(tabName).classList.add('active');
                this.classList.add('active');
               
                // Update URL with tab parameter
                const url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', url);
            });
        });
       
        // Check for tab parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.classList.remove('active');
            });
           
            document.getElementById(activeTab).classList.add('active');
            document.querySelector(`.sidebar a[data-tab="${activeTab}"]`).classList.add('active');
        }
       
        // Review modal functionality
        const reviewModal = document.getElementById('reviewModal');
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const productId = button.getAttribute('data-product-id');
                const productName = button.getAttribute('data-product-name');
               
                document.getElementById('review-product-id').value = productId;
                document.getElementById('review-product-name').textContent = productName;
            });
        }
    </script>




    <!-- Add this modal at the end of your HTML, before closing body tag -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="profileModalLabel">My Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($profile): ?>
                    <div class="text-center mb-4">
                        <?php if (!empty($profile['picture'])): ?>
                            <img src="<?php echo $profile['picture']; ?>" class="profile-img" alt="Profile Picture">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/150" class="profile-img" alt="Profile Picture">
                        <?php endif; ?>
                        <h4 class="mt-3"><?php echo htmlspecialchars($profile['name']); ?></h4>
                    </div>
                   
                    <div class="row">
                        <div class="col-md-6">
                            <div class="profile-detail">
                                <strong>Age:</strong> <?php echo htmlspecialchars($profile['age']); ?>
                            </div>
                            <div class="profile-detail">
                                <strong>Skin Tone:</strong> <?php echo htmlspecialchars($profile['skintone']); ?>
                            </div>
                            <div class="profile-detail">
                                <strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($profile['gender'])); ?>
                            </div>
                            <div class="profile-detail">
                                <strong>Blood Group:</strong> <?php echo htmlspecialchars($profile['blood_group']); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-detail">
                                <strong>Condition Start:</strong> <?php echo htmlspecialchars($profile['condition_start']); ?>
                            </div>
                            <div class="profile-detail">
                                <strong>Taken Medications:</strong> <?php echo htmlspecialchars(ucfirst($profile['taken_meds'])); ?>
                            </div>
                        </div>
                    </div>
                   
                    <div class="profile-detail">
                        <strong>Skin Condition:</strong> <?php echo htmlspecialchars($profile['skin_condition']); ?>
                    </div>
                    <div class="profile-detail">
                        <strong>Suffering From:</strong> <?php echo htmlspecialchars($profile['suffering_from']); ?>
                    </div>
                    <div class="profile-detail">
                        <strong>Goals:</strong> <?php echo htmlspecialchars($profile['goals']); ?>
                    </div>
                    <div class="profile-detail">
                        <strong>Products Used:</strong> <?php echo !empty($profile['products_used']) ? htmlspecialchars($profile['products_used']) : 'None'; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No profile data available.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Edit Profile</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>







