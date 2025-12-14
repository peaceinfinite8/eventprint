<?php
// views/admin/blog/edit.php

$baseUrl = $baseUrl ?? '/eventprint/public';
$post    = $post ?? null;

if (!$post) {
    echo "<p>Artikel tidak ditemukan.</p>";
    return;
}

// Ambil error & old input (kalau sebelumnya validasi gagal)
$errors = class_exists('Validation') ? Validation::errors() : [];
$old    = $_SESSION['old_input'] ?? [];
if (class_exists('Validation')) {
    Validation::clear();
}

// CSRF token di–inject dari layout (main.php)
// fallback kalau nggak ada
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<h1 class="h3 mb-3">Edit Artikel</h1>

<div class="card">
  <div class="card-body">

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/blog/update/<?php echo (int)$post['id']; ?>"
          enctype="multipart/form-data">

      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $fieldErrors): ?>
              <?php foreach ($fieldErrors as $msg): ?>
                <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input type="text"
               name="title"
               class="form-control"
               required
               value="<?php echo htmlspecialchars($old['title'] ?? $post['title'], ENT_QUOTES, 'UTF-8'); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Excerpt (opsional)</label>
        <textarea name="excerpt"
                  rows="3"
                  class="form-control"><?php
          echo htmlspecialchars($old['excerpt'] ?? ($post['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Konten</label>
        <textarea name="content"
                  rows="8"
                  class="form-control"><?php
          echo htmlspecialchars($old['content'] ?? ($post['content'] ?? ''), ENT_QUOTES, 'UTF-8');
        ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail (opsional)</label>
        <input type="file" name="thumbnail" class="form-control">

        <?php if (!empty($post['thumbnail'])): ?>
          <div class="mt-2">
            <small class="text-muted d-block mb-1">
              Thumbnail saat ini:
            </small>
            <img src="<?php echo $baseUrl . '/' . htmlspecialchars($post['thumbnail'], ENT_QUOTES, 'UTF-8'); ?>"
                 alt=""
                 style="max-height: 140px; border-radius: 4px;">
          </div>
        <?php endif; ?>

        <small class="text-muted d-block">
          Biarkan kosong jika tidak ingin mengubah thumbnail.
        </small>
      </div>

      <div class="form-check mb-3">
        <input type="checkbox"
               name="is_published"
               class="form-check-input"
               id="publishedCheck"
               <?php
               // pakai old kalau ada; kalau tidak pakai nilai dari DB
               $published = isset($old['is_published'])
                 ? (bool)$old['is_published']
                 : !empty($post['is_published']);
               echo $published ? 'checked' : '';
               ?>>
        <label class="form-check-label" for="publishedCheck">
          Publish
        </label>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?php echo $baseUrl; ?>/admin/blog"
           class="btn btn-secondary">Kembali</a>
      </div>

    </form>

  </div>
</div>
