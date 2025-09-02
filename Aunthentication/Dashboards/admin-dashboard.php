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
   
    // Fetch all users
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Count users by role
    $roleCountStmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleCounts = $roleCountStmt->fetchAll(PDO::FETCH_ASSOC);
   
    // Total user count
    $totalUsers = count($users);
   
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Skin Glow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        :root {
            --primary: #8A2BE2;
            --secondary: #FF7F50;
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
            background: linear-gradient(to bottom, var(--primary), #6a11cb);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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
            background: #e67345;
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
       
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
       
        .stat-info p {
            color: #6c757d;
            font-size: 0.9rem;
        }
       
        /* Users Table */
        .users-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
       
        .role-cosmetic {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
        }
       
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            margin-right: 5px;
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
       
        /* Responsive */
        @media (max-width: 992px) {
            .dashboard {
                flex-direction: column;
            }
           
            .sidebar {
                width: 100%;
                padding: 10px 0;
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
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand">
                <h2><i class="fas fa-spa"></i> Skin<span>Glow</span> Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="#"><i class="fas fa-shopping-bag"></i> Products</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            </ul>
        </div>
       
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome">
                    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>
                    <p>Here's what's happening with your website today.</p>
                </div>
                <div class="admin-info">
                    <a href="../logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
           
            <!-- Stats Cards -->
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
           
            <!-- Users Table -->
            <div class="users-table">
                <div class="table-header">
                    <h2><i class="fas fa-users"></i> All Registered Users</h2>
                    <div class="actions">
                        <button class="action-btn view-btn"><i class="fas fa-download"></i> Export</button>
                    </div>
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
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete this user?')) {
                        // Here you would typically make an AJAX request or form submission
                        alert('User deletion functionality would be implemented here.');
                    }
                });
            });
        });
    </script>
</body>
</html>