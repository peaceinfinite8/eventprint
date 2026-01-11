<?php
/**
 * Our Home Page (1:1 Parity with Reference)
 * Reference: frontend/public/views/our-home.html
 */
?>

<style>
    /* Our Home Page Specific Styles (from reference our-home.html lines 14-164) */
    body {
        /* Default background */
    }

    .our-home-section {
        padding: 40px 0 60px;
    }

    .our-home-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--gray-900);
    }

    .stores-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .store-card {
        background: var(--white);
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 16px;
    }

    .store-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .store-image {
        width: 160px;
        width: 160px;
        aspect-ratio: 1/1;
        /* Square 1:1 */
        /* height: 160px; REMOVED for 4:3 */
        background: #E5E7EB;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9CA3AF;
        font-size: 0.9rem;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .store-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .store-label {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(0, 0, 0, 0.75);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .store-info {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .info-row {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .info-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        color: var(--primary-cyan);
        margin-top: 2px;
    }

    .info-icon svg {
        width: 100%;
        height: 100%;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 2px;
    }

    .info-text {
        font-size: 0.8rem;
        color: var(--gray-600);
        line-height: 1.4;
    }

    /* Machine Gallery Styles */
    .machine-gallery-section {
        padding: 60px 0;
        background-color: var(--gray-50);
    }

    .gallery-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .gallery-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 10px;
    }

    .gallery-subtitle {
        font-size: 1rem;
        color: var(--gray-600);
        max-width: 600px;
        margin: 0 auto;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .gallery-item img {
        width: 100%;
        width: 100%;
        /* height: 220px; REMOVED fixed height */
        aspect-ratio: 4/3;
        object-fit: cover;
        display: block;
    }

    .gallery-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background-color: var(--primary-cyan);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 10;
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0) 100%);
        color: white;
        padding: 20px 15px 15px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-item-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .gallery-item-caption {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.9);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .stores-grid {
            gap: 16px;
        }

        .store-card {
            padding: 14px;
        }

        .store-image {
            width: 140px;
            /* height automatically 1:1 via aspect-ratio */
        }

        .gallery-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .stores-grid {
            grid-template-columns: 1fr;
        }

        .store-card {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .store-image {
            width: 100%;
            height: auto;
            /* Allow aspect-ratio 1/1 to work */
        }

        .our-home-title {
            font-size: 1.5rem;
        }

        .gallery-title {
            font-size: 1.5rem;
        }

        .gallery-subtitle {
            font-size: 0.9rem;
        }

        .gallery-item img {
            /* height: 180px; */
        }
    }
</style>

<!-- Our Home Content -->
<section class="our-home-section">
    <div class="container">
        <h1 class="our-home-title"><?= e($content['page_title'] ?? 'Our Home') ?></h1>
        <div id="storesGrid" class="stores-grid">
            <?php if (!empty($stores)): ?>
                <?php foreach ($stores as $store): ?>
                    <div class="store-card">
                        <div class="store-image">
                            <?php if (!empty($store['thumbnail'])): ?>
                                <img src="<?= uploadUrl($store['thumbnail']) ?>" alt="<?= e($store['name']) ?>">
                            <?php else: ?>
                                <span>Gambar</span>
                            <?php endif; ?>
                            <div class="store-label">EventPrint Tempat</div>
                        </div>

                        <div class="store-info">
                            <!-- Address -->
                            <div class="info-row">
                                <div class="info-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Alamat</div>
                                    <div class="info-text"><?= e($store['address']) ?></div>
                                </div>
                            </div>

                            <!-- Email -->
                            <?php if (!empty($store['email'])): ?>
                                <div class="info-row">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                            <path d="M3 7l9 6 9-6"></path>
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Email</div>
                                        <div class="info-text"><?= e($store['email']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- WhatsApp -->
                            <?php if (!empty($store['whatsapp'])): ?>
                                <div class="info-row">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">WhatsApp</div>
                                        <div class="info-text"><?= e($store['whatsapp']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Operating Hours -->
                            <?php if (!empty($store['hours'])): ?>
                                <div class="info-row">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 6v6l4 2"></path>
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <div class="info-label">Jam Kerja</div>
                                        <div class="info-text">
                                            <?php
                                            // Handle hours as array or string
                                            $hoursValue = $store['hours'];
                                            $hoursArray = @json_decode($hoursValue, true);
                                            if (is_array($hoursArray) && !empty($hoursArray)) {
                                                echo implode('<br>', array_map('e', $hoursArray));
                                            } else {
                                                echo e($hoursValue);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Google Maps Link -->
                            <?php if (!empty($store['gmaps_url'])): ?>
                                <div class="info-row">
                                    <div class="info-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="info-content">
                                        <a href="<?= e($store['gmaps_url']) ?>" target="_blank"
                                            class="btn btn-sm btn-outline-primary"
                                            style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; padding: 4px 12px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            Lihat di Google Maps
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--gray-600); padding: 40px 0; grid-column: 1 / -1;">Belum ada data
                    lokasi toko.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Machine Gallery Section -->
<section class="machine-gallery-section">
    <div class="container">
        <div class="gallery-header">
            <h2 class="gallery-title"><?= e($content['gallery_title'] ?? 'Galeri Mesin Produksi') ?></h2>
            <p class="gallery-subtitle">
                <?= e($content['gallery_subtitle'] ?? 'Lihat mesin yang kami gunakan untuk menjaga kualitas & kecepatan produksi') ?>
            </p>
        </div>
        <div id="galleryGrid" class="gallery-grid">
            <?php if (!empty($machines)): ?>
                <?php foreach ($machines as $item): ?>
                    <div class="gallery-item">
                        <?php if (!empty($item['type'])): ?>
                            <div class="gallery-badge"><?= e($item['type']) ?></div>
                        <?php endif; ?>
                        <?php
                        // Determine if image is uploaded file or external URL
                        $imageUrl = '';
                        if (!empty($item['image'])) {
                            // Check if it's an external URL (http/https)
                            if (preg_match('/^https?:\/\//i', $item['image'])) {
                                $imageUrl = $item['image']; // External URL (from page_contents)
                            } else {
                                $imageUrl = uploadUrl($item['image']); // Uploaded file (from gallery)
                            }
                        } else {
                            $imageUrl = assetUrl('frontend/images/placeholder-general.png');
                        }
                        ?>
                        <img src="<?= e($imageUrl) ?>" alt="<?= e($item['title'] ?? '') ?>" loading="lazy">
                        <div class="gallery-overlay">
                            <div class="gallery-item-title"><?= e($item['title'] ?? '') ?></div>
                            <div class="gallery-item-caption"><?= e($item['caption'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: var(--gray-600); padding: 40px 0; grid-column: 1 / -1;">Belum ada data
                    mesin produksi.</p>
            <?php endif; ?>
        </div>
    </div>
</section>