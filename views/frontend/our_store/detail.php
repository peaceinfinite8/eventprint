<?php
$baseUrl = $baseUrl ?? '/eventprint';
$item    = $item ?? null;
if (!$item) {
    echo "<p>Data tidak ditemukan.</p>";
    return;
}
?>

<div class="mb-3">
  <a href="<?php echo $baseUrl; ?>/our-home" class="text-decoration-none small text-muted">
    &laquo; Kembali ke Our Home
  </a>
</div>

<div class="row">
  <div class="col-12 col-lg-7 mb-3">
    <?php if (!empty($item['thumbnail'])): ?>
      <img src="<?php echo $baseUrl . '/' . htmlspecialchars($item['thumbnail']); ?>"
           class="img-fluid rounded shadow-sm mb-3"
           alt="<?php echo htmlspecialchars($item['title']); ?>">
    <?php endif; ?>
  </div>
  <div class="col-12 col-lg-5 mb-3">
    <h1 class="h3 mb-2"><?php echo htmlspecialchars($item['title']); ?></h1>

    <?php if (!empty($item['client_name']) || !empty($item['category']) || !empty($item['project_date'])): ?>
      <dl class="row small mb-3">
        <?php if (!empty($item['client_name'])): ?>
          <dt class="col-4">Client</dt>
          <dd class="col-8"><?php echo htmlspecialchars($item['client_name']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($item['category'])): ?>
          <dt class="col-4">Kategori</dt>
          <dd class="col-8"><?php echo htmlspecialchars($item['category']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($item['project_date'])): ?>
          <dt class="col-4">Tanggal</dt>
          <dd class="col-8"><?php echo htmlspecialchars($item['project_date']); ?></dd>
        <?php endif; ?>
      </dl>
    <?php endif; ?>

    <?php if (!empty($item['short_description'])): ?>
      <p class="mb-2">
        <?php echo nl2br(htmlspecialchars($item['short_description'])); ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($item['description'])): ?>
  <div class="mt-4">
    <h2 class="h5 mb-2">Detail Project</h2>
    <div class="text-muted">
      <?php echo nl2br(htmlspecialchars($item['description'])); ?>
    </div>
  </div>
<?php endif; ?>
