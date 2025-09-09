<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SkinGlow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }


        :root {
            --blue: #7e57c2;
            --light-blue: #6bb9f0;
            --orange: #ff8a65;
            --light-orange: #ffc77f;
            --dark: #2c3e50;
            --light: #f9f9f9;
        }


        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }


        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 70px;
        }


        /* Hero Section */
        .hero {
            text-align: center;
            padding: 4rem 1rem;
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23f8f9fa"/><circle cx="30" cy="30" r="5" fill="%23ff9a3c" opacity="0.3"/><circle cx="70" cy="70" r="8" fill="%233498db" opacity="0.3"/><circle cx="50" cy="20" r="6" fill="%23ff9a3c" opacity="0.2"/></svg>');
            background-size: cover;
            border-radius: 10px;
            margin-bottom: 3rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }


        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }


        .hero h1 span {
            color: var(--orange);
        }


        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 2rem;
            color: #555;
        }


        /* Section Styles */
        .section {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 4rem;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }


        .section:nth-child(even) {
            flex-direction: row-reverse;
        }


        .section-content {
            flex: 1;
            padding: 3rem;
            min-width: 300px;
        }


        .section-image {
            flex: 1;
            min-width: 300px;
            height: 400px;
            background-color: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 5rem;
        }


        .section:nth-child(odd) .section-image {
            background: linear-gradient(45deg, var(--blue), var(--light-blue));
        }


        .section:nth-child(even) .section-image {
            background: linear-gradient(45deg, var(--orange), var(--light-orange));
        }


        .section h2 {
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            color: var(--blue);
        }


        .section h2 span {
            color: var(--orange);
        }


        .section p {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: #555;
        }


        .btn {
            display: inline-block;
            background: var(--blue);
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }


        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
            background: #2980b9;
        }


        /* Values Section */
        .values {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            border-radius: 10px;
            margin-bottom: 3rem;
        }


        .values h2 {
            color: var(--blue);
            font-size: 2.5rem;
            margin-bottom: 3rem;
        }


        .values h2 span {
            color: var(--orange);
        }


        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }


        .value-item {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }


        .value-item:hover {
            transform: translateY(-10px);
        }


        .value-item i {
            font-size: 3rem;
            color: var(--blue);
            margin-bottom: 1.5rem;
        }


        .value-item h3 {
            color: var(--orange);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }


        /* Team Section */
        .team {
            padding: 4rem 2rem;
            text-align: center;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }


        .team h2 {
            color: var(--blue);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }


        .team h2 span {
            color: var(--orange);
        }


        .team p {
            max-width: 800px;
            margin: 0 auto 3rem;
            font-size: 1.1rem;
            color: #555;
        }


        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }


        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }


        .team-member:hover {
            transform: translateY(-10px);
        }


        .member-image {
            height: 250px;
            background: linear-gradient(45deg, var(--blue), var(--light-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }


        .member-info {
            padding: 1.5rem;
        }


        .member-info h3 {
            color: var(--blue);
            margin-bottom: 0.5rem;
        }


        .member-info p {
            color: var(--orange);
            font-weight: 600;
            margin-bottom: 1rem;
        }


        /* Responsive Design */
        @media (max-width: 900px) {
            .hero h1 {
                font-size: 2.5rem;
            }
        }


        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
           
            .section, .section:nth-child(even) {
                flex-direction: column;
            }
           
            .section-image {
                width: 100%;
                height: 300px;
            }
           
            .section-content {
                padding: 2rem;
            }
        }


        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.8rem;
            }
           
            .hero p {
                font-size: 1rem;
            }
           
            .section h2 {
                font-size: 1.8rem;
            }
           
            .values h2, .team h2 {
                font-size: 2rem;
            }
           
            .container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>


    <!-- Main Content -->
    <div class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1>About <span>Skin Glow</span></h1>
            <p>Discover our journey to revolutionize skincare with natural ingredients and science-backed formulations that bring out your natural glow.</p>
            <a href="#" class="btn">Explore Our Products</a>
        </section>


        <!-- Section 1: Our Story -->
        <section class="section">
            <div class="section-content">
                <h2>Our <span>Story</span></h2>
                <p>Founded in 2025, Skin Glow began as a small family-owned business with a passion for natural skincare. Our founder, Emily Roberts, started creating homemade creams and serums in her kitchen to address her own skin concerns.</p>
                <p>Today, we've grown into a trusted brand with a full range of products that combine the best of nature and science. We're committed to creating effective, sustainable, and cruelty-free skincare solutions that deliver visible results.</p>
                <a href="#" class="btn">Learn More</a>
            </div>
            <div class="section-image">
                <i class="fas fa-seedling"></i>
            </div>
        </section>


        <!-- Section 2: Our Philosophy -->
        <section class="section">
            <div class="section-content">
                <h2>Our <span>Philosophy</span></h2>
                <p>We believe that healthy skin starts with respecting both your body and the planet. That's why we formulate our products with carefully selected natural ingredients that are ethically sourced and environmentally friendly.</p>
                <p>Our approach is simple: fewer chemicals, more effectiveness. We avoid harsh additives and instead harness the power of botanicals, vitamins, and minerals to nurture your skin's natural balance and radiance.</p>
                <a href="#" class="btn">Our Ingredients</a>
            </div>
            <div class="section-image">
                <i class="fas fa-leaf"></i>
            </div>
        </section>


        <!-- Values Section -->
        <section class="values">
            <h2>Our <span>Values</span></h2>
            <div class="values-grid">
                <div class="value-item">
                    <i class="fas fa-recycle"></i>
                    <h3>Sustainability</h3>
                    <p>We're committed to eco-friendly practices, from sourcing to packaging, to minimize our environmental footprint.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-heart"></i>
                    <h3>Natural Ingredients</h3>
                    <p>We use only the purest natural ingredients, carefully selected for their effectiveness and safety.</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-award"></i>
                    <h3>Quality</h3>
                    <p>Every product undergoes rigorous testing to ensure the highest standards of quality and effectiveness.</p>
                </div>
            </div>
        </section>


    </div>
        <?php include '../includes/footer.php'; ?>




    <script>
        // Simple animation for elements when they come into view
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.section, .value-item, .team-member');
           
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
           
            sections.forEach(section => {
                section.style.opacity = 0;
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(section);
            });
        });
    </script>
</body>
</html>
