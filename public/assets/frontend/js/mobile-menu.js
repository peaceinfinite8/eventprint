/**
 * Enhanced Mobile Menu Handler
 * Fixes hamburger menu toggle functionality
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('Mobile menu init started');

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navbarNav = document.querySelector('.navbar-nav');
    const menuIcon = mobileMenuBtn?.querySelector('.menu-icon');
    const closeIcon = mobileMenuBtn?.querySelector('.close-icon');

    console.log('Menu elements:', {
        btn: !!mobileMenuBtn,
        nav: !!navbarNav,
        menuIcon: !!menuIcon,
        closeIcon: !!closeIcon
    });

    if (!mobileMenuBtn || !navbarNav) {
        console.warn('Mobile menu elements not found');
        return;
    }

    // Toggle menu
    mobileMenuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        console.log('Menu button clicked');

        const isActive = navbarNav.classList.contains('active');

        if (isActive) {
            navbarNav.classList.remove('active');
            if (menuIcon) menuIcon.style.display = 'block';
            if (closeIcon) closeIcon.style.display = 'none';
            console.log('Menu closed');
        } else {
            navbarNav.classList.add('active');
            if (menuIcon) menuIcon.style.display = 'none';
            if (closeIcon) closeIcon.style.display = 'block';
            console.log('Menu opened');
        }
    });

    // Close menu when clicking a link
    const navLinks = navbarNav.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            console.log('Nav link clicked, closing menu');
            navbarNav.classList.remove('active');
            if (menuIcon) menuIcon.style.display = 'block';
            if (closeIcon) closeIcon.style.display = 'none';
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (event) {
        if (!navbarNav.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            if (navbarNav.classList.contains('active')) {
                console.log('Clicked outside, closing menu');
                navbarNav.classList.remove('active');
                if (menuIcon) menuIcon.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            }
        }
    });

    console.log('Mobile menu init completed');
});
