<?php
/**
 * Frontend Navbar - Enhanced with Mobile Menu
 */

// Settings
$siteName = $settings['site_name'] ?? 'EventPrint';
$logo = $settings['logo'] ?? null;
$phone = $settings['phone'] ?? '0812-9898-4414';
$whatsapp = $settings['whatsapp'] ?? '';
$operatingHours = $settings['operating_hours'] ?? 'Senin–Jumat 09.00–18.00 | Sabtu 08.00–18.00 | Minggu & Tanggal Merah Libur';
$tagline = $settings['site_tagline'] ?? '';

// WhatsApp URL with wa.me prefix
$waNumber = normalizeWhatsApp($whatsapp ?: $phone);
$waUrl = 'https://wa.me/' . $waNumber;
$waDisplay = $phone ?: $whatsapp;
?>

<div id="navbar">
    <!-- Topbar -->
    <div class="topbar--fullbleed">
        <div class="topbar__inner">
            <span class="topbar-text">
                <?php if (!empty($tagline)): ?>
                    <span class="fw-bold"><?= e($tagline) ?></span> &nbsp;|&nbsp;
                <?php endif; ?>
                <?= e($operatingHours) ?>
            </span>

            <?php if (!empty($whatsapp)): ?>
                <a href="<?= e($waUrl) ?>" target="_blank" class="topbar-cta" rel="noopener" style="text-decoration: none;">
                    <i class="fab fa-whatsapp" style="font-size: 1.1em;"></i> <?= e($whatsapp) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar" style="padding-top: 15px; padding-bottom: 15px;">
        <div class="container"
            style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            <!-- Brand -->
            <a href="<?= baseUrl('/') ?>" class="navbar-brand" style="display: flex; align-items: center;">
                <?php if (!empty($logo)): ?>
                    <img src="<?= uploadUrl($logo) ?>?v=<?= time() ?>" alt="<?= e($siteName) ?>"
                        style="max-height: 60px; object-fit: contain;">
                <?php else: ?>
                    <?= e($siteName) ?>
                <?php endif; ?>
            </a>

            <!-- Search Container (Responsive) -->
            <div id="navSearchContainer" class="nav-search-container">
                <div class="nav-search">
                    <form action="<?= baseUrl('/products') ?>" method="GET" style="display: contents;">
                        <input type="text" id="globalSearchInput" name="search" class="search-input"
                            placeholder="Cari produk..." value="<?= e($_GET['search'] ?? '') ?>">
                        <button type="submit" class="search-icon"
                            style="background: none; border: none; cursor: pointer;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                            </svg>
                        </button>
                    </form>
                    <div id="searchDropdown" class="search-dropdown hidden"></div>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <ul class="navbar-nav desktop-nav">
                <li>
                    <a href="<?= baseUrl('/') ?>"
                        class="nav-link <?= (currentPath() === '/' || currentPath() === '/eventprint/' || currentPath() === '/eventprint') ? 'active' : '' ?>">
                        Home
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/products') ?>" class="nav-link <?= isActive('/products') ? 'active' : '' ?>">
                        All Product
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/our-home') ?>" class="nav-link <?= isActive('/our-home') ? 'active' : '' ?>">
                        Our Home
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/blog') ?>" class="nav-link <?= isActive('/blog') ? 'active' : '' ?>">
                        Artikel
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/contact') ?>" class="nav-link <?= isActive('/contact') ? 'active' : '' ?>">
                        Contact
                    </a>
                </li>
            </ul>

            <!-- Mobile Menu Button REMOVED - replaced by floating hamburger -->

        </div>
    </nav>

    <!-- Mobile Menu Modal (only visible on mobile) -->
    <nav class="mobile-menu-modal" id="mobileMenuModal">
        <div class="menu-modal-header">
            <h3 class="menu-modal-title">Menu</h3>
            <button class="menu-close-btn" id="menuCloseBtn">×</button>
        </div>
        <div class="menu-modal-content">
            <ul>
                <li>
                    <a href="<?= baseUrl('/') ?>"
                        class="nav-link <?= (currentPath() === '/' || currentPath() === '/eventprint/' || currentPath() === '/eventprint') ? 'active' : '' ?>">
                        Home
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/products') ?>" class="nav-link <?= isActive('/products') ? 'active' : '' ?>">
                        All Product
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/our-home') ?>" class="nav-link <?= isActive('/our-home') ? 'active' : '' ?>">
                        Our Home
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/blog') ?>" class="nav-link <?= isActive('/blog') ? 'active' : '' ?>">
                        Artikel
                    </a>
                </li>
                <li>
                    <a href="<?= baseUrl('/contact') ?>" class="nav-link <?= isActive('/contact') ? 'active' : '' ?>">
                        Contact
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>
</div>