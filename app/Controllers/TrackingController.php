<?php
// app/Controllers/TrackingController.php
namespace App\Controllers;

use App\Helpers\Security;
use App\Models\Order;

class TrackingController
{
    public function view(): void
    {
        $orderCode = Security::sanitize($_GET['code'] ?? '');
        $order = null;
        $orderItems = [];
        $orderLogs = [];
        $userOrders = [];

        if (!empty($orderCode)) {
            $order = Order::findByCode($orderCode);
            if ($order) {
                $orderItems = Order::getItems($order['id']);
                $orderLogs = Order::getLogs($order['id']);
            }
        }

        // Jika user sedang login, muat riwayat pesanannya
        if (!empty($_SESSION['user']['id'])) {
            $userOrders = Order::byUser((int)$_SESSION['user']['id']);
        }

        $pageTitle = 'Pelacakan Status Pesanan - Aksara Unsulbar';
        $activeTab = 'tracking';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/tracking.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }
}
