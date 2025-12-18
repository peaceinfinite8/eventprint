<?php
/**
 * Frontend Footer Component
 * Displays footer with contact info, links, and social media
 */

// Ensure settings is defined
$settings = $settings ?? [];

// Get settings (will be passed from controller)
$siteName = $settings['site_name'] ?? 'EventPrint';
$address = $settings['address'] ?? '';
$phone = $settings['phone'] ?? '';
$email = $settings['email'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$facebook = $settings['facebook'] ?? '';
$instagram = $settings['instagram'] ?? '';
$twitter = $settings['twitter'] ?? '';
$youtube = $settings['youtube'] ?? '';
$operatingHours = $settings['operating_hours'] ?? '';

// Format WhatsApp URL
$waUrl = $whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) : '';
?>

<footer class="footer">
    <div class="container footer__container">
        <div class="footer__grid">
            <!-- Company Info -->
            <div class="footer__col">
                <h3 class="footer__title"><?= e($siteName) ?></h3>
                <p class="footer__text"><?= e($address) ?></p>

                <?php if ($operatingHours): ?>
                    <p class="footer__text">
                        <strong>Jam Operasional:</strong><br>
                        <?= e($operatingHours) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="footer__col">
                <h4 class="footer__subtitle">Quick Links</h4>
                <ul class="footer__links">
                    <li><a href="<?= baseUrl('/') ?>">Home</a></li>
                    <li><a href="<?= baseUrl('/products') ?>">Products</a></li>
                    <li><a href="<?= baseUrl('/our-home') ?>">Our Home</a></li>
                    <li><a href="<?= baseUrl('/blog') ?>">Blog</a></li>
                    <li><a href="<?= baseUrl('/contact') ?>">Contact</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer__col">
                <h4 class="footer__subtitle">Contact Us</h4>
                <ul class="footer__contact">
                    <?php if ($phone): ?>
                        <li>
                            <i class="icon-phone"></i>
                            <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if ($email): ?>
                        <li>
                            <i class="icon-email"></i>
                            <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                        </li>
                    <?php endif; ?>

                    <?php if ($waUrl): ?>
                        <li>
                            <i class="icon-whatsapp"></i>
                            <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener">WhatsApp</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="footer__col">
                <h4 class="footer__subtitle">Follow Us</h4>
                <div class="footer__social">
                    <?php if ($facebook): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" class="social-link"
                            aria-label="Facebook">
                            <i class="icon-facebook"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($instagram): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" class="social-link"
                            aria-label="Instagram">
                            <i class="icon-instagram"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($twitter): ?>
                        <a href="<?= e($twitter) ?>" target="_blank" rel="noopener" class="social-link"
                            aria-label="Twitter">
                            <i class="icon-twitter"></i>
                        </a>
                    <?php endif; ?>

                    <?php if ($youtube): ?>
                        <a href="<?= e($youtube) ?>" target="_blank" rel="noopener" class="social-link"
                            aria-label="YouTube">
                            <i class="icon-youtube"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.
            </p>
        </div>
    </div>
</footer>