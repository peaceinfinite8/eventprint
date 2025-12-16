<?php
require_once __DIR__ . '/../models/PageContent.php';
require_once __DIR__ . '/../helpers/Security.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $sections = [
            [
                'key'         => 'hero',
                'name'        => 'Hero Slides',
                'description' => 'Kelola slider/slide hero di halaman Home.',
                'manage_url'  => $this->baseUrl('admin/home/hero'),
            ],
        ];

        $this->renderAdmin('home/index', ['sections' => $sections], 'Home');
    }

    public function heroIndex(): void
    {
        $pc = new PageContent();
        $items = $pc->getSectionItems('home', 'hero');

        usort($items, fn($a,$b) => (int)($a['position'] ?? 1) <=> (int)($b['position'] ?? 1));

        // PAKAI FILE YANG SUDAH ADA: views/admin/home/hero_edit.php
        $this->renderAdmin('home/hero_edit', [
            'mode'      => 'index',
            'items'     => $items,
            'csrfToken' => Security::csrfToken(),
        ], 'Hero Slides');
    }

    public function heroCreateForm(): void
    {
        $this->renderAdmin('home/hero_edit', [
            'mode'      => 'create',
            'item'      => [
                'item_key'   => '',
                'title'      => '',
                'subtitle'   => '',
                'badge'      => '',
                'cta_text'   => '',
                'cta_link'   => '',
                'image'      => '',
                'position'   => 1,
                'is_active'  => 1,
            ],
            'csrfToken' => Security::csrfToken(),
        ], 'Tambah Slide');
    }

    public function heroStore(): void
    {
        Security::requireCsrfToken();

        $pc  = new PageContent();
        $key = 'slide_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3));

        $pc->saveItemFields('home', 'hero', $key, $this->collectHeroFieldsFromPost());

        $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil dibuat.');
    }

    public function heroEditForm(string $key): void
    {
        $pc = new PageContent();
        $items = $pc->getSectionItems('home', 'hero');

        $item = null;
        foreach ($items as $it) {
            if (($it['item_key'] ?? '') === $key) { $item = $it; break; }
        }

        if (!$item) {
            $this->redirectWithError('admin/home/hero', 'Slide tidak ditemukan.');
        }

        $this->renderAdmin('home/hero_edit', [
            'mode'      => 'edit',
            'item'      => $item,
            'csrfToken' => Security::csrfToken(),
        ], 'Edit Slide');
    }

    public function heroUpdate(string $key): void
    {
        Security::requireCsrfToken();

        $pc = new PageContent();
        $pc->saveItemFields('home', 'hero', $key, $this->collectHeroFieldsFromPost());

        $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil diupdate.');
    }

    public function heroDelete(string $key): void
    {
        Security::requireCsrfToken();

        $pc = new PageContent();
        $pc->deleteItem('home','hero',$key);

        $this->redirectWithSuccess('admin/home/hero', 'Slide hero berhasil dihapus.');
    }

    private function collectHeroFieldsFromPost(): array
    {
        return [
            'title'     => trim($_POST['title'] ?? ''),
            'subtitle'  => trim($_POST['subtitle'] ?? ''),
            'badge'     => trim($_POST['badge'] ?? ''),
            'cta_text'  => trim($_POST['cta_text'] ?? ''),
            'cta_link'  => trim($_POST['cta_link'] ?? ''),
            'image'     => trim($_POST['image'] ?? ''),
            'position'  => (string)max(1, (int)($_POST['position'] ?? 1)),
            'is_active' => isset($_POST['is_active']) ? '1' : '0',
        ];
    }
}
