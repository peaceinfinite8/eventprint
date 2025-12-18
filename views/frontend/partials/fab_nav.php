<?php
/**
 * FAB (Floating Action Button) Navigation for Mobile
 * Provides quick access to key pages on mobile devices
 */

$waUrl = normalizeWhatsApp($settings['whatsapp'] ?? '');
?>

<!-- FAB Navigation (Mobile) -->
<div class="fabNav" id="fabNav">
    <button class="fabNav__toggle" id="fabNavToggle" aria-label="Quick Navigation">
        <i class="fas fa-bars"></i>
    </button>

    <div class="fabNav__menu" id="fabNavMenu" style="display: none;">
        <a href="<?= baseUrl('/') ?>" class="fabNav__item">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?= baseUrl('/products') ?>" class="fabNav__item">
            <i class="fas fa-shopping-bag"></i>
            <span>Products</span>
        </a>
        <?php if ($waUrl): ?>
            <a href="<?= e($waUrl) ?>" target="_blank" class="fabNav__item fabNav__item--wa" rel="noopener">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp</span>
            </a>
        <?php endif; ?>
        <a href="<?= baseUrl('/contact') ?>" class="fabNav__item">
            <i class="fas fa-envelope"></i>
            <span>Contact</span>
        </a>
    </div>
</div>

<!-- FAB Navigation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fabToggle = document.getElementById('fabNavToggle');
        const fabMenu = document.getElementById('fabNavMenu');

        if (fabToggle && fabMenu) {
            fabToggle.addEventListener('click', function (e) {
                e.preventDefault();
                const isVisible = fabMenu.style.display !== 'none';
                fabMenu.style.display = isVisible ? 'none' : 'flex';
                fabToggle.classList.toggle('active');
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                const fabNav = document.getElementById('fabNav');
                if (fabNav && !fabNav.contains(e.target)) {
                    fabMenu.style.display = 'none';
                    fabToggle.classList.remove('active');
                }
            });
        }
    });
</script>