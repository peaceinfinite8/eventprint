<?php
/**
 * Our Home Page (Store Locations/Portfolio)
 * Display all store locations with contact info and maps
 */
?>

<div class="our-home-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Lokasi Toko Kami</h1>
            <p class="page-subtitle">Temukan cabang Event Print terdekat dari lokasi Anda</p>
        </div>

        <?php if (!empty($stores)): ?>
            <div class="stores-grid">
                <?php foreach ($stores as $store): ?>
                    <div class="store-card">
                        <?php if (!empty($store['thumbnail'])): ?>
                            <div class="store-image">
                                <img src="<?= imageUrl($store['thumbnail'], 'frontend/images/store-placeholder.jpg') ?>"
                                    alt="<?= e($store['name']) ?>" loading="lazy">
                            </div>
                        <?php endif; ?>

                        <div class="store-content">
                            <div class="store-header">
                                <h3 class="store-name"><?= e($store['name']) ?></h3>
                                <?php if ($store['office_type'] === 'hq'): ?>
                                    <span class="store-badge hq">Kantor Pusat</span>
                                <?php endif; ?>
                            </div>

                            <div class="store-info">
                                <!-- Address -->
                                <div class="info-item">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path
                                            d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z" />
                                    </svg>
                                    <div>
                                        <strong>Alamat:</strong>
                                        <p><?= e($store['address']) ?></p>
                                        <?php if (!empty($store['city'])): ?>
                                            <p class="city"><?= e(ucwords($store['city'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <?php if (!empty($store['phone'])): ?>
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path
                                                d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328z" />
                                        </svg>
                                        <div>
                                            <strong>Telepon:</strong>
                                            <a href="tel:<?= e($store['phone']) ?>"><?= e($store['phone']) ?></a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- WhatsApp -->
                                <?php if (!empty($store['whatsapp'])): ?>
                                    <div class="info-item">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path
                                                d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                                        </svg>
                                        <div>
                                            <strong>WhatsApp:</strong>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $store['whatsapp']) ?>"
                                                target="_blank" rel="noopener">
                                                <?= e($store['whatsapp']) ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Maps Button -->
                            <?php if (!empty($store['gmaps_url'])): ?>
                                <a href="<?= e($store['gmaps_url']) ?>" target="_blank" rel="noopener"
                                    class="btn btn-outline btn-maps">
                                    📍 Lihat di Google Maps
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">🏪</div>
                <h3>Informasi Toko Segera Hadir</h3>
                <p>Kami sedang mempersiapkan informasi lokasi toko untuk Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>