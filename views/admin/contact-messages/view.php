<?php
// views/admin/contact-messages/view.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');
$message = $vars['message'] ?? [];
?>

<div class="container-fluid p-0">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto">
            <h3><strong>View Message</strong> #<?= (int) $message['id'] ?></h3>
        </div>
        <div class="col-auto ms-auto text-end">
            <a href="<?= $baseUrl ?>/admin/contact-messages" class="btn btn-secondary">
                ← Back to Messages
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?= htmlspecialchars($message['subject']) ?></h5>
                        <?php if ($message['is_read']): ?>
                            <span class="badge bg-secondary">Read</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Unread</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>From:</strong> <?= htmlspecialchars($message['name']) ?>
                        <br>
                        <strong>Email:</strong> <a
                            href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a>
                        <?php if (!empty($message['phone'])): ?>
                            <br>
                            <strong>Phone:</strong> <?= htmlspecialchars($message['phone']) ?>
                        <?php endif; ?>
                        <br>
                        <strong>Received:</strong> <?= htmlspecialchars($message['created_at']) ?>
                    </div>

                    <hr>

                    <div class="message-content" style="white-space: pre-wrap; line-height: 1.6;">
                        <?= htmlspecialchars($message['message']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <button id="btnToggleRead" class="btn btn-outline-primary w-100 mb-2">
                        <?= $message['is_read'] ? 'Mark as Unread' : 'Mark as Read' ?>
                    </button>

                    <button id="btnDelete" class="btn btn-outline-danger w-100">
                        Delete Message
                    </button>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Reply</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Balas pesan melalui email:</p>
                    <button
                        onclick="replyViaEmail('<?= htmlspecialchars($message['email']) ?>', '<?= htmlspecialchars($message['subject']) ?>', '<?= htmlspecialchars($message['name']) ?>')"
                        class="btn btn-success w-100">
                        <i class="fas fa-envelope"></i> Reply via Email
                    </button>
                    <p class="text-muted small mt-2">Akan membuka aplikasi email Anda</p>

                    <?php if (!empty($message['phone'])): ?>
                        <hr class="my-3">

                        <p class="text-muted small">Balas pesan melalui WhatsApp:</p>
                        <button
                            onclick="replyViaWhatsApp('<?= htmlspecialchars($message['phone']) ?>', '<?= htmlspecialchars($message['name']) ?>', '<?= htmlspecialchars($message['subject']) ?>')"
                            class="btn btn-success w-100" style="background-color: #25D366; border-color: #25D366;">
                            <i class="fab fa-whatsapp"></i> Reply via WhatsApp
                        </button>
                        <p class="text-muted small mt-2">Akan membuka WhatsApp Web/App</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const baseUrl = '<?= $baseUrl ?>';
        const messageId = <?= (int) $message['id'] ?>;

        // Toggle read/unread
        document.getElementById('btnToggleRead')?.addEventListener('click', async function () {
            try {
                const response = await fetch(`${baseUrl}/admin/contact-messages/${messageId}/toggle-read`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to update status');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        // Delete message
        document.getElementById('btnDelete')?.addEventListener('click', async function () {
            if (!confirm('Are you sure you want to delete this message?')) return;

            try {
                const response = await fetch(`${baseUrl}/admin/contact-messages/${messageId}/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();
                if (data.success) {
                    window.location.href = `${baseUrl}/admin/contact-messages`;
                } else {
                    alert('Failed to delete message');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        /**
         * Reply via Email with mailto link
         * Includes fallback if mailto doesn't work
         */
        window.replyViaEmail = function (email, subject, name) {
            // Create mailto link with pre-filled subject
            const mailtoLink = `mailto:${email}?subject=Re: ${encodeURIComponent(subject)}`;

            // Try to open default email client
            window.location.href = mailtoLink;

            // Show helpful message after short delay
            setTimeout(() => {
                const opened = confirm(
                    `Email client will open to send reply to:\n\n` +
                    `To: ${name} (${email})\n` +
                    `Subject: Re: ${subject}\n\n` +
                    `If email client didn't open, click OK to copy email address.`
                );

                if (opened) {
                    // Copy email to clipboard
                    navigator.clipboard.writeText(email).then(() => {
                        alert(`Email address copied: ${email}`);
                    }).catch(() => {
                        // Fallback if clipboard API fails
                        prompt('Copy email address:', email);
                    });
                }
            }, 1000);
        };
        
        /**
         * Reply via WhatsApp
         * Normalizes phone number and opens WhatsApp with pre-filled message
         */
        window.replyViaWhatsApp = function(phone, name, subject) {
            // Normalize phone number (remove non-digits, add 62 prefix)
            let waNumber = phone.replace(/\D/g, '');
            
            // Convert 08xxx to 628xxx
            if (waNumber.startsWith('0')) {
                waNumber = '62' + waNumber.substring(1);
            } else if (!waNumber.startsWith('62')) {
                waNumber = '62' + waNumber;
            }
            
            // Create pre-filled message
            const message = 
                `Halo ${name},\n\n` +
                `Terima kasih telah menghubungi kami mengenai: "${subject}".\n\n` +
                `Kami ingin membalas pesan Anda...\n\n` +
                `---\n` +
                `Event Print Admin`;
            
            // Open WhatsApp
            const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank');
        };
    });
</script>