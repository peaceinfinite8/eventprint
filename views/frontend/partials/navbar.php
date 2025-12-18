<?php
/**
 * Frontend Navbar Component
 * Displays site navigation with active menu highlighting
 */

// Ensure settings is defined
$settings = $settings ?? [];

// Get settings for logo and site name (will be passed from controller)
$siteName = $settings['site_name'] ?? 'EventPrint';
$logo = $settings['logo'] ?? null;
?>

<nav class="navbar">
    <div class="container navbar__container">
        <div class="navbar__brand">
            <a href="<?= baseUrl('/') ?>" class="brand-link">
                <?php if ($logo): ?>
                    <img src="<?= uploadUrl($logo) ?>" alt="<?= e($siteName) ?>" class="brand-logo">
                <?php else: ?>
                    <span class="brand-text"><?= e($siteName) ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="navbar__menu">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?= baseUrl('/') ?>"
                        class="nav-link <?= isActive('/') && currentPath() === '/' ? 'active' : '' ?>">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= baseUrl('/products') ?>" class="nav-link <?= isActive('/products') ? 'active' : '' ?>">
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= baseUrl('/our-home') ?>" class="nav-link <?= isActive('/our-home') ? 'active' : '' ?>">
                        Our Home
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= baseUrl('/blog') ?>" class="nav-link <?= isActive('/blog') ? 'active' : '' ?>">
                        Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= baseUrl('/contact') ?>" class="nav-link <?= isActive('/contact') ? 'active' : '' ?>">
                        Contact
                    </a>
                </li>
            </ul>
        </div>

        <!-- Mobile Menu Toggle (optional) -->
        <button class="navbar__toggle" id="navbarToggle" aria-label="Toggle navigation">
            <span class="toggle-icon"></span>
            <span class="toggle-icon"></span>
            <span class="toggle-icon"></span>
        </button>
    </div>
</nav>