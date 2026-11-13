<?php
// app/Controllers/ProductController.php
namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Security;
use App\Models\Category;
use App\Models\Product;

class ProductController
{
    public function catalog(): void
    {
        $categories = Category::all();

        // Ambil filter dari GET query parameters
        $filters = [
            'search' => Security::sanitize($_GET['search'] ?? ''),
            'category' => Security::sanitize($_GET['category'] ?? ''),
            'min_price' => isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'in_stock_only' => !empty($_GET['in_stock']),
            'sort' => Security::sanitize($_GET['sort'] ?? 'newest'),
        ];

        $products = Product::all($filters);

        $pageTitle = 'Pemesanan & Katalog Produk - Aksara Unsulbar';
        $activeTab = 'catalog';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/catalog.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }

    /**
     * Endpoint API JSON untuk Live Search dan Filter Asinkronus (Real-time tanpa reload)
     */
    public function apiList(): void
    {
        $filters = [
            'search' => Security::sanitize($_GET['search'] ?? ''),
            'category' => Security::sanitize($_GET['category'] ?? ''),
            'min_price' => isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'in_stock_only' => !empty($_GET['in_stock']),
            'sort' => Security::sanitize($_GET['sort'] ?? 'newest'),
        ];

        $products = Product::all($filters);

        Response::json([
            'status' => 'success',
            'count' => count($products),
            'data' => $products
        ]);
    }

    /**
     * Halaman Admin: Monitoring Produk & Manajemen Stok
     */
    public function adminMonitoring(): void
    {
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            Response::redirect('?page=dashboard');
            return;
        }

        $categories = Category::all();
        $categoryFilter = Security::sanitize($_GET['category'] ?? '');
        $searchFilter = Security::sanitize($_GET['search'] ?? '');
        $sortFilter = Security::sanitize($_GET['sort'] ?? 'newest');

        $products = Product::all([
            'category' => $categoryFilter,
            'search' => $searchFilter,
            'sort' => $sortFilter,
        ]);

        $pageTitle = 'Monitoring Produk & Stok - Admin Aksara';
        $activeTab = 'admin_products';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/admin-products.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }

    /**
     * API Admin: Update Stok Produk secara spesifik
     */
    public function apiUpdateStock(): void
    {
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            Response::error('Akses ditolak. Fitur ini hanya untuk pengelola/admin.', 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productId = (int)($input['product_id'] ?? 0);
        $newStock = isset($input['stock']) ? (int)$input['stock'] : null;

        if ($productId <= 0 || $newStock === null || $newStock < 0) {
            Response::error('ID produk atau jumlah stok tidak valid.', 422);
            return;
        }

        $success = Product::updateStock($productId, $newStock);
        if ($success) {
            Response::success('Stok produk berhasil diperbarui!', [
                'product_id' => $productId,
                'stock' => $newStock
            ]);
        } else {
            Response::error('Gagal memperbarui stok di database.', 500);
        }
    }

    /**
     * API Admin: Tambah / Kurangi Stok Produk (+1, -1, +5, -5, +10, -10)
     */
    public function apiAdjustStock(): void
    {
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            Response::error('Akses ditolak. Fitur ini hanya untuk pengelola/admin.', 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productId = (int)($input['product_id'] ?? 0);
        $delta = (int)($input['delta'] ?? 0);

        if ($productId <= 0 || $delta === 0) {
            Response::error('Parameter produk atau penyesuaian stok tidak valid.', 422);
            return;
        }

        $result = Product::adjustStock($productId, $delta);
        if ($result['success']) {
            Response::success("Stok {$result['name']} berhasil diubah menjadi {$result['current_stock']}!", [
                'product_id' => $productId,
                'stock' => $result['current_stock']
            ]);
        } else {
            Response::error($result['error'] ?? 'Gagal menyesuaikan stok.', 500);
        }
    }
}
