<?php
// app/controllers/HomeController.php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../helpers/Security.php';

class HomeController extends Controller
{
    
public function index(): void
{
    $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
    $db = db();

    // ===== 1) categories (dropdown mapping) =====
    $categories = [];
    $res = $db->query("SELECT id, name, slug, icon FROM product_categories WHERE is_active=1 ORDER BY sort_order ASC, id ASC");
    if ($res) while ($r = $res->fetch_assoc()) $categories[] = $r;

    // ===== 2) home_content (mapping + contact + cta) =====
    $homeContent = [];
    if ($stmt = $db->prepare("SELECT field, value FROM page_contents WHERE page_slug='home' AND section='home_content'")) {
        $stmt->execute();
        $rs = $stmt->get_result();
        while ($r = $rs->fetch_assoc()) {
            $homeContent[(string)$r['field']] = (string)($r['value'] ?? '');
        }
        $stmt->close();
    }

    $printId = (int)($homeContent['home_print_category_id'] ?? 0);
    $mediaId = (int)($homeContent['home_media_category_id'] ?? 0);

    // ===== 3) hero stats =====
    $heroTotal = 0;
    $heroActive = 0;
    if ($stmt = $db->prepare("
        SELECT COUNT(*) AS total,
               SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END) AS active
        FROM hero_slides
        WHERE page_slug='home'
    ")) {
        $stmt->execute();
        $rs = $stmt->get_result();
        $row = $rs ? $rs->fetch_assoc() : null;
        $heroTotal  = (int)($row['total'] ?? 0);
        $heroActive = (int)($row['active'] ?? 0);
        $stmt->close();
    }

    // ===== 4) mapping category names =====
    $mapNames = ['print' => '', 'media' => ''];
    if ($printId > 0) {
        $stmt = $db->prepare("SELECT name FROM product_categories WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $printId);
        $stmt->execute();
        $rs = $stmt->get_result();
        $r = $rs ? $rs->fetch_assoc() : null;
        $mapNames['print'] = (string)($r['name'] ?? '');
        $stmt->close();
    }
    if ($mediaId > 0) {
        $stmt = $db->prepare("SELECT name FROM product_categories WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $mediaId);
        $stmt->execute();
        $rs = $stmt->get_result();
        $r = $rs ? $rs->fetch_assoc() : null;
        $mapNames['media'] = (string)($r['name'] ?? '');
        $stmt->close();
    }

    // ===== 5) completion: contact =====
    $contactFilled = 0;
    foreach (['contact_address','contact_email','contact_whatsapp'] as $f) {
        if (trim((string)($homeContent[$f] ?? '')) !== '') $contactFilled++;
    }
    $contactPct = (int)round(($contactFilled / 3) * 100);

    // ===== 6) completion: mapping =====
    $mappingPct = 0;
    if ($printId > 0) $mappingPct += 50;
    if ($mediaId > 0) $mappingPct += 50;

    // ===== 7) IMPORTANT: hitung produk aktif untuk kategori mapping =====
    $countActiveByCategory = function(int $catId) use ($db): int {
        if ($catId <= 0) return 0;
        $sql = "SELECT COUNT(*) AS cnt
                FROM products
                WHERE is_active=1
                  AND deleted_at IS NULL
                  AND category_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $catId);
        $stmt->execute();
        $rs = $stmt->get_result();
        $row = $rs ? $rs->fetch_assoc() : null;
        $stmt->close();
        return (int)($row['cnt'] ?? 0);
    };

    $printCount = $countActiveByCategory($printId);
    $mediaCount = $countActiveByCategory($mediaId);

    // featured count (buat sanity check)
    $featuredCount = 0;
    if ($stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM products WHERE is_active=1 AND deleted_at IS NULL AND is_featured=1")) {
        $stmt->execute();
        $rs = $stmt->get_result();
        $row = $rs ? $rs->fetch_assoc() : null;
        $featuredCount = (int)($row['cnt'] ?? 0);
        $stmt->close();
    }

    $stats = [
        'hero_total'       => $heroTotal,
        'hero_active'      => $heroActive,
        'contact_pct'      => $contactPct,
        'mapping_pct'      => $mappingPct,
        'print_id'         => $printId,
        'media_id'         => $mediaId,
        'print_name'       => $mapNames['print'],
        'media_name'       => $mapNames['media'],
        'print_prod_count' => $printCount,
        'media_prod_count' => $mediaCount,
        'featured_count'   => $featuredCount,
    ];

    $this->renderAdmin('home/index', [
        'baseUrl'     => $baseUrl,
        'categories'  => $categories,
        'homeContent' => $homeContent,
        'stats'       => $stats,
        'csrfToken'   => Security::csrfToken(),
    ], 'Konten Beranda');
}


    public function content(): void
{
    $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
    $db = db();

    $page = 'home';

    // ambil semua field home_content
    $stmt = $db->prepare("SELECT field, value FROM page_contents WHERE page_slug=? AND section='home_content' ORDER BY field ASC");
    $stmt->bind_param('s', $page);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $data[(string)$r['field']] = (string)($r['value'] ?? '');
        }
    }
    $stmt->close();

    // default fields
    $data = array_merge([
        'contact_address'  => '',
        'contact_email'    => '',
        'contact_whatsapp' => '',
        'cta_left_text'    => 'Baca Artikel!',
        'cta_left_link'    => $baseUrl . '/blog',
        'cta_right_text'   => 'Kenapa Pilih Kami?',
        'cta_right_link'   => $baseUrl . '/our-home',
    ], $data);

    $this->renderAdmin('home/content', [
        'baseUrl'    => $baseUrl,
        'content'    => $data,
        'csrfToken'  => Security::csrfToken(), // ✅ INI YANG KAMU KURANG
    ], 'Edit Konten Home');
}


    public function contentUpdate(): void
    {
        Security::requireCsrfToken();

        $db = db();
        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');

        $page = 'home';
        $section = 'home_content';

        $allowed = [
            'contact_address',
            'contact_email',
            'contact_whatsapp',
            'cta_left_text',
            'cta_left_link',
            'cta_right_text',
            'cta_right_link',
        ];

        foreach ($allowed as $field) {
            $value = trim((string)($_POST[$field] ?? ''));
            $this->upsertPageContent($db, $page, $section, $field, $value);
        }

        $this->redirectWithSuccess('admin/home/content', 'Konten Home berhasil disimpan.');
    }

    public function updateHomeCategoryMap(): void
    {
        Security::requireCsrfToken();

        $db = db();
        $printId = (int)($_POST['home_print_category_id'] ?? 0);
        $mediaId = (int)($_POST['home_media_category_id'] ?? 0);

        $this->upsertPageContent($db, 'home', 'home_content', 'home_print_category_id', (string)$printId);
        $this->upsertPageContent($db, 'home', 'home_content', 'home_media_category_id', (string)$mediaId);

        $this->redirectWithSuccess('admin/home', 'Mapping kategori Home berhasil disimpan.');
    }

    private function upsertPageContent(mysqli $db, string $page, string $section, string $field, string $value): void
    {
        // UPDATE dulu
        if ($stmt = $db->prepare("UPDATE page_contents SET value=?, updated_at=CURRENT_TIMESTAMP WHERE page_slug=? AND section=? AND field=?")) {
            $stmt->bind_param('ssss', $value, $page, $section, $field);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($affected > 0) return;
        }

        // kalau belum ada row, INSERT
        if ($stmt = $db->prepare("INSERT INTO page_contents (page_slug, section, field, value) VALUES (?,?,?,?)")) {
            $stmt->bind_param('ssss', $page, $section, $field, $value);
            $stmt->execute();
            $stmt->close();
        }
    }

    private function uploadHeroImage(string $inputName, string $oldPath = ''): string
{
    // kalau tidak ada file, return old
    if (empty($_FILES[$inputName]) || ($_FILES[$inputName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return (string)$oldPath;
    }

    $f = $_FILES[$inputName];

    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new Exception('Upload gagal. Kode error: ' . (int)$f['error']);
    }

    // limit size 3MB
    $max = 3 * 1024 * 1024;
    if (($f['size'] ?? 0) > $max) {
        throw new Exception('Ukuran gambar terlalu besar. Maksimal 3MB.');
    }

    $tmp = $f['tmp_name'] ?? '';
    if (!$tmp || !is_uploaded_file($tmp)) {
        throw new Exception('File upload tidak valid.');
    }

    // cek mime (lebih aman dari sekedar ekstensi)
    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = $fi->file($tmp) ?: '';

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        throw new Exception('Format gambar tidak didukung. Pakai JPG/PNG/WebP.');
    }

    $ext = $allowed[$mime];
    $name = 'hero_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    // target folder (public/uploads/hero)
    $publicDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/eventprint/public';
    // kalau project kamu beda, ganti '/eventprint/public' di atas sesuai lokasi public kamu.

    $targetDir = $publicDir . '/uploads/hero';
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0775, true);
    }

    $dest = $targetDir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new Exception('Gagal memindahkan file upload.');
    }

    // hapus file lama kalau ada (optional tapi harusnya iya)
    $oldPath = trim((string)$oldPath);
    if ($oldPath !== '' && !preg_match('#^https?://#i', $oldPath)) {
        $oldAbs = $publicDir . '/' . ltrim($oldPath, '/');
        if (is_file($oldAbs)) @unlink($oldAbs);
    }

    // simpan RELATIVE path ke DB
    return 'uploads/hero/' . $name;
}


    // ===================== HERO SLIDES (hero_slides table) =====================

    public function heroIndex(): void
    {
        $db = db();

        $items = [];
        if ($stmt = $db->prepare("SELECT * FROM hero_slides WHERE page_slug='home' ORDER BY position ASC, id ASC")) {
            $stmt->execute();
            $rs = $stmt->get_result();
            $items = $rs ? $rs->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }

        $this->renderAdmin('home/hero_index', [
            'items'     => $items,
            'csrfToken' => Security::csrfToken(),
        ], 'Hero Slides');
    }

    public function heroCreateForm(): void
    {
        $this->renderAdmin('home/hero_form', [
            'mode'      => 'create',
            'item'      => [
                'title'     => '',
                'subtitle'  => '',
                'badge'     => '',
                'cta_text'  => '',
                'cta_link'  => '',
                'image'     => '',
                'position'  => 1,
                'is_active' => 1,
            ],
            'csrfToken' => Security::csrfToken(),
        ], 'Tambah Slide');
    }

   public function heroStore(): void
{
    Security::requireCsrfToken();

    $db = db();

    $title    = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $badge    = trim($_POST['badge'] ?? '');
    $ctaText  = trim($_POST['cta_text'] ?? '');
    $ctaLink  = trim($_POST['cta_link'] ?? '');
    $pos      = max(1, (int)($_POST['position'] ?? 1));
    $active   = isset($_POST['is_active']) ? 1 : 0;

    $page = 'home';

    // upload image (kalau tidak upload => '')
    $image = $this->uploadHeroImage('image_file', '');

    $stmt = $db->prepare("INSERT INTO hero_slides (page_slug,title,subtitle,badge,cta_text,cta_link,image,position,is_active)
                          VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssii', $page, $title, $subtitle, $badge, $ctaText, $ctaLink, $image, $pos, $active);
    $stmt->execute();
    $stmt->close();

    $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil dibuat.');
}


    public function heroEditForm(int $id): void
    {
        $db = db();

        $item = null;
        $stmt = $db->prepare("SELECT * FROM hero_slides WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $rs = $stmt->get_result();
        $item = $rs ? $rs->fetch_assoc() : null;
        $stmt->close();

        if (!$item) {
            $this->redirectWithError('admin/home/hero', 'Slide tidak ditemukan.');
            return;
        }

        $this->renderAdmin('home/hero_form', [
            'mode'      => 'edit',
            'item'      => $item,
            'csrfToken' => Security::csrfToken(),
        ], 'Edit Slide');
    }

   public function heroUpdate(int $id): void
{
    Security::requireCsrfToken();

    $db = db();

    $title    = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $badge    = trim($_POST['badge'] ?? '');
    $ctaText  = trim($_POST['cta_text'] ?? '');
    $ctaLink  = trim($_POST['cta_link'] ?? '');
    $pos      = max(1, (int)($_POST['position'] ?? 1));
    $active   = isset($_POST['is_active']) ? 1 : 0;

    // ambil old image dari hidden
    $oldImage = trim((string)($_POST['old_image'] ?? ''));

    // kalau upload baru, replace + delete old
    $image = $this->uploadHeroImage('image_file', $oldImage);

    $stmt = $db->prepare("UPDATE hero_slides
                          SET title=?, subtitle=?, badge=?, cta_text=?, cta_link=?, image=?, position=?, is_active=?, updated_at=CURRENT_TIMESTAMP
                          WHERE id=?");
    $stmt->bind_param('sssssssii', $title, $subtitle, $badge, $ctaText, $ctaLink, $image, $pos, $active, $id);
    $stmt->execute();
    $stmt->close();

    $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil diupdate.');
}


    public function heroDelete(int $id): void
{
    Security::requireCsrfToken();

    $db = db();

    // ambil image dulu
    $img = '';
    if ($stmt = $db->prepare("SELECT image FROM hero_slides WHERE id=? LIMIT 1")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $rs = $stmt->get_result();
        $row = $rs ? $rs->fetch_assoc() : null;
        $img = (string)($row['image'] ?? '');
        $stmt->close();
    }

    // delete row
    if ($stmt = $db->prepare("DELETE FROM hero_slides WHERE id=?")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    // delete file (kalau lokal)
    if ($img !== '' && !preg_match('#^https?://#i', $img)) {
        $publicDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/eventprint/public';
        $abs = $publicDir . '/' . ltrim($img, '/');
        if (is_file($abs)) @unlink($abs);
    }

    $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil dihapus.');
}


}
