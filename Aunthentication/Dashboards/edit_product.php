<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'cosmetic') {
    header("Location: ../login.php");
    exit;
}


// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skincare_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Initialize variables
$product = null;
$error_message = '';
$success_message = '';


// Get product ID from URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION['id'];
   
    // Fetch product details
    $sql = "SELECT * FROM products WHERE id='$product_id' AND user_id='$user_id'";
    $result = $conn->query($sql);
   
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $error_message = "Product not found or you don't have permission to edit it.";
    }
} else {
    $error_message = "No product ID specified.";
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $area = $_POST['area'];
    $user_id = $_SESSION['id'];
   
    // Check if user owns this product
    $check_sql = "SELECT id FROM products WHERE id='$product_id' AND user_id='$user_id'";
    $check_result = $conn->query($check_sql);
   
    if ($check_result->num_rows > 0) {
        // Handle image upload if a new image is provided
        $image_path = $_POST['existing_image'];
       
        if (!empty($_FILES["image"]["name"])) {
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
            if ($_FILES["image"]["size"] > 2000000) {
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
                    // Delete old image if it exists
                    if (file_exists($_POST['existing_image'])) {
                        unlink($_POST['existing_image']);
                    }
                    $image_path = $target_file;
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            } else {
                $error_message = "Sorry, your image was not uploaded. Please ensure it's a JPG, JPEG, PNG, or GIF file under 2MB.";
            }
        }
       
        // Update product in database
        if (empty($error_message)) {
            $sql = "UPDATE products SET name='$name', description='$description', price='$price', area='$area', image='$image_path' WHERE id='$product_id' AND user_id='$user_id'";
           
            if ($conn->query($sql) === TRUE) {
                $success_message = "Product updated successfully!";
                // Refetch the product to show updated values
                $sql = "SELECT * FROM products WHERE id='$product_id' AND user_id='$user_id'";
                $result = $conn->query($sql);
                $product = $result->fetch_assoc();
            } else {
                $error_message = "Error updating product: " . $conn->error;
            }
        }
    } else {
        $error_message = "Product not found or you don't have permission to edit it.";
    }
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Cosmetic Dashboard</title>
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
            padding: 20px;
        }
       
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }
       
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
       
        .header h1 {
            font-size: 28px;
            color: var(--primary-purple);
        }
       
        .back-btn {
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
       
        .back-btn:hover {
            background: var(--secondary-orange);
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
       
        .image-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed var(--border-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
       
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
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
       
        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Product</h1>
            <a href="cosmetic-dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>


        <!-- Display success/error messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
       
        <?php if (isset($error_message) && !empty($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
       
        <?php if ($product): ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo $product['image']; ?>">
               
                <div class="form-group">
                    <label for="product-name">Product Name</label>
                    <input type="text" class="form-control" id="product-name" name="name"
                           value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
               
                <div class="form-group">
                    <label for="product-description">Description</label>
                    <textarea class="form-control" id="product-description" name="description"
                              required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
               
                <div class="form-group">
                    <label for="product-price">Price ($)</label>
                    <input type="number" class="form-control" id="product-price" name="price"
                           value="<?php echo $product['price']; ?>" step="0.01" required>
                </div>
               
                <div class="form-group">
                    <label for="product-area">Available Area</label>
                    <input type="text" class="form-control" id="product-area" name="area"
                           value="<?php echo htmlspecialchars($product['area']); ?>" required>
                </div>
               
                <div class="form-group">
                    <label for="product-image">Current Image</label>
                    <div class="image-preview">
                        <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                            <img src="<?php echo $product['image']; ?>" alt="Current product image">
                        <?php else: ?>
                            <span>No image available</span>
                        <?php endif; ?>
                    </div>
                </div>
               
                <div class="form-group">
                    <label for="product-image">Update Image (optional)</label>
                    <input type="file" class="form-control" id="product-image" name="image" accept="image/*">
                    <small>Leave empty to keep current image. Max size: 2MB</small>
                </div>
               
                <button type="submit" name="update_product" class="btn-primary">Update Product <i class="fas fa-check"></i></button>
            </form>
        <?php else: ?>
            <p>Product not found. <a href="dashboard.php">Return to dashboard</a></p>
        <?php endif; ?>
    </div>


    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
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
       
        // Image preview functionality
        const imageInput = document.getElementById('product-image');
        const imagePreview = document.querySelector('.image-preview');
       
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
               
                reader.addEventListener('load', function() {
                    imagePreview.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = reader.result;
                    imagePreview.appendChild(img);
                });
               
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
