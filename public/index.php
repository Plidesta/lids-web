<?php
// public/index.php - Front Controller & Core Router
declare(strict_types=1);

// Definisi Base Directory
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Autoloader sederhana untuk namespace App
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Inisialisasi Sesi & Proteksi HTTP Security Headers
\App\Config\App::init();

// Routing Berdasarkan Parameter `action` (API/POST) atau `page` (Tampilan HTML)
$action = $_GET['action'] ?? null;
$page = $_GET['page'] ?? 'dashboard';

try {
    if ($action) {
        switch ($action) {
            case 'api/products':
                (new \App\Controllers\ProductController())->apiList();
                break;
            case 'admin/update-stock':
                (new \App\Controllers\ProductController())->apiUpdateStock();
                break;
            case 'admin/adjust-stock':
                (new \App\Controllers\ProductController())->apiAdjustStock();
                break;
            case 'cart/add':
                (new \App\Controllers\CartController())->apiAdd();
                break;
            case 'cart/update':
                (new \App\Controllers\CartController())->apiUpdate();
                break;
            case 'cart/remove':
                (new \App\Controllers\CartController())->apiRemove();
                break;
            case 'cart/get':
                (new \App\Controllers\CartController())->apiGet();
                break;
            case 'cart/save-address':
                (new \App\Controllers\CartController())->apiSaveAddress();
                break;
            case 'order/checkout':
                (new \App\Controllers\OrderController())->processCheckout();
                break;
            case 'order/upload-proof':
                (new \App\Controllers\OrderController())->apiUploadProof();
                break;
            case 'order/update-status':
                (new \App\Controllers\OrderController())->apiUpdateStatus();
                break;
            case 'auth/login':
                (new \App\Controllers\AuthController())->login();
                break;
            case 'auth/register':
                (new \App\Controllers\AuthController())->register();
                break;
            case 'auth/logout':
                (new \App\Controllers\AuthController())->logout();
                break;
            default:
                \App\Helpers\Response::error('Aksi tidak ditemukan.', 404);
                break;
        }
        exit;
    }

    // Routing Halaman (Pages)
    switch ($page) {
        case 'catalog':
        case 'pemesanan':
            (new \App\Controllers\ProductController())->catalog();
            break;
        case 'cart':
        case 'keranjang':
            (new \App\Controllers\CartController())->viewCart();
            break;
        case 'checkout':
            (new \App\Controllers\CartController())->viewCheckout();
            break;
        case 'tracking':
        case 'status':
            (new \App\Controllers\TrackingController())->view();
            break;
        case 'admin-products':
        case 'monitoring-produk':
        case 'monitoring':
            (new \App\Controllers\ProductController())->adminMonitoring();
            break;
        case 'admin-orders':
        case 'admin':
            (new \App\Controllers\OrderController())->adminOrders();
            break;
        case 'contact':
        case 'kontak':
            (new \App\Controllers\ContactController())->view();
            break;
        case 'dashboard':
        case 'home':
        default:
            (new \App\Controllers\HomeController())->index();
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<div style="font-family: sans-serif; padding: 2rem; max-width: 600px; margin: 40px auto; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px;">';
    echo '<h2 style="color: #9f1239; margin-top: 0;">Terjadi Kesalahan Sistem</h2>';
    echo '<p style="color: #4c0519;">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<a href="?page=dashboard" style="display: inline-block; padding: 8px 16px; background: #9f1239; color: #fff; text-decoration: none; border-radius: 4px;">Kembali ke Dashboard</a>';
    echo '</div>';
}
