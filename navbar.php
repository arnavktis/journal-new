<?php
/**
 * Generic Navbar Component
 * This file contains the common navigation bar used across all pages
 */
?>
<!-- Navigation -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <img src="images/PHI_White.png" alt="PHI Logo" class="nav-logo-phi">
            <img src="images/continuum-logo-white.png" alt="The Continuum Logo" class="nav-logo-continuum">
        </div>
        <div class="nav-menu">
            <a href="index.php" class="nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">Home</a>
            <a href="about.php" class="nav-link <?php echo ($currentPage == 'about') ? 'active' : ''; ?>">About</a>
            <a href="index.php#mission" class="nav-link <?php echo ($currentPage == 'mission') ? 'active' : ''; ?>">Mission</a>
            <a href="reviewers.php" class="nav-link <?php echo ($currentPage == 'reviewers') ? 'active' : ''; ?>">Editorial Board</a>
            <a href="index.php#issues" class="nav-link <?php echo ($currentPage == 'issues') ? 'active' : ''; ?>">Issues</a>
            
            <!-- Authors Dropdown (for home page) -->
            <?php if ($currentPage == 'home'): ?>
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" id="authorsDropdownBtn">For Authors <i class="fas fa-chevron-down"></i></button>
                <div class="nav-dropdown-menu" id="authorsDropdownMenu">
                    <a href="submit.php" class="nav-dropdown-item">Submit Article</a>
                    <a href="#" class="nav-dropdown-item">Author Guidelines</a>
                </div>
            </div>
            <?php else: ?>
            <a href="submit.php" class="nav-link <?php echo ($currentPage == 'submit') ? 'active' : ''; ?>">Submit</a>
            <?php endif; ?>
            
            <a href="index.php#contact" class="nav-link <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>">Contact</a>
            
            <!-- Issues dropdown (for certain pages) -->
            <?php if (in_array($currentPage, ['preview', 'full-access'])): ?>
            <div class="nav-dropdown">
                <a href="all-issues.php" class="nav-link">Issues ▾</a>
                <div class="dropdown-content">
                    <a href="all-issues.php">All Issues</a>
                    <a href="preview.php">Preview</a>
                    <a href="full-access.php">Full Access</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- CTA Button (for submit page) -->
            <?php if ($currentPage == 'submit'): ?>
            <div class="nav-cta">
                <a href="index.php" class="btn btn-primary">Enter Journal</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>