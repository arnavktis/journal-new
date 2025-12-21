<?php 
$currentPage = 'preview';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Continuum | Preview</title>
    <link rel="stylesheet" href="intro-styles.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Charlevoix+Pro:wght@700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        /* Custom styles for article cards */
        .article-number {
            background: var(--gradient-primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .article-author {
            color: var(--text-light);
            font-style: italic;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .preview-hero {
            background-image: linear-gradient(rgba(77, 127, 184, 0.8), rgba(93, 140, 196, 0.8)), url('bg-images/images-26.png');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero preview-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hero-badge" data-aos="fade-down" data-aos-delay="200">
                    <i class="fas fa-book-open"></i>
                    <span>Issue #1 Preview</span>
                </div>

                <img src="images/continuum-logo-white.png" alt="The Continuum" class="hero-logo" data-aos="zoom-in"
                    data-aos-delay="400">

                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="600">
                    You're Reading Issue #1 Of <span class="hero-title">The Continuum</span>!
                </h1>

                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="800">
                    This is a bi-annual publication featuring cutting-edge interdisciplinary research and scholarly
                    discourse.
                </p>

                <div class="hero-actions" data-aos="fade-up" data-aos-delay="1000">
                    <a href="all-issues.php" class="btn btn-primary btn-large">
                        <i class="fas fa-unlock"></i>
                        Get Full Access
                    </a>
                    <a href="all-issues.php" class="btn btn-primary btn-large">
                        <i class="fas fa-archive"></i>
                        Browse All Issues
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Articles Grid Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Issue #1 Articles</h2>
                <p class="section-subtitle">Explore the diverse collection of scholarly articles in this issue</p>
            </div>

            <div class="features-grid">
                <!-- Article 1 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <span class="article-number">1</span>
                    </div>
                    <h3>Interdisciplinary Research Methods</h3>
                    <p class="article-author">Dr. Sarah Johnson, Harvard University</p>
                    <p>Contemporary approaches to qualitative and quantitative research across academic disciplines.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>

                <!-- Article 2 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <span class="article-number">2</span>
                    </div>
                    <h3>Digital Humanities Perspectives</h3>
                    <p class="article-author">Prof. Michael Chen, Stanford University</p>
                    <p>The intersection of technology and humanistic inquiry in modern academia.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>

                <!-- Article 3 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <span class="article-number">3</span>
                    </div>
                    <h3>Environmental Policy and Society</h3>
                    <p class="article-author">Dr. Emma Williams, Oxford University</p>
                    <p>Policy frameworks for addressing contemporary environmental challenges.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>

                <!-- Article 4 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon">
                        <span class="article-number">4</span>
                    </div>
                    <h3>Cultural Studies and Globalization</h3>
                    <p class="article-author">Dr. James Anderson, Cambridge University</p>
                    <p>Cultural identity formation in an increasingly connected world.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>

                <!-- Article 5 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon">
                        <span class="article-number">5</span>
                    </div>
                    <h3>Economic Theory and Social Justice</h3>
                    <p class="article-author">Dr. Lisa Rodriguez, Yale University</p>
                    <p>Exploring the relationship between economic models and equitable outcomes.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>

                <!-- Article 6 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon">
                        <span class="article-number">6</span>
                    </div>
                    <h3>Philosophy of Science and Technology</h3>
                    <p class="article-author">Prof. Robert Kim, Princeton University</p>
                    <p>Philosophical implications of technological advancement in research.</p>
                    <div class="feature-highlight">
                        <span>Preview Available</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Purchase Enquiry Section -->
    <section class="contact-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Purchase Enquiry</h2>
                <p class="section-subtitle">Interested in accessing the full issue? Get in touch with us for purchase
                    details and subscription options.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-info" data-aos="fade-right">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>Full Issue Access</h3>
                        <p>Get complete access to all articles in this issue</p>
                        <a href="all-issues.php" class="contact-link">
                            View Full Access Options
                        </a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Purchase Inquiries</h3>
                        <p>For purchasing and subscription questions</p>
                        <a href="mailto:marketing@phindia.com" class="contact-link">
                            marketing@phindia.com
                        </a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-archive"></i>
                        </div>
                        <h3>All Issues</h3>
                        <p>Browse our complete collection</p>
                        <a href="all-issues.php" class="contact-link">
                            View All Issues
                        </a>
                    </div>
                </div>

                <div class="contact-form" data-aos="fade-left">
                    <form id="enquiryForm" class="form">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="institution">Institution (optional)</label>
                            <input type="text" id="institution" name="institution">
                        </div>

                        <div class="form-group">
                            <label for="message">Message / Details</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-paper-plane"></i>
                            Submit Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="footer-logos">
                        <img src="images/PHI_White.png" alt="PHI Logo" class="footer-logo">
                        <img src="images/continuum-logo-white.png" alt="The Continuum Logo" class="footer-logo">
                    </div>
                    <h3>The Continuum</h3>
                    <p>An interdisciplinary academic journal fostering rigorous research and enabling dialogue between
                        emerging scholars across all subjects.</p>
    
                    <div class="social-links">
                        <a href="https://www.facebook.com/PHILEARNING" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/philearningdelhi/" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/86816950/" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.youtube.com/@philearning" target="_blank">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
    
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Journal</h4>
                        <ul>
                            <!-- <li><a href="intro.html">Home</a></li> -->
                            <li><a href="index.html#about">About Us</a></li>
                            <li><a href="reviewers.html">Our Reviewers</a></li>
                            <li><a href="all-issues.php">All Issues</a></li>
                        </ul>
                    </div>
    
                    <div class="footer-column">
                        <h4>For Authors</h4>
                        <ul>
                            <li><a href="#contact">Submit Article</a></li>
                            <li><a href="#">Author Guidelines</a></li>
                        </ul>
                    </div>
    
                    <div class="footer-column">
                        <h4>Access</h4>
                        <ul>
                            <li><a href="all-issues.php">Free Preview</a></li>
                            <li><a href="all-issues.php">Full Access</a></li>
                            <li><a href="all-issues.php">Subscription</a></li>
                            <!-- <li><a href="all-issues.php">Open Access</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>
    
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; 2025 PHI Learning Pvt Ltd. All rights reserved.</p>
                </div>
                <!-- <div class="footer-legal">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Editorial Policy</a>
                    </div> -->
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="intro-script.js"></script>
</body>

</html>