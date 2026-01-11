<?php
// views/frontend/layout/footer.php

$config = require __DIR__ . '/../../../app/config/app.php';
$baseUrl = rtrim($config['base_url'] ?? ($vars['baseUrl'] ?? '/eventprint'), '/');
$db = db();

// Fetch Footer Content using SQL
$content = [];
$res = $db->query("SELECT field, value FROM page_contents WHERE page_slug='footer' AND section='main'");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $content[$r['field']] = $r['value'];
    }
}

// Decode Data
$productLinks = json_decode($content['product_links'] ?? '[]', true);
$paymentMethods = json_decode($content['payment_methods'] ?? '[]', true);
$copyright = $content['copyright'] ?? '© 2026 EventPrint. All rights reserved.';

// Fallback for "Alamat" and "Jam Operasional" if not in DB, use Site JSON for now
$site = [
  'footer' => [
    'alamat' => 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat',
    'jam_operasional' => ['Senin - Sabtu: 08:00 - 19:00']
  ]
];
// Try to load site.json for address/hours override
$siteJsonPath = realpath(__DIR__ . '/../../../public/assets/frontend/data/site.json');
if ($siteJsonPath && file_exists($siteJsonPath)) {
  $json = json_decode(file_get_contents($siteJsonPath), true);
  if (is_array($json)) $site = array_replace_recursive($site, $json);
}
?>

<footer class="footer bg-primary text-white pt-5 pb-3"> <!-- Added classes for modern look if bootstrap is used, or rely on existing CSS -->
  <div class="container">
    <div class="row"> <!-- Bootstrap Grid -->
      
      <!-- Column 1: Brand & Links -->
      <div class="col-md-3 mb-4">
        <h4 class="fw-bold mb-3">EventPrint</h4>
        <ul class="list-unstyled footer-links">
          <li><a href="<?= $baseUrl ?>/" class="text-white text-decoration-none">Home</a></li>
          <li><a href="<?= $baseUrl ?>/products" class="text-white text-decoration-none">All Product</a></li>
          <li><a href="<?= $baseUrl ?>/our-home" class="text-white text-decoration-none">Our Home</a></li>
          <li><a href="<?= $baseUrl ?>/blog" class="text-white text-decoration-none">Blog</a></li>
          <li><a href="<?= $baseUrl ?>/contact" class="text-white text-decoration-none">Contact</a></li>
        </ul>
      </div>

      <!-- Column 2: Product Categories (Dynamic) -->
      <div class="col-md-3 mb-4">
        <h4 class="fw-bold mb-3">Produk Kami</h4>
        <ul class="list-unstyled footer-links">
          <?php if (!empty($productLinks)): ?>
            <?php foreach ($productLinks as $link): ?>
              <li>
                  <a href="<?= $baseUrl . htmlspecialchars($link['url']) ?>" class="text-white text-decoration-none">
                      <?= htmlspecialchars($link['label']) ?>
                  </a>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
             <li><a href="<?= $baseUrl ?>/products" class="text-white text-decoration-none">Kartu Nama</a></li>
             <li><a href="<?= $baseUrl ?>/products" class="text-white text-decoration-none">Brosur</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Column 3: Payment Methods (Dynamic) -->
      <div class="col-md-3 mb-4">
        <h4 class="fw-bold mb-3">Pembayaran</h4>
        <div class="d-flex flex-wrap gap-2 mb-3">
             <?php if (!empty($paymentMethods)): ?>
                <?php foreach ($paymentMethods as $pm): ?>
                    <?php if (!empty($pm['image'])): ?>
                        <div class="bg-white rounded p-1 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 35px; overflow: hidden;"
                             title="<?= htmlspecialchars($pm['label'] ?? '') ?>">
                            <img src="<?= $baseUrl ?>/<?= htmlspecialchars($pm['image']) ?>" 
                                 alt="<?= htmlspecialchars($pm['label'] ?? 'Payment') ?>"
                                 style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
             <?php else: ?>
                <div class="text-white opacity-50 small">Belum ada metode pembayaran.</div>
             <?php endif; ?>
        </div>
      </div>

      <!-- Column 4: Address & Info -->
      <div class="col-md-3 mb-4">
        <h4 class="fw-bold mb-3">Alamat</h4>
        <p class="small mb-3"><?= htmlspecialchars($site['footer']['alamat'] ?? '') ?></p>
        
        <h4 class="fw-bold mb-3">Jam Operasional</h4>
        <div class="small">
          <?php foreach (($site['footer']['jam_operasional'] ?? []) as $j): ?>
            <div><?= htmlspecialchars($j) ?></div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>

  <div class="border-top border-white border-opacity-25 mt-4 pt-3 text-center">
    <p class="small mb-0"><?= htmlspecialchars($copyright) ?></p>
  </div>
</footer>

<style>
/* Optional Frontend Footer CSS overrides if not using Bootstrap classes fully */
.footer-links li {
    margin-bottom: 8px;
}
.footer-links a {
    transition: opacity 0.2s;
}
.footer-links a:hover {
    opacity: 0.8;
}
</style>
