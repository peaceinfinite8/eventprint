<?php
/**
 * Contact Page
 * 100% Parity with frontend/public/views/contact.html
 */
?>

<!-- Contact Content -->
<section class="section">
    <div class="container">
        <div class="contact-row">
            <!-- Get in Touch -->
            <div>
                <h2 class="mb-3">Get in Touch</h2>
                <div id="contactDetails">
                    <!-- Rendered by JS -->
                </div>

                <h4 class="mt-3 mb-2">Ikuti Kami</h4>
                <div id="socialIcons" class="social-icons">
                    <!-- Rendered by JS -->
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-box">
                <h3 class="mb-3">Kirim Pesan</h3>
                <form id="contactForm" onsubmit="handleContactSubmit(event)">
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-input" placeholder="Nama Anda" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No.Telepon/WhatsApp</label>
                        <input type="tel" name="phone" class="form-input" placeholder="08123456789" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Isi Pesan
                            <span class="text-muted small" style="float: right;" id="charCounter">0/1000</span>
                        </label>
                        <textarea name="message" id="messageInput" class="form-textarea"
                            placeholder="Tulis pesan Anda..." maxlength="1000" oninput="updateCharCounter()"
                            required></textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>

                    <button type="submit" class="btn btn-secondary">Kirim sekarang</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="section">
    <div class="container">
        <?php if (!empty($settings['gmaps_embed'])): ?>
            <div class="contact-box" style="height: 400px; overflow: hidden; border-radius: 12px;">
                <div class="ratio ratio-16x9">
                    <?= $settings['gmaps_embed'] ?>
                </div>
            </div>
        <?php else: ?>
            <div id="mapPlaceholder" class="contact-box" style="height: 400px;">
                <span>Gambar GMaps</span>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Page-Specific Scripts -->
<script src="<?= assetUrl('frontend/js/render/renderContact.js') ?>"></script>
<script>
    // Initialize contact page on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactPage);
    } else {
        initContactPage();
    }
</script>