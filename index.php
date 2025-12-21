<?php 
$currentPage = 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Continuum | Interdisciplinary Academic Journal</title>
    <link rel="stylesheet" href="intro-styles.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Charlevoix+Pro:wght@700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <div data-aos-delay="200" style="display: flex; justify-content: center;">
                    <img src="images/continuum-logo-white.png" alt="Continuum Logo" class="hero-logo" />
                </div>

                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="600">
                    Welcome to <span class="hero-title">The Continuum</span>
                </h1>

                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="800">
                    An interdisciplinary academic journal fostering rigorous research and enabling dialogue between
                    emerging scholars across all subjects.
                </p>

                <div class="hero-features" data-aos="fade-up" data-aos-delay="1000">
                    <div class="feature-pill">
                        <i class="fas fa-network-wired"></i>
                        <span>Interdisciplinary</span>
                    </div>
                    <div class="feature-pill">
                        <i class="fas fa-eye"></i>
                        <span>Double-blind Review</span>
                    </div>
                    <div class="feature-pill">
                        <i class="fas fa-globe"></i>
                        <span>International distribution network</span>
                    </div>
                </div>

                <div class="hero-actions" data-aos="fade-up" data-aos-delay="1200">
                    <a href="#about" class="btn btn-primary btn-large">
                        <i class="fas fa-book-open"></i>
                        Discover Our Journal
                    </a>
                    <!-- <a href="submit.html" class="btn btn-primary btn-large">
                            <i class="fas fa-pen-fancy"></i>
                            Submit Your Research
                        </a> -->
                </div>
            </div>

            <div class="hero-visual" data-aos="fade-left" data-aos-delay="800">
                <div class="journal-preview">
                    <img src="images/cover.png" alt="Journal Cover" class="cover-image">
                    <div class="preview-overlay">
                        <div class="preview-content">
                            <h3>Latest Issue</h3>
                            <p>Exploring Contemporary Themes in Academic Research</p>
                            <a href="preview.html" class="preview-btn">
                                <i class="fas fa-eye"></i>
                                Preview
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <div class="scroll-text">Explore</div>
            <div class="scroll-arrow"></div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">About The Continuum</h2>
                <p class="section-subtitle">Bridging disciplines, fostering innovation, and empowering emerging voices
                    in academia</p>
            </div>

            <div class="about-content">
                <div class="about-text" data-aos="fade-right">
                    <div class="about-highlight">
                        <h3>We are an interdisciplinary academic journal</h3>
                        <p class="lead">The Continuum is a bi-annual peer-reviewed academic journal by PHI Learning Pvt.
                            Ltd. that encourages bold, interdisciplinary research and unconventional ideas.</p>
                    </div>

                    <div class="about-description">
                        <p>Our mission is to foster rigorous interdisciplinary research and enable dialogue between
                            emerging scholars across all subjects. We envision a dynamic academic platform that reflects
                            the pulse of contemporary thought while staying rooted in critical traditions.</p>

                        <div class="about-values">
                            <h4>Our Core Values</h4>
                            <ul class="values-list">
                                <li><i class="fas fa-lightbulb"></i> <strong>Innovation:</strong> Encouraging fresh
                                    perspectives and unconventional approaches</li>
                                <li><i class="fas fa-handshake"></i> <strong>Collaboration:</strong> Building bridges
                                    between disciplines and communities</li>
                                <li><i class="fas fa-balance-scale"></i> <strong>Integrity:</strong> Maintaining the
                                    highest standards of academic rigour</li>
                                <li><i class="fas fa-users"></i> <strong>Inclusivity:</strong> Amplifying diverse voices
                                    from around the world</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="about-visual" data-aos="fade-left">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-number" data-target="0">0</div>
                            <div class="stat-label">Issues Published</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-number" data-target="0">0</div>
                            <div class="stat-label">Research Articles</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number" data-target="0">0</div>
                            <div class="stat-label">Contributing Authors</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-globe-americas"></i>
                            </div>
                            <div class="stat-number" data-target="0">0</div>
                            <div class="stat-label">Countries Represented</div>
                        </div>
                    </div>

                    <div class="about-image">
                        <img src="images/typography.jpeg" alt="Academic Excellence" class="feature-image"
                            style="max-width: 420px; width: 100%; height: auto;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section id="mission" class="mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="mission-icon">
                        <i class="fas fa-telescope"></i>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To become a leading platform for interdisciplinary academic discourse, fostering innovation and
                        collaboration across traditional boundaries while maintaining the highest standards of scholarly
                        excellence.</p>
                </div>

                <div class="mission-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="mission-icon">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h3>Our Mission</h3>
                    <p>We are committed to publishing high-quality, peer-reviewed research that challenges conventional
                        thinking, promotes interdisciplinary dialogue, and contributes meaningfully to academic and
                        social progress.</p>
                </div>

                <div class="mission-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="mission-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Our Purpose</h3>
                    <p>To provide emerging scholars with a prestigious platform to share their research, connect with
                        peers globally, and contribute to the advancement of knowledge across disciplines.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">What Makes Us Unique</h2>
                <p class="section-subtitle">Discover the features that set The Continuum apart in academic publishing
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h3>Interdisciplinary Focus</h3>
                    <p>Breaking down academic silos by encouraging research that spans multiple disciplines, fostering
                        innovation through diverse perspectives.</p>
                    <div class="feature-highlight">
                        <span>Cross-disciplinary collaboration</span>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Emerging Scholar Platform</h3>
                    <p>Dedicated to amplifying the voices of graduate students, postdocs, and early-career researchers
                        across the globe.</p>
                    <div class="feature-highlight">
                        <span>Supporting new voices</span>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Rigorous Peer Review</h3>
                    <p>Our double-blind peer review process ensures the highest quality standards while maintaining
                        fairness and objectivity.</p>
                    <div class="feature-highlight">
                        <span>Quality assurance</span>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>International distribution network</h3>
                    <p>Connecting researchers worldwide through our international distribution network and online
                        accessibility.</p>
                    <div class="feature-highlight">
                        <span>International community</span>
                    </div>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Timely Publication</h3>
                    <p>Bi-annual publication schedule ensures timely dissemination of research while maintaining
                        thorough review processes.</p>
                    <div class="feature-highlight">
                        <span>Efficient timeline</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Publication Process Section -->
    <section id="publish" class="process-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Your Path to Publication</h2>
                <p class="section-subtitle">A streamlined, transparent process designed to support authors at every step
                </p>
            </div>

            <div class="process-timeline">
                <div class="timeline-item" data-aos="fade-right" data-aos-delay="100">
                    <div class="timeline-marker">
                        <span class="step-number">1</span>
                    </div>
                    <div class="timeline-content">
                        <h3>Submit Your Research</h3>
                        <p>Submit your manuscript through our user-friendly portal. Our team provides guidance on
                            formatting and requirements to ensure your submission meets our standards.</p>
                        <div class="timeline-features">
                            <span class="feature-tag"><i class="fas fa-check"></i> Online submission</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Format guidance</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Quick confirmation</span>
                        </div>
                    </div>
                </div>

                <div class="timeline-item" data-aos="fade-left" data-aos-delay="200">
                    <div class="timeline-marker">
                        <span class="step-number">2</span>
                    </div>
                    <div class="timeline-content" style="text-align: left;">
                        <h3>Editorial Assessment</h3>
                        <p>Our senior editors evaluate your submission for relevance, originality, and adherence to
                            journal guidelines. We provide constructive feedback at every stage.</p>
                        <div class="timeline-features">
                            <span class="feature-tag"><i class="fas fa-check"></i> Expert evaluation</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Detailed feedback</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> 2-3 week timeline</span>
                        </div>
                    </div>
                </div>


                <div class="timeline-item" data-aos="fade-right" data-aos-delay="300">
                    <div class="timeline-marker">
                        <span class="step-number">3</span>
                    </div>
                    <div class="timeline-content">
                        <h3>Peer Review Process</h3>
                        <p>Your manuscript undergoes rigorous double-blind peer review by experts in your field,
                            ensuring quality and academic integrity.</p>
                        <div class="timeline-features">
                            <span class="feature-tag"><i class="fas fa-check"></i> Double-blind review</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Expert reviewers</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> 6-8 week process</span>
                        </div>
                    </div>
                </div>

                <div class="timeline-item" data-aos="fade-left" data-aos-delay="400">
                    <div class="timeline-marker">
                        <span class="step-number">4</span>
                    </div>
                    <div class="timeline-content" style="text-align: left;">
                        <h3>Revision & Finalisation</h3>
                        <p>Based on reviewer feedback, you will have the opportunity to revise your work. Our editorial
                            team will assist you throughout the revision process.</p>
                        <div class="timeline-features">
                            <span class="feature-tag"><i class="fas fa-check"></i> Guided revisions</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Editorial support</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Clear timeline</span>
                        </div>
                    </div>
                </div>

                <div class="timeline-item" data-aos="fade-right" data-aos-delay="500">
                    <div class="timeline-marker">
                        <span class="step-number">5</span>
                    </div>
                    <div class="timeline-content">
                        <h3 style="font-size: 1.9rem;">Publication & Promotion</h3>
                        <p>Your accepted manuscript is professionally formatted, published online and in print, and
                            promoted through our academic networks.</p>
                        <div class="timeline-features">
                            <span class="feature-tag"><i class="fas fa-check"></i> Professional formatting</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> International distribution
                                network</span>
                            <span class="feature-tag"><i class="fas fa-check"></i> Promotional support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Issues & Access Section -->
    <section id="issues" class="issues-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Explore Our Issues</h2>
                <p class="section-subtitle">Discover cutting-edge research across diverse themes and disciplines</p>
            </div>

            <div class="issues-grid">
                <div class="issue-card featured" data-aos="fade-up" data-aos-delay="100">
                    <div class="issue-badge">Latest Issue</div>
                    <div class="issue-cover">
                        <img src="images/cover.png" alt="Latest Issue">
                    </div>
                    <div class="issue-content">
                        <h3>Contemporary Themes in Academic Research</h3>
                        <p class="issue-date">September 2025 • Volume 6, Issue 2</p>
                        <p class="issue-description">Exploring the intersection of technology, society, and human
                            experience in the digital age.</p>
                        <div class="issue-actions">
                            <a href="preview.html" class="btn btn-primary">
                                <i class="fas fa-eye"></i>
                                Preview
                            </a>
                            <a href="all-issues.php" class="btn btn-outline">
                                <i class="fas fa-download"></i>
                                Full Access
                            </a>
                        </div>
                    </div>
                </div>

                <div class="access-options" data-aos="fade-up" data-aos-delay="300">
                    <h3>Access Options</h3>

                    <div class="access-card">
                        <div class="access-icon">
                            <i class="fas fa-unlock"></i>
                        </div>
                        <h4>Free Preview</h4>
                        <p>Browse abstracts, introductions, and selected content from each issue.</p>
                        <a href="preview.html" class="access-link">
                            Start Reading <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="access-card">
                        <div class="access-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h4>Full Access</h4>
                        <p>Complete access to all published articles, research data, and supplementary materials.</p>
                        <a href="all-issues.php" class="access-link">
                            Get Access <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="access-card">
                        <div class="access-icon">
                            <i class="fas fa-archive"></i>
                        </div>
                        <h4>Archive</h4>
                        <p>Explore our complete collection of past issues and research articles.</p>
                        <a href="all-issues.php" class="access-link">
                            Browse Archive <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Connect With Us</h2>
                <p class="section-subtitle">We are here to support your academic journey. Reach out with questions,
                    submissions, or collaboration ideas.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-info" data-aos="fade-right">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Editorial Inquiries</h3>
                        <p>For submissions, peer review, and editorial questions</p>
                        <a href="mailto:thecontinuum@phindia.com" class="contact-link">
                            thecontinuum@phindia.com
                        </a>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3>Our Office</h3>
                        <p>We are located at</p>
                        <div class="contact-address">
                            Rimjhim House, 111<br>
                            Patparganj Industrial Estate<br>
                            Delhi 110092<br>
                            India
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Phone</h3>
                        <p>Speak with our editorial team</p>
                        <a href="tel:+911143031100" class="contact-link">
                            +91 11 43031100
                        </a>
                    </div>
                </div>

                <div class="contact-form" data-aos="fade-left">
                    <form id="contactForm" class="form" method="post" action="process_submission.php"
                        enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="affiliation">Institution/Affiliation</label>
                            <input type="text" id="affiliation" name="affiliation">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="submission">Article Submission</option>
                                <!-- <option value="review">Peer Review</option>
                                    <option value="access">Access & Subscription</option>
                                    <option value="collaboration">Collaboration</option>
                                    <option value="other">Other</option> -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="article_synopsis">Article Synopsis</label>
                            <textarea id="article_synopsis" name="article_synopsis" rows="8" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="manuscript">Manuscript (required) — PDF or Word</label>
                            <input type="file" id="manuscript" name="manuscript" accept=".pdf, .doc, .docx" required>
                            <div id="manuscript-list" class="file-list" aria-live="polite"
                                style="margin-top:8px;font-size:0.95rem;color:#374151"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-paper-plane"></i>
                            Submit
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

                            <li><a href="#about">About Us</a></li>
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
    <div id="submissionModal" class="submission-modal">
        <div class="submission-modal-content">
            <button type="button" class="submission-modal-close">&times;</button>
            <h2>Thank you!</h2>
            <p id="submissionModalMessage"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="intro-script.js"></script>
</body>

</html>