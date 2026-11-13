<?php
// app/Controllers/OrderController.php
namespace App\Controllers;

use App\Config\App;
use App\Helpers\Response;
use App\Helpers\Security;
use App\Helpers\WhatsApp;
use App\Models\Order;
use App\Models\User;

class OrderController
{
    /**
     * Memproses pesanan checkout dari formulir (Mendukung Form Data & File Upload)
     */
    public function processCheckout(): void
    {
        $input = $_POST;
        if (empty($input)) {
            $raw = file_get_contents('php://input');
            $input = json_decode($raw, true) ?? [];
        }

        // 1. Verifikasi Anti-CSRF Token
        $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Security::validateCsrf($csrfToken)) {
            Response::error('Sesi tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman.', 403);
            return;
        }

        // 2. Validasi Input Pengguna
        $customerName = Security::sanitize($input['customer_name'] ?? '');
        $customerPhone = Security::sanitize($input['customer_phone'] ?? '');
        $customerAddress = Security::sanitize($input['customer_address'] ?? '');
        $mapsUrl = Security::sanitize($input['maps_url'] ?? '');
        $notes = Security::sanitize($input['notes'] ?? '');
        $paymentMethod = strtoupper(Security::sanitize($input['payment_method'] ?? 'COD'));

        if (empty($customerName)) {
            Response::error('Nama penerima wajib diisi.', 422);
            return;
        }

        if (empty($customerPhone) || strlen($customerPhone) < 8) {
            Response::error('Nomor telepon/WhatsApp aktif wajib diisi dengan benar.', 422);
            return;
        }

        if (empty($customerAddress) || strlen($customerAddress) < 5) {
            Response::error('Alamat pengantaran wajib diisi dengan jelas.', 422);
            return;
        }

        $allowedPayments = ['QRIS', 'COD', 'TRANSFER'];
        if (!in_array($paymentMethod, $allowedPayments, true)) {
            Response::error('Metode pembayaran tidak valid.', 422);
            return;
        }

        // 3. Tangani Upload Bukti Pembayaran (jika ada file terlampir saat checkout)
        $paymentProofPath = null;
        if (!empty($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadedPath = $this->handleProofUpload($_FILES['payment_proof']);
            if ($uploadedPath) {
                $paymentProofPath = $uploadedPath;
            }
        }

        // 4. Ambil isi keranjang belanja dari sesi
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            Response::error('Keranjang belanja Anda kosong.', 400);
            return;
        }

        $userId = $_SESSION['user']['id'] ?? null;

        // Jika user login dan centang simpan alamat, update di database
        if ($userId && !empty($input['save_address_to_profile'])) {
            User::updateAddress((int)$userId, $customerAddress, $mapsUrl);
            $_SESSION['user']['address'] = $customerAddress;
            $_SESSION['user']['maps_url'] = $mapsUrl;
        }

        $distanceInput = isset($input['delivery_distance']) && is_numeric($input['delivery_distance']) ? (float)$input['delivery_distance'] : null;

        // Simpan juga ke session delivery address agar selalu tersinkron
        App::setDeliveryAddress([
            'label' => Security::sanitize($input['label'] ?? 'Rumah'),
            'recipient_name' => $customerName,
            'phone' => $customerPhone,
            'city' => Security::sanitize($input['city'] ?? ''),
            'address' => $customerAddress,
            'benchmark' => Security::sanitize($input['benchmark'] ?? ''),
            'maps_url' => $mapsUrl,
            'notes' => $notes,
            'lat' => Security::sanitize($input['delivery_lat'] ?? ''),
            'lng' => Security::sanitize($input['delivery_lng'] ?? ''),
            'distance' => $distanceInput
        ]);

        $orderData = [
            'user_id' => $userId,
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'customer_address' => $customerAddress,
            'maps_url' => $mapsUrl,
            'delivery_lat' => !empty($input['delivery_lat']) ? (float)$input['delivery_lat'] : null,
            'delivery_lng' => !empty($input['delivery_lng']) ? (float)$input['delivery_lng'] : null,
            'delivery_distance' => $distanceInput,
            'notes' => $notes,
            'payment_method' => $paymentMethod,
            'payment_proof' => $paymentProofPath,
        ];

        // 5. Buat pesanan secara aman di sisi server (Atomic Transaction)
        $result = Order::create($orderData, $cart);

        if (!$result['success']) {
            Response::error($result['error'] ?? 'Terjadi kesalahan saat memproses pesanan.', 500);
            return;
        }

        // 6. Kosongkan keranjang belanja setelah sukses
        unset($_SESSION['cart']);

        // 7. Buat tautan WhatsApp otomatis untuk diteruskan ke Kurir Unsulbar
        $courierPhone = App::COURIER_PHONE;
        $waLink = WhatsApp::buildCourierLink($courierPhone, $result, $result['items']);

