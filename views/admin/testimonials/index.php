<?php
$baseUrl = $baseUrl ?? '/eventprint/public';
$items = $items ?? [];
$csrfToken = $csrfToken ?? (class_exists('Security') ? Security::csrfToken() : '');
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Testimonials Management</h1>
        <p class="text-muted small mb-0">Kelola ulasan dan testimoni pelanggan</p>
    </div>
    <a class="btn btn-primary shadow-sm" href="<?= $baseUrl ?>/admin/testimonials/create">
        <i class="fas fa-plus me-2"></i>Tambah Testimonial
    </a>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-star me-2"></i>Daftar Testimonial</h5>
    </div>

    <div class="p-0">
        <div class="table-responsive">
            <table class="table table-custom table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:60px;">ID</th>
                        <th>Pelanggan</th>
                        <th>Posisi</th>
                        <th style="width:150px;">Rating</th>
                        <th style="width:100px;">Status</th>
                        <th class="text-end pe-4" style="width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-comment-slash fa-3x text-muted opacity-25"></i>
                                </div>
                                <p class="text-muted mb-0">Belum ada testimonial.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $id = (int) ($item['id'] ?? 0);
                            $name = htmlspecialchars($item['name'] ?? '');
                            $position = htmlspecialchars($item['position'] ?? '');
                            $rating = (int) ($item['rating'] ?? 5);
                            $active = (int) ($item['is_active'] ?? 1) === 1;
                            $hasPhoto = !empty($item['photo']);
                            ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?= $id ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($hasPhoto): ?>
                                            <div class="me-2 rounded-circle border overflow-hidden" style="width:36px;height:36px;">
                                                <img src="<?= $baseUrl . '/' . htmlspecialchars($item['photo'], ENT_QUOTES, 'UTF-8') ?>"
                                                    alt="Avatar" class="w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="me-2 rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary small fw-bold"
                                                style="width:36px;height:36px;">
                                                <?= strtoupper(substr($name, 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="fw-bold text-dark"><?= $name ?></div>
                                    </div>
                                </td>
                                <td><?= $position ?: '<span class="text-muted small">-</span>' ?></td>
                                <td>
                                    <div class="text-warning small" style="letter-spacing:1px;">
                                        <?= str_repeat('<i class="fas fa-star"></i>', $rating) ?>
                                        <?= str_repeat('<i class="far fa-star text-muted opacity-25"></i>', 5 - $rating) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($active): ?>
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                            <i class="fas fa-times-circle me-1"></i>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a class="btn btn-icon btn-sm text-primary"
                                            href="<?= $baseUrl ?>/admin/testimonials/edit/<?= $id ?>" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="post" action="<?= $baseUrl ?>/admin/testimonials/delete/<?= $id ?>"
                                            class="d-inline delete-form">
                                            <input type="hidden" name="_token"
                                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-icon btn-sm text-danger" type="submit" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    });
</script>