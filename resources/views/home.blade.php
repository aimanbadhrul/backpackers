<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backpackers - Explore the World</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
        }

        h1, h2, h3 {
            text-align: center;
            margin: 1rem 0;
        }

        p {
            text-align: center;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
        }

        /* Hero Section */
        .hero {
            background: url('https://images.pexels.com/photos/12057/pexels-photo-12057.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .hero p {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .hero button {
            background-color: #ff6f61;
            color: white;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .hero button:hover {
            background-color: #e05d51;
        }

        /* Features Section */
        .features {
            padding: 4rem 1rem;
            background-color: #f9f9f9;
        }

        .features-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
        }

        .feature {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .feature img {
            width: 100px;
            height: 100px;
            margin-bottom: 1rem;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 4rem 1rem;
            background-color: #f2f2f2;
        }

        .testimonial {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 1rem auto;
            max-width: 600px;
        }

        /* Call to Action */
        .cta {
            background-color: #ff6f61;
            color: white;
            text-align: center;
            padding: 2rem 1rem;
        }

        .cta h2 {
            margin-bottom: 1rem;
        }

        .cta button {
            background-color: white;
            color: #ff6f61;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .cta button:hover {
            background-color: #e0e0e0;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to Backpackers</h1>
        <p>Your Adventure Starts Here</p>
        <a href="https://backpackers.test/admin/login" class="button-link">
            <button>Explore Now</button>
        </a>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Why Choose Backpackers?</h2>
        <div class="features-container">
            <div class="feature">
                <img src="https://cdn-icons-png.freepik.com/256/1706/1706736.png?semt=ais_hybrid" alt="Adventure Icon">
                <h3>Unique Adventures</h3>
                <p>Discover hidden gems and breathtaking landscapes.</p>
            </div>
            <div class="feature">
                <img src="https://cdn-icons-png.freepik.com/512/3090/3090423.png" alt="Community Icon">
                <h3>Vibrant Community</h3>
                <p>Connect with fellow travelers from around the world.</p>
            </div>
            <div class="feature">
                <img src="https://cdn-icons-png.freepik.com/256/2057/2057748.png?semt=ais_hybrid" alt="Support Icon">
                <h3>24/7 Support</h3>
                <p>We're here to help you every step of the way.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>What Our Travelers Say</h2>
        <div class="testimonial">
            <p>"Backpackers made my dream trip a reality. I can't wait to plan my next adventure!"</p>
            <p><strong>- Sarah L.</strong></p>
        </div>
        <div class="testimonial">
            <p>"The community is so supportive, and the guides are incredibly knowledgeable."</p>
            <p><strong>- James K.</strong></p>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta">
        <h2>Ready to Embark on Your Journey?</h2>
        <button>Join Us Today</button>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 Backpackers. All rights reserved.</p>
    </footer>

</body>
</html>