// The Continuum Journal - Intro Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // Initialize all interactive features
    initSmoothScrolling();
    initNavbarEffects();
    initMobileMenu();
    initStatsCounter();
    initContactForm(); // wired to process_submission.php
    initSubmissionModal(); // modal for submission success
    initHeroAnimations();
    initScrollProgress();
});

// Smooth scrolling for navigation links
function initSmoothScrolling() {
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            if (targetSection) {
                const offsetTop = targetSection.offsetTop - 80;
                window.scrollTo({ top: offsetTop, behavior: 'smooth' });
                updateActiveNavLink(targetId);
            }
        });
    });
}

// Update active navigation link
function updateActiveNavLink(targetId) {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === targetId) {
            link.classList.add('active');
        }
    });
}

// Navbar scroll effects
function initNavbarEffects() {
    const navbar = document.querySelector('.navbar');
    let lastScrollY = window.scrollY;
    window.addEventListener('scroll', () => {
        const currentScrollY = window.scrollY;
        if (currentScrollY > 100) {
            navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.2)';
        } else {
            navbar.style.boxShadow = 'none';
        }
        if (currentScrollY > lastScrollY && currentScrollY > 200) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        lastScrollY = currentScrollY;
        updateActiveSection();
    });
}

// Update active section based on scroll position
function updateActiveSection() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 150;
        const sectionHeight = section.clientHeight;
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
}

// Mobile menu functionality
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.className = 'fas fa-times';
                document.body.style.overflow = 'hidden';
            } else {
                icon.className = 'fas fa-bars';
                document.body.style.overflow = '';
            }
        });
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                mobileToggle.querySelector('i').className = 'fas fa-bars';
                document.body.style.overflow = '';
            });
        });
        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                mobileToggle.querySelector('i').className = 'fas fa-bars';
                document.body.style.overflow = '';
            }
        });
    }
}

// Statistics counter animation
function initStatsCounter() {
    const statNumbers = document.querySelectorAll('.stat-number[data-target]');
    const animateCounter = (element, target) => {
        const duration = 2000;
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const target = parseInt(element.getAttribute('data-target'));
                animateCounter(element, target);
                observer.unobserve(element);
            }
        });
    }, { threshold: 0.5 });
    statNumbers.forEach(stat => observer.observe(stat));
}

// ===== Submission modal =====
let submissionModal, submissionModalMessage;

function initSubmissionModal() {
    submissionModal = document.getElementById('submissionModal');
    submissionModalMessage = document.getElementById('submissionModalMessage');
    if (!submissionModal || !submissionModalMessage) return;

    const closeBtn = submissionModal.querySelector('.submission-modal-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeSubmissionModal);
    }

    submissionModal.addEventListener('click', (e) => {
        if (e.target === submissionModal) {
            closeSubmissionModal();
        }
    });
}

function openSubmissionModal(fullName) {
    if (!submissionModal || !submissionModalMessage) return;
    const safeName = (fullName || 'Author').trim();

    submissionModalMessage.innerHTML =
        `Thank you <strong>${safeName}</strong>, your submission has been received.<br><br>` +
        `You will receive an email with the copyright transfer form. ` +
        `Please download it, fill it, and send it to <strong>thecontinuum@phindia.com</strong>.`;

    submissionModal.classList.add('open');
}

function closeSubmissionModal() {
    if (submissionModal) {
        submissionModal.classList.remove('open');
    }
}

// Contact form handling (POST → process_submission.php)
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    initFileInputs();

    contactForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const fd = new FormData(this);
        const data = Object.fromEntries(fd.entries());
        if (!validateForm(data)) return;

        const manuscriptInput = document.getElementById('manuscript');
        const fileCheck = validateFiles(manuscriptInput);
        if (!fileCheck.valid) { showNotification(fileCheck.message, 'error'); return; }

        const btn = this.querySelector('button[type="submit"]');
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        btn.disabled = true;

        try {
            const res = await fetch('process_submission.php', { method: 'POST', body: fd });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.ok) throw new Error(json.error || 'Submission failed');

            const fullName = (fd.get('fullname') || '').toString().trim();

            // Full-screen modal
            openSubmissionModal(fullName);

            // Optional toast with ref
            showNotification('Submitted successfully. Ref #' + json.id, 'success');

            this.reset();
            const msList = document.getElementById('manuscript-list');
            if (msList) msList.textContent = '';
        } catch (err) {
            showNotification(String(err.message || err), 'error');
        } finally {
            btn.innerHTML = old;
            btn.disabled = false;
        }
    });
}

// Initialize file inputs UI (manuscript only)
function initFileInputs() {
    const manuscriptInput = document.getElementById('manuscript');
    if (manuscriptInput) {
        manuscriptInput.addEventListener('change', function () {
            const list = document.getElementById('manuscript-list');
            renderFileList(this.files, list);
        });
    }
}

// Render a short file list (name + size)
function renderFileList(fileList, container) {
    if (!container) return;
    if (!fileList || fileList.length === 0) {
        container.textContent = '';
        return;
    }
    const items = [];
    Array.from(fileList).forEach(file => {
        items.push(`${file.name} (${formatFileSize(file.size)})`);
    });
    container.textContent = items.join(', ');
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
}

// Validate manuscript only
function validateFiles(manuscriptInput) {
    const allowed = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    const maxMB = 15;
    if (!manuscriptInput || !manuscriptInput.files || manuscriptInput.files.length === 0) {
        return { valid: false, message: 'Attach your manuscript (PDF/Word).' };
    }
    const f = manuscriptInput.files[0];
    if (!allowed.includes(f.type)) return { valid: false, message: 'Only PDF, DOC, DOCX allowed.' };
    if (f.size > maxMB * 1024 * 1024) return { valid: false, message: `Max ${maxMB} MB.` };
    return { valid: true };
}

// Form validation (match backend names)
function validateForm(d) {
    const required = ['fullname', 'email', 'subject', 'article_synopsis'];
    const missing = required.filter(k => !d[k] || String(d[k]).trim() === '');
    if (missing.length) { showNotification('Fill required: ' + missing.join(', '), 'error'); return false; }
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(d.email || '');
    if (!emailOk) { showNotification('Enter a valid email.', 'error'); return false; }
    return true;
}

// Hero animations and interactions
function initHeroAnimations() {
    const heroPattern = document.querySelector('.hero-pattern');
    const journalPreview = document.querySelector('.journal-preview');
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        if (heroPattern) {
            heroPattern.style.transform = `translateY(${scrolled * 0.3}px)`;
        }
    });
    document.addEventListener('mousemove', (e) => {
        if (heroPattern) {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            heroPattern.style.backgroundPosition =
                `${mouseX * 20}px ${mouseY * 20}px, 
                 ${mouseX * -15}px ${mouseY * -15}px, 
                 ${mouseX * 10}px ${mouseY * 10}px`;
        }
    });
    if (journalPreview) {
        journalPreview.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02) rotateY(5deg)';
        });
        journalPreview.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotateY(0deg)';
        });
    }
}

// Scroll progress indicator
function initScrollProgress() {
    const progressBar = document.createElement('div');
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(135deg, #002147 0%, #D4AF37 50%, #002147 100%);
        z-index: 10001;
        transition: width 0.1s ease;
    `;
    document.body.appendChild(progressBar);
    window.addEventListener('scroll', () => {
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        progressBar.style.width = scrollPercent + '%';
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 400px;
        display: flex;
        align-items: center;
        gap: 1rem;
    `;
    const content = notification.querySelector('.notification-content');
    content.style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
    `;
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: background 0.2s ease;
    `;
    closeBtn.addEventListener('mouseenter', () => { closeBtn.style.background = 'rgba(255, 255, 255, 0.2)'; });
    closeBtn.addEventListener('mouseleave', () => { closeBtn.style.background = 'none'; });
    document.body.appendChild(notification);
    setTimeout(() => { notification.style.transform = 'translateX(0)'; }, 10);
    closeBtn.addEventListener('click', () => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    });
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}
function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}
function getNotificationColor(type) {
    switch (type) {
        case 'success': return '#10b981';
        case 'error': return '#ef4444';
        case 'warning': return '#f59e0b';
        default: return '#3b82f6';
    }
}

// Feature card interactions
document.addEventListener('DOMContentLoaded', function() {
    const featureCards = document.querySelectorAll('.feature-card, .mission-card, .access-card, .contact-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Timeline animations
function initTimelineAnimations() {
    const timelineItems = document.querySelectorAll('.timeline-item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.3 });
    timelineItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(50px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
    });
}
initTimelineAnimations();

// Lazy loading for images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    images.forEach(img => imageObserver.observe(img));
}
initLazyLoading();

// Button ripple effect
function addRippleEffect() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            setTimeout(() => { ripple.remove(); }, 600);
        });
    });
}
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to { transform: scale(4); opacity: 0; }
    }
    @media (max-width: 768px) {
        .nav-menu.active {
            position: fixed; top: 70px; left: 0; right: 0;
            background: rgba(0, 33, 71, 0.98);
            backdrop-filter: blur(10px);
            flex-direction: column; padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); z-index: 999;
        }
        .nav-menu.active .nav-link {
            padding: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%; text-align: center;
        }
        .nav-menu.active .nav-cta { margin: 1rem 0 0 0; }
    }
`;
document.head.appendChild(style);
addRippleEffect();

// Smooth entrance animations for hero content
window.addEventListener('load', () => {
    const heroElements = [
        '.hero-badge','.hero-logo','.hero-title',
        '.hero-subtitle','.hero-features','.hero-actions'
    ];
    heroElements.forEach((selector, index) => {
        const element = document.querySelector(selector);
        if (element) {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            setTimeout(() => {
                element.style.transition = 'all 0.8s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 300 + (index * 200));
        }
    });
});
