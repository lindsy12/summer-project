<?php
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


// Fetch all products from the database
$sql = "SELECT p.*, u.name as user_name FROM products p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);


// Function to get correct image path
function getImagePath($image_path) {
    // Check if the image exists at the given path
    if (file_exists($image_path)) {
        return $image_path;
    }
   
    // Check if it exists in the parent directory
    $parent_path = "../" . $image_path;
    if (file_exists($parent_path)) {
        return $parent_path;
    }
   
    // Check if it's just a filename in the uploads directory
    $filename_only = "uploads/" . basename($image_path);
    if (file_exists($filename_only)) {
        return $filename_only;
    }
   
    // If all else fails, return empty
    return "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Products</title>
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
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
        }
       
       
       
        /* Hero Section */
        .hero {
            background-size: cover;
            background-position: center;
            color: purple;
            text-align: center;
            padding: 80px 20px;
            margin-bottom: 40px;
            background-color: black;
        }
       
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
       
        .hero h2 {
            font-size: 2.8rem;
            margin-bottom: 20px;
        }
       
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
       
        /* Introduction Section */
        .intro {
            padding: 60px 0;
            text-align: center;
        }
       
        .section-title {
            font-size: 2.2rem;
            color: var(--primary-purple);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
       
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-orange);
            border-radius: 2px;
        }
       
        .intro-content {
            max-width: 800px;
            margin: 40px auto 0;
            font-size: 1.1rem;
            line-height: 1.8;
        }
       
        /* About Section */
        .about {
            background: linear-gradient(to right, #f9f9f9, #fff);
            padding: 60px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
       
        .about-content {
            display: flex;
            align-items: center;
            gap: 40px;
            flex-wrap: wrap;
        }
       
        .about-text {
            flex: 1;
            min-width: 300px;
        }
       
        .about-image {
            flex: 1;
            min-width: 300px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
       
        .about-image img {
            width: 100%;
            height: auto;
            display: block;
        }
       
        /* Products Section */
        .products {
            padding: 60px 0;
        }
       
        .products-header {
            text-align: center;
            margin-bottom: 40px;
        }
       
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
       
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
       
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
       
        .product-image {
            height: 220px;
            background: linear-gradient(45deg, var(--primary-purple), var(--primary-orange));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
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
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
       
        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
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
       
        .product-provider {
            font-size: 14px;
            color: #6c757d;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
       
        /* Contact Section */
        .contact {
            background: linear-gradient(to right, var(--primary-purple), var(--secondary-purple));
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-top: 40px;
        }
       
        .contact h2 {
            margin-bottom: 20px;
        }
       
        .contact p {
            max-width: 600px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
       
        .contact-info {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
       
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
       
        .contact-item i {
            font-size: 24px;
            color: var(--primary-orange);
        }
       
        /* Footer */
        footer {
            background: #333;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
       
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
       
        .social-links {
            display: flex;
            gap: 15px;
        }
       
        .social-links a {
            color: white;
            font-size: 20px;
            transition: color 0.3s;
        }
       
        .social-links a:hover {
            color: var(--primary-orange);
        }
       
        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
       
        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
            color: var(--primary-purple);
        }
       
        /* Responsive Styles */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
           
            nav ul {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
           
            .hero h2 {
                font-size: 2.2rem;
            }
           
            .about-content {
                flex-direction: column;
            }
           
            .contact-info {
                flex-direction: column;
                gap: 20px;
            }
           
            .footer-content {
                flex-direction: column;
            }
        }
       
        @media (max-width: 576px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
           
            .hero {
                padding: 50px 20px;
            }
           
            .hero h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
        <?php include '../includes/header.php'; ?>


    <!-- Header -->


    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Discover Your Perfect Skincare Routine</h2>
                <p>Premium quality skincare products crafted with natural ingredients for radiant, healthy-looking skin.</p>
            </div>
        </div>
    </section>


    <!-- Introduction Section -->
    <section class="intro">
        <div class="container">
            <h2 class="section-title">Our Products</h2>
            <div class="intro-content">
                <p>At SkinGlow, we believe that everyone deserves to have healthy, radiant skin. Our carefully curated collection of skincare products is formulated with the finest natural ingredients, scientifically proven to nourish and rejuvenate your skin.</p>
                <p>Each product is crafted with love and attention to detail, ensuring that you receive only the best for your skincare routine. Explore our collection below and find the perfect products for your skin type.</p>
            </div>
        </div>
    </section>


    <!-- About Section -->
    <section class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">About Our Products</h2>
                    <p>Our skincare line is the result of extensive research and development by dermatologists and skincare experts. We use only premium, ethically sourced ingredients that are free from harmful chemicals and toxins.</p>
                    <p>All our products are cruelty-free and environmentally conscious. We're committed to sustainability throughout our production process, from sourcing to packaging.</p>
                    <p>Whether you have dry, oily, combination, or sensitive skin, we have products specifically formulated to address your unique skincare needs and help you achieve a natural, healthy glow.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Skincare products">
                </div>
            </div>
        </div>
    </section>


    <!-- Products Section -->
    <section class="products">
        <div class="container">
            <div class="products-header">
                <h2 class="section-title">Featured Products</h2>
                <p>Browse our collection of premium skincare solutions</p>
            </div>
           
            <div class="product-grid">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php
                        // Get the correct image path
                        $image_path = getImagePath($row['image']);
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($image_path)): ?>
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=\'fas fa-spa\' style=\'font-size: 50px;\'></i>';">
                                <?php else: ?>
                                    <i class="fas fa-spa" style="font-size: 50px;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="product-details">
                                <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="product-meta">
                                    <span class="product-price">$<?php echo number_format($row['price'], 2); ?></span>
                                    <span class="product-stock">Available: <?php echo htmlspecialchars($row['area']); ?></span>
                                </div>
                                <?php if (!empty($row['user_name'])): ?>
                                    <div class="product-provider">
                                        Provided by: <?php echo htmlspecialchars($row['user_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h3>No Products Available</h3>
                        <p>Check back soon for our amazing skincare products!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- Contact Section -->
    <section class="contact">
        <div class="container">
            <h2>Interested in Our Products?</h2>
            <p>Contact us for more information, pricing, or to place an order. Our skincare specialists are ready to assist you.</p>
           
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span>+1 (555) 123-4567</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>info@skinglow.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>123 Beauty Street, Cosmetic City</span>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
         <?php include '../includes/footer.php'; ?>


</body>
</html>
<?php
$conn->close();
?>

