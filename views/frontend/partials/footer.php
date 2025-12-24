<?php
/**
 * Frontend Footer Component (Dynamic)
 */

// Ensure settings is defined
$settings = $settings ?? [];
$footer = $footer ?? [];

// Get settings
$siteName = $settings['site_name'] ?? 'EventPrint';
$address = $settings['address'] ?? 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517';
$phone = $settings['phone'] ?? '';
$email = $settings['email'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$facebook = $settings['facebook'] ?? '';
$instagram = $settings['instagram'] ?? '';
$twitter = $settings['twitter'] ?? '';
$tiktok = $settings['tiktok'] ?? '';
$youtube = $settings['youtube'] ?? '';
$linkedin = $settings['linkedin'] ?? '';
$operatingHours = $settings['operating_hours'] ?? "Senin – Jum'at : 09.00 – 18.00\nSabtu : 08.00 – 18.00";

// Footer Content
$copyright = $footer['copyright'] ?? "© " . date('Y') . " $siteName. All rights reserved.";
$productLinks = json_decode($footer['product_links'] ?? '[]', true);
$paymentMethods = json_decode($footer['payment_methods'] ?? '[]', true);

// WhatsApp normalization
$waUrl = normalizeWhatsApp($whatsapp);

// Parse operating hours into array
$jamOperasional = explode("\n", $operatingHours);
?>

<footer id="footer" class="footer">
    <style>
        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
        }

        .footer-social-section {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .payment-icon img {
            height: 30px;
            object-fit: contain;
        }

        /* Responsive overrides matching main.css breakpoints */
        @media (max-width: 1024px) {
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container footer-wrapper-flex">
        <!-- Footer Content (4 Columns) -->
        <div class="footer-content">
            <!-- Column 1: EventPrint Links -->
            <div class="footer-column">
                <h4><?= e($siteName) ?></h4>
                <ul class="footer-links">
                    <li><a href="<?= baseUrl('/') ?>">Home</a></li>
                    <li><a href="<?= baseUrl('/products') ?>">All Product</a></li>
                    <li><a href="<?= baseUrl('/our-home') ?>">Our Home</a></li>
                    <li><a href="<?= baseUrl('/blog') ?>">Blog</a></li>
                    <li><a href="<?= baseUrl('/contact') ?>">Contact</a></li>
                </ul>
            </div>

            <!-- Column 2: Produk Kami (Dynamic) -->
            <div class="footer-column">
                <h4>Produk Kami</h4>
                <ul class="footer-links">
                    <?php if (!empty($productLinks)): ?>
                        <?php foreach ($productLinks as $link): ?>
                            <li><a href="<?= e($link['url']) ?>"><?= e($link['label']) ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="<?= baseUrl('/products') ?>">Print Warna</a></li>
                        <li><a href="<?= baseUrl('/products') ?>">Media Cetak Promosi</a></li>
                        <li><a href="<?= baseUrl('/products') ?>">Dan Lainnya</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Column 3: Pembayaran (Dynamic) -->
            <div class="footer-column">
                <h4>Pembayaran</h4>
                <div class="footer-payment-icons">
                    <?php if (!empty($paymentMethods)): ?>
                        <?php foreach ($paymentMethods as $pm): ?>
                            <div class="payment-icon">
                                <?php if (!empty($pm['image'])): ?>
                                    <img src="<?= baseUrl($pm['image']) ?>" alt="<?= e($pm['label']) ?>"
                                        title="<?= e($pm['label']) ?>">
                                <?php else: ?>
                                    <span
                                        style="font-size: 0.8rem; font-weight: 600; color: var(--primary-cyan);"><?= e($pm['label']) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="payment-icon">QRIS</div>
                        <div class="payment-icon">Bank Transfer</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Column 4: Alamat + Jam Operasional -->
            <div class="footer-column">
                <h4>Alamat</h4>
                <?php if (!empty($settings['maps_link'])): ?>
                    <a href="<?= e($settings['maps_link']) ?>" target="_blank" rel="noopener"
                        class="footer-text d-block mb-3" style="text-decoration: none; color: inherit;">
                        <?= nl2br(e($address)) ?>
                    </a>
                <?php else: ?>
                    <p class="footer-text"><?= nl2br(e($address)) ?></p>
                <?php endif; ?>
                <h4 class="mt-3">Jam Operasional</h4>
                <div class="footer-text">
                    <?php foreach ($jamOperasional as $jam): ?>
                        <div><?= e($jam) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Social Icons (Centered Bottom) -->
        <div class="footer-social-section">
            <div class="footer-social-icons">
                <?php if ($facebook): ?>
                    <a href="<?= e($facebook) ?>" class="social-icon-btn" aria-label="Facebook" target="_blank"
                        rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($instagram): ?>
                    <a href="<?= e($instagram) ?>" class="social-icon-btn" aria-label="Instagram" target="_blank"
                        rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($twitter): ?>
                    <a href="<?= e($twitter) ?>" class="social-icon-btn" aria-label="Twitter" target="_blank"
                        rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($tiktok): ?>
                    <a href="<?= e($tiktok) ?>" class="social-icon-btn" aria-label="TikTok" target="_blank" rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($youtube): ?>
                    <a href="<?= e($youtube) ?>" class="social-icon-btn" aria-label="YouTube" target="_blank"
                        rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php if ($linkedin): ?>
                    <a href="<?= e($linkedin) ?>" class="social-icon-btn" aria-label="LinkedIn" target="_blank"
                        rel="noopener">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Bar (Copyright) -->
        <div class="footer-bar">
            <p><?= e($copyright) ?></p>
        </div>
</footer>