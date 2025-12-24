<?php
/**
 * FAB (Floating Action Button) Navigation for Mobile
 * Provides quick access to key pages on mobile devices
 */

$waUrl = normalizeWhatsApp($settings['whatsapp'] ?? '');
?>

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
            <h5>Quick Menu</h5>
            <button class="fab-close" id="fabNavClose" aria-label="Close Menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <ul class="fab-menu-list">
            <li>
                <a href="<?= baseUrl('/') ?>"
                    class="fab-link <?= (currentPath() === '/' || currentPath() === '/eventprint/public/' || currentPath() === '/eventprint/public') ? 'active' : '' ?>">
                    <i class="fas fa-home me-2"></i> Home
                </a>
            </li>
            <li>
                <a href="<?= baseUrl('/products') ?>" class="fab-link <?= isActive('/products') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-bag me-2"></i> Products
                </a>
            </li>
            <?php if ($waUrl): ?>
                <li>
                    <a href="<?= e($waUrl) ?>" target="_blank" class="fab-link text-success" rel="noopener">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="<?= baseUrl('/contact') ?>" class="fab-link <?= isActive('/contact') ? 'active' : '' ?>">
                    <i class="fas fa-envelope me-2"></i> Contact
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
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                    document.body.style.overflow = ''; // Restore scrolling
                } else {
                    container.classList.add('open');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling when menu is open
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