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
      <div class="contact-text">${settings.address || 'Alamat belum diatur'}</div>
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
 * Render social media icons
 */
function renderSocialIcons(settings) {
  const container = document.getElementById('socialIcons');
  if (!container) return;

  const socials = [];

  if (settings.facebook) {
    socials.push({ platform: 'Facebook', url: settings.facebook, icon: '📘' });
  }
  if (settings.instagram) {
    socials.push({ platform: 'Instagram', url: settings.instagram, icon: '📷' });
  }
  if (settings.whatsapp) {
    const waUrl = settings.whatsapp.startsWith('http') ? settings.whatsapp : `https://wa.me/${settings.whatsapp.replace(/\D/g, '')}`;
    socials.push({ platform: 'WhatsApp', url: waUrl, icon: '💬' });
  }

  if (socials.length === 0) return;

  const html = socials.map(social => `
    <a href="${social.url}" class="social-icon" title="${social.platform}" aria-label="${social.platform}" target="_blank">
      ${social.icon}
    </a>
  `).join('');

  container.innerHTML = html;
}

/**
 * Handle contact form submission
 */
function handleContactSubmit(event) {
  event.preventDefault();

  // In production, this would send to API
  alert('Terima kasih! Pesan Anda telah dikirim. Kami akan segera menghubungi Anda.');

  // Reset form
  event.target.reset();

  return false;
}
