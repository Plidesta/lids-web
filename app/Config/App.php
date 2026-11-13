<?php
// app/Config/App.php
namespace App\Config;

class App
{
    public const APP_NAME = 'Aksara Mahasiswa Berdampak';
    public const INSTANCE = 'Universitas Sulawesi Barat';
    public const APP_VERSION = '2.0.0';
    public const DEFAULT_CURRENCY = 'Rp';
    public const COURIER_PHONE = '6282190001234'; // Default Hotline Kurir Unsulbar
    public const ORIGIN_LAT = -3.4116417; // Koordinat Sentra Produksi Desa Papandangan, Kec. Anreapi, Kab. Polewali Mandar
    public const ORIGIN_LNG = 119.342125;
    public const BASE_SHIPPING_DISTANCE = 5.0; // 5 km pertama
    public const BASE_SHIPPING_FEE = 15000; // Tarif 5 km pertama Rp 15.000
    public const EXTRA_SHIPPING_FEE_PER_KM = 2000; // Tambahan per km Rp 2.000
    public const HANDLING_FEE_PER_PCS = 2000; // Biaya penanganan per pcs Rp 2.000 untuk semua metode bayar

    public const BANK_ACCOUNTS = [
        'BRI' => [
            'bank' => 'Bank BRI',
            'account_number' => '0234-01-002938-53-1',
            'account_name' => 'Aksara Berdampak Unsulbar'
        ],
        'BSI' => [
            'bank' => 'Bank Syariah Indonesia (BSI)',
            'account_number' => '7182930415',
            'account_name' => 'Aksara Mahasiswa Berdampak'
        ],
        'MANDIRI' => [
            'bank' => 'Bank Mandiri',
            'account_number' => '152-00-1928374-6',
            'account_name' => 'Aksara Unsulbar'
        ]
    ];

    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionDir = dirname(__DIR__, 2) . '/database/sessions';
            if (!is_dir($sessionDir)) {
                @mkdir($sessionDir, 0777, true);
            }
            if (is_dir($sessionDir) && is_writable($sessionDir)) {
                @session_save_path($sessionDir);
            }

