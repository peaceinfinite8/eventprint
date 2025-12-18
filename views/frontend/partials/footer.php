<?php
/**
 * Frontend Footer Component (1:1 with Frontend Reference)
 * Structure: footer-wrapper-flex with multiple columns
 */

// Ensure settings is defined
$settings = $settings ?? [];

// Get settings
$siteName = $settings['site_name'] ?? 'EventPrint';
$siteTagline = $settings['site_tagline'] ?? 'Solusi Cetak Digital Berkualitas';
$address = $settings['address'] ?? 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517';
$phone = $settings['phone'] ?? '';
$email = $settings['email'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$facebook = $settings['facebook'] ?? '';
$instagram = $settings['instagram'] ?? '';
$twitter = $settings['twitter'] ?? '';
$operatingHours = $settings['operating_hours'] ?? "Senin – Jum'at : 09.00 – 18.00\nSabtu : 08.00 – 18.00";

// WhatsApp normalization
$waUrl = normalizeWhatsApp($whatsapp);
?>

<footer class="footer">
    <div class="footer-wrapper-flex">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info Column -->
                <div class="footer-col">
                    <h3 class="footer-title"><?= e($siteName) ?></h3>

                    <?php if ($siteTagline): ?>
                        <p class="footer-tagline" style="margin-bottom: 1rem; color: var(--gray-600); font-size: 0.95rem;">
                            <?= e($siteTagline) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($address): ?>
                        <p class="footer-text"><?= nl2br(e($address)) ?></p>
                    <?php endif; ?>

                    <?php if ($operatingHours): ?>
                        <p class="footer-text">
                            <strong>Jam Operasional:</strong><br>
                            <?= nl2br(e($operatingHours)) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Quick Links Column -->
                <div class="footer-col">
                    <h4 class="footer-subtitle">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?= baseUrl('/') ?>">Home</a></li>
                        <li><a href="<?= baseUrl('/products') ?>">All Product</a></li>
                        <li><a href="<?= baseUrl('/our-home') ?>">Our Home</a></li>
                        <li><a href="<?= baseUrl('/blog') ?>">Blog</a></li>
                        <li><a href="<?= baseUrl('/contact') ?>">Contact</a></li>
                    </ul>
                </div>

                <!-- Produk Kami Column (NEW - from reference) -->
                <div class="footer-col">
                    <h4 class="footer-subtitle">Produk Kami</h4>
                    <ul class="footer-links">
                        <li><a href="<?= baseUrl('/products') ?>">Print Warna</a></li>
                        <li><a href="<?= baseUrl('/products') ?>">Media Cetak Promosi</a></li>
                        <li><a href="<?= baseUrl('/products') ?>">Dan Lainnya</a></li>
                    </ul>
                </div>

                <!-- Contact Column -->
                <div class="footer-col">
                    <h4 class="footer-subtitle">Contact Us</h4>
                    <ul class="footer-contact">
                        <?php if ($phone): ?>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($email): ?>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                            </li>
                        <?php endif; ?>

                        <?php if ($waUrl): ?>
                            <li>
                                <i class="fab fa-whatsapp"></i>
                                <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener">WhatsApp</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Footer Extras: Social + Payment Icons -->
            <div class="footer-extras">
                <div class="footer-social">
                    <?php if ($facebook): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" class="social-icon"
                            aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($instagram): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" class="social-icon"
                            aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($twitter): ?>
                        <a href="<?= e($twitter) ?>" target="_blank" rel="noopener" class="social-icon"
                            aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Payment Icons Panel (from reference) -->
                <div class="footer-payment">
                    <span class="payment-label">Payment Methods:</span>
                    <div class="payment-icons">
                        <span class="payment-icon">💳 QRIS</span>
                        <span class="payment-icon">🏦 BCA</span>
                        <span class="payment-icon">🏦 Mandiri</span>
                        <span class="payment-icon">🏦 BNI</span>
                        <span class="payment-icon">🏦 BRI</span>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; <?= date('Y') ?> <?= e($siteName) ?> by Peace Infinite. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>