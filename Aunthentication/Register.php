<?php
session_start();
include "config.php";


$error = '';
$success = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
   
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
       
        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
           
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
           
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again later.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LinkedSkin</title>
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
        }
       
        .register-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
       
        .register-left {
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
       
        .register-left h2 {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }
       
        .register-left p {
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
       
        .register-right {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
       
        .register-right h2 {
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
       
        select.form-control {
            padding-left: 15px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237e57c2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
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
       
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
        }
       
        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
       
        .login-link a:hover {
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
       
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
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
        @media (max-width: 900px) {
            .register-container {
                flex-direction: column;
                max-width: 500px;
            }
           
            .register-left, .register-right {
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
    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>


    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="register-container">
                <div class="register-left">
                    <h2>Join Our Community</h2>
                    <p>Create an account to access personalized skincare recommendations, book appointments with dermatologists, and explore our premium products.</p>
                   
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
                   
                    <p>Already have an account? <a href="login.php" style="color: var(--secondary-light); font-weight: 600;">Login here</a></p>
                </div>
               
                <div class="register-right">
                    <h2>Create Your Account</h2>
                   
                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                   
                    <?php if (!empty($success)): ?>
                        <div class="success-message"><?php echo $success; ?></div>
                    <?php else: ?>
                   
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="role">Account Type</label>
                            <select id="role" name="role" class="form-control" required>
                            <option value="">Select your role</option>
                                <option value="client" <?php echo (isset($_POST['role']) && $_POST['role'] === 'client') ? 'selected' : ''; ?>>Client/Customer</option>
                                <option value="dermatologist" <?php echo (isset($_POST['role']) && $_POST['role'] === 'dermatologist') ? 'selected' : ''; ?>>Dermatologist</option>
                                <option value="cosmetic" <?php echo (isset($_POST['role']) && $_POST['role'] === 'cosmetic') ? 'selected' : ''; ?>>Cosmetic Agent</option>
                                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>


                            </select>
                        </div>
                       
                        <button type="submit" class="btn btn-primary">Create Account</button>
                       
                        <div class="login-link">
                            Already have an account? <a href="login.php">Login here</a>
                        </div>
                    </form>
                   
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>


    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>

