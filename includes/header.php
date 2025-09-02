<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Site</title>
    <style>
    /* Navigation Styles */
    :root {
        --color-primary: #0066cc;
        --color-primary-dark: #0055aa;
        --color-white: #fff;
        --color-text: #333;
        --color-shadow: rgba(0, 0, 0, 0.1);
    }


    .header {
        background: var(--color-white);
        box-shadow: 0 2px 10px var(--color-shadow);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }


    .navbar {
        padding: 1rem 2rem;
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
        font-size: 1rem;
        font-weight: bold;
        color: var(--color-text);
        text-decoration: none;
    }


    /* Desktop Menu */
    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        align-items: center;
    }


    .nav-item {
        margin-left: 1.5rem;
        position: relative;
    }


    .nav-link {
        color: var(--color-text);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 0;
        display: block;
        transition: color 0.3s;
    }


    .nav-link:hover {
        color: var(--color-primary);


    }


    .nav-link.active {
        color: var(--color-primary);
        border-bottom: 2px solid var(--color-primary);
    }


    .register-btn {
        background: var(--color-primary);
        color: var(--color-white) !important;
        padding: 0.5rem 1rem !important;
        border-radius: 4px;
    }


    .register-btn:hover {
        background: var(--color-primary-dark);
    }


    /* Dropdown Menu */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--color-white);
        box-shadow: 0 2px 10px var(--color-shadow);
        border-radius: 4px;
        padding: 0.5rem 0;
        min-width: 200px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
        z-index: 100;
    }


    .dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
    }


    .dropdown-menu li {
        padding: 0;
    }


    .dropdown-menu a {
        display: block;
        padding: 0.4rem 12px;
        color: var(--color-text);
        text-decoration: none;
        transition: background 0.3s;
    }


    .dropdown-menu a:hover {
        background: #f5f5f5;
    }


    .dropdown-arrow {
        font-size: 0.7rem;
        margin-left: 0.3rem;
    }


    /* Hamburger Menu */
    .hamburger {
        display: none;
        cursor: pointer;
        width: 30px;
        height: 24px;
        position: relative;
        z-index: 1001;
    }


    .hamburger span {
        display: block;
        width: 100%;
        height: 3px;
        background: var(--color-text);
        position: absolute;
        left: 0;
        transition: all 0.3s ease;
    }


    .hamburger span:nth-child(1) {
        top: 0;
    }


    .hamburger span:nth-child(2) {
        top: 50%;
        transform: translateY(-50%);
    }


    .hamburger span:nth-child(3) {
        bottom: 0;
    }


    /* Mobile Menu */
    @media (max-width: 992px) {
        .hamburger {
            display: block;
        }
       
        .nav-menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            max-width: 300px;
            height: 90vh;
            background: var(--color-white);
            flex-direction: column;
            align-items: flex-start;
            padding: 6px 6px;
            transition: left 0.3s ease;
            box-shadow: 2px 0 10px var(--color-shadow);
            z-index: 1000;
        }
       
        .nav-menu.active {
            left: 0;
        }
       
        .nav-item {
            margin: 14px 0;
            width: 100%;
        }
       
        .nav-link {
            padding: 0.5rem 0;
        }
       
        /* Mobile Dropdown */
        .dropdown-menu {
            position: static;
            box-shadow: none;
            opacity: 1;
            visibility: visible;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0;
            width: 100%;
        }
       
        .dropdown.active .dropdown-menu {
            max-height: 400px;
            padding: 6px 0 6px 6px;
        }
       
        .dropdown-arrow {
            display: inline-block;
            transition: transform 0.3s;
        }
       
        .dropdown.active .dropdown-arrow {
            transform: rotate(180deg);
        }
       
        /* Hamburger Animation */
        .hamburger.active span:nth-child(1) {
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
        }
       
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
       
        .hamburger.active span:nth-child(3) {
            bottom: 50%;
            transform: translateY(50%) rotate(-45deg);
        }
    }
    </style>
</head>
<body>    
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
<div class="nav-logo" style="display: flex; align-items: center; padding: 2px;">
    <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <img src="../assets/logo2.png"
             alt="skincare"
             style="height: 60px; margin-right: 10px;"
             onerror="this.onerror=null; this.style.display='none'; document.getElementById('logo-error').style.display='block'">
        <span id="logo-error" style="display: none; color: red; padding: 5px;">
            <!-- [Logo missing: Check logo2.png path] -->
        </span>
        <span style="font-weight: bold; font-size: 1rem;">SkinCare</span>
    </a>
</div>
       <ul class="nav-menu" id="navMenu">
                    <li class="nav-item"><a href="#" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="./pages/about.php" class="nav-link">About</a></li>                    
                    <li class="nav-item"><a href="./pages/products.php" class="nav-link">Products</a></li>                    
                    <li class="nav-item"><a href="./pages/dematologist.php" class="nav-link">Dematologist</a></li>                    
                    <li class="nav-item"><a href="../authentication/login.php" class="nav-link">Login</a></li>  
                    <li class="nav-item"><a href="../authentication/register.php" class="nav-link">Register</a></li>  








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
        const dropdowns = document.querySelectorAll('.dropdown');
       
        // Toggle mobile menu
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
       
        // Close menu when clicking a link
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    hamburger.classList.remove('active');
                    navMenu.classList.remove('active');
                   
                    // Close any open dropdowns
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                }
            });
        });
       
        // Mobile dropdown functionality
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.nav-link');
           
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 992) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                   
                    // Close other dropdowns
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('active');
                        }
                    });
                }
            });
        });
       
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 &&
                !navMenu.contains(e.target) &&
                !hamburger.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
               
                // Close any open dropdowns
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });
       
        // Update on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
               
                // Close any open dropdowns
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });
    });
    </script>
    


