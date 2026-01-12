<?php
/**
 * FAB (Floating Action Button) Navigation for Mobile
 * Provides quick access to key pages on mobile devices
 */

$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
// Helper simple untuk cek active state (karena helper global mungkin tidak cover semua case)
$isCurrent = function ($path) use ($currentUri) {
    if ($path === '/' && ($currentUri === '/' || $currentUri === '/eventprint/'))
        return true;
    if ($path !== '/' && strpos($currentUri, $path) !== false)
        return true;
    return false;
};
?>

<style>
    /* FAB Animation & Styling Overrides */
    .fab-menu-panel {
        /* Start hidden & small */
        transform: scale(0.8);
        opacity: 0;
        visibility: hidden;
        transform-origin: bottom right;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);

        position: fixed;
        bottom: 96px;
        right: 24px;
        left: auto;
        /* Clear left */
        width: 240px;
        /* Compact width */

        /* Floating Card Style */
        background: white;
        border-radius: 20px;
        padding-bottom: 20px;
        z-index: 2001;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .fab-nav-container.open .fab-menu-panel {
        transform: scale(1);
        opacity: 1;
        visibility: visible;
    }

    /* Main Floating Button Styling */
    .fab-main-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        left: auto;
        transform: none;
        /* Center Button */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background-color: #0d6efd;
        /* Bootstrap Primary */
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        z-index: 2002;
        /* Higher than panel */
        cursor: pointer;
        font-size: 24px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* When open, maintain center but rotate */
    .fab-nav-container.open .fab-main-btn {
        background-color: #dc3545;
        transform: rotate(90deg);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }



    .fab-overlay {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s;
    }

    .fab-nav-container.open .fab-overlay {
        opacity: 0;
        visibility: hidden;
        display: none;
    }

    /* Active State Styling */
    .fab-link.active {
        color: #0d6efd !important;
        /* Bootstrap Primary Blue */
        background-color: rgba(13, 110, 253, 0.05);
        /* Slight blue background */
        font-weight: 700;
        border-left: 4px solid #0d6efd;
        padding-left: 12px;
        /* Adjust padding for border */
    }

    /* Additional Styling for Icons */
    .fab-link i {
        width: 24px;
        text-align: center;
        transition: transform 0.2s;
    }

    .fab-link:hover i {
        transform: scale(1.1);
    }

    /* Smooth Icon Transition for Button */
    .fab-main-btn i {
        transition: transform 0.3s ease, opacity 0.2s;
    }
</style>

<!-- FAB Navigation (Mobile) -->
<div class="fab-nav-container" id="fabNavContainer">
    <!-- Overlay -->
    <div class="fab-overlay" id="fabOverlay"></div>

    <!-- Main Toggle Button -->
    <button class="fab-main-btn" id="fabNavToggle" aria-label="Toggle Quick Navigation">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Slide-up Menu Panel -->
    <div class="fab-menu-panel">
        <div class="fab-menu-header">
            <h5 class="mb-0 fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i>Quick Menu</h5>
            <button class="fab-close" id="fabNavClose" aria-label="Close Menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="fab-menu-list">
            <li>
                <a href="<?= baseUrl('/') ?>"
                    class="fab-link <?= $isCurrent('/') && !$isCurrent('/products') && !$isCurrent('/our-home') && !$isCurrent('/blog') && !$isCurrent('/contact') ? 'active' : '' ?>">
                    <i class="fas fa-home me-2"></i> Home
                </a>
            </li>
            <li>
                <a href="<?= baseUrl('/products') ?>" class="fab-link <?= $isCurrent('/products') ? 'active' : '' ?>">
                    <i class="fas fa-box-open me-2"></i> All Product
                </a>
            </li>
            <li>
                <a href="<?= baseUrl('/our-home') ?>" class="fab-link <?= $isCurrent('/our-home') ? 'active' : '' ?>">
                    <i class="fas fa-store me-2"></i> Our Home
                </a>
            </li>
            <li>
                <a href="<?= baseUrl('/blog') ?>" class="fab-link <?= $isCurrent('/blog') ? 'active' : '' ?>">
                    <i class="fas fa-newspaper me-2"></i> Artikel
                </a>
            </li>

        </ul>
    </div>
</div>

<!-- FAB Navigation Script -->
<script>
    (function () {
        // GUARD: Prevent duplicate initialization
        if (window.fabNavInitialized) return;
        window.fabNavInitialized = true;

        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('fabNavContainer');
            const toggleBtn = document.getElementById('fabNavToggle');
            const closeBtn = document.getElementById('fabNavClose');
            const overlay = document.getElementById('fabOverlay');
            const icon = toggleBtn.querySelector('i');

            function toggleMenu() {
                const isOpen = container.classList.contains('open');

                if (isOpen) {
                    container.classList.remove('open');
                    // Icon logic handled by CSS rotation, but we swap class for safety
                    setTimeout(() => {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }, 200);
                    document.body.style.overflow = '';
                } else {
                    container.classList.add('open');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                    document.body.style.overflow = 'hidden';
                }
            }

            if (toggleBtn) toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleMenu();
            });

            if (closeBtn) closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleMenu();
            });

            if (overlay) overlay.addEventListener('click', function (e) {
                e.preventDefault();
                toggleMenu();
            });
        });
    })();
</script>