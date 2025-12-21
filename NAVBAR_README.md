# Navbar Implementation Guide

## Overview
The navbar has been consolidated into a single, reusable PHP component that is included across all pages. This eliminates code duplication and makes maintenance much easier.

## Files Modified
- **navbar.php** - Main navbar component file
- **index.php** - Converted from index.html
- **about.php** - Converted from about.html  
- **submit.php** - Converted from submit.html
- **reviewers.php** - Converted from reviewers.html
- **preview.php** - Converted from preview.html
- **full-access.php** - Converted from full-access.html
- **preview_new.php** - Converted from preview_new.html
- **full-access_new.php** - Converted from full-access_new.html

## How It Works

### 1. Page Variable
Each PHP file sets a `$currentPage` variable at the top:
```php
<?php 
$currentPage = 'home'; // or 'about', 'submit', 'reviewers', etc.
?>
```

### 2. Include Statement
The navbar is included using:
```php
<?php include 'navbar.php'; ?>
```

### 3. Active State Management
The navbar.php file uses the `$currentPage` variable to determine which navigation item should be highlighted:
```php
<a href="index.php" class="nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>">Home</a>
```

### 4. Conditional Content
The navbar displays different content based on the page:
- **Home page**: Shows "For Authors" dropdown
- **Submit page**: Shows CTA button "Enter Journal"
- **Preview/Full-access pages**: Shows "Issues" dropdown

## Page Types and Navigation
- **home**: Main landing page (index.php)
- **about**: About page (about.php)
- **submit**: Submission page (submit.php)
- **reviewers**: Editorial board page (reviewers.php)
- **preview**: Issue preview pages (preview.php, preview_new.php)
- **full-access**: Full access pages (full-access.php, full-access_new.php)

## Benefits
1. **Single source of truth** - All navbar changes happen in one file
2. **Easy maintenance** - Update navbar.php to change navigation across all pages
3. **Consistent behavior** - All pages use identical navbar structure
4. **Dynamic highlighting** - Active page automatically highlighted
5. **No code duplication** - Eliminates repeated navbar HTML

## Adding New Pages
To add a new page:
1. Create your PHP file with `$currentPage = 'pagename';` at the top
2. Include the navbar: `<?php include 'navbar.php'; ?>`
3. If needed, modify navbar.php to handle the new page type

## Customizing the Navbar
To modify the navbar:
1. Edit **navbar.php** only
2. Use the `$currentPage` variable for conditional logic
3. All pages will automatically reflect changes