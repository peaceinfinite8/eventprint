// ============================================
// EventPrint - Contact Page Renderer (PHP API Version)
// ============================================

/**
 * Initialize Contact page
 */
async function initContactPage() {
  try {
    const data = await loadData('/api/contact');

    if (data && data.success && data.settings) {
      renderContactDetails(data.settings);
      renderSocialIcons(data.settings);
    }

  } catch (error) {
    console.error('Error loading contact page:', error);
  }
}

/**
 * Render contact details
 */
function renderContactDetails(settings) {
  const container = document.getElementById('contactDetails');
  if (!container) return;

  const html = `
    <div class="contact-detail mb-2">
      <div class="contact-icon">📍</div>
      <div class="contact-text">
        ${settings.maps_link
      ? `<a href="${settings.maps_link}" target="_blank" rel="noopener" style="color: inherit; text-decoration: none;">${settings.address || 'Alamat belum diatur'}</a>`
      : (settings.address || 'Alamat belum diatur')}
      </div>
    </div>
    
    <div class="contact-detail mb-2">
      <div class="contact-icon">📞</div>
      <div class="contact-text">${settings.phone || 'Telepon belum diatur'}</div>
    </div>
    
    <div class="contact-detail mb-2">
      <div class="contact-icon">✉️</div>
      <div class="contact-text">${settings.email || 'Email belum diatur'}</div>
    </div>
    
    <div class="contact-detail mb-2">
      <div class="contact-icon">💬</div>
      <div class="contact-text">${settings.whatsapp || 'WhatsApp belum diatur'}</div>
    </div>
  `;

  container.innerHTML = html;
}

/**
 * Render social media icons dynamically from settings
 * Supports all platforms in settings table
 */
function renderSocialIcons(settings) {
  const container = document.getElementById('socialIcons');
  if (!container) return;

  // Define all supported social platforms
  const socialPlatforms = {
    facebook: { name: 'Facebook', icon: '📘', color: '#1877f2' },
    instagram: { name: 'Instagram', icon: '📷', color: '#e4405f' },
    twitter: { name: 'Twitter', icon: '🐦', color: '#1da1f2' },
    tiktok: { name: 'TikTok', icon: '🎵', color: '#000000' },
    youtube: { name: 'YouTube', icon: '▶️', color: '#ff0000' },
    pinterest: { name: 'Pinterest', icon: '📌', color: '#e60023' },
    linkedin: { name: 'LinkedIn', icon: '💼', color: '#0077b5' },
    whatsapp: { name: 'WhatsApp', icon: '💬', color: '#25d366' }
  };

  const socials = [];

  // Iterate through all possible platforms
  for (const [platform, config] of Object.entries(socialPlatforms)) {
    const url = settings[platform];
    if (url && url.trim() !== '') {
      // Special handling for WhatsApp (convert phone to wa.me URL)
      const finalUrl = platform === 'whatsapp'
        ? (url.startsWith('http') ? url : `https://wa.me/${url.replace(/\D/g, '')}`)
        : url;

      socials.push({
        platform: config.name,
        url: finalUrl,
        icon: config.icon
      });
    }
  }

  if (socials.length === 0) {
    container.innerHTML = '<p style="color: var(--gray-600); font-size: 0.9rem;">Belum ada media sosial</p>';
    return;
  }

  const html = socials.map(social => `
    <a href="${social.url}" class="social-icon" title="${social.platform}" aria-label="${social.platform}" target="_blank" rel="noopener noreferrer">
      ${social.icon}
    </a>
  `).join('');

  container.innerHTML = html;
}

/**
 * Handle contact form submission
 */
async function handleContactSubmit(event) {
  event.preventDefault();

  const form = event.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.innerHTML;

  // Basic validation
  const name = form.querySelector('[name="name"]')?.value;
  const phone = form.querySelector('[name="phone"]')?.value;
  const message = form.querySelector('[name="message"]')?.value;

  if (!name || !phone || !message) {
    alert('Mohon lengkapi semua bidang yang wajib diisi.');
    return false;
  }

  try {
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    submitBtn.disabled = true;

    // Prepare form data
    const formData = new FormData(form);

    // Send to API
    const response = await fetch(`${window.EP_BASE_URL}/contact/send`, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      // Show success message
      alert(result.message || 'Pesan Anda berhasil terkirim!');
      form.reset();
    } else {
      // Show error message
      const errorMessage = result.errors ? result.errors.join('\n') : 'Terjadi kesalahan saat mengirim pesan.';
      alert(errorMessage);
    }

  } catch (error) {
    console.error('Error submitting contact form:', error);
    alert('Maaf, terjadi kesalahan koneksi. Silakan coba lagi nanti.');
  } finally {
    // Restore button state
    submitBtn.innerHTML = originalBtnText;
    submitBtn.disabled = false;
  }

  return false;
}
