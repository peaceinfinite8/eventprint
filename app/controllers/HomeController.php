<?php
// app/controllers/HomeController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Validation.php';

class HomeController extends Controller
{
    protected $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    /* ===================== HUB HOME ===================== */

    public function index()
    {
        $hero = $this->getHero();

        $sections = [
            [
                'key'         => 'hero',
                'name'        => 'Hero Section',
                'description' => 'Judul, subjudul, dan tombol utama di halaman Home.',
                'summary'     => $hero,
                'manage_url'  => $this->baseUrl('admin/home/hero'),
            ],
        ];

        $this->renderAdmin('home/index', [
            'sections' => $sections,
        ], 'Home');
    }

    /* ===================== HERO EDIT ===================== */

    public function editHero()
    {
        if (!Auth::isSuperAdmin()) {
            $this->redirectWithError('admin/home', 'Akses ditolak. Hanya super admin yang boleh mengedit Home.');
        }

        $hero = $this->getHero();

        $this->renderAdmin('home/hero_edit', [
            'hero' => $hero,
        ], 'Edit Hero Home');
    }

    public function updateHero()
    {
        if (!Auth::isSuperAdmin()) {
            $this->redirectWithError('admin/home', 'Akses ditolak. Hanya super admin yang boleh mengedit Home.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try {
            Security::requireCsrfToken();
        } catch (Exception $e) {
            http_response_code(419);
            echo "CSRF token tidak valid atau sesi kadaluarsa.";
            return;
        }

        $rules = [
            'title'       => 'required|min:3|max:150',
            'subtitle'    => 'nullable|max:500',
            'button_text' => 'nullable|max:100',
            'button_link' => 'nullable|max:255',
        ];

        $input = Validation::validateOrRedirect(
            $_POST,
            $rules,
            $this->baseUrl('admin/home/hero')
        );

        $fields = [
            'title'       => $input['title'],
            'subtitle'    => $input['subtitle'] ?? '',
            'button_text' => $input['button_text'] ?? '',
            'button_link' => $input['button_link'] ?? '',
        ];

        foreach ($fields as $field => $value) {
            $this->upsertPageContent('home', 'hero', $field, $value);
        }

        $_SESSION['flash_success'] = 'Hero Home berhasil diperbarui.';
        header('Location: ' . $this->baseUrl('admin/home'));
        exit;
    }

    /* ===================== INTERNAL UTILS ===================== */

    protected function getHero(): array
    {
        $sql = "SELECT field, value
                FROM page_contents
                WHERE page_slug = 'home'
                  AND section   = 'hero'";

        $res  = $this->db->query($sql);
        $hero = [
            'title'       => '',
            'subtitle'    => '',
            'button_text' => '',
            'button_link' => '',
        ];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $field = $row['field'];
                if (array_key_exists($field, $hero)) {
                    $hero[$field] = (string)$row['value'];
                }
            }
        }

        return $hero;
    }

    protected function upsertPageContent(string $pageSlug, string $section, string $field, string $value): void
    {
        $sql = "INSERT INTO page_contents (page_slug, section, field, value)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE value = VALUES(value)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param('ssss', $pageSlug, $section, $field, $value);
        $stmt->execute();
        $stmt->close();
    }
}
