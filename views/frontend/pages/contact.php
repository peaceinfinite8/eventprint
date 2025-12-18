<?php
/**
 * Contact Page
 * Display contact information and message form
 */

$address = $settings['address'] ?? '';
$phone = $settings['phone'] ?? '';
$email = $settings['email'] ?? '';
$whatsapp = $settings['whatsapp'] ?? '';
$operatingHours = $settings['operating_hours'] ?? '';
$gmapsEmbed = $settings['gmaps_embed'] ?? '';
$facebook = $settings['facebook'] ?? '';
$instagram = $settings['instagram'] ?? '';
$twitter = $settings['twitter'] ?? '';

// Format WhatsApp URL
$waUrl = $whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) : '';
?>

<div class="contact-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Hubungi Kami</h1>
            <p class="page-subtitle">Kami siap membantu kebutuhan cetak digital Anda</p>
        </div>

        <div class="contact-grid">
            <!-- Contact Information -->
            <div class="contact-info-section">
                <h2>Get In Touch</h2>

                <div class="contact-info-list">
                    <!-- Address -->
                    <?php if ($address): ?>
                        <div class="contact-info-item">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Alamat</h4>
                                <p><?= e($address) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Email -->
                    <?php if ($email): ?>
                        <div class="contact-info-item">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Email</h4>
                                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Phone -->
                    <?php if ($phone): ?>
                        <div class="contact-info-item">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328z" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Telepon</h4>
                                <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- WhatsApp -->
                    <?php if ($waUrl): ?>
                        <div class="contact-info-item">
                            <div class="info-icon whatsapp">
                                <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064  7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326z" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>WhatsApp</h4>
                                <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener"><?= e($whatsapp) ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Operating Hours -->
                    <?php if ($operatingHours): ?>
                        <div class="contact-info-item">
                            <div class="info-icon">
                                <svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor">
                                    <path
                                        d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                </svg>
                            </div>
                            <div class="info-content">
                                <h4>Jam Operasional</h4>
                                <p><?= e($operatingHours) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Social Media -->
                <?php if ($facebook || $instagram || $twitter): ?>
                    <div class="social-media-section">
                        <h4>Follow Us</h4>
                        <div class="social-links">
                            <?php if ($facebook): ?>
                                <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" class="social-link facebook"
                                    aria-label="Facebook">
                                    <svg width="20" height="20" fill="currentColor">
                                        <use href="#icon-facebook" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if ($instagram): ?>
                                <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" class="social-link instagram"
                                    aria-label="Instagram">
                                    <svg width="20" height="20" fill="currentColor">
                                        <use href="#icon-instagram" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if ($twitter): ?>
                                <a href="<?= e($twitter) ?>" target="_blank" rel="noopener" class="social-link twitter"
                                    aria-label="Twitter">
                                    <svg width="20" height="20" fill="currentColor">
                                        <use href="#icon-twitter" />
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-section">
                <div class="form-container">
                    <h2>Kirim Pesan</h2>
                    <p class="form-description">Isi form di bawah dan kami akan segera menghubungi Anda</p>

                    <form id="contactForm" class="contact-form">
                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="phone">No. Telepon / WhatsApp</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subjek</label>
                            <input type="text" id="subject" name="subject" class="form-control"
                                value="<?= $productName ? e($productName) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="message">Pesan <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="6" required
                                class="form-control"><?= $productName ? "Saya tertarik dengan produk: " . e($productName) : '' ?></textarea>
                        </div>

                        <div id="formMessage" class="form-message"></div>

                        <button type="submit" class="btn btn-primary btn-large" id="submitBtn">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        <?php if ($gmapsEmbed): ?>
            <div class="maps-section">
                <h2>Lokasi Kami</h2>
                <div class="maps-container">
                    <?= $gmapsEmbed ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('contactForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formMessage = document.getElementById('formMessage');
        const formData = new FormData(this);

        // Disable button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim...';
        formMessage.textContent = '';
        formMessage.className = 'form-message';

        try {
            const response = await fetch(<?= json_encode(baseUrl('/contact/send')) ?>, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                formMessage.textContent = result.message;
                formMessage.className = 'form-message success';
                this.reset();
            } else {
                formMessage.textContent = result.errors.join(', ');
                formMessage.className = 'form-message error';
            }
        } catch (error) {
            formMessage.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
            formMessage.className = 'form-message error';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Kirim Pesan';
        }
    });
</script>