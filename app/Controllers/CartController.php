<?php
// app/Controllers/CartController.php
namespace App\Controllers;

use App\Config\App;
use App\Helpers\Response;
use App\Helpers\Security;
use App\Models\Product;
use App\Models\User;

class CartController
{
    private static function getCart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private static function saveCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    public function viewCart(): void
    {
        $cartSummary = $this->calculateSummary();
        $deliveryAddress = App::getDeliveryAddress();

        $pageTitle = 'Keranjang Belanja - Aksara Unsulbar';
        $activeTab = 'cart';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/cart.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }

    public function viewCheckout(): void
    {
        $cartSummary = $this->calculateSummary();

        // Jika keranjang kosong, redirect ke katalog
        if (empty($cartSummary['items'])) {
            Response::redirect('?page=catalog');
            return;
        }

        $deliveryAddress = App::getDeliveryAddress();
        $pageTitle = 'Checkout & Pembayaran - Aksara Unsulbar';
        $activeTab = 'cart';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/checkout.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }

    /**
     * Hitung total belanjaan dan verifikasi harga dari database (Anti-tampering)
     * Termasuk rumus ongkir dinamis (5 km pertama Rp 15.000, +Rp 2.000/km)
     * dan biaya penanganan Rp 2.000 per pcs untuk seluruh metode bayar
     */
    public function calculateSummary(?float $customDistance = null): array
    {
        $cart = self::getCart();
        $items = [];
        $subtotal = 0;
        $totalItems = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find((int)$productId);
            if (!$product) continue;

            $qty = max(1, min((int)$item['quantity'], $product['stock']));
            $itemSubtotal = $product['price'] * $qty;
            $subtotal += $itemSubtotal;
            $totalItems += $qty;

            $items[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'unit' => $product['unit'],
                'image_url' => $product['image_url'],
                'stock' => $product['stock'],
                'quantity' => $qty,
                'subtotal' => $itemSubtotal,
            ];
        }

        $deliveryAddress = App::getDeliveryAddress();
        $distance = $customDistance ?? (float)($deliveryAddress['distance'] ?? App::BASE_SHIPPING_DISTANCE);
        if ($distance <= 0) {
            $distance = App::BASE_SHIPPING_DISTANCE;
        }

        // Rumus Ongkir: 5 km pertama Rp 15.000, km selanjutnya +Rp 2.000/km
        $shippingFee = empty($items) ? 0 : App::calculateShippingFee($distance, $totalItems);

        // Rumus Biaya Penanganan: Rp 2.000 per pcs (semua metode pembayaran)
        $handlingFee = empty($items) ? 0 : App::calculateHandlingFee($totalItems);

        $total = $subtotal + $shippingFee + $handlingFee;

        return [
            'items' => $items,
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'distance' => $distance,
            'shipping_fee' => $shippingFee,
            'service_fee' => $handlingFee, // Biaya penanganan
            'handling_fee' => $handlingFee,
            'handling_fee_per_pcs' => App::HANDLING_FEE_PER_PCS,
            'total_amount' => $total,
        ];
    }

    /**
     * API: Tambah produk ke keranjang
     */
    public function apiAdd(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = max(1, (int)($input['quantity'] ?? 1));

        $product = Product::find($productId);
        if (!$product) {
            Response::error('Produk tidak ditemukan.', 404);
            return;
        }

        if ($product['stock'] < 1) {
            Response::error('Maaf, stok produk ini sedang habis.', 400);
            return;
        }

        $cart = self::getCart();
        $currentQty = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $product['stock']) {
            Response::error("Stok tidak mencukupi. Maksimal pesanan: {$product['stock']} {$product['unit']}.", 400);
            return;
        }

        $cart[$productId] = [
            'product_id' => $productId,
            'quantity' => $newQty
        ];

        self::saveCart($cart);
        $summary = $this->calculateSummary();

        Response::success("{$product['name']} berhasil ditambahkan ke keranjang!", [
            'cart_count' => $summary['total_items'],
            'summary' => $summary,
            'product' => [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url']
            ]
        ]);
    }

    /**
     * API: Update kuantitas produk dalam keranjang
     */
    public function apiUpdate(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 1);

        $cart = self::getCart();
        if (!isset($cart[$productId])) {
            Response::error('Item tidak ada di keranjang.', 404);
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::find($productId);
            if (!$product || $quantity > $product['stock']) {
                Response::error("Kuantitas melebihi batas stok tersedia ({$product['stock']}).", 400);
                return;
            }
            $cart[$productId]['quantity'] = $quantity;
        }

        self::saveCart($cart);
        $summary = $this->calculateSummary();

        Response::success('Keranjang berhasil diperbarui.', [
            'cart_count' => $summary['total_items'],
            'summary' => $summary
        ]);
    }

    /**
     * API: Hapus produk dari keranjang
     */
    public function apiRemove(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productId = (int)($input['product_id'] ?? 0);

        $cart = self::getCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            self::saveCart($cart);
        }

        $summary = $this->calculateSummary();

        Response::success('Produk berhasil dihapus dari keranjang.', [
            'cart_count' => $summary['total_items'],
            'summary' => $summary
        ]);
    }

    /**
     * API: Ambil status ringkasan keranjang saat ini
     */
    public function apiGet(): void
    {
        $summary = $this->calculateSummary();
        $deliveryAddress = App::getDeliveryAddress();
        Response::json([
            'status' => 'success',
            'data' => $summary,
            'delivery_address' => $deliveryAddress
        ]);
    }

    /**
     * API: Simpan / Perbarui Alamat Pengantaran Fleksibel ala E-Commerce
     */
    public function apiSaveAddress(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $label = Security::sanitize($input['label'] ?? 'Rumah');
        $recipientName = Security::sanitize($input['recipient_name'] ?? '');
        $phone = Security::sanitize($input['phone'] ?? '');
        $city = Security::sanitize($input['city'] ?? '');
        $address = Security::sanitize($input['address'] ?? '');
        $benchmark = Security::sanitize($input['benchmark'] ?? '');
        $mapsUrl = Security::sanitize($input['maps_url'] ?? '');
        $notes = Security::sanitize($input['notes'] ?? '');
        $lat = Security::sanitize($input['lat'] ?? '');
        $lng = Security::sanitize($input['lng'] ?? '');
        $distance = isset($input['distance']) && is_numeric($input['distance']) ? (float)$input['distance'] : null;
        $saveToProfile = !empty($input['save_to_profile']);

        if (empty($address)) {
            Response::error('Alamat pengantaran tidak boleh kosong.', 422);
            return;
        }

        // Jika distance belum diset tapi ada lat & lng, hitung otomatis
        if (($distance === null || $distance <= 0) && is_numeric($lat) && is_numeric($lng)) {
            $distance = App::calculateDistance(App::ORIGIN_LAT, App::ORIGIN_LNG, (float)$lat, (float)$lng);
        }
        if ($distance === null || $distance <= 0) {
            $distance = App::BASE_SHIPPING_DISTANCE;
        }

        // Jika mapsUrl kosong tapi ada lat & lng, buat link maps presisi
        if (empty($mapsUrl) && !empty($lat) && !empty($lng)) {
            $mapsUrl = "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}";
        } elseif (empty($mapsUrl)) {
            $searchQuery = trim($address . ($city ? ', ' . $city : '') . ', Sulawesi Barat');
            $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($searchQuery);
        }

        $addressData = [
            'label' => $label,
            'recipient_name' => $recipientName,
            'phone' => $phone,
            'city' => $city,
            'address' => $address,
            'benchmark' => $benchmark,
            'maps_url' => $mapsUrl,
            'notes' => $notes,
            'lat' => $lat,
            'lng' => $lng,
            'distance' => $distance
        ];

        App::setDeliveryAddress($addressData);

        // Jika user login dan opsi simpan profil aktif (atau default login), simpan permanen ke database users
        $currentUser = $_SESSION['user'] ?? null;
        if (!empty($currentUser['id'])) {
            $fullAddressProfile = $address;
            if ($city && !str_contains($address, $city)) {
                $fullAddressProfile .= ' (' . $city . ')';
            }
            if ($benchmark) {
                $fullAddressProfile .= ' - Patokan: ' . $benchmark;
            }

            User::updateProfile((int)$currentUser['id'], [
                'full_name' => $recipientName ?: $currentUser['full_name'],
                'phone' => $phone ?: ($currentUser['phone'] ?? ''),
                'address' => $fullAddressProfile,
                'maps_url' => $mapsUrl
            ]);

            $_SESSION['user']['address'] = $fullAddressProfile;
            $_SESSION['user']['maps_url'] = $mapsUrl;
            if ($phone) {
                $_SESSION['user']['phone'] = $phone;
            }
        }

        $newSummary = $this->calculateSummary($distance);

        Response::success('Alamat pengantaran berhasil disinkronkan!', [
            'address' => $addressData,
            'summary' => $newSummary
        ]);
    }
}
