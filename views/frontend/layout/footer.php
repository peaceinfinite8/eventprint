<?php
$baseUrl = rtrim(($vars['baseUrl'] ?? '/eventprint/public'), '/');

$site = [
  'footer' => [
    'produk_kami' => ['Banner','Sticker','Kartu Nama'],
    'alamat' => 'Alamat toko kamu di sini',
    'jam_operasional' => ['Senin - Sabtu 08:00 - 18:00'],
    'copyright' => '© EventPrint'
  ]
];

$siteJsonPath = realpath(__DIR__ . '/../../../public/assets/frontend/data/site.json');
if ($siteJsonPath && file_exists($siteJsonPath)) {
  $json = json_decode(file_get_contents($siteJsonPath), true);
  if (is_array($json)) $site = array_replace_recursive($site, $json);
}
?>

<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-column">
        <h4>EventPrint</h4>
        <ul class="footer-links">
          <li><a href="<?= $baseUrl ?>/">Home</a></li>
          <li><a href="<?= $baseUrl ?>/products">All Product</a></li>
          <li><a href="<?= $baseUrl ?>/our-home">Our Home</a></li>
          <li><a href="<?= $baseUrl ?>/blog">Blog</a></li>
          <li><a href="<?= $baseUrl ?>/contact">Contact</a></li>
        </ul>
      </div>

      <div class="footer-column">
        <h4>Produk Kami</h4>
        <ul class="footer-links">
          <?php foreach (($site['footer']['produk_kami'] ?? []) as $p): ?>
            <li><a href="<?= $baseUrl ?>/products"><?= htmlspecialchars($p) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-column">
        <h4>Alamat</h4>
        <p class="footer-text"><?= htmlspecialchars($site['footer']['alamat'] ?? '') ?></p>
        <h4 class="mt-3">Jam Operasional</h4>
        <div class="footer-text">
          <?php foreach (($site['footer']['jam_operasional'] ?? []) as $j): ?>
            <div><?= htmlspecialchars($j) ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bar">
    <p><?= htmlspecialchars($site['footer']['copyright'] ?? '') ?></p>
  </div>
</footer>
