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

  let html = `
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

  // Add sales contacts if available
  if (settings.sales_contacts) {
    try {
      const salesContacts = typeof settings.sales_contacts === 'string'
        ? JSON.parse(settings.sales_contacts)
        : settings.sales_contacts;

      if (Array.isArray(salesContacts) && salesContacts.length > 0) {
        html += `
          <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--gray-200);">
            <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: var(--gray-900);">
              👥 Kontak Sales Team
            </h4>
        `;

        salesContacts.forEach(contact => {
          if (contact.name && contact.number) {
            // Normalize WhatsApp number
            let waNumber = contact.number.replace(/\D/g, '');
            if (waNumber.startsWith('0')) {
              waNumber = '62' + waNumber.substring(1);
            } else if (!waNumber.startsWith('62')) {
              waNumber = '62' + waNumber;
            }

            html += `
              <div class="contact-detail mb-2" style="align-items: center;">
                <div class="contact-icon">💼</div>
                <div class="contact-text" style="flex: 1;">
                  <strong>${contact.name}</strong>
                  <br>
                  <span style="color: var(--gray-600); font-size: 0.9rem;">${contact.number}</span>
                </div>
                <a href="https://wa.me/${waNumber}" 
                   target="_blank" 
                   rel="noopener"
                   style="
                     padding: 6px 12px;
                     background: #25D366;
                     color: white;
                     border-radius: 6px;
                     text-decoration: none;
                     font-size: 0.85rem;
                     font-weight: 500;
                     display: inline-flex;
                     align-items: center;
                     gap: 5px;
                     transition: all 0.3s ease;
                   "
                   onmouseover="this.style.background='#128C7E'; this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='#25D366'; this.style.transform='translateY(0)'"
                >
                  💬 Chat
                </a>
              </div>
            `;
          }
        });

        html += `</div>`;
      }
    } catch (e) {
      console.error('Error parsing sales_contacts:', e);
    }
  }

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

      // Build WhatsApp message
      const waMessage = `🌟 *PESAN DARI WEBSITE*\n` +
        `━━━━━━━━━━━━━━━━\n\n` +
        `👤 *Nama:* ${name}\n` +
        `📱 *No. Telepon:* ${phone}\n\n` +
        `💬 *Pesan:*\n${message}\n\n` +
        `━━━━━━━━━━━━━━━━\n` +
        `Terima kasih telah menghubungi kami!`;

      // Get WhatsApp number from settings
      const waNumber = window.EP_SETTINGS?.whatsapp || '6281234567890';
      const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`;

      // Redirect to WhatsApp
      setTimeout(() => {
        window.open(waUrl, '_blank');
      }, 500);

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