        // 8. Siapkan detail pembayaran QRIS jika dipilih
        $qrisData = null;
        if ($paymentMethod === 'QRIS') {
            $qrisData = [
                'merchant' => 'Aksara Unsulbar Berdampak',
                'nmid' => 'ID1020261028001',
                'amount' => $result['total_amount'],
                'qr_code_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . rawurlencode("00020101021226670014ID.LINKAJA.WWW01189360091100202610280215ID10202610280010303UMI51440014ID.CO.QRIS.WWW0215ID10202610280010303UMI520454115303360540" . strlen($result['total_amount']) . $result['total_amount'] . "5802ID5916Aksara Unsulbar6006Majene61059141262070703A016304"),
            ];
        }

        Response::success('Pesanan berhasil dibuat!', [
            'order' => $result,
            'whatsapp_url' => $waLink,
            'qris_data' => $qrisData,
            'bank_accounts' => App::BANK_ACCOUNTS,
            'tracking_url' => '?page=tracking&code=' . urlencode($result['order_code']),
        ]);
    }

    /**
     * Endpoint API: Upload Bukti Pembayaran susulan (dari halaman pelacakan atau modal)
     */
    public function apiUploadProof(): void
    {
        $orderCode = Security::sanitize($_POST['order_code'] ?? '');
        if (empty($orderCode)) {
            Response::error('Kode pesanan tidak ditemukan.', 422);
            return;
        }

        $order = Order::findByCode($orderCode);
        if (!$order) {
            Response::error('Pesanan tidak ditemukan.', 404);
            return;
        }

        if (empty($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
            Response::error('Silakan pilih file bukti pembayaran yang valid (JPG, PNG, atau PDF).', 422);
            return;
        }

        $uploadedPath = $this->handleProofUpload($_FILES['payment_proof'], $order['order_code']);
        if (!$uploadedPath) {
            Response::error('Gagal mengunggah file bukti. Pastikan format file JPG/PNG/PDF dan ukuran maks. 5MB.', 400);
            return;
        }

        $success = Order::updatePaymentProof((int)$order['id'], $uploadedPath);
        if (!$success) {
            Response::error('Gagal memperbarui data pesanan.', 500);
            return;
        }

        Response::success('Bukti pembayaran berhasil diunggah! Status pembayaran Anda telah diperbarui.', [
            'proof_url' => $uploadedPath
        ]);
    }

    /**
     * Helper untuk memvalidasi dan menyimpan bukti pembayaran ke public/uploads/proofs/
     */
    private function handleProofUpload(array $file, string $orderCode = ''): ?string
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        $maxSize = 5 * 1024 * 1024; // 5 MB

        if ($file['size'] > $maxSize) {
            return null;
        }

        $fileName = $file['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return null;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/proofs';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $orderCode ?: 'order');
        $newFileName = 'proof_' . $safeCode . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destination = $uploadDir . '/' . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/proofs/' . $newFileName;
        }

        return null;
    }

    /**
     * Admin: Update Status Pesanan (PENDING -> PROCESSED -> SHIPPING -> COMPLETED)
     */
    public function apiUpdateStatus(): void
    {
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            Response::error('Akses ditolak. Fitur ini hanya untuk pengelola/admin.', 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $orderId = (int)($input['order_id'] ?? 0);
        $newStatus = strtoupper(trim($input['order_status'] ?? ''));
        $paymentStatus = isset($input['payment_status']) ? strtoupper(trim($input['payment_status'])) : null;
        $notes = trim($input['notes'] ?? '');

        $allowedStatuses = ['PENDING', 'PROCESSED', 'SHIPPING', 'COMPLETED'];
        if (!in_array($newStatus, $allowedStatuses, true)) {
            Response::error('Status tidak valid.', 422);
            return;
        }

        $success = Order::updateStatus($orderId, $newStatus, $paymentStatus, $notes);
        if ($success) {
            Response::success('Status pesanan berhasil diperbarui.', [
                'order_status' => $newStatus,
                'payment_status' => $paymentStatus
            ]);
        } else {
            Response::error('Gagal memperbarui status.', 500);
        }
    }

    /**
     * Halaman Admin: Kelola Seluruh Pesanan
     */
    public function adminOrders(): void
    {
        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || ($currentUser['role'] ?? '') !== 'admin') {
            Response::redirect('?page=dashboard');
            return;
        }

        $statusFilter = Security::sanitize($_GET['status'] ?? '');
        $searchFilter = Security::sanitize($_GET['search'] ?? '');

        $orders = Order::all([
            'status' => $statusFilter,
            'search' => $searchFilter
        ]);

        $pageTitle = 'Manajemen Pesanan Masuk - Admin Aksara';
        $activeTab = 'admin';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/admin-orders.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }
}
