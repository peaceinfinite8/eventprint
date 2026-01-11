<?php
// views/admin/contact-messages/index.php
$baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
$baseUrl = rtrim($baseUrl, '/');
$messages = $vars['messages'] ?? [];
$currentPage = $vars['currentPage'] ?? 1;
$totalPages = $vars['totalPages'] ?? 1;
$total = $vars['total'] ?? 0;
?>

<div class="d-flex align-items-center justify-content-between mb-4 fade-in">
    <div>
        <h1 class="h4 mb-1 fw-bold text-gradient">Contact Messages</h1>
        <p class="text-muted small mb-0">Kelola pesan masuk dari formulir kontak</p>
    </div>
</div>

<div class="dash-container-card fade-in delay-1">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-envelope me-2"></i>Daftar Pesan (<?= (int) $total ?>)
        </h5>
    </div>

    <div class="p-0">
        <?php if (!empty($messages)): ?>
            <div class="table-responsive">
                <table class="table table-custom table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 70px;">ID</th>
                            <th>Nama</th>
                            <th>Email & Telepon</th>
                            <th>Subjek</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 180px;">Diterima</th>
                            <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="<?= $msg['is_read'] ? '' : 'table-warning_custom' // Optional custom class if needed ?>">
                                <td class="ps-4 text-muted small">#<?= (int) $msg['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark text-break" style="min-width: 150px;">
                                        <?= htmlspecialchars($msg['name']) ?></div>
                                </td>
                                <td>
                                    <div class="small text-dark text-break" style="max-width: 200px;">
                                        <?= htmlspecialchars($msg['email']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($msg['phone'] ?? '-') ?></div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;"> <!-- Reduced width for better fit -->
                                        <?= htmlspecialchars($msg['subject']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($msg['is_read']): ?>
                                        <span
                                            class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                            <i class="fas fa-envelope-open me-1"></i>Read
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">
                                            <i class="fas fa-envelope me-1"></i>Unread
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    <?= htmlspecialchars($msg['created_at']) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= $baseUrl ?>/admin/contact-messages/<?= (int) $msg['id'] ?>"
                                        class="btn btn-sm btn-outline-primary shadow-sm bg-white">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-- Pagination -->
            <?php
            $perPage = ceil($total / max(1, $totalPages));
            echo renderPagination($baseUrl, '/admin/contact-messages', ['total' => $total, 'page' => $currentPage, 'per_page' => $perPage], []);
            ?>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-inbox fa-3x text-muted opacity-25"></i>
                </div>
                <p class="text-muted mb-0">Belum ada pesan masuk.</p>
            </div>
        <?php endif; ?>
    </div>
</div>