<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'dermatologist') {
    header("Location: ../login.php");
    exit;
}


include "../config.php";


// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $years_experience = $_POST['years_experience'];
    $address = $_POST['address'];
    $experience1 = $_POST['experience1'];
    $experience2 = $_POST['experience2'];
    $availability = $_POST['availability'];
    $quote = $_POST['quote'];
   
    
    // Handle image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/profiles/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
       
        $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $file_name = "profile_" . $_SESSION['id'] . "_" . time() . "." . $file_extension;
        $file_path = $upload_dir . $file_name;
       
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
            $profile_image = "uploads/profiles/" . $file_name;
        }
    }
   
    
    // Check if profile already exists
    $check_sql = "SELECT id, profile_image FROM dermatologist_profiles WHERE dermatologist_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $_SESSION['id']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
   
    if ($check_result->num_rows > 0) {
        $existing_profile = $check_result->fetch_assoc();
        $current_image = $existing_profile['profile_image'];
       
        
        // Update existing profile
        if ($profile_image) {
            // Delete old image if it exists
            if ($current_image && file_exists("../" . $current_image)) {
                unlink("../" . $current_image);
            }
            $sql = "UPDATE dermatologist_profiles SET name=?, years_experience=?, address=?, experience1=?, experience2=?, availability=?, quote=?, profile_image=? WHERE dermatologist_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissssssi", $name, $years_experience, $address, $experience1, $experience2, $availability, $quote, $profile_image, $_SESSION['id']);
        } else {
            $sql = "UPDATE dermatologist_profiles SET name=?, years_experience=?, address=?, experience1=?, experience2=?, availability=?, quote=? WHERE dermatologist_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisssssi", $name, $years_experience, $address, $experience1, $experience2, $availability, $quote, $_SESSION['id']);
        }
    } else {
        // Insert new profile
        $sql = "INSERT INTO dermatologist_profiles (dermatologist_id, name, years_experience, address, experience1, experience2, availability, quote, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isissssss", $_SESSION['id'], $name, $years_experience, $address, $experience1, $experience2, $availability, $quote, $profile_image);
    }
   
    if ($stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}



// Handle product review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];
   
    $sql = "INSERT INTO product_reviews (product_id, dermatologist_id, rating, review_text) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $product_id, $_SESSION['id'], $rating, $review_text);
   
    if ($stmt->execute()) {
        $review_success = "Review submitted successfully!";
    } else {
        $review_error = "Error submitting review: " . $conn->error;
    }
}


// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_appointment_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
   
    $sql = "UPDATE appointments SET status = ? WHERE id = ? AND dermatologist_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $status, $appointment_id, $_SESSION['id']);
   
    if ($stmt->execute()) {
        $appointment_success = "Appointment status updated successfully!";
    } else {
        $appointment_error = "Error updating appointment status: " . $conn->error;
    }
}


// Get dermatologist's profile
$profile = null;
$sql = "SELECT * FROM dermatologist_profiles WHERE dermatologist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $profile = $result->fetch_assoc();
}


// Get all dermatologists (including those without profiles)
$dermatologists_sql = "SELECT u.id, u.name, u.email, p.years_experience, p.address, p.experience1, p.experience2, p.availability, p.quote, p.profile_image
                       FROM users u
                       LEFT JOIN dermatologist_profiles p ON u.id = p.dermatologist_id
                       WHERE u.role = 'dermatologist'";
$dermatologists_result = $conn->query($dermatologists_sql);


// Get all products
$products_sql = "SELECT * FROM products";
$products_result = $conn->query($products_sql);


// Get dermatologist's reviews
$my_reviews_sql = "SELECT pr.*, p.name as product_name
                   FROM product_reviews pr
                   JOIN products p ON pr.product_id = p.id
                   WHERE pr.dermatologist_id = ?
                   ORDER BY pr.created_at DESC";
