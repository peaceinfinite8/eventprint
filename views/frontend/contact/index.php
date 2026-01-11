<?php
// views/frontend/contact/index.php
$baseUrl = $baseUrl ?? '/eventprint';
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
            <input type="text" class="form-input" placeholder="Nama Anda" required>
          </div>

          <div class="form-group">
            <label class="form-label">No.Telepon/WhatsApp</label>
            <input type="tel" class="form-input" placeholder="08123456789" required>
          </div>

          <div class="form-group">
            <label class="form-label">Isi Pesan</label>
            <textarea class="form-textarea" placeholder="Tulis pesan Anda..." required></textarea>
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
    <div id="mapPlaceholder" class="contact-box" style="height: 400px;">
      <span>Gambar GMaps</span>
    </div>
  </div>
</section>