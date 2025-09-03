<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'cosmetic') {
    header("Location: ../login.php");
    exit;
}


// Database connection
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "skincare_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle form submission for new products
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $user_id = $_SESSION['id'];
   
    // Handle image upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $image_name = time() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
   
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
   
    // Check file size (max 2MB)
    if ($_FILES["image"]["size"] > 3000000) {
        $uploadOk = 0;
    }
   
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }
   
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Image uploaded successfully, now insert into database
            $sql = "INSERT INTO products (name, description, price, area, image, user_id)
                    VALUES ('$name', '$description', '$price', '$area', '$target_file', '$user_id')";
           
            if ($conn->query($sql) === TRUE) {
                $success_message = "Product added successfully!";
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error_message = "Sorry, your image was not uploaded. Please ensure it's a JPG, JPEG, PNG, or GIF file under 2MB.";
    }
}


// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $user_id = $_SESSION['id'];
   
    // First get the image path to delete the file
    $sql = "SELECT image FROM products WHERE id='$delete_id' AND user_id='$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (file_exists($row['image'])) {
            unlink($row['image']);
        }
    }
   
    // Now delete the product from database
    $sql = "DELETE FROM products WHERE id='$delete_id' AND user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $success_message = "Product deleted successfully!";
    } else {
        $error_message = "Error deleting product: " . $conn->error;
    }
}


// Fetch all products for the logged-in user
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM products WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmetic Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-purple: #6a11cb;
            --secondary-purple: #8a2be2;
            --primary-orange: #ff7e29;
            --secondary-orange: #ff9a53;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #fff;
            --border-color: #e0e0e0;
        }
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        body {
            background-color: var(--light-bg);
            color: var(--dark-text);
            line-height: 1.6;
        }
       
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
       
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, var(--primary-purple), var(--secondary-purple));
            color: var(--light-text);
            padding: 20px 0;
            transition: all 0.3s ease;
        }
       
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
       
        .sidebar-header h2 {
            margin-top: 10px;
            font-size: 22px;
        }
       
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
       
        .sidebar-menu li {
            padding: 10px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
       
        .sidebar-menu li.active {
            border-left: 4px solid var(--primary-orange);
            background: rgba(255, 255, 255, 0.1);
        }
       
        .sidebar-menu li:hover {
            background: rgba(255, 255, 255, 0.1);
        }
       
        .sidebar-menu a {
            color: var(--light-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
       
        .sidebar-menu i {
            font-size: 18px;
        }
       
        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 20px;
        }
       
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
       
        .welcome-message h1 {
            font-size: 28px;
            color: var(--primary-purple);
        }
       
        .welcome-message p {
            color: #666;
        }
       
        .logout-btn {
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
       
        .logout-btn:hover {
            background: var(--secondary-orange);
        }
       
        /* Tab Content Styles */
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
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
       
        .card-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--primary-purple);
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
       
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
       
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
       
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
       
        .form-control:focus {
            border-color: var(--primary-purple);
            outline: none;
        }
       
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
       
        .btn-primary {
            background: linear-gradient(to right, var(--primary-purple), var(--secondary-purple));
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }
       
        .btn-primary:hover {
            background: linear-gradient(to right, var(--secondary-purple), var(--primary-purple));
            transform: translateY(-2px);
        }
       
        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
       
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
       
        .product-card:hover {
            transform: translateY(-5px);
        }
       
        .product-image {
            height: 200px;
            background: linear-gradient(45deg, var(--primary-purple), var(--primary-orange));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 50px;
            overflow: hidden;
        }
       
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
       
        .product-details {
            padding: 20px;
        }
       
        .product-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--primary-purple);
        }
       
        .product-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
       
        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
       
        .product-price {
            font-weight: bold;
            color: var(--primary-orange);
            font-size: 18px;
        }
       
        .product-stock {
            color: #28a745;
            font-weight: 500;
        }
       
        .product-actions {
            display: flex;
            gap: 10px;
        }
       
        .btn-action {
            flex: 1;
            padding: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
       
        .btn-edit {
            background: var(--primary-purple);
            color: white;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
       
        .btn-delete {
            background: #dc3545;
            color: white;
        }
       
        .btn-view {
            background: var(--primary-orange);
            color: white;
        }
       
        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
       
        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
       
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
       
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
       
        /* Responsive Styles */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
            }
           
            .sidebar {
                width: 100%;
                padding: 10px 0;
            }
           
            .sidebar-header {
                text-align: left;
                display: flex;
                align-items: center;
                gap: 15px;
            }
           
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
                padding: 10px 0;
            }
           
            .sidebar-menu li {
                border-left: none;
                border-bottom: 4px solid transparent;
                padding: 10px 15px;
                white-space: nowrap;
            }
           
            .sidebar-menu li.active {
                border-left: none;
                border-bottom: 4px solid var(--primary-orange);
            }
        }
       
        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
           
            .product-grid {
                grid-template-columns: 1fr;
            }
           
            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-spa fa-2x"></i>
                <h2>Cosmetic Dashboard</h2>
            </div>
            <ul class="sidebar-menu">
                <li class="active"><a href="#upload" onclick="switchTab('upload')"><i class="fas fa-upload"></i> Upload Products</a></li>
                <li><a href="#manage" onclick="switchTab('manage')"><i class="fas fa-cog"></i> Manage Products</a></li>
                <li><a href="#"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            </ul>
        </div>


        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="welcome-message">
                    <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
                    <p>Manage your cosmetic products and inventory</p>
                </div>
                <button class="logout-btn" onclick="location.href='../logout.php'">Logout <i class="fas fa-sign-out-alt"></i></button>
            </div>


            <!-- Display success/error messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
           
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>


            <!-- Upload Products Tab -->
            <div id="upload-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-upload"></i> Upload New Product
                    </div>
                    <form id="product-form" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" class="form-control" id="product-name" name="name" placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="product-description">Description</label>
                            <textarea class="form-control" id="product-description" name="description" placeholder="Enter product description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product-price">Price ($)</label>
                            <input type="number" class="form-control" id="product-price" name="price" placeholder="Enter price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="product-area">Available Area</label>
                            <input type="text" class="form-control" id="product-area" name="area" placeholder="Enter available areas" required>
                        </div>
                        <div class="form-group">
                            <label for="product-image">Product Image</label>
                            <input type="file" class="form-control" id="product-image" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" name="add_product" class="btn-primary">Upload Product <i class="fas fa-check"></i></button>
                    </form>
                </div>
            </div>


            <!-- Manage Products Tab -->
            <div id="manage-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cog"></i> Manage Products
                        <span style="margin-left: auto; font-size: 16px; color: var(--primary-purple);">
                            <?php echo $result->num_rows; ?> product(s) found
                        </span>
                    </div>
                    <div class="product-grid">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                                            <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                                        <?php else: ?>
                                            <i class="fas fa-spa"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h3 class="product-title"><?php echo $row['name']; ?></h3>
                                        <p class="product-description"><?php echo $row['description']; ?></p>
                                        <div class="product-meta">
                                            <span class="product-price">$<?php echo number_format($row['price'], 2); ?></span>
                                            <span class="product-stock">Available: <?php echo $row['area']; ?></span>
                                        </div>
                                        <div class="product-actions">
<a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
    <i class="fas fa-edit"></i> Edit
</a>                                            <button class="btn-action btn-delete" onclick="deleteProduct(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-box-open" style="font-size: 60px; margin-bottom: 20px; opacity: 0.5;"></i>
                                <h3>No products found</h3>
                                <p>You haven't added any products yet. Click on the "Upload Products" tab to add your first product.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
           
            // Remove active class from all menu items
            document.querySelectorAll('.sidebar-menu li').forEach(item => {
                item.classList.remove('active');
            });
           
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
           
            // Set active class to clicked menu item
            event.currentTarget.classList.add('active');
        }
       
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                window.location.href = '?delete_id=' + id;
            }
        }
       
        function editProduct(id) {
            alert('Edit functionality for product ID ' + id + ' would be implemented here.');
            // In a real implementation, this would open a modal or redirect to an edit page
        }
       
        function viewProduct(id) {
            alert('View details for product ID ' + id);
            // In a real implementation, this would open a modal with product details
        }
       
        // Form validation
        document.getElementById('product-form').addEventListener('submit', function(e) {
            const price = document.getElementById('product-price').value;
            if (price <= 0) {
                e.preventDefault();
                alert('Please enter a valid price greater than 0.');
                return false;
            }
           
            const fileInput = document.getElementById('product-image');
            const file = fileInput.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    e.preventDefault();
                    alert('Please select an image smaller than 2MB.');
                    return false;
                }
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
