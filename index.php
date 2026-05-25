    <?php
    /**
     * Home page – safe version
     * Uses DB only if available, never crashes
     */

    $currentPage = 'home';
    $latestVolume = null;

    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';

        if (isset($DB) && $DB instanceof PDO) {
            try {
                $stmt = $DB->query("
                    SELECT id, title, year, volume_no, cover_image
                    FROM volumes
                    ORDER BY year DESC, volume_no DESC
                    LIMIT 1
                ");
                $latestVolume = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Throwable $e) {
                error_log('INDEX latest volume query failed: ' . $e->getMessage());
                $latestVolume = null;
            }
        }
    }

    $stats = [
    'volumes' => 0,
    'articles' => 0,
    'authors' => 0,
    'countries' => 1
    ];

    if (isset($DB) && $DB instanceof PDO) {
    try {
        $stats['volumes']  = (int)$DB->query("SELECT COUNT(*) FROM volumes")->fetchColumn();
        $stats['articles'] = (int)$DB->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $stats['authors']  = (int)$DB->query("SELECT COUNT(*) FROM authors")->fetchColumn();

        // optional, only if you store country in authors
        // $stats['countries'] = (int)$DB->query("SELECT COUNT(DISTINCT country) FROM authors")->fetchColumn();

    } catch (Throwable $e) {
        // fail silently
    }
    }


    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>The Continuum:  An Academic Journal</title>
        
        <!-- Preconnect to external resources -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        
        <!-- Critical CSS -->
        <link rel="stylesheet" href="intro-styles.css">
        
        <!-- Optimized fonts - single family with font-display swap -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Deferred non-critical CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
        </noscript>

        <style>
            .cover-wrap{
        width:100%;
        aspect-ratio: 604 / 782; /* journal ratio */
    }

    .cover-wrap img{
        width:100%;
        height:100%;
        object-fit: contain;
    }

    /* Aim and Scope Styling */
    .aim-scope {
        margin: 3rem 0 0 0;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-top: 4px solid #2c3e50;
    }

    .aim-scope h3 {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .aim-scope h3::before {
        content: none;
    }

    .aim-scope-content {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .aim-scope-block {
        background: white;
        border-radius: 8px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .aim-scope-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .aim-scope-block h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
    }

    .aim-scope-block p {
        color: #212529;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 0.5rem;
    }

    .aim-scope-block ul {
        list-style: none;
        padding: 0;
        margin: 0.75rem 0 0.75rem 0;
    }

    .aim-scope-block ul li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.6rem;
        color: #212529;
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .aim-scope-block ul li::before {
        content: "\25B8";
        position: absolute;
        left: 0;
        color: #2c3e50;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .aim-scope {
            padding: 2rem 1rem;
        }

        .aim-scope h3 {
            font-size: 1.5rem;
        }

        .aim-scope-content {
            padding: 0;
        }

        .aim-scope-block {
            padding: 1rem;
        }
    }

    /* Journal Particulars Styling */
    .journal-particulars {
        margin: 3rem 0 0 0;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-top: 4px solid #2c3e50;
    }

    .journal-particulars h3 {
        font-size: 2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2rem;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .journal-particulars h3::before {
        content: "📋";
        margin-right: 0.75rem;
        font-size: 2rem;
    }

    .particulars-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.25rem;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .particular-item {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 1rem;
        padding: 1rem 1.25rem;
        background: white;
        border-radius: 8px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .particular-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .particular-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .particular-value {
        color: #212529;
        font-size: 0.9rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        line-height: 1.5;
    }

    @media (max-width: 768px) {
        .particulars-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 0;
        }
        
        .particular-item {
            grid-template-columns: 1fr;
            gap: 0.5rem;
            padding: 1rem;
        }
        
        .journal-particulars {
            padding: 2rem 1rem;
        }
        
        .journal-particulars h3 {
            font-size: 1.5rem;
        }
    }

            </style>

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
                        <img src="/images/continuum-logo-white.png" alt="Continuum Logo" class="hero-logo" width="200" height="200" decoding="async" fetchpriority="high" />
                    </div>

                    <h1 class="hero-title" data-aos="fade-up" data-aos-delay="600">
                        Welcome to <span class="hero-title">The Continuum <br>  <i>An Academic Journal</i></span>
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
                            <span>International reach</span>
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
    <?php if ($latestVolume && !empty($latestVolume['cover_image'])): ?>
        <img src="/uploads/volume_covers/<?= htmlspecialchars($latestVolume['cover_image']) ?>" alt="<?= htmlspecialchars($latestVolume['title']) ?>" class="cover-image" width="604" height="782" decoding="async" fetchpriority="high" style="object-fit: contain;">
    <?php else: ?>
        <img src="/images/cover.png" alt="Journal Cover" class="cover-image" width="604" height="782" decoding="async" fetchpriority="high" style="object-fit: contain;">
    <?php endif; ?>
                        <div class="preview-overlay">
                            <div class="preview-content">
                            <h3>Latest Volume</h3>
    <?php if ($latestVolume): ?>
        <p><?= htmlspecialchars($latestVolume['title']) ?></p>
        <a href="volume.php?id=<?= (int)$latestVolume['id'] ?>" class="preview-btn">
            <i class="fas fa-eye"></i>
            Preview
        </a>
    <?php else: ?>
        <p>Exploring Contemporary Academic Research</p>
        <a href="volumes.php" class="preview-btn">
            <i class="fas fa-eye"></i>
            View Volumes
        </a>
    <?php endif; ?>

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
                    <h2 class="section-title">About The Journal</h2>
                    <p class="section-subtitle">Bridging disciplines, fostering innovation, and empowering emerging voices
                        in academia</p>
                </div>

                <div class="about-content">
                    <div class="about-text" data-aos="fade-right">
                        <div class="about-highlight">
                            <h3>We are an interdisciplinary academic journal</h3>
                            <p class="lead">The Journal is an annual peer-reviewed academic journal by PHI Learning Pvt.
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
                            <div class="stat-number" data-target="<?= $stats['volumes'] ?>">0</div>

                                <div class="stat-label">Volumes Published</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            <div class="stat-number" data-target="<?= $stats['articles'] ?>">0</div>

                                <div class="stat-label">Research Articles</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-number" data-target="<?= $stats['authors'] ?>">0</div>
                                <div class="stat-label">Contributing Authors</div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-globe-americas"></i>
                                </div>
                                <div class="stat-number" data-target="<?= $stats['countries'] ?>">0</div>
                                <div class="stat-label">Countries Represented</div>
                            </div>
                        </div>

                        <div class="about-image">
                            <img src="/images/typography.jpeg" alt="Academic Excellence" class="feature-image"
                                width="420" height="280" loading="lazy" decoding="async"
                                style="max-width: 420px; width: 100%; height: auto;">
                        </div>
                    </div>
                </div>

                <!-- Aim and Scope Section -->
                <div id="aim-scope" class="aim-scope" data-aos="fade-up">
                    <h3>Aim and Scope of The Journal</h3>
                    <div class="aim-scope-content">
                        <div class="aim-scope-block">
                            <h4>Aim</h4>
                            <p>The Continuum aims to promote rigorous interdisciplinary scholarship by providing a platform for innovative and critical research that crosses traditional disciplinary boundaries. Published annually by PHI Learning Private Limited, the journal seeks to encourage dialogue among scholars from diverse academic backgrounds while maintaining the highest standards of academic integrity through a double-blind peer-review process. It particularly supports emerging scholars, early-career researchers, and independent academics by offering an international forum for the dissemination of original research and fresh perspectives that contribute to contemporary intellectual and social debates.</p>
                        </div>
                        <div class="aim-scope-block">
                            <h4>Scope</h4>
                            <p>The journal publishes high-quality, peer-reviewed research articles that engage with multiple disciplines and methodological approaches. Its scope includes, but is not limited to:</p>
                            <ul>
                                <li>Interdisciplinary and cross-disciplinary research that bridges fields such as the humanities, social sciences, cultural studies, media studies, education, and related areas.</li>
                                <li>Theoretical, analytical, and empirical studies that explore emerging ideas, contemporary issues, and evolving academic debates.</li>
                                <li>Research that combines perspectives from different disciplines to generate new frameworks, insights, or applications.</li>
                                <li>Scholarly work by emerging researchers that contributes to global academic discourse and encourages intellectual collaboration across institutions and regions.</li>
                            </ul>
                            <p>Through its interdisciplinary orientation and commitment to academic excellence, The Continuum seeks to facilitate meaningful scholarly exchange and advance knowledge across diverse fields of study for academic institutions.</p>
                        </div>
                    </div>
                </div>

                <!-- Journal Particulars Section -->
                <div class="journal-particulars" data-aos="fade-up">
                    <h3>Journal Particulars</h3>
                    <div class="particulars-grid">
                        <div class="particular-item">
                            <span class="particular-label">Title</span>
    <span class="particular-value">
    The Continuum<br>
    <i>An Academic Journal</i>
</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Frequency</span>
                            <span class="particular-value">Annual</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">ISSN</span>
                            <span class="particular-value">XXXX-XXXX</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Publisher name</span>
                            <span class="particular-value">PHI Learning Private Limited</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Publisher address</span>
                            <span class="particular-value">Rimjhim House, 111, Patparganj Industrial Estate, Delhi 110092</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Starting Year</span>
                            <span class="particular-value">2026</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Subject</span>
                            <span class="particular-value">Multi-Disciplinary Subjects</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Language</span>
                            <span class="particular-value">English</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Publication Format</span>
                            <span class="particular-value">Online</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Email Id</span>
                            <span class="particular-value">thecontinuum@phindia.com</span>
                        </div>
                        <div class="particular-item">
                            <span class="particular-label">Mobile No.</span>
                            <span class="particular-value">011-43031100</span>
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
                    <p class="section-subtitle">Discover the features that set our Journal apart in academic publishing
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
                        <h3>International Reach</h3>
                        <p>Connecting researchers worldwide through our international reach and online
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
                        <p>Annual publication schedule ensures timely dissemination of research while maintaining
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
                    <h2 class="section-title">Explore Our Volumes</h2>
                    <p class="section-subtitle">Discover cutting-edge research across diverse themes and disciplines</p>
                </div>

                <div class="issues-grid">
                    <div class="issue-card featured" data-aos="fade-up" data-aos-delay="100">
                        <div class="issue-badge">Latest Volume</div>
                        <div class="issue-cover">
    <?php if ($latestVolume && !empty($latestVolume['cover_image'])): ?>
        <div class="cover-wrap">
        <img 
            src="/uploads/volume_covers/<?= htmlspecialchars($latestVolume['cover_image']) ?>" 
            alt="<?= htmlspecialchars($latestVolume['title']) ?>"
            loading="lazy"
            decoding="async"
        >
    </div>

    <?php else: ?>
        <img src="/images/cover.png" alt="Default Cover" width="604" height="782" loading="lazy" decoding="async" style="width: 100%; height: auto; object-fit: contain; max-height: 500px;">
    <?php endif; ?>
    </div>

                        <div class="issue-content">
                        <?php if ($latestVolume): ?>
        <h3><?= htmlspecialchars($latestVolume['title']) ?></h3>
        <p class="issue-date">
            Volume <?= $latestVolume['volume_no'] ?> (<?= $latestVolume['year'] ?>)
        </p>
        <div class="issue-actions">
            <a href="volume.php?id=<?= (int)$latestVolume['id'] ?>" class="btn btn-primary">
                <i class="fas fa-eye"></i>
                Preview
            </a>
            <a href="volumes.php" class="btn btn-outline">
                <i class="fas fa-archive"></i>
                Full Access
            </a>
        </div>
    <?php endif; ?>

                        </div>
                    </div>

                    <div class="access-options" data-aos="fade-up" data-aos-delay="300">
                        <h3>Access Options</h3>

                        <div class="access-card">
                            <div class="access-icon">
                                <i class="fas fa-unlock"></i>
                            </div>
                            <h4>Free Preview</h4>
                            <p>Browse abstracts, introductions, and selected content from each volume.</p>
                            <a href="volumes.php" class="access-link">
                                Start Reading <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="access-card">
                            <div class="access-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <h4>Full Access</h4>
                            <p>Complete access to all published articles, research data, and supplementary materials.</p>
                            <a href="volumes.php" class="access-link">
                                Get Access <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="access-card">
                            <div class="access-icon">
                                <i class="fas fa-archive"></i>
                            </div>
                            <h4>Archive</h4>
                            <p>Explore our complete collection of past volumes and research articles.</p>
                            <a href="volumes.php" class="access-link">
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
        <footer class="footer"> <div class="container"> <div class="footer-content"> <div class="footer-brand"> <div class="footer-logos"> <img src="/images/PHI_White.png" alt="PHI Logo" class="footer-logo" width="120" height="40" loading="lazy" decoding="async"> <img src="/images/continuum-logo-white.png" alt="The Continuum Logo" class="footer-logo" width="120" height="40" loading="lazy" decoding="async"> </div> <h3>
    The Continuum<br>
    <i>An Academic Journal</i>
</h3> <p>An interdisciplinary academic journal fostering rigorous research and enabling dialogue between emerging scholars across all subjects.</p> <div class="social-links"> <a href="https://www.facebook.com/PHILEARNING" target="_blank"> <i class="fab fa-facebook-f"></i> </a> <a href="https://www.instagram.com/philearningdelhi/" target="_blank"> <i class="fab fa-instagram"></i> </a> <a href="https://www.linkedin.com/company/86816950/" target="_blank"> <i class="fab fa-linkedin-in"></i> </a> <a href="https://www.youtube.com/@philearning" target="_blank"> <i class="fab fa-youtube"></i> </a> </div> </div> <div class="footer-links"> <div class="footer-column"> <h4>Journal</h4> <ul> <li><a href="#about">About Us</a></li> <li><a href="/reviewers.html">Our Reviewers</a></li> <li><a href="/volumes.php">All Volumes</a></li> </ul> </div> <div class="footer-column"> <h4>For Authors</h4> <ul> <li><a href="#contact">Submit Article</a></li> <li><a href="/Author_Guidelines.pdf"  target="_blank">Author Guidelines</a></li> </ul> </div> <div class="footer-column"> <h4>Access</h4> <ul> <li><a href="/volumes.php">Free Preview</a></li> <li><a href="/volumes.php">Full Access</a></li> <li><a href="/volumes.php">Subscription</a></li> <!-- <li><a href="/volumes.php">Open Access</a></li> --> </ul> </div> </div> </div> <div class="footer-bottom"> <div class="footer-copyright"> <p>&copy; 2025 PHI Learning Pvt Ltd. All rights reserved.</p> </div> <div class="footer-legal"> <a href="/privacy.php">Privacy Policy</a> <a href="/terms.php">Terms of Service</a> <a href="/refunds.php">Refund Policy</a> </div> </div> </div> </footer>

        <div id="submissionModal" class="submission-modal">
            <div class="submission-modal-content">
                <button type="button" class="submission-modal-close">&times;</button>
                <h2>Thank you!</h2>
                <p id="submissionModalMessage"></p>
            </div>
        </div>

        <!-- Scripts - deferred for performance -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" defer></script>
        <script src="intro-script.js" defer></script>
        
        <!-- Fallback for AOS initialization -->
        <script>
            // Initialize AOS when scripts load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof AOS !== 'undefined') AOS.init();
                });
            } else {
                if (typeof AOS !== 'undefined') AOS.init();
            }
        </script>
    </body>

    </html>