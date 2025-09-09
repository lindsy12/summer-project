<?php
session_start();
include "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['id']   = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];


        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: dashboards/admin-dashboard.php");
                break;
            case 'client':
                header("Location: dashboards/client-dashboard.php");
                break;
            case 'dermatologist':
                header("Location: dashboards/dermatologist-dashboard.php");
                break;
            case 'cosmetic':
                header("Location: dashboards/cosmetic-dashboard.php");
                break;
        }
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Skincare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        :root {
            --primary: #7e57c2;
            --primary-light: #b085f5;
            --primary-dark: #4d2c91;
            --secondary: #ff8a65;
            --secondary-light: #ffbb93;
            --secondary-dark: #c75b39;
            --text: #37474f;
            --text-light: #78909c;
            --background: #f5f5f5;
            --white: #ffffff;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --button-shadow: 0 5px 15px rgba(126, 87, 194, 0.3);
        }
       
        body {
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
       
       
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            margin-top: 70px;
            align-items: center;
        }
       
        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
       
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
       
        .login-left h2 {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }
       
        .login-left p {
            margin-bottom: 2rem;
            opacity: 0.9;
        }
       
        .avatar-container {
            display: flex;
            gap: 1.5rem;
            margin: 2rem 0;
        }
       
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--white);
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
       
        .login-right {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
       
        .login-right h2 {
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
        }
       
        .form-group {
            margin-bottom: 1.5rem;
        }
       
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
        }
       
        .input-with-icon {
            position: relative;
        }
       
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
       
        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
       
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(126, 87, 194, 0.1);
        }
       
        .btn {
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
       
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            box-shadow: var(--button-shadow);
        }
       
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
       
        .btn-secondary {
            background-color: var(--secondary);
            color: var(--white);
            box-shadow: 0 5px 15px rgba(255, 138, 101, 0.3);
        }
       
        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
        }
       
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
        }
       
        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
       
        .register-link a:hover {
            text-decoration: underline;
        }
       
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
       
        /* Footer Styles */
        footer {
            background-color: var(--primary-dark);
            color: var(--white);
            padding: 2rem 0;
            margin-top: auto;
        }
       
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
       
        .footer-links {
            display: flex;
            gap: 1.5rem;
        }
       
        .footer-links a {
            color: var(--white);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
       
        .footer-links a:hover {
            opacity: 1;
        }
       
        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
       
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 400px;
            }
           
            .login-left, .login-right {
                padding: 2rem;
            }
           
            .avatar-container {
                justify-content: center;
            }
           
            .avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
           
            .footer-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
           
            .footer-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>


    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="login-container">
                <div class="login-left">
                    <h2>Welcome Back!</h2>
                    <p>Sign in to access your personalized skincare dashboard and continue your journey to healthier skin.</p>
                   
                    <div class="avatar-container">
                        <div class="avatar">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="avatar">
                            <i class="fas fa-user-cog"></i>
                        </div>
                    </div>
                   
                    <p>Join thousands of satisfied customers who trust GlowSkin for their skincare needs.</p>
                </div>
               
                <div class="login-right">
                    <h2>Login to Your Account</h2>
                   
                    <?php if (isset($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                   
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                        </div>
                       
                        <button type="submit" class="btn btn-primary">Login</button>
                       
                        <div class="register-link">
                            Don't have an account? <a href="register.php">Register here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>


</body>
</html>