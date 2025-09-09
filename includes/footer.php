<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma,  Verdana, sans-serif;
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
        }
       
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
       
        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
       
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }
       
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
       
        .logo-icon {
            font-size: 2rem;
            color: var(--primary);
        }
       
        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
       
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
       
        .nav-link {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }
       
        .nav-link:hover {
            color: var(--primary);
            background-color: rgba(126, 87, 194, 0.1);
        }
       
        .auth-buttons {
            display: flex;
            gap: 1rem;
        }
       
        .auth-btn {
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
       
        .login-btn {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
       
        .login-btn:hover {
            background-color: var(--primary);
            color: var(--white);
            box-shadow: var(--button-shadow);
        }
       
        .register-btn {
            background-color: var(--primary);
            color: var(--white);
            box-shadow: var(--button-shadow);
        }
       
        .register-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
        }
       
        /* Hero Section */
        .hero {
            padding: 8rem 0 5rem;
            background: linear-gradient(135deg, rgba(126, 87, 194, 0.1) 0%, rgba(255, 138, 101, 0.1) 100%);
            position: relative;
            overflow: hidden;
        }
       
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
       
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
       
        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            line-height: 1.7;
        }
       
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
       
        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
       
        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
            box-shadow: var(--button-shadow);
        }
       
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
        }
       
        .btn-secondary {
            background-color: var(--secondary);
            color: var(--white);
            box-shadow: 0 5px 15px rgba(255, 138, 101, 0.3);
        }
       
        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-3px);
        }
       
        .hero-image {
            position: relative;
        }
       
        .hero-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }
       
        /* Features Section */
        .features {
            padding: 5rem 0;
        }
       
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
       
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }
       
        .section-title p {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }
       
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
       
        .feature-card {
            background-color: var(--white);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
       
        .feature-card:hover {
            transform: translateY(-10px);
        }
       
        .feature-icon {
            width: 80px;
            height: 80px;
            background-color: rgba(126, 87, 194, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: var(--primary);
        }
       
        .feature-card h3 {
            font-size: 1.5rem;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }
       
        .feature-card p {
            color: var(--text-light);
        }
       
        /* Products Section */
        .products {
            padding: 5rem 0;
            background-color: var(--white);
        }
       
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
       
        .product-card {
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
        }
       
        .product-card:hover {
            transform: translateY(-10px);
        }
       
        .product-image {
            height: 200px;
            overflow: hidden;
        }
       
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
       
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
       
        .product-info {
            padding: 1.5rem;
        }
       
        .product-info h3 {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }
       
        .product-info p {
            color: var(--text-light);
            margin-bottom: 1rem;
        }
       
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 1rem;
        }
       
        /* Testimonials Section */
        .testimonials {
            padding: 5rem 0;
            background: linear-gradient(135deg, rgba(126, 87, 194, 0.1) 0%, rgba(255, 138, 101, 0.1) 100%);
        }
       
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
       
        .testimonial-card {
            background-color: var(--white);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }
       
        .testimonial-text {
            font-style: italic;
            color: var(--text);
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 2rem;
        }
       
        .testimonial-text::before {
            content: """;
            font-size: 4rem;
            color: var(--primary-light);
            position: absolute;
            left: -1rem;
            top: -1rem;
            opacity: 0.3;
        }
       
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
       
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
        }
       
        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
       
        .author-info h4 {
            color: var(--primary-dark);
            margin-bottom: 0.2rem;
        }
       
        .author-info p {
            color: var(--text-light);
            font-size: 0.9rem;
        }
       
        /* CTA Section */
        .cta {
            padding: 5rem 0;
            text-align: center;
            background-color: var(--primary);
            color: var(--white);
        }
       
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
       
        .cta p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            opacity: 0.9;
        }
       
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
       
        .btn-light {
            background-color: var(--white);
            color: var(--primary);
        }
       
        .btn-light:hover {
            background-color: var(--background);
            transform: translateY(-3px);
        }
       
        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: var(--white);
            padding: 4rem 0 2rem;
        }
       
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
       
        .footer-column h3 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
       
        .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--secondary);
        }
       
        .footer-column p {
            opacity: 0.8;
            margin-bottom: 1rem;
            line-height: 1.7;
        }
       
        .footer-links {
            list-style: none;
        }
       
        .footer-links li {
            margin-bottom: 0.8rem;
        }
       
        .footer-links a {
            color: var(--white);
            opacity: 0.8;
            text-decoration: none;
            transition: all 0.3s ease;
        }
       
        .footer-links a:hover {
            opacity: 1;
            padding-left: 5px;
        }
       
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
       
        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s ease;
        }
       
        .social-link:hover {
            background-color: var(--secondary);
            transform: translateY(-3px);
        }
       
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
       
        .footer-bottom p {
            opacity: 0.7;
        }
       
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 3rem;
            }
           
            .hero-text h1 {
                font-size: 2.8rem;
            }
           
            .hero-buttons {
                justify-content: center;
            }
        }
       
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1rem;
            }
           
            .nav-menu {
                gap: 1rem;
            }
           
            .auth-buttons {
                margin-top: 1rem;
            }
           
            .hero {
                padding: 7rem 0 4rem;
            }
           
            .hero-text h1 {
                font-size: 2.2rem;
            }
           
            .btn {
                padding: 0.8rem 1.5rem;
            }
        }
       
        @media (max-width: 576px) {
            .nav-menu {
                flex-direction: column;
                gap: 0.5rem;
            }
           
            .auth-buttons {
                flex-direction: column;
                width: 100%;
            }
           
            .auth-btn {
                width: 100%;
                justify-content: center;
            }
           
            .hero-text h1 {
                font-size: 2rem;
            }
           
            .hero-buttons {
                flex-direction: column;
            }
           
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
 
   
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>GlowSkin</h3>
                    <p>Premium skincare products developed with dermatologists to help you achieve healthy, radiant skin.</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
               
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
               
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Track Order</a></li>
                    </ul>
                </div>
               
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Beauty Street, Skincare City</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@glowskin.com</p>
                </div>
            </div>
           
            <div class="footer-bottom">
                <p>&copy; 2023 GlowSkin. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
