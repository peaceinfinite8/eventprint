<?php
// views/admin/blog/create.php

$baseUrl = $baseUrl ?? '/eventprint/public';

// dari controller (opsional, kalau nggak ada tetap aman)
$errors = $errors ?? [];
$old    = $old ?? [];
?>

<h1 class="h3 mb-3">Tambah Artikel</h1>

<div class="card">
  <div class="card-body">

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

    <form method="post"
          action="<?php echo $baseUrl; ?>/admin/blog/store"
          enctype="multipart/form-data">

      <!-- CSRF -->
      <input type="hidden" name="_token"
             value="<?php echo htmlspecialchars($csrfToken ?? Security::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">

      <div class="mb-3">
        <label class="form-label">Judul</label>
        <input
          type="text"
          name="title"
          class="form-control"
          value="<?php echo htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
          required
        >
      </div>

      <div class="mb-3">
        <label class="form-label">Excerpt (opsional)</label>
        <textarea
          name="excerpt"
          rows="3"
          class="form-control"
        ><?php echo htmlspecialchars($old['excerpt'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Konten</label>
        <textarea
          name="content"
          rows="8"
          class="form-control"
          required
        ><?php echo htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Thumbnail (opsional)</label>
        <input type="file" name="thumbnail" class="form-control">
        <small class="text-muted">Format: JPG, PNG, WEBP.</small>
      </div>

      <div class="form-check mb-3">
        <input
          type="checkbox"
          name="is_published"
          class="form-check-input"
          id="publishedCheck"
          <?php echo !empty($old['is_published']) ? 'checked' : ''; ?>
        >
        <label class="form-check-label" for="publishedCheck">
          Publish sekarang
        </label>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?php echo $baseUrl; ?>/admin/blog"
           class="btn btn-secondary">Kembali</a>
      </div>

    </form>

  </div>
</div>