            if (!headers_sent()) {
                session_set_cookie_params([
                    'lifetime' => 86400 * 7,
                    'path' => '/',
                    'domain' => '',
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
            @session_start();
        }

        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }

    public static function baseUrl(string $path = ''): string
    {
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($scriptName, '/');
        $path = ltrim($path, '/');
        return $path ? "$base/$path" : ($base ?: '/');
    }

    public static function getDeliveryAddress(): array
    {
        if (!empty($_SESSION['delivery_address'])) {
            $addr = $_SESSION['delivery_address'];
            $lat = $addr['lat'] ?? '';
            $lng = $addr['lng'] ?? '';
            $dist = isset($addr['distance']) && is_numeric($addr['distance']) ? (float)$addr['distance'] : null;

            if ($dist === null && is_numeric($lat) && is_numeric($lng)) {
                $dist = self::calculateDistance(self::ORIGIN_LAT, self::ORIGIN_LNG, (float)$lat, (float)$lng);
            }
            if ($dist === null || $dist <= 0) {
                $dist = self::BASE_SHIPPING_DISTANCE;
            }

            return [
                'label' => $addr['label'] ?? 'Alamat Pengiriman',
                'recipient_name' => $addr['recipient_name'] ?? '',
                'phone' => $addr['phone'] ?? '',
                'city' => $addr['city'] ?? 'Kab. Majene',
                'address' => $addr['address'] ?? '',
                'benchmark' => $addr['benchmark'] ?? '',
                'maps_url' => $addr['maps_url'] ?? '',
                'notes' => $addr['notes'] ?? '',
                'lat' => $lat,
                'lng' => $lng,
                'distance' => $dist
            ];
        }

        // Fallback ke profil user yang sedang login jika ada
        if (!empty($_SESSION['user']['address'])) {
            return [
                'label' => 'Alamat Rumah / Kos',
                'recipient_name' => $_SESSION['user']['full_name'] ?? '',
                'phone' => $_SESSION['user']['phone'] ?? '',
                'city' => 'Kab. Majene',
                'address' => $_SESSION['user']['address'],
                'benchmark' => '',
                'maps_url' => $_SESSION['user']['maps_url'] ?? '',
                'notes' => '',
                'lat' => '',
                'lng' => '',
                'distance' => self::BASE_SHIPPING_DISTANCE
            ];
        }

        return [
            'label' => 'Sentra Papandangan',
            'recipient_name' => '',
            'phone' => '',
            'city' => 'Kabupaten Polewali Mandar',
            'address' => 'Desa Papandangan, Kec. Anreapi, Kab. Polewali Mandar',
            'benchmark' => 'Sentra Kumbung Produksi Jamur Tiram Mahasiswa Berdampak',
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=-3.4116417,119.342125',
            'notes' => '',
            'lat' => '-3.4116417',
            'lng' => '119.342125',
            'distance' => 0.5
        ];
    }

    public static function setDeliveryAddress(array $data): void
    {
        $lat = trim($data['lat'] ?? '');
        $lng = trim($data['lng'] ?? '');
        $distance = isset($data['distance']) && is_numeric($data['distance']) ? (float)$data['distance'] : null;

        if (($distance === null || $distance <= 0) && is_numeric($lat) && is_numeric($lng)) {
            $distance = self::calculateDistance(self::ORIGIN_LAT, self::ORIGIN_LNG, (float)$lat, (float)$lng);
        }
        if ($distance === null || $distance <= 0) {
            $distance = self::BASE_SHIPPING_DISTANCE;
        }

        $_SESSION['delivery_address'] = [
            'label' => trim($data['label'] ?? 'Alamat Pengiriman'),
            'recipient_name' => trim($data['recipient_name'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
            'city' => trim($data['city'] ?? ''),
            'address' => trim($data['address'] ?? ''),
            'benchmark' => trim($data['benchmark'] ?? ''),
            'maps_url' => trim($data['maps_url'] ?? ''),
            'notes' => trim($data['notes'] ?? ''),
            'lat' => $lat,
            'lng' => $lng,
            'distance' => $distance
        ];
    }

    /**
     * Hitung jarak estimasi jalan raya antara dua titik koordinat (Haversine formula + road factor)
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // radius bumi dalam km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $straight = $earthRadius * $c;

        // Faktor jalan raya wilayah pesisir & pegunungan Majene/Sulbar (~1.2x garis lurus)
        $roadDist = $straight * 1.2;
        return max(0.1, round($roadDist, 1));
    }

    /**
     * Rumus Ongkir Resmi:
     * - 5 km pertama: Rp 15.000
     * - Untuk km selanjutnya: + Rp 2.000 / km
     */
    public static function calculateShippingFee(float $distanceKm, int $itemCount = 1): float
    {
        if ($itemCount <= 0) {
            return 0.0;
        }

        $baseDistance = self::BASE_SHIPPING_DISTANCE; // 5.0 km
        $baseFee = self::BASE_SHIPPING_FEE; // Rp 15.000
        $ratePerKm = self::EXTRA_SHIPPING_FEE_PER_KM; // Rp 2.000/km

        if ($distanceKm <= $baseDistance) {
            return (float)$baseFee;
        }

        $extraKm = ceil($distanceKm - $baseDistance);
        return (float)($baseFee + ($extraKm * $ratePerKm));
    }

    /**
     * Rumus Biaya Penanganan:
     * - Rp 2.000 per pcs untuk seluruh metode pembayaran
     */
    public static function calculateHandlingFee(int $totalItems): float
    {
        if ($totalItems <= 0) {
            return 0.0;
        }
        return (float)($totalItems * self::HANDLING_FEE_PER_PCS);
    }
}