$my_reviews_stmt = $conn->prepare($my_reviews_sql);
$my_reviews_stmt->bind_param("i", $_SESSION['id']);
$my_reviews_stmt->execute();
$my_reviews_result = $my_reviews_stmt->get_result();


// Get all reviews
$all_reviews_sql = "SELECT pr.*, p.name as product_name, u.name as dermatologist_name
                    FROM product_reviews pr
                    JOIN products p ON pr.product_id = p.id
                    JOIN users u ON pr.dermatologist_id = u.id
                    ORDER BY pr.created_at DESC";
$all_reviews_result = $conn->query($all_reviews_sql);


// Get appointments for this dermatologist
$appointments_sql = "SELECT a.*, u.name as patient_name, u.email as patient_email
                     FROM appointments a
                     JOIN users u ON a.patient_email = u.email
                     WHERE a.dermatologist_id = ?
                     ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments_stmt = $conn->prepare($appointments_sql);
$appointments_stmt->bind_param("i", $_SESSION['id']);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dermatologist Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-purple: #6a11cb;
            --secondary-orange: #ff7e5f;
            --light-purple: #f0e6ff;
            --dark-purple: #4d0ca2;
            --light-orange: #ffefe6;
        }
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        body {
            background-color: #f8f9fa;
            color: #333;
        }
       
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
       
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, var(--primary-purple), var(--dark-purple));
            color: white;
            padding: 1.5rem 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
       
        .sidebar-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
       
        .sidebar-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
       
        .sidebar-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
       
        .nav-links {
            list-style: none;
        }
       
        .nav-links li {
            margin-bottom: 0.5rem;
        }
       
        .nav-links a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
       
        .nav-links a:hover, .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }
       
        .nav-links a i {
            margin-right: 0.75rem;
        }
       
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }
       
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
       
        .welcome-message h1 {
            color: var(--primary-purple);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
       
        .welcome-message p {
            color: #666;
        }
       
        .logout-btn {
            background: linear-gradient(to right, var(--primary-purple), var(--secondary-orange));
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
       
        .tab-content {
            display: none;
        }
       
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }
       
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
       
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
       
        .card-header {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .card-header h2 {
            color: var(--primary-purple);
            font-size: 1.4rem;
        }
       
        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
       
        .data-table th, .data-table td {
            padding: 0.45rem 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
       
        .data-table th {
            background-color: var(--light-purple);
            color: var(--primary-purple);
            font-weight: 600;
        }
       
        .data-table tr:hover {
            background-color: #f9f9f9;
        }
       
        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }
       
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }
       
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
       
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
       
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
       
        .btn-primary {
            background: linear-gradient(to right, var(--primary-purple), var(--secondary-orange));
            color: white;
        }
       
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
       
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
       
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
       
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
       
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
       
        /* Image Upload Styles */
        .image-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }
       
        .image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid var(--light-purple);
            overflow: hidden;
            margin-bottom: 1rem;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
       
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
       
        .image-preview i {
            font-size: 3rem;
            color: #ccc;
        }
       
        .upload-btn {
            background-color: var(--light-purple);
            color: var(--primary-purple);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
        }
       
        .upload-btn:hover {
            background-color: var(--primary-purple);
            color: white;
        }
       
        .upload-btn input {
            display: none;
        }
       
        /* Profile Card Styles */
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: flex-start;
        }
       
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1.5rem;
            flex-shrink: 0;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
       
        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
       
        .profile-image i {
            font-size: 2rem;
            color: #ccc;
        }
       
        .profile-info {
            flex: 1;
        }
       
        .profile-info h3 {
            color: var(--primary-purple);
            margin-bottom: 0.5rem;
        }
       
        .profile-info p {
            margin-bottom: 0.5rem;
            color: #555;
        }
       
        .profile-quote {
            font-style: italic;
            color: var(--dark-purple);
            margin-top: 0.5rem;
            padding: 0.5rem;
            background-color: var(--light-purple);
            border-left: 3px solid var(--primary-purple);
            border-radius: 0 5px 5px 0;
        }
       
        /* Review Form Styles */
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
       
        .rating input {
            display: none;
        }
       
        .rating label {
            cursor: pointer;
            width: 30px;
            height: 30px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ccc"><polygon points="12,2 15,8 22,9 17,14 18,21 12,18 6,21 7,14 2,9 9,8"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 70%;
        }
       
        .rating input:checked ~ label {
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ffc107"><polygon points="12,2 15,8 22,9 17,14 18,21 12,18 6,21 7,14 2,9 9,8"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 70%;
        }
       
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
       
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
       
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }
           
            .main-content {
                margin-left: 200px;
            }
        }
       
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }
           
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 1rem;
            }
           
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
           
            .nav-links {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
           
            .nav-links li {
                margin: 0.25rem;
            }
           
            .header {
                flex-direction: column;
                text-align: center;
            }
           
            .welcome-message {
                margin-bottom: 1rem;
            }
           
            .profile-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
           
            .profile-image {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
       
        @media (max-width: 576px) {
            .card {
                padding: 1rem;
            }
           
            .data-table {
                font-size: 0.9rem;
            }
           
            .data-table th, .data-table td {
                padding: 0.5rem;
            }
           
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
           
            .profile-image {
                width: 80px;
                height: 80px;
            }
           
            .image-preview {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>SkinCare Expert</h2>
                <p>Dermatologist Dashboard</p>
            </div>
            <ul class="nav-links">
                <li><a href="#dermatologists" class="active" onclick="showTab('dermatologists')"><i class="fas fa-user-md"></i> Dermatologists</a></li>
                <li><a href="#products" onclick="showTab('products')"><i class="fas fa-pills"></i> Products</a></li>
                <li><a href="#profile" onclick="showTab('profile')"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="#appointments" onclick="showTab('appointments')"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="#review-product" onclick="showTab('review-product')"><i class="fas fa-star"></i> Review Product</a></li>
                <li><a href="#my-reviews" onclick="showTab('my-reviews')"><i class="fas fa-comments"></i> My Reviews</a></li>
            </ul>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome-message">
                    <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
                    <p>Manage your dermatology practice and review products</p>
                </div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>


            <!-- Dermatologists Tab -->
            <div id="dermatologists" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h2>All Dermatologists</h2>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Experience</th>
                                    <th>Specialization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dermatologists_result->data_seek(0);
                                while ($dermatologist = $dermatologists_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $dermatologist['name']; ?></td>
                                    <td><?php echo $dermatologist['email']; ?></td>
                                    <td><?php echo $dermatologist['years_experience'] ? $dermatologist['years_experience'] . ' years' : 'Not specified'; ?></td>
                                    <td>
                                        <?php
                                        if ($dermatologist['experience1']) {
                                            echo $dermatologist['experience1'];
                                            if ($dermatologist['experience2']) {
                                                echo ', ' . $dermatologist['experience2'];
                                            }
                                        } else {
                                            echo 'Not specified';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Products Tab -->
            <div id="products" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>All Products</h2>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $products_result->data_seek(0);
                                while ($product = $products_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo $product['name']; ?></td>
                                    <td>$<?php echo $product['price']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- My Profile Tab -->
            <div id="profile" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>My Profile</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="success-message"><?php echo $success; ?></div>
                        <?php endif; ?>
                       
                        <?php if (isset($error)): ?>
                            <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                       
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                           
                            <div class="image-upload">
                                <div class="image-preview" id="imagePreview">
                                    <?php if ($profile && $profile['profile_image']): ?>
                                        <img src="../<?php echo $profile['profile_image']; ?>" alt="Profile Image">
                                    <?php else: ?>
                                        <i class="fas fa-user-md"></i>
                                    <?php endif; ?>
                                </div>
                                <label class="upload-btn">
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(event)">
                                    Upload Photo
                                </label>
                            </div>
                           
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?php echo $profile ? $profile['name'] : $_SESSION['name']; ?>" required>
                            </div>
                           
                            <div class="form-group">
                                <label for="years_experience">Years of Experience</label>
                                <input type="number" id="years_experience" name="years_experience" class="form-control" value="<?php echo $profile ? $profile['years_experience'] : ''; ?>" required min="0">
                            </div>
                           
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" required><?php echo $profile ? $profile['address'] : ''; ?></textarea>
                            </div>
                           
                            <div class="form-group">
                                <label for="experience1">Primary Expertise/Experience</label>
                                <textarea id="experience1" name="experience1" class="form-control" required><?php echo $profile ? $profile['experience1'] : ''; ?></textarea>
                            </div>
                           
                            <div class="form-group">
                                <label for="experience2">Secondary Expertise/Experience</label>
                                <textarea id="experience2" name="experience2" class="form-control" required><?php echo $profile ? $profile['experience2'] : ''; ?></textarea>
                            </div>
                           
                            <div class="form-group">
                                <label for="availability">Availability</label>
                                <input type="text" id="availability" name="availability" class="form-control" value="<?php echo $profile ? $profile['availability'] : ''; ?>" required placeholder="e.g., Monday to Friday, 9am-5pm">
                            </div>
                           
                            <div class="form-group">
                                <label for="quote">Personal Quote</label>
                                <textarea id="quote" name="quote" class="form-control" required><?php echo $profile ? $profile['quote'] : ''; ?></textarea>
                            </div>
                           
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Appointments Tab -->
            <div id="appointments" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>My Appointments</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($appointment_success)): ?>
                            <div class="success-message"><?php echo $appointment_success; ?></div>
                        <?php endif; ?>
                       
                        <?php if (isset($appointment_error)): ?>
                            <div class="error-message"><?php echo $appointment_error; ?></div>
                        <?php endif; ?>
                       
                        <?php
                        // Get appointments for this dermatologist with corrected query
                        $appointments_sql = "SELECT a.*
                                             FROM appointments a
                                             WHERE a.dermatologist_id = ?
                                             ORDER BY a.appointment_date DESC, a.appointment_time DESC";
                        $appointments_stmt = $conn->prepare($appointments_sql);
                        $appointments_stmt->bind_param("i", $_SESSION['id']);
                        $appointments_stmt->execute();
                        $appointments_result = $appointments_stmt->get_result();
                       
                        if ($appointments_result->num_rows > 0):
                        ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Client Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Concern</th>
                                        <th>Status</th>
                                        <th>Booked On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($appointment = $appointments_result->fetch_assoc()):
                                        $status_class = '';
                                        switch ($appointment['status']) {
                                            case 'confirmed':
                                                $status_class = 'status-confirmed';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'status-cancelled';
                                                break;
                                            default:
                                                $status_class = 'status-pending';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['patient_email']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['patient_phone']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['concern']); ?></td>
                                        <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($appointment['status']); ?></span></td>
                                        <td><?php echo date('M j, Y', strtotime($appointment['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <input type="hidden" name="update_appointment_status" value="1">
                                                <div class="action-buttons">
                                                    <?php if ($appointment['status'] != 'confirmed'): ?>
                                                    <button type="submit" name="status" value="confirmed" class="btn btn-primary btn-sm" title="Confirm Appointment">
                                                        <i class="fas fa-check"></i> Confirm
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if ($appointment['status'] != 'cancelled'): ?>
                                                    <button type="submit" name="status" value="cancelled" class="btn btn-danger btn-sm" title="Cancel Appointment">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if ($appointment['status'] == 'confirmed' || $appointment['status'] == 'cancelled'): ?>
                                                    <button type="button" class="btn btn-info btn-sm view-details" data-appointment='<?php echo json_encode($appointment); ?>' title="View Details">
                                                        <i class="fas fa-eye"></i> Details
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="no-appointments">
                            <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--light-purple); margin-bottom: 1rem;"></i>
                            <h3>No Appointments Yet</h3>
                            <p>You don't have any appointments scheduled yet. Check back later or share your profile with clients.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- Appointment Details Modal -->
                <div class="modal" id="appointmentDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center;">
                    <div class="modal-content" style="background-color: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 500px;">
                        <span class="close-modal" onclick="closeAppointmentModal()" style="position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; cursor: pointer;">&times;</span>
                        <div class="modal-header">
                            <h2>Appointment Details</h2>
                        </div>
                        <div id="appointmentDetailsContent">
                            <!-- Details will be inserted here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>


            <style>
                .action-buttons {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
               
                .action-buttons button {
                    margin: 0;
                }
               
                .table-responsive {
                    overflow-x: auto;
                }
               
                .no-appointments {
                    text-align: center;
                    padding: 2rem;
                    color: var(--text-light);
                }
               
                .no-appointments h3 {
                    color: var(--primary-purple);
                    margin-bottom: 1rem;
                }
               
                @media (max-width: 768px) {
                    .action-buttons {
                        flex-direction: row;
                        flex-wrap: wrap;
                    }
                   
                    .data-table th:nth-child(3),
                    .data-table td:nth-child(3),
                    .data-table th:nth-child(8),
                    .data-table td:nth-child(8) {
                        display: none;
                    }
                }
               
                @media (max-width: 576px) {
                    .data-table th:nth-child(2),
                    .data-table td:nth-child(2) {
                        display: none;
                    }
                }
            </style>


            <script>
                // Function to show appointment details
                function showAppointmentDetails(appointment) {
                    const modal = document.getElementById('appointmentDetailsModal');
                    const content = document.getElementById('appointmentDetailsContent');
                   
                    content.innerHTML = `
                        <div class="appointment-detail">
                            <p><strong>Client Name:</strong> ${appointment.patient_name}</p>
                            <p><strong>Email:</strong> ${appointment.patient_email}</p>
                            <p><strong>Phone:</strong> ${appointment.patient_phone}</p>
                            <p><strong>Appointment Date:</strong> ${new Date(appointment.appointment_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p><strong>Time:</strong> ${new Date('2000-01-01T' + appointment.appointment_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                            <p><strong>Status:</strong> <span class="status-${appointment.status}">${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}</span></p>
                            <p><strong>Concern:</strong></p>
                            <p>${appointment.concern}</p>
                            <p><strong>Booked On:</strong> ${new Date(appointment.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                    `;
                   
                    modal.style.display = 'flex';
                }
               
                // Function to close appointment modal
                function closeAppointmentModal() {
                    document.getElementById('appointmentDetailsModal').style.display = 'none';
                }
               
                // Add event listeners to view details buttons
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.view-details').forEach(button => {
                        button.addEventListener('click', function() {
                            const appointmentData = JSON.parse(this.getAttribute('data-appointment'));
                            showAppointmentDetails(appointmentData);
                        });
                    });
                   
                    // Close modal when clicking outside
                    window.addEventListener('click', function(event) {
                        const modal = document.getElementById('appointmentDetailsModal');
                        if (event.target === modal) {
                            closeAppointmentModal();
                        }
                    });
                });
            </script>
            <!-- Other Profiles Tab -->
            <div id="other-profiles" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>Other Dermatologists' Profiles</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        $dermatologists_result->data_seek(0);
                        while ($dermatologist = $dermatologists_result->fetch_assoc()):
                            if ($dermatologist['years_experience'] !== null && $dermatologist['id'] != $_SESSION['id']):
                        ?>
                        <div class="profile-card">
                            <div class="profile-image">
                                <?php if ($dermatologist['profile_image']): ?>
                                    <img src="../<?php echo $dermatologist['profile_image']; ?>" alt="<?php echo $dermatologist['name']; ?>">
                                <?php else: ?>
                                    <i class="fas fa-user-md"></i>
                                <?php endif; ?>
                            </div>
                            <div class="profile-info">
                                <h3><?php echo $dermatologist['name']; ?></h3>
                                <p><strong><?php echo $dermatologist['years_experience']; ?> years of experience</strong></p>
                                <p><strong>Address:</strong> <?php echo $dermatologist['address']; ?></p>
                                <p><strong>Expertise:</strong> <?php echo $dermatologist['experience1']; ?>, <?php echo $dermatologist['experience2']; ?></p>
                                <p><strong>Availability:</strong> <?php echo $dermatologist['availability']; ?></p>
                                <div class="profile-quote">"<?php echo $dermatologist['quote']; ?>"</div>
                            </div>
                        </div>
                        <?php
                            endif;
                        endwhile;
                        ?>
                    </div>
                </div>
            </div>


            <!-- Review Product Tab -->
            <div id="review-product" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>Review a Product</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($review_success)): ?>
                            <div class="success-message"><?php echo $review_success; ?></div>
                        <?php endif; ?>
                       
                        <?php if (isset($review_error)): ?>
                            <div class="error-message"><?php echo $review_error; ?></div>
                        <?php endif; ?>
                       
                        <form method="POST" action="">
                            <input type="hidden" name="submit_review" value="1">
                           
                            <div class="form-group">
                                <label for="product_id">Select Product</label>
                                <select id="product_id" name="product_id" class="form-control" required>
                                    <option value="">-- Select a Product --</option>
                                    <?php
                                    $products_result->data_seek(0);
                                    while ($product = $products_result->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                           
                            <div class="form-group">
                                <label>Rating</label>
                                <div class="rating">
                                    <input type="radio" id="star5" name="rating" value="5" required>
                                    <label for="star5"></label>
                                    <input type="radio" id="star4" name="rating" value="4">
                                    <label for="star4"></label>
                                 <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3"></label>
                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2"></label>
                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1"></label>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="review_text">Review</label>
                            <textarea id="review_text" name="review_text" class="form-control" required placeholder="Share your experience with this product..."></textarea>
                        </div>
                       
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>


        <!-- My Reviews Tab -->
        <div id="my-reviews" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h2>My Reviews</h2>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($review = $my_reviews_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $review['product_name']; ?></td>
                                <td>
                                    <?php
                                    for ($i = 0; $i < $review['rating']; $i++) {
                                        echo "★";
                                    }
                                    for ($i = $review['rating']; $i < 5; $i++) {
                                        echo "☆";
                                    }
                                    echo " (" . $review['rating'] . ")";
                                    ?>
                                </td>
                                <td><?php echo $review['review_text']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
           
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2>All Reviews</h2>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Reviewer</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($review = $all_reviews_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $review['product_name']; ?></td>
                                <td><?php echo $review['dermatologist_name']; ?></td>
                                <td>
                                    <?php
                                    for ($i = 0; $i < $review['rating']; $i++) {
                                        echo "★";
                                    }
                                    for ($i = $review['rating']; $i < 5; $i++) {
                                        echo "☆";
                                    }
                                    echo " (" . $review['rating'] . ")";
                                    ?>
                                </td>
                                <td><?php echo $review['review_text']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function showTab(tabId) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(tab => tab.classList.remove('active'));
       
        // Show the selected tab content
        document.getElementById(tabId).classList.add('active');
       
        // Update active navigation link
        const navLinks = document.querySelectorAll('.nav-links a');
        navLinks.forEach(link => link.classList.remove('active'));
       
        event.currentTarget.classList.add('active');
    }
   
    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        const reader = new FileReader();
       
        reader.onload = function() {
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = reader.result;
            preview.appendChild(img);
        }
       
        if (file) {
            reader.readAsDataURL(file);
        }
    }
   
    // Initialize the rating stars
    document.addEventListener('DOMContentLoaded', function() {
        const ratingStars = document.querySelectorAll('.rating input');
        ratingStars.forEach(star => {
            star.addEventListener('change', function() {
                // Remove any existing checked states
                ratingStars.forEach(s => s.checked = false);
                // Set the current one as checked
                this.checked = true;
            });
        });
    });
</script>





