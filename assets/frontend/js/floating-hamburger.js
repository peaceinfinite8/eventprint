/**
 * Floating Hamburger with Modal Menu
 * Simplified version using dedicated mobile menu element
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('Floating hamburger modal init started');

    // Only create on mobile
    if (window.innerWidth <= 767) {
        createFloatingHamburger();
    }

    // Handle resize
    let resizeTimeout;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            const existingFloat = document.querySelector('.hamburger-float');

            if (window.innerWidth <= 767 && !existingFloat) {
                createFloatingHamburger();
            } else if (window.innerWidth > 767) {
                if (existingFloat) existingFloat.remove();
                closeMenu();
            }
        }, 250);
    });
});

function createFloatingHamburger() {
    console.log('Creating floating hamburger button');

    // Remove existing if any
    const existing = document.querySelector('.hamburger-float');
    if (existing) existing.remove();

    // Create floating button
    const floatDiv = document.createElement('div');
    floatDiv.className = 'hamburger-float';
    floatDiv.innerHTML = `
    <button class="hamburger-float-btn" id="floatingHamburger" aria-label="Toggle menu">
      <svg width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
      </svg>
    </button>
  `;

    document.body.appendChild(floatDiv);

    // Setup click handlers
    const btn = document.getElementById('floatingHamburger');
    const modal = document.getElementById('mobileMenuModal');
    const overlay = document.getElementById('menuOverlay');
    const closeBtn = document.getElementById('menuCloseBtn');

    if (!btn || !modal || !overlay) {
        console.error('Required elements not found!', { btn: !!btn, modal: !!modal, overlay: !!overlay });
        return;
    }

    // Open menu on hamburger click
    btn.onclick = function (e) {
        e.preventDefault();
        e.stopPropagation();
        openMenu();
    };

    // Close menu on close button
    if (closeBtn) {
        closeBtn.onclick = function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeMenu();
        };
    }

    // Close menu on overlay click
    overlay.onclick = function () {
        closeMenu();
    };

    // Close on link click
    const links = modal.querySelectorAll('.nav-link');
    links.forEach(function (link) {
        link.addEventListener('click', function () {
            closeMenu();
        });
    });

    console.log('Floating hamburger setup completed');
}

function openMenu() {
    const modal = document.getElementById('mobileMenuModal');
    const overlay = document.getElementById('menuOverlay');

    if (modal) modal.classList.add('active');
    if (overlay) overlay.classList.add('active');

    // Prevent body scroll
    document.body.style.overflow = 'hidden';

    console.log('Menu opened (modal visible)');
}

function closeMenu() {
    const modal = document.getElementById('mobileMenuModal');
    const overlay = document.getElementById('menuOverlay');

    if (modal) modal.classList.remove('active');
    if (overlay) overlay.classList.remove('active');

    // Restore body scroll
    document.body.style.overflow = '';

    console.log('Menu closed');
}
