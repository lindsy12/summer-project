<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}


// Database connection
$host = 'localhost';
$dbname = 'skincare_db';
$username = 'root'; // Change if needed
$password = ''; // Change if needed


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    // Get current page
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
   
    // Fetch all users
    $usersStmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Count users by role
    $roleCountStmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleCounts = $roleCountStmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Total user count
    $totalUsers = count($users);
   
    // Fetch all products
    $productsStmt = $pdo->query("
        SELECT p.*, u.name as user_name, u.email as user_email
        FROM products p
        LEFT JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
    $totalProducts = count($products);
   
    // Fetch all dermatologists with profiles
    $dermatologistsStmt = $pdo->query("
        SELECT u.id, u.name, u.email, u.created_at,
               p.years_experience, p.experience1, p.experience2, p.availability, p.quote, p.profile_image
        FROM users u
        LEFT JOIN dermatologist_profiles p ON u.id = p.dermatologist_id
        WHERE u.role = 'dermatologist'
        ORDER BY u.created_at DESC
    ");
    $dermatologists = $dermatologistsStmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Fetch all appointments
    $appointmentsStmt = $pdo->query("
        SELECT a.*, u.name as patient_name, u.email as patient_email,
               d.name as dermatologist_name, d.email as dermatologist_email
        FROM appointments a
        JOIN users u ON a.patient_email = u.email
        JOIN users d ON a.dermatologist_id = d.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);
    $totalAppointments = count($appointments);
   
    // Fetch all product reviews
    $reviewsStmt = $pdo->query("
        SELECT r.*, p.name as product_name, u.name as reviewer_name, u.email as reviewer_email
        FROM product_reviews r
        JOIN products p ON r.product_id = p.id
        JOIN users u ON r.dermatologist_id = u.id
        ORDER BY r.created_at DESC
    ");
    $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
    $totalReviews = count($reviews);
   
    // Fetch recent activities
    $activitiesStmt = $pdo->query("
        (SELECT 'user_registered' as type, name as title, email as description, created_at as date FROM users ORDER BY created_at DESC LIMIT 5)
        UNION ALL
        (SELECT 'product_added' as type, name as title, description, created_at as date FROM products ORDER BY created_at DESC LIMIT 5)
        UNION ALL
        (SELECT 'appointment_booked' as type, CONCAT('Appointment with ', d.name) as title, CONCAT('by ', u.name) as description, a.created_at as date
         FROM appointments a
         JOIN users u ON a.patient_email = u.email
         JOIN users d ON a.dermatologist_id = d.id
         ORDER BY a.created_at DESC LIMIT 5)
        ORDER BY date DESC LIMIT 10
    ");
    $recentActivities = $activitiesStmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SkinCare Expert</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        :root {
            --primary: #6a11cb;
            --primary-light: #b085f5;
            --primary-dark: #4d2c91;
            --secondary: #ff8a65;
            --secondary-light: #ffbb93;
            --secondary-dark: #c75b39;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
        }
       
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }
       
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
       
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
            color: white;
            padding: 20px 0;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            overflow-y: auto;
            z-index: 1000;
        }
       
        .brand {
            padding: 0 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
       
        .brand h2 {
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
       
        .brand span {
            color: var(--secondary);
        }
       
        .sidebar-menu {
            list-style: none;
        }
       
        .sidebar-menu li {
            margin-bottom: 5px;
        }
       
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
       
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--secondary);
        }
       
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
       
        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 250px;
        }
       
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
       
        .welcome h1 {
            font-size: 1.8rem;
            color: var(--dark);
        }
       
        .welcome p {
            color: #6c757d;
        }
       
        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
       
        .admin-info .admin-name {
            font-weight: 500;
        }
       
        .admin-info .logout {
            background: var(--secondary);
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
       
        .admin-info .logout:hover {
            background: var(--secondary-dark);
        }
       
        .admin-info .logout i {
            margin-right: 5px;
        }
       
        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
       
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
       
        .stat-card {
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
       
        .stat-card:hover {
            transform: translateY(-5px);
        }
       
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 15px;
        }
       
        .stat-icon.users {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
       
        .stat-icon.admin {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
       
        .stat-icon.client {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
       
        .stat-icon.dermatologist {
            background: rgba(155, 89, 182, 0.2);
            color: #9b59b6;
        }
       
        .stat-icon.products {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }
       
        .stat-icon.appointments {
            background: rgba(142, 68, 173, 0.2);
            color: #8e44ad;
        }
       
        .stat-icon.reviews {
            background: rgba(230, 126, 34, 0.2);
            color: #e67e22;
        }
       
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
       
        .stat-info p {
            color: #6c757d;
            font-size: 0.9rem;
        }
       
        /* Table Styles */
        .data-table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
       
        .table-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .table-header h2 {
            font-size: 1.4rem;
            color: var(--dark);
        }
       
        table {
            width: 100%;
            border-collapse: collapse;
        }
       
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
       
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
       
        tr:hover {
            background-color: #f8f9fa;
        }
       
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
       
        .role-admin {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
       
        .role-client {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
       
        .role-dermatologist {
            background: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }
       
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
       
        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
       
        .status-confirmed {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
       
        .status-cancelled {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
       
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 5px;
            transition: all 0.3s;
        }
       
        .view-btn {
            background: #3498db;
            color: white;
        }
       
        .edit-btn {
            background: #f1c40f;
            color: white;
        }
       
        .delete-btn {
            background: #e74c3c;
            color: white;
        }
       
        .action-btn:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
       
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
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
            background: linear-gradient(45deg, var(--primary), var(--secondary));
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
            color: var(--primary);
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
            color: var(--secondary);
            font-size: 18px;
        }
       
        .product-user {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
            border-top: 1px solid #eee;
            padding-top: 10px;
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
            display: flex;
            align-items: center;
            justify-content: center;
        }
       
        .btn-edit {
            background: var(--primary);
            color: white;
        }
       
        .btn-delete {
            background: #dc3545;
            color: white;
        }
       
        .btn-view {
            background: var(--secondary);
            color: white;
        }
       
        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
       
        /* Dermatologist Cards */
        .dermatologist-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
        }
       
        .dermatologist-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
       
        .dermatologist-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
       
        .dermatologist-info {
            flex: 1;
        }
       
        .dermatologist-info h3 {
            color: var(--primary);
            margin-bottom: 5px;
        }
       
        .dermatologist-info p {
            color: #666;
            margin-bottom: 5px;
        }
       
        .dermatologist-quote {
            font-style: italic;
            color: var(--primary-dark);
            margin-top: 10px;
            padding: 10px;
            background-color: rgba(106, 17, 203, 0.1);
            border-left: 3px solid var(--primary);
            border-radius: 0 5px 5px 0;
        }
       
        /* Activity Feed */
        .activity-feed {
            list-style: none;
        }
       
        .activity-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
       
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
       
        .activity-user {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }
       
        .activity-product {
            background: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }
       
        .activity-appointment {
            background: rgba(142, 68, 173, 0.2);
            color: #8e44ad;
        }
       
        .activity-content {
            flex: 1;
        }
       
        .activity-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
       
        .activity-description {
            color: #6c757d;
            font-size: 0.9rem;
        }
       
        .activity-time {
            color: #6c757d;
            font-size: 0.8rem;
            margin-top: 5px;
        }
       
        /* Search and Filter */
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
       
        .search-box {
            flex: 1;
            position: relative;
        }
       
        .search-box input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
       
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
       
        .filter-btn {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
       
        .filter-btn i {
            margin-right: 5px;
            color: #6c757d;
        }
       
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
       
        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
       
        .empty-state h3 {
            margin-bottom: 10px;
            color: #495057;
        }
       
        /* Responsive */
        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }
           
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                margin-bottom: 20px;
            }
           
            .main-content {
                margin-left: 0;
            }
           
            .sidebar-menu {
                display: flex;
                overflow-x: auto;
            }
           
            .sidebar-menu li {
                margin-bottom: 0;
                margin-right: 5px;
            }
           
            .sidebar-menu a {
                padding: 10px 15px;
            }
           
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
       
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
           
            .stats-cards {
                grid-template-columns: 1fr;
            }
           
            table {
                display: block;
                overflow-x: auto;
            }
           
            .products-grid {
                grid-template-columns: 1fr;
            }
           
            .dermatologist-card {
                flex-direction: column;
                text-align: center;
            }
           
            .dermatologist-image {
                margin-right: 0;
                margin-bottom: 15px;
                align-self: center;
            }
           
            .search-filter {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h2><i class="fas fa-spa"></i> Skin<span>Care</span> Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="?page=dashboard" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="?page=users" class="<?php echo $currentPage === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="?page=dermatologists" class="<?php echo $currentPage === 'dermatologists' ? 'active' : ''; ?>"><i class="fas fa-user-md"></i> Dermatologists</a></li>
                <li><a href="?page=products" class="<?php echo $currentPage === 'products' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> Products</a></li>
                <li><a href="?page=appointments" class="<?php echo $currentPage === 'appointments' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                <li><a href="?page=reviews" class="<?php echo $currentPage === 'reviews' ? 'active' : ''; ?>"><i class="fas fa-star"></i> Reviews</a></li>
            </ul>
        </div>
       
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome">
                    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
                    <p>
                        <?php
                        switch($currentPage) {
                            case 'users': echo 'Manage all users in the system'; break;
                            case 'dermatologists': echo 'View and manage dermatologists'; break;
                            case 'products': echo 'Manage all products in the system'; break;
                            case 'appointments': echo 'View and manage appointments'; break;
                            case 'reviews': echo 'Manage product reviews'; break;
                            default: echo 'Here\'s what\'s happening with your website today.';
                        }
                        ?>
                    </p>
                </div>
                <div class="admin-info">
                    <span class="admin-name"><?php echo $_SESSION['name']; ?></span>
                    <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
           
            <!-- Dashboard Stats Cards -->
            <?php if ($currentPage === 'dashboard'): ?>
                <div class="stats-cards">
                    <a href="?page=users" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon users">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalUsers; ?></h3>
                                <p>Total Users</p>
                            </div>
                        </div>
                    </a>
                   
                    <?php
                    $roleCountsAssoc = [];
                    foreach ($roleCounts as $roleCount) {
                        $roleCountsAssoc[$roleCount['role']] = $roleCount['count'];
                    }
                    ?>
                   
                    <a href="?page=users#admin" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon admin">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo isset($roleCountsAssoc['admin']) ? $roleCountsAssoc['admin'] : 0; ?></h3>
                                <p>Admin Users</p>
                            </div>
                        </div>
                    </a>
                   
                    <a href="?page=dermatologists" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon dermatologist">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo isset($roleCountsAssoc['dermatologist']) ? $roleCountsAssoc['dermatologist'] : 0; ?></h3>
                                <p>Dermatologists</p>
                            </div>
                        </div>
                    </a>
                   
                    <a href="?page=users#client" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon client">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo isset($roleCountsAssoc['client']) ? $roleCountsAssoc['client'] : 0; ?></h3>
                                <p>Clients</p>
                            </div>
                        </div>
                    </a>
                   
                    <a href="?page=products" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon products">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalProducts; ?></h3>
                                <p>Products</p>
                            </div>
                        </div>
                    </a>
                   
                    <a href="?page=appointments" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon appointments">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalAppointments; ?></h3>
                                <p>Appointments</p>
                            </div>
                        </div>
                    </a>
                   
                    <a href="?page=reviews" style="text-decoration: none;">
                        <div class="card stat-card">
                            <div class="stat-icon reviews">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $totalReviews; ?></h3>
                                <p>Reviews</p>
                            </div>
                        </div>
                    </a>
                </div>
               
                <!-- Recent Activities -->
                <div class="card">
                    <div class="table-header">
                        <h2><i class="fas fa-history"></i> Recent Activities</h2>
                    </div>
                   
                    <ul class="activity-feed">
                        <?php if (count($recentActivities) > 0): ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <li class="activity-item">
                                    <div class="activity-icon
                                        <?php
                                        if ($activity['type'] === 'user_registered') echo 'activity-user';
                                        elseif ($activity['type'] === 'product_added') echo 'activity-product';
                                        else echo 'activity-appointment';
                                        ?>
                                    ">
                                        <?php
                                        if ($activity['type'] === 'user_registered') echo '<i class="fas fa-user-plus"></i>';
                                        elseif ($activity['type'] === 'product_added') echo '<i class="fas fa-cube"></i>';
                                        else echo '<i class="fas fa-calendar-check"></i>';
                                        ?>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                                        <div class="activity-description"><?php echo htmlspecialchars($activity['description']); ?></div>
                                        <div class="activity-time"><?php echo date('M j, Y g:i A', strtotime($activity['date'])); ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                <h3>No Recent Activities</h3>
                                <p>There are no recent activities to display.</p>
                            </div>
                        <?php endif; ?>
                    </ul>
                </div>
               
                <!-- Recent Users Table -->
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-users"></i> Recent Users</h2>
                        <a href="?page=users" class="action-btn view-btn">View All</a>
                    </div>
                   
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($users, 0, 5) as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
               
                <!-- Recent Appointments Table -->
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-calendar-alt"></i> Recent Appointments</h2>
                        <a href="?page=appointments" class="action-btn view-btn">View All</a>
                    </div>
                   
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Dermatologist</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($appointments, 0, 5) as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['id']; ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['dermatologist_name']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
           
            <!-- Users Page -->
            <?php if ($currentPage === 'users'): ?>
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalUsers; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                   
                    <?php
                    $roleCountsAssoc = [];
                    foreach ($roleCounts as $roleCount) {
                        $roleCountsAssoc[$roleCount['role']] = $roleCount['count'];
                    }
                    ?>
                   
                    <div class="card stat-card">
                        <div class="stat-icon admin">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo isset($roleCountsAssoc['admin']) ? $roleCountsAssoc['admin'] : 0; ?></h3>
                            <p>Admin Users</p>
                        </div>
                    </div>
                   
                    <div class="card stat-card">
                        <div class="stat-icon dermatologist">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo isset($roleCountsAssoc['dermatologist']) ? $roleCountsAssoc['dermatologist'] : 0; ?></h3>
                            <p>Dermatologists</p>
                        </div>
                    </div>
                   
                    <div class="card stat-card">
                        <div class="stat-icon client">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo isset($roleCountsAssoc['client']) ? $roleCountsAssoc['client'] : 0; ?></h3>
                            <p>Clients</p>
                        </div>
                    </div>
                </div>
               
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search users..." id="userSearch">
                    </div>
                    <div class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </div>
                </div>
               
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-users"></i> All Registered Users</h2>
                    </div>
                   
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="action-btn view-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete-btn"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
           
            <!-- Dermatologists Page -->
            <?php if ($currentPage === 'dermatologists'): ?>
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon dermatologist">
                            <i class="fas fa-user-md"></i>
                        </div>
                                                    <p>Dermatologists</p>


                        <!-- <div class="stat-info">
                            <h3><?php echo isset($roleCountsAssoc['dermatologist']) ? $roleCountsAssoc['dermatologist'] : 0; ?></h3>
                           <p>Dermatologists</p>
                        </div> -->
                    </div>
                </div>
               
                <!-- <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search dermatologists..." id="dermSearch">
                    </div>
                    <div class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </div>
                </div>
               
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-user-md"></i> All Dermatologists</h2>
                    </div> -->
                   
                    <?php if (count($dermatologists) > 0): ?>
                        <?php foreach ($dermatologists as $derm): ?>
                        <div class="dermatologist-card">
                            <div class="dermatologist-image">
                                <?php if (!empty($derm['profile_image'])): ?>
                                    <img src="../<?php echo $derm['profile_image']; ?>" alt="<?php echo htmlspecialchars($derm['name']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-user-md"></i>
                                <?php endif; ?>
                            </div>
                            <div class="dermatologist-info">
                                <h3><?php echo htmlspecialchars($derm['name']); ?></h3>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($derm['email']); ?></p>
                                <p><strong>Experience:</strong> <?php echo $derm['years_experience'] ? $derm['years_experience'] . ' years' : 'Not specified'; ?></p>
                                <p><strong>Expertise:</strong> <?php echo htmlspecialchars($derm['experience1']); ?>, <?php echo htmlspecialchars($derm['experience2']); ?></p>
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($derm['availability']); ?></p>
                                <?php if (!empty($derm['quote'])): ?>
                                <div class="dermatologist-quote">"<?php echo htmlspecialchars($derm['quote']); ?>"</div>
                                <?php endif; ?>
                            </div>
                            <!-- <div class="product-actions">
                                <button class="btn-action btn-view"><i class="fas fa-eye"></i></button>
                                <button class="btn-action btn-edit"><i class="fas fa-edit"></i></button>
                                <button class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                            </div> -->
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-md"></i>
                            <h3>No Dermatologists Found</h3>
                            <p>No dermatologists have registered in the system yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
           
            <!-- Products Page -->
            <?php if ($currentPage === 'products'): ?>
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon products">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalProducts; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                </div>
               
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search products..." id="productSearch">
                    </div>
                    <div class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </div>
                </div>
               
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-shopping-bag"></i> All Products</h2>
                    </div>
                   
                    <div class="products-grid">
                        <?php if ($totalProducts > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="product-card">
                                    <div class="product-image">
                                        <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            <i class="fas fa-spa"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                        <div class="product-meta">
                                            <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                            <!-- <span class="product-stock">Category: <?php echo htmlspecialchars($product['category']); ?></span> -->
                                        </div>
                                        <div class="product-user">
                                            <strong>Uploaded by:</strong> <?php echo htmlspecialchars($product['user_name']); ?> (<?php echo htmlspecialchars($product['user_email']); ?>)
                                        </div>
                                        <!-- <div class="product-actions">
                                            <button class="btn-action btn-view"><i class="fas fa-eye"></i></button>
                                            <button class="btn-action btn-edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn-action btn-delete"><i class="fas fa-trash"></i></button>
                                        </div> -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state" style="grid-column: 1 / -1;">
                                <i class="fas fa-box-open"></i>
                                <h3>No Products Found</h3>
                                <p>No products have been added to the system yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
           
            <!-- Appointments Page -->
            <?php if ($currentPage === 'appointments'): ?>
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon appointments">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalAppointments; ?></h3>
                            <p>Total Appointments</p>
                        </div>
                    </div>
                </div>
               
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search appointments..." id="appointmentSearch">
                    </div>
                    <div class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </div>
                </div>
               
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-calendar-alt"></i> All Appointments</h2>
                    </div>
                   
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Dermatologist</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['id']; ?></td>
                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['dermatologist_name']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- <button class="action-btn view-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete-btn"><i class="fas fa-trash"></i></button> -->
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
           
            <!-- Reviews Page -->
            <?php if ($currentPage === 'reviews'): ?>
                <div class="stats-cards">
                    <div class="card stat-card">
                        <div class="stat-icon reviews">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $totalReviews; ?></h3>
                            <p>Total Reviews</p>
                        </div>
                    </div>
                </div>
               
                <!-- <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search reviews..." id="reviewSearch">
                    </div>
                    <div class="filter-btn">
                        <i class="fas fa-filter"></i>
                        <span>Filter</span>
                    </div>
                </div>
                 -->
                <div class="data-table-container">
                    <div class="table-header">
                        <h2><i class="fas fa-star"></i> All Reviews</h2>
                    </div>
                   
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Reviewer</th>
                                <th>Rating</th>
                                <!-- <th>Comment</th> -->
                                <th>Date</th>
                                <!-- <th>Actions</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['reviewer_name']); ?></td>
                                <td>
                                    <?php
                                    $rating = $review['rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star" style="color: #f1c40f;"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="color: #f1c40f;"></i>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($review['created_at'])); ?></td>
                                <td>
                                    <!-- <button class="action-btn view-btn"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn edit-btn"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn delete-btn"><i class="fas fa-trash"></i></button> -->
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <script>
        // Simple JavaScript for interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add active class to clicked menu items
            const menuItems = document.querySelectorAll('.sidebar-menu a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
           
            // Confirm before deleting
            const deleteButtons = document.querySelectorAll('.delete-btn, .btn-delete');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this item?')) {
                        // Here you would typically make an AJAX request or form submission
                        alert('Delete functionality would be implemented here.');
                    }
                });
            });
           
            // Make stat cards clickable
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('click', function() {
                    const link = this.closest('a');
                    if (link) {
                        window.location.href = link.href;
                    }
                });
            });
           
            // Simple search functionality
            const searchInputs = document.querySelectorAll('input[id$="Search"]');
            searchInputs.forEach(input => {
                input.addEventListener('keyup', function() {
                    const tableId = this.id.replace('Search', '');
                    const value = this.value.toLowerCase();
                   
                    // Determine what type of content we're searching through
                    let rows;
                    if (this.id === 'userSearch') {
                        rows = document.querySelectorAll('#userTable tbody tr');
                    } else if (this.id === 'productSearch') {
                        rows = document.querySelectorAll('.product-card');
                    } else if (this.id === 'appointmentSearch') {
                        rows = document.querySelectorAll('#appointmentTable tbody tr');
                    } else if (this.id === 'reviewSearch') {
                        rows = document.querySelectorAll('#reviewTable tbody tr');
                    } else if (this.id === 'dermSearch') {
                        rows = document.querySelectorAll('.dermatologist-card');
                    } else {
                        rows = document.querySelectorAll('tbody tr');
                    }
                   
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.indexOf(value) > -1) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>

