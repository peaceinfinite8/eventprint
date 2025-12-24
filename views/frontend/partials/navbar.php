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
            <?php if ($waUrl): ?>
                <a href="<?= e($waUrl) ?>" target="_blank" class="topbar-cta" rel="noopener">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                    </svg>
                    <?= e($waDisplay) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container"
            style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            <!-- Brand -->
            <a href="<?= baseUrl('/') ?>" class="navbar-brand">
                <?php
                // Logo with fallback
                $logoPath = $logo ?? '';
                $logoFullPath = !empty($logoPath) ? realpath(__DIR__ . '/../../../public/' . ltrim($logoPath, '/')) : '';
                $logoExists = !empty($logoFullPath) && file_exists($logoFullPath);

                if ($logoExists):
                    ?>
                    <img src="<?= uploadUrl($logoPath) ?>" alt="<?= e($siteName) ?>" style="max-height: 40px;">
                <?php elseif (!empty($logoPath)): ?>
                    <img src="<?= assetUrl('frontend/images/placeholder-logo.png') ?>" alt="<?= e($siteName) ?>"
                        style="max-height: 40px;">
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
                        class="nav-link <?= (currentPath() === '/' || currentPath() === '/eventprint/public/' || currentPath() === '/eventprint/public') ? 'active' : '' ?>">
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
                        class="nav-link <?= (currentPath() === '/' || currentPath() === '/eventprint/public/' || currentPath() === '/eventprint/public') ? 'active' : '' ?>">
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