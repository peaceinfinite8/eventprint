// ============================================
// EventPrint - Contact Page Renderer
// ============================================

/**
 * Initialize Contact page
 */
async function initContactPage() {
    try {
        const data = await loadData('../data/contact.json');

        renderContactDetails(data.contact);
        renderSocialIcons(data.contact.socials);

    } catch (error) {
        console.error('Error loading contact page:', error);
    }
}

/**
 * Render contact details
 */
function renderContactDetails(contact) {
    const container = document.getElementById('contactDetails');
    if (!container) return;

    const html = `
    <div class="contact-detail mb-2">
      <div class="contact-icon">📍</div>
      <div class="contact-text">${contact.address}</div>
    </div>
    
    <div class="contact-detail mb-2">
      <div class="contact-icon">✉️</div>
      <div class="contact-text">${contact.email}</div>
    </div>
    
    <div class="contact-detail mb-2">
      <div class="contact-icon">💬</div>
      <div class="contact-text">${contact.whatsapp}</div>
    </div>
  `;

    container.innerHTML = html;
}

/**
 * Render social media icons
 */
function renderSocialIcons(socials) {
    const container = document.getElementById('socialIcons');
    if (!container) return;

    if (!socials || socials.length === 0) return;

    const html = socials.map(platform => `
    <a href="#" class="social-icon" title="${platform}" aria-label="${platform}">
      ${getSocialIcon(platform)}
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
