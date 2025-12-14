    <?php
    $baseUrl  = $baseUrl ?? '/eventprint/public';
    $sections = $sections ?? [];
    ?>

    <h1 class="h3 mb-3">Contact – Master Data</h1>

    <div class="card mb-3">
    <div class="card-body">
        <p class="mb-0 text-muted">
        Halaman ini merangkum semua data dan pengaturan terkait menu <strong>Contact</strong>
        di website EventPrint.
        </p>
    </div>
    </div>

    <div class="card">
    <div class="card-body">
        <?php if (!empty($sections)): ?>
        <div class="table-responsive">
            <table class="table align-middle">
            <thead>
            <tr>
                <th style="width: 220px;">Section</th>
                <th>Deskripsi</th>
                <th>Ringkasan</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($sections as $section): ?>
                <tr>
                <td>
                    <strong><?php echo htmlspecialchars($section['name']); ?></strong>
                </td>
                <td class="small text-muted">
                    <?php echo htmlspecialchars($section['description']); ?>
                </td>
                <td class="small">
                    <?php
                    $stats  = $section['stats']  ?? [];
                    $latest = $section['latest'] ?? [];

                    $total  = (int)($stats['total']  ?? 0);
                    $unread = (int)($stats['unread'] ?? 0);
                    ?>
                    <div class="mb-1">
                    <strong>Total pesan:</strong> <?php echo $total; ?>,
                    <strong>Belum dibaca:</strong> <?php echo $unread; ?>
                    </div>

                    <?php if (!empty($latest)): ?>
                    <div class="small text-muted">Terbaru:</div>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($latest as $msg): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($msg['name']); ?></strong>
                            <span class="text-muted">
                            – <?php echo htmlspecialchars($msg['subject'] ?? ''); ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <span class="text-muted">(Belum ada pesan)</span>
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <a href="<?php echo htmlspecialchars($section['manage_url']); ?>"
                    class="btn btn-sm btn-primary">
                    Kelola
                    </a>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="mb-0 text-muted">
            Belum ada section yang dikonfigurasi untuk Contact.
        </p>
        <?php endif; ?>
    </div>
    </div>
