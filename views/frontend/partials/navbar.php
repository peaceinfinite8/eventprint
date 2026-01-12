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
    <!-- Topbar with decorative waves -->
    <div class="topbar--fullbleed">
        <!-- Left Wave Decoration -->
        <div class="topbar-wave-left">
            <svg width="174" height="77" viewBox="0 0 174 77" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M172.965 35.037C169.709 23.3037 147.421 6.79012 136.684 0H0V77H147.878C169.196 77 177.034 49.7037 172.965 35.037Z"
                    fill="#3DDFFF" />
            </svg>
        </div>

        <!-- Center Text -->
        <div class="topbar__inner">
            <span class="topbar-text">
                <?php if (!empty($tagline)): ?>
                    <span class="fw-bold"><?= e($tagline) ?></span> &nbsp;|&nbsp;
                <?php endif; ?>
                <?= e($operatingHours) ?>
            </span>
        </div>

        <!-- Right Wave Decoration -->
        <div class="topbar-wave-right">
            <svg width="174" height="77" viewBox="0 0 174 77" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M1.03529 41.963C4.2908 53.6963 26.5787 70.2099 37.3157 77L174 77L174 0L26.1224 1.29279e-05C4.80428 1.47916e-05 -3.03409 27.2963 1.03529 41.963Z"
                    fill="#3DDFFF" />
            </svg>
        </div>
    </div>

    <!-- MAIN HEADER - Unified Container -->
    <div class="header-main">
        <div class="container">
            <!-- ROW 1: Logo + Tagline | Search Bar (stretch) | Social Icons -->
            <div class="header-row-top">
                <!-- Logo - Far Left -->
                <a href="<?= baseUrl('/') ?>" class="header-brand">
                    <?php if (!empty($logo)): ?>
                        <img src="<?= uploadUrl($logo) ?>?v=<?= time() ?>" alt="<?= e($siteName) ?>">
                    <?php else: ?>
                        <span><?= e($siteName) ?></span>
                    <?php endif; ?>
                </a>

                <!-- Tagline - Next to Logo -->
                <span class="header-tagline"><?= e($tagline) ?></span>

                <!-- Global Search - Stretches Between -->
                <div id="navSearchContainer" class="header-search-stretch">
                    <form action="<?= baseUrl('/products') ?>" method="GET">
                        <div class="search-input-wrapper">
                            <input type="text" id="globalSearchInput" class="search-input" name="search"
                                placeholder="Search Product" value="<?= e($_GET['search'] ?? '') ?>" autocomplete="off">
                            <button type="submit" class="header-search-icon"
                                style="background:none;border:none;padding:0;cursor:pointer;">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                </svg>
                            </button>
                            <div id="searchDropdown" class="search-dropdown hidden"></div>
                        </div>
                    </form>
                </div>

                <!-- Social Icons - Far Right -->
                <div class="header-social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>

            <!-- ROW 2: Navbar - White BG, Black Text -->
            <nav class="header-navbar-inner">
                <ul class="navbar-nav-main">
                    <li>
                        <a href="<?= baseUrl('/') ?>"
                            class="nav-link-main <?= (currentPath() === '/' || currentPath() === '/eventprint/public/' || currentPath() === '/eventprint/public') ? 'active' : '' ?>">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= baseUrl('/products') ?>"
                            class="nav-link-main <?= isActive('/products') ? 'active' : '' ?>">
                            All Product
                        </a>
                    </li>
                    <li>
                        <a href="<?= baseUrl('/our-home') ?>"
                            class="nav-link-main <?= isActive('/our-home') ? 'active' : '' ?>">
                            Our Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= baseUrl('/blog') ?>"
                            class="nav-link-main <?= isActive('/blog') ? 'active' : '' ?>">
                            Artikel
                        </a>
                    </li>
                </ul>

                <!-- WhatsApp CTA with Phone Icon (Moved Back) -->
                <?php if (!empty($whatsapp)): ?>
                    <a href="<?= e($waUrl) ?>" target="_blank" class="cta-phone">
                        <div class="cta-phone-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect y="3.8147e-06" width="32" height="32" rx="6" fill="#00B3E0" />
                                <path
                                    d="M17.7002 21.4846L20.2058 23.9901C20.5427 24.3271 20.5427 24.8733 20.2058 25.2103C18.3842 27.0318 15.5004 27.2368 13.4395 25.6911L11.9489 24.5732C10.2993 23.3359 8.83384 21.8705 7.59659 20.2208L6.47864 18.7302C4.93299 16.6694 5.13794 13.7856 6.9595 11.964C7.29643 11.6271 7.84269 11.6271 8.17962 11.964L10.6852 14.4695C11.0547 14.839 11.0547 15.4381 10.6852 15.8076L9.719 16.7738C9.56545 16.9273 9.52739 17.1619 9.6245 17.3561C10.7473 19.6017 12.5681 21.4225 14.8136 22.5453C15.0078 22.6424 15.2424 22.6043 15.396 22.4508L16.3621 21.4846C16.7316 21.1151 17.3307 21.1151 17.7002 21.4846Z"
                                    stroke="white" stroke-width="1.89232" />
                                <path
                                    d="M26.0484 18.0416C25.5496 15.2626 24.3281 12.6638 22.5066 10.5064C20.6851 8.34911 18.3278 6.70924 15.6717 5.75168"
                                    stroke="white" stroke-width="1.89232" stroke-linecap="round" />
                                <path
                                    d="M21.3915 18.8774C21.0394 16.9157 20.1771 15.0812 18.8914 13.5584C17.6056 12.0356 15.9417 10.878 14.0668 10.2021"
                                    stroke="white" stroke-width="1.89232" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="cta-phone-text">
                            <span class="cta-label">Need Booking?</span>
                            <span class="cta-number"><?= e($whatsapp) ?></span>
                        </div>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

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

            </ul>
        </div>
    </nav>

    <!-- Menu Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>
</div>