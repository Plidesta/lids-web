<?php
// app/Models/Order.php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Security;
use Exception;
use PDO;

class Order
{
    /**
     * Buat pesanan secara atomik dengan validasi harga dan stok server-side ketat
     */
    public static function create(array $orderData, array $cartItems): array
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Validasi ulang harga dan ketersediaan stok produk dari database
            $verifiedItems = [];
            $subtotal = 0;

            foreach ($cartItems as $productId => $item) {
                $qty = (int)($item['quantity'] ?? 1);
                if ($qty <= 0) continue;

                $product = Product::find((int)$productId);
                if (!$product) {
                    throw new Exception("Produk dengan ID {$productId} tidak ditemukan.");
                }

                if ($product['stock'] < $qty) {
                    throw new Exception("Stok untuk {$product['name']} tidak mencukupi (sisa: {$product['stock']}).");
                }

                // Pengurangan stok secara atomik
                $reduced = Product::reduceStock((int)$productId, $qty, $pdo);
                if (!$reduced) {
                    throw new Exception("Gagal memotong stok untuk {$product['name']}. Silakan coba lagi.");
                }

                $itemSubtotal = $product['price'] * $qty;
                $subtotal += $itemSubtotal;

                $verifiedItems[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'unit_price' => $product['price'],
                    'quantity' => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            if (empty($verifiedItems)) {
                throw new Exception("Keranjang belanja kosong atau produk tidak valid.");
            }

            // 2. Kalkulasi biaya pengiriman & penanganan di server (Anti-tampering)
            $totalItemsCount = 0;
            foreach ($verifiedItems as $vi) {
                $totalItemsCount += (int)$vi['quantity'];
            }

            $distance = isset($orderData['delivery_distance']) && is_numeric($orderData['delivery_distance'])
                ? (float)$orderData['delivery_distance']
                : null;

            if (($distance === null || $distance <= 0) && !empty($orderData['delivery_lat']) && !empty($orderData['delivery_lng'])) {
                $distance = \App\Config\App::calculateDistance(
                    \App\Config\App::ORIGIN_LAT,
                    \App\Config\App::ORIGIN_LNG,
                    (float)$orderData['delivery_lat'],
                    (float)$orderData['delivery_lng']
                );
            }
            if ($distance === null || $distance <= 0) {
                $distance = \App\Config\App::BASE_SHIPPING_DISTANCE;
            }

            // Rumus Ongkir: 5 km pertama Rp 15.000, km selanjutnya +Rp 2.000/km
            $shippingFee = \App\Config\App::calculateShippingFee($distance, $totalItemsCount);

            // Rumus Biaya Penanganan: Rp 2.000 per pcs (semua metode pembayaran)
            $serviceFee = \App\Config\App::calculateHandlingFee($totalItemsCount);

            $totalAmount = $subtotal + $shippingFee + $serviceFee;
            $orderCode = Security::generateOrderCode();

            $mapsUrl = $orderData['maps_url'] ?? '';
            if (empty($mapsUrl) && !empty($orderData['customer_address'])) {
                // Auto generate link Google Maps pencarian berdasarkan alamat
                $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($orderData['customer_address'] . ', Majene, Sulawesi Barat');
            }

            $paymentProof = $orderData['payment_proof'] ?? null;
            $paymentStatus = ($orderData['payment_method'] === 'COD') ? 'UNPAID' : (!empty($paymentProof) ? 'PAID' : 'PENDING');

            // Pastikan kolom baru sudah siap
            Database::ensureSchemaUpdated($pdo);

            // 3. Simpan ke tabel orders
            $stmtOrder = $pdo->prepare("INSERT INTO orders (
                order_code, user_id, customer_name, customer_phone, customer_address, 
                maps_url, delivery_lat, delivery_lng, delivery_distance, notes, payment_method, 
                payment_status, order_status, payment_proof, payment_proof_uploaded_at,
                subtotal, shipping_fee, service_fee, total_amount, wa_sent_at, created_at
            ) VALUES (
                :order_code, :user_id, :customer_name, :customer_phone, :customer_address, 
                :maps_url, :delivery_lat, :delivery_lng, :delivery_distance, :notes, :payment_method, 
                :payment_status, :order_status, :payment_proof, :payment_proof_uploaded_at,
                :subtotal, :shipping_fee, :service_fee, :total_amount, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )");

            $stmtOrder->execute([
                ':order_code' => $orderCode,
                ':user_id' => $orderData['user_id'] ?? null,
                ':customer_name' => $orderData['customer_name'],
                ':customer_phone' => $orderData['customer_phone'],
                ':customer_address' => $orderData['customer_address'],
                ':maps_url' => $mapsUrl,
                ':delivery_lat' => $orderData['delivery_lat'] ?? null,
                ':delivery_lng' => $orderData['delivery_lng'] ?? null,
                ':delivery_distance' => $distance,
                ':notes' => $orderData['notes'] ?? '',
                ':payment_method' => $orderData['payment_method'],
                ':payment_status' => $paymentStatus,
                ':order_status' => 'PENDING',
                ':payment_proof' => $paymentProof,
                ':payment_proof_uploaded_at' => !empty($paymentProof) ? date('Y-m-d H:i:s') : null,
                ':subtotal' => $subtotal,
                ':shipping_fee' => $shippingFee,
                ':service_fee' => $serviceFee,
                ':total_amount' => $totalAmount,
            ]);

            $orderId = (int)$pdo->lastInsertId();

            // 4. Simpan masing-masing item ke order_items
            $stmtItem = $pdo->prepare("INSERT INTO order_items (
                order_id, product_id, product_name, unit_price, quantity, subtotal
            ) VALUES (
                :order_id, :product_id, :product_name, :unit_price, :quantity, :subtotal
            )");

            foreach ($verifiedItems as $item) {
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':product_name' => $item['product_name'],
                    ':unit_price' => $item['unit_price'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $item['subtotal'],
                ]);
            }

            // 5. Tambah log awal ke order_logs
            $logNote = "Pesanan baru berhasil dibuat via {$orderData['payment_method']}.";
            if (!empty($paymentProof)) {
                $logNote .= " Bukti pembayaran berhasil diunggah.";
            } else {
                $logNote .= " Menunggu bukti pembayaran / kurir.";
            }

            $stmtLog = $pdo->prepare("INSERT INTO order_logs (order_id, status_from, status_to, notes) 
                                      VALUES (:order_id, NULL, 'PENDING', :notes)");
            $stmtLog->execute([
                ':order_id' => $orderId,
                ':notes' => $logNote,
            ]);

            $pdo->commit();

            return [
                'success' => true,
                'order_id' => $orderId,
                'order_code' => $orderCode,
                'subtotal' => $subtotal,
                'delivery_distance' => $distance,
                'shipping_fee' => $shippingFee,
                'service_fee' => $serviceFee,
                'total_items' => $totalItemsCount,
                'total_amount' => $totalAmount,
                'customer_name' => $orderData['customer_name'],
                'customer_phone' => $orderData['customer_phone'],
                'customer_address' => $orderData['customer_address'],
                'maps_url' => $mapsUrl,
                'payment_method' => $orderData['payment_method'],
                'payment_status' => $paymentStatus,
                'order_status' => 'PENDING',
                'payment_proof' => $paymentProof,
                'created_at' => date('Y-m-d H:i:s'),
                'items' => $verifiedItems,
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public static function findByCode(string $orderCode): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = :code LIMIT 1");
        $stmt->execute([':code' => trim($orderCode)]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public static function getItems(int $orderId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT oi.*, p.image_url 
                               FROM order_items oi 
                               LEFT JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public static function getLogs(int $orderId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM order_logs WHERE order_id = :order_id ORDER BY id ASC");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public static function byUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function all(array $filters = []): array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT o.*, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND o.order_status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_code LIKE :search OR o.customer_name LIKE :search OR o.customer_phone LIKE :search)";
            $params[':search'] = '%' . trim($filters['search']) . '%';
        }

        $sql .= " ORDER BY o.id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updatePaymentProof(int $orderId, string $proofPath): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE orders SET payment_proof = :proof, payment_proof_uploaded_at = CURRENT_TIMESTAMP, payment_status = 'PAID' WHERE id = :id");
        $success = $stmt->execute([
            ':proof' => $proofPath,
            ':id' => $orderId
        ]);

        if ($success) {
            $stmtLog = $pdo->prepare("INSERT INTO order_logs (order_id, status_from, status_to, notes) VALUES (:order_id, 'PENDING', 'PENDING', 'Bukti bayar baru diunggah oleh pembeli.')");
            $stmtLog->execute([':order_id' => $orderId]);
        }

        return $success;
    }

    public static function updateStatus(int $orderId, string $newStatus, ?string $paymentStatus = null, ?string $notes = null): bool
    {
        $pdo = Database::getConnection();
        $order = self::find($orderId);
        if (!$order) return false;

        $oldStatus = $order['order_status'];

        $sql = "UPDATE orders SET order_status = :order_status";
        $params = [
            ':order_status' => $newStatus,
            ':id' => $orderId
        ];

        if ($paymentStatus !== null) {
            $sql .= ", payment_status = :payment_status";
            $params[':payment_status'] = $paymentStatus;
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($params);

        if ($success) {
            $logNote = $notes ?: "Status pesanan diubah dari {$oldStatus} ke {$newStatus}.";
            $stmtLog = $pdo->prepare("INSERT INTO order_logs (order_id, status_from, status_to, notes) VALUES (:order_id, :from, :to, :notes)");
            $stmtLog->execute([
                ':order_id' => $orderId,
                ':from' => $oldStatus,
                ':to' => $newStatus,
                ':notes' => $logNote
            ]);
        }

        return $success;
    }
}
