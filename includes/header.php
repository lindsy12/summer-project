<?php
ob_start();
// Get current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    /* Navigation Styles */
    :root {
        --color-primary: #6a11cb;
        --color-primary-dark: #4d2c91;
        --color-secondary: #ff7e5f;
        --color-white: #fff;
        --color-text: #333;
        --color-light-text: #666;
        --color-shadow: rgba(0, 0, 0, 0.1);
        --color-light-bg: #f8f9fa;
    }

    body {
        padding-top: 80px;
        background-color: var(--color-light-bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .header {
        background: var(--color-white);
        box-shadow: 0 2px 15px var(--color-shadow);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .navbar {
        padding: 0.5rem 2rem;
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .nav-logo a {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--color-primary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Desktop Menu */
    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
        gap: 1.5rem;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        color: var(--color-text);
        text-decoration: none;
        font-weight: 500;
        padding: 0.8rem 0;
        display: block;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        color: var(--color-primary);
    }

    .nav-link.active {
        color: var(--color-primary);
        font-weight: 600;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        border-radius: 2px;
    }

    .register-btn {
        background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        color: var(--color-white) !important;
        padding: 0.6rem 1.2rem !important;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .register-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
    }

    /* Hamburger Menu */
    .hamburger {
        display: none;
        cursor: pointer;
        width: 30px;
        height: 24px;
        position: relative;
        z-index: 1001;
        flex-direction: column;
        justify-content: space-between;
    }

    .hamburger span {
        display: block;
        width: 100%;
        height: 3px;
        background: var(--color-primary);
        border-radius: 3px;
        transition: all 0.3s ease;
        transform-origin: center;
    }

    .hamburger.active span:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }

    .hamburger.active span:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active span:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }

    /* Mobile Menu */
    @media (max-width: 992px) {
        body {
            padding-top: 70px;
        }

        .navbar {
            padding: 0.5rem 1rem;
        }

        .hamburger {
            display: flex;
        }
        
        .nav-menu {
            position: fixed;
            top: 70px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 70px);
            background: var(--color-white);
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem 0;
            transition: left 0.3s ease;
            box-shadow: 2px 0 10px var(--color-shadow);
            z-index: 999;
            gap: 0;
        }
        
        .nav-menu.active {
            left: 0;
        }
        
        .nav-item {
            margin: 1rem 0;
            width: 100%;
            text-align: center;
        }
        
        .nav-link {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            width: 100%;
        }

        .nav-link.active::after {
            width: 80%;
            left: 10%;
        }

        .register-btn {
            margin: 1rem auto;
            width: 200px;
            text-align: center;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-logo a span {
            font-size: 1.1rem;
        }
        
        .nav-logo img {
            height: 45px !important;
        }
    }

    @media (max-width: 480px) {
        .navbar {
            padding: 0.5rem;
        }
        
        .nav-logo a span {
            font-size: 1rem;
        }
        
        .nav-logo img {
            height: 40px !important;
        }
    }
    </style>
</head>
<body>    
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="../index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                        <img src="../assets/logo2.png" 
                             alt="SkinCare"
                             style="height: 50px; margin-right: 10px;" 
                    </a>
                </div>
                
                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item">
                        <a href="../index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="./pages/about.php" class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>
                    </li>                    
                    <li class="nav-item">
                        <a href="../pages/products.php" class="nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
                    </li>                    
                    <li class="nav-item">
                        <a href="../pages/dematologist.php" class="nav-link <?php echo ($current_page == 'dermatologist.php') ? 'active' : ''; ?>">Dermatologists</a>
                    </li>                    
                    <li class="nav-item">
                        <a href="../authentication/login.php" class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>">Login</a>
                    </li>  
                    <li class="nav-item">
                        <a href="../authentication/register.php" class="nav-link register-btn <?php echo ($current_page == 'register.php') ? 'active' : ''; ?>">Register</a>
                    </li>
                </ul>
                
                <div class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('navMenu');
        const navLinks = document.querySelectorAll('.nav-link');
        
        // Toggle mobile menu
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });
        
        // Close menu when clicking a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && 
                !navMenu.contains(e.target) && 
                !hamburger.contains(e.target) &&
                navMenu.classList.contains('active')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Update on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    </script>
