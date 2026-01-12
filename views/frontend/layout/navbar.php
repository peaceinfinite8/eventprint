<?php
$baseUrl = rtrim(($vars['baseUrl'] ?? '/eventprint/public'), '/');

// ambil data menu dari site.json (optional tapi rapi)
$site = [
  'brand' => ['logoText' => 'EventPrint'],
  'nav' => [
    ['label'=>'Home','href'=>'/'],
    ['label'=>'All Product','href'=>'/products'],
    ['label'=>'Our Home','href'=>'/our-home'],
    ['label'=>'Blog','href'=>'/blog'],
    ['label'=>'Contact','href'=>'/contact'],
  ],
];

$siteJsonPath = realpath(__DIR__ . '/../../../public/assets/frontend/data/site.json');
if ($siteJsonPath && file_exists($siteJsonPath)) {
  $json = json_decode(file_get_contents($siteJsonPath), true);
  if (is_array($json)) $site = array_replace_recursive($site, $json);
}

// hitung current path relatif terhadap baseUrl
$uriPath  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$basePath = parse_url($baseUrl, PHP_URL_PATH) ?: '';
$basePath = rtrim($basePath, '/');

$current = $uriPath;
if ($basePath !== '' && strpos($current, $basePath) === 0) {
  $current = substr($current, strlen($basePath));
}
$current = '/' . trim($current, '/');
if ($current === '/') $current = '/';

function ep_nav_active(string $current, string $href): bool {
  $href = '/' . trim($href, '/');
  if ($href === '//') $href = '/';
  if ($href === '/') return $current === '/';
  return strpos($current, $href) === 0;
}
?>

<nav class="navbar">
  <div class="container">
    <a href="<?= htmlspecialchars($baseUrl) ?>/" class="navbar-brand">
      <?= htmlspecialchars($site['brand']['logoText'] ?? 'EventPrint') ?>
    </a>

    <ul class="navbar-nav">
      <?php foreach (($site['nav'] ?? []) as $item): ?>
        <?php
          $href = $item['href'] ?? '/';
          if (!preg_match('#^https?://#i', $href)) {
            $href = $baseUrl . (str_starts_with($href, '/') ? $href : '/' . $href);
          }
          $rawHref = $item['href'] ?? '/';
          $active = ep_nav_active($current, $rawHref);
        ?>
        <li>
          <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= htmlspecialchars($href) ?>">
            <?= htmlspecialchars($item['label'] ?? '-') ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>
