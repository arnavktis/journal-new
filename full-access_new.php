<?php 
$currentPage = 'full-access';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Continuum | Full Access - Issue #1</title>
    <link rel="stylesheet" href="intro-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Charlevoix+Pro:wght@700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        /* Custom styles for full access page */
        .article-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin: 40px 0;
            box-shadow: 0 5px 20px rgba(77, 127, 184, 0.1);
            border: 1px solid rgba(77, 127, 184, 0.1);
        }
        
        .article-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--border-light);
        }
        
        .article-header h1 {
            color: var(--primary-blue);
            font-size: 2.5rem;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .article-author {
            color: var(--text-light);
            font-style: italic;
            font-size: 1.1rem;
        }
        
        .article-sidebar {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(77, 127, 184, 0.1);
            border: 1px solid rgba(77, 127, 184, 0.1);
            position: sticky;
            top: 120px;
            height: fit-content;
        }
        
        .sidebar-cover {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .chapter-list {
            list-style: none;
            padding: 0;
        }
        
        .chapter-list li {
            margin-bottom: 12px;
            padding: 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        
        .chapter-list li:hover {
            background: var(--bg-light);
        }
        
        .chapter-list li.active {
            background: var(--gradient-primary);
            color: white;
        }
        
        .chapter-list a {
            color: inherit;
            text-decoration: none;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .chapter-list li.active a {
            color: white;
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        
        .abstract {
            background: var(--bg-light);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border-left: 4px solid var(--primary-blue);
        }
        
        .article-text h2 {
            color: var(--primary-blue);
            margin-top: 35px;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .article-text p {
            line-height: 1.8;
            margin-bottom: 20px;
            text-align: justify;
        }
        
        .article-text figure {
            margin: 30px 0;
            text-align: center;
        }
        
        .article-text figure img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .article-text figcaption {
            margin-top: 10px;
            font-style: italic;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .article-text table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .article-text table th,
        .article-text table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
        }
        
        .article-text table th {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
        }
        
        .article-text table tr:hover {
            background: var(--bg-light);
        }
        
        .bibliography {
            background: var(--bg-light);
            padding: 25px;
            border-radius: 12px;
            margin-top: 30px;
        }
        
        .bib-entry {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-light);
            line-height: 1.6;
        }
        
        .bib-entry:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        sup {
            color: var(--primary-blue);
            font-weight: 600;
        }
        
        .access-hero {
            background-image: linear-gradient(rgba(77, 127, 184, 0.8), rgba(93, 140, 196, 0.8)), url('bg-images/images-26.png');
            background-size: cover;
            background-position: center;
        }
        
        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .article-sidebar {
                position: static;
                order: -1;
            }
            
            .article-content {
                padding: 25px;
            }
        }
        
        @media (max-width: 768px) {
            .article-header h1 {
                font-size: 2rem;
            }
            
            .article-content {
                padding: 20px;
                margin: 20px 0;
            }
            
            .article-sidebar {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero access-hero">
        <div class="hero-background">
            <div class="hero-pattern"></div>
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hero-badge" data-aos="fade-down" data-aos-delay="200">
                    <i class="fas fa-unlock"></i>
                    <span>Full Access</span>
                </div>
                
                <img src="images/continuum-logo-white.png" alt="The Continuum" class="hero-logo" data-aos="zoom-in" data-aos-delay="400">
                
                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="600">
                    Issue #1 of <span class="gradient-text">The Continuum</span>
                </h1>
                
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="800">
                    Complete access to all articles in this bi-annual interdisciplinary research publication.
                </p>
                
                <div class="hero-actions" data-aos="fade-up" data-aos-delay="1000">
                    <a href="preview.html" class="btn btn-outline btn-large">
                        <i class="fas fa-eye"></i>
                        View Preview
                    </a>
                    <a href="all-issues.html" class="btn btn-outline btn-large">
                        <i class="fas fa-archive"></i>
                        All Issues
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="about-section">
        <div class="container">
            <div class="main-grid">
                <!-- Sidebar -->
                <aside class="article-sidebar" data-aos="fade-right">
                    <img src="images/cover.png" alt="Issue Cover" class="sidebar-cover">
                    <h3 style="color: var(--primary-blue); margin-bottom: 20px;">
                        <i class="fas fa-list"></i> Articles in this Issue
                    </h3>
                    <ul class="chapter-list">
                        <li class="active">
                            <a href="#article1">
                                <strong>Article 1:</strong> Impact of Digital Learning on Higher Education
                            </a>
                        </li>
                        <li>
                            <a href="#article2">
                                <strong>Article 2:</strong> Interdisciplinary Research Methodologies
                            </a>
                        </li>
                        <li>
                            <a href="#article3">
                                <strong>Article 3:</strong> Cultural Studies in the Digital Age
                            </a>
                        </li>
                        <li>
                            <a href="#article4">
                                <strong>Article 4:</strong> Environmental Policy and Social Justice
                            </a>
                        </li>
                        <li>
                            <a href="#article5">
                                <strong>Article 5:</strong> Philosophy of Technology and Society
                            </a>
                        </li>
                        <li>
                            <a href="#article6">
                                <strong>Article 6:</strong> Economic Theory and Global Development
                            </a>
                        </li>
                        <li>
                            <a href="#article7">
                                <strong>Article 7:</strong> Media Studies and Public Discourse
                            </a>
                        </li>
                        <li>
                            <a href="#article8">
                                <strong>Article 8:</strong> Historical Perspectives on Modern Academia
                            </a>
                        </li>
                    </ul>
                </aside>

                <!-- Article Content -->
                <div class="article-content" data-aos="fade-left">
                    <header class="article-header">
                        <h1>Impact of Digital Learning on Higher Education</h1>
                        <p class="article-author">
                            <i class="fas fa-user"></i> By Dr. Jane Doe, University of Excellence
                        </p>
                    </header>

                    <div class="article-text">
                        <section class="abstract">
                            <strong><i class="fas fa-file-alt"></i> Abstract:</strong>
                            This article examines the transformative role of digital learning technologies within higher education systems. Drawing on empirical data and comparative studies, it evaluates the pedagogical efficacy of blended instructional models in university settings and explores institutional adaptations during and beyond the pandemic.
                        </section>

                        <h2>1. Introduction</h2>
                        <p>
                            The digitalization of education has emerged as a defining feature of contemporary pedagogy. Recent scholarship emphasizes the necessity for flexible, tech-integrated teaching models to address evolving learner needs.<sup>1</sup> The global shift during the COVID-19 pandemic accelerated these transitions, prompting structural reforms across academic institutions.<sup>2</sup>
                        </p>

                        <h2>2. Methodology</h2>
                        <p>
                            This study employs a mixed-methods research design, incorporating both quantitative survey responses and qualitative interviews. Data collection occurred across four higher education institutions between 2022 and 2024, offering cross-sectional insights into faculty and student adaptation to digital tools.
                        </p>

                        <h2>3. Results</h2>
                        <figure>
                            <img alt="Sample Graph showing student performance metrics" src="https://picsum.photos/400/300?random=1" />
                            <figcaption>
                                Figure 1. Average student scores before and after digital tool adoption across participating institutions.
                            </figcaption>
                        </figure>
                        
                        <p>
                            Findings indicate a statistically significant improvement in academic performance following the integration of digital instructional tools.<sup>3</sup> The data highlight positive trends in student engagement, participation, and course completion rates.
                        </p>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>University</th>
                                    <th>Pre-Digital GPA</th>
                                    <th>Post-Digital GPA</th>
                                    <th>Improvement</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>University A</td>
                                    <td>3.1</td>
                                    <td>3.4</td>
                                    <td>+0.3</td>
                                </tr>
                                <tr>
                                    <td>University B</td>
                                    <td>3.0</td>
                                    <td>3.3</td>
                                    <td>+0.3</td>
                                </tr>
                                <tr>
                                    <td>University C</td>
                                    <td>2.9</td>
                                    <td>3.2</td>
                                    <td>+0.3</td>
                                </tr>
                                <tr>
                                    <td>University D</td>
                                    <td>3.2</td>
                                    <td>3.5</td>
                                    <td>+0.3</td>
                                </tr>
                            </tbody>
                        </table>

                        <h2>4. Discussion</h2>
                        <p>
                            These results substantiate existing research on the advantages of blended learning environments. Nevertheless, the analysis also reveals regional disparities influenced by infrastructure access and socio-economic variables. As one scholar argues, equitable access remains a critical barrier to universal digital learning success.<sup>4</sup>
                        </p>
                        
                        <p>
                            The implementation of digital learning tools has also transformed the traditional classroom dynamic, enabling more personalized learning experiences and flexible scheduling options. Faculty members reported increased student engagement through interactive digital platforms, though they also noted the need for additional training and technical support.<sup>5</sup>
                        </p>

                        <h2>5. Conclusion</h2>
                        <p>
                            Digital learning holds considerable promise for enhancing academic outcomes. However, its effectiveness is contingent upon institutional investment, comprehensive faculty development, and inclusive access strategies that prioritize equity and adaptability. Future research should focus on long-term sustainability and the development of standardized assessment metrics for digital learning efficacy.<sup>6</sup>
                        </p>

                        <h2>6. References</h2>
                        <div class="bibliography">
                            <div class="bib-entry">
                                <sup>1</sup> Doe, Jane, and John Smith. <em>Digital Transformation in Higher Education</em>. London: Academic Press, 2025.
                            </div>
                            <div class="bib-entry">
                                <sup>2</sup> Johnson, Sarah. "Pandemic-Driven Educational Innovation." <em>Journal of Educational Technology</em> 15, no. 2 (2024): 45–62.
                            </div>
                            <div class="bib-entry">
                                <sup>3</sup> Chen, Michael. "Measuring Digital Learning Effectiveness." <em>Higher Education Research Quarterly</em> 8, no. 3 (2023): 78–95.
                            </div>
                            <div class="bib-entry">
                                <sup>4</sup> Williams, Emma. "Digital Divide in Academic Settings." <em>Education Policy Review</em> 12, no. 1 (2024): 23–40.
                            </div>
                            <div class="bib-entry">
                                <sup>5</sup> Anderson, James. <em>Faculty Perspectives on Educational Technology</em>. New York: University Publications, 2023.
                            </div>
                            <div class="bib-entry">
                                <sup>6</sup> Rodriguez, Lisa. "Sustainable Digital Learning Models." <em>International Journal of Educational Innovation</em> 9, no. 4 (2024): 112–128.
                            </div>
                        </div>
                    </div>
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
                    <p>An interdisciplinary academic journal fostering rigorous research and enabling dialogue between emerging scholars across humanities and social sciences.</p>
                    
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
                            <li><a href="intro.html">Home</a></li>
                            <li><a href="intro.html#about">About Us</a></li>
                            <li><a href="reviewers.html">Our Reviewers</a></li>
                            <li><a href="all-issues.html">All Issues</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>For Authors</h4>
                        <ul>
                            <li><a href="submit.html">Submit Article</a></li>
                            <li><a href="#">Author Guidelines</a></li>
                            <li><a href="#">Peer Review Process</a></li>
                            <li><a href="#">Publication Ethics</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Access</h4>
                        <ul>
                            <li><a href="preview.html">Free Preview</a></li>
                            <li><a href="full-access.html">Full Access</a></li>
                            <li><a href="#">Subscription</a></li>
                            <li><a href="#">Open Access</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; 2025 PHI Learning Pvt Ltd. All rights reserved.</p>
                </div>
                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Editorial Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="intro-script.js"></script>
</body>
</html>
