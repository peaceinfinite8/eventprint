# Patch Script 1: Add apiList() method to ProductPublicController
# Backup original file
$file = "c:\xampp\htdocs\eventprint\app\controllers\ProductPublicController.php"
$backup = "$file.backup_" + (Get-Date -Format "yyyyMMdd_HHmmss")
Copy-Item $file $backup
Write-Output "✅ Backup created: $backup"

# Read file content
$content = Get-Content $file -Raw

# Check if method already exists
if ($content -match 'public function apiList\(\)') {
    Write-Output "⚠️  Method apiList() already exists! Skipping..."
    exit 0
}

# Method to insert
$newMethod = @'

    // GET /api/products - List products with pagination
    public function apiList(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
        $categoryId = $_GET['category'] ?? null;
        $offset = ($page - 1) * $perPage;

        // Build WHERE clause
        $where = "p.is_active = 1";
        $params = [];
        $types = '';

        if ($categoryId && $categoryId !== '') {
            $where .= " AND p.category_id = ?";
            $params[] = $categoryId;
            $types .= 'i';
        }

        // Count total
        $countSql = "SELECT COUNT(*) as total FROM products p WHERE $where";
        if ($types) {
            $stmt = $this->db->prepare($countSql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $totalProducts = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
        } else {
            $result = $this->db->query($countSql);
            $totalProducts = $result->fetch_assoc()['total'];
        }

        // Fetch products
        $products = [];
        $sql = "
            SELECT p.id, p.name, p.slug, p.base_price, p.thumbnail, p.main_image,
                   pc.name as category_name, pc.slug as category_slug, p.category_id
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE $where
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();

        echo json_encode([
            'success' => true,
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => (int) $totalProducts,
                'total_pages' => ceil($totalProducts / $perPage)
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

'@

# Insert before apiCategories method
$pattern = '(\s+)(public function apiCategories\(\): void)'
$replacement = $newMethod + '$1$2'
$newContent = $content -replace $pattern, $replacement

# Save patched file
Set-Content -Path $file -Value $newContent -NoNewline
Write-Output "✅ Method apiList() added successfully!"
Write-Output "✅ File patched: $file"
Write-Output ""
Write-Output "Testing endpoint..."

# Test the endpoint
try {
    $response = Invoke-WebRequest -Uri "http://localhost/eventprint/public/api/products" -UseBasicParsing -TimeoutSec 5
    if ($response.StatusCode -eq 200 -and $response.Content -match '^\{') {
        Write-Output "✅ API endpoint working! Status: 200, Returns: JSON"
    } else {
        Write-Output "⚠️  Endpoint returns Status $($response.StatusCode)"
    }
} catch {
    Write-Output "❌ Test failed: $_"
}
