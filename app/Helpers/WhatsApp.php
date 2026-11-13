<?php
// app/Helpers/WhatsApp.php
namespace App\Helpers;

use App\Config\App;

class WhatsApp
{
    /**
     * Generate format teks pesanan terstruktur untuk kurir dengan Peta Maps & Bukti Bayar
     */
    public static function formatOrderMessage(array $order, array $items): string
    {
        $dateFormatted = date('d F Y, H:i', strtotime($order['created_at'] ?? 'now')) . ' WITA';
        $subtotal = number_format($order['subtotal'], 0, ',', '.');
        $shipping = number_format($order['shipping_fee'], 0, ',', '.');
        $handling = number_format($order['service_fee'] ?? 0, 0, ',', '.');
        $total = number_format($order['total_amount'], 0, ',', '.');
        $distance = isset($order['delivery_distance']) ? number_format((float)$order['delivery_distance'], 1, ',', '.') : '5,0';

        $totalPcs = 0;
        foreach ($items as $it) {
            $totalPcs += (int)($it['quantity'] ?? 1);
        }

        $mapsUrl = $order['maps_url'] ?? '';
        if (empty($mapsUrl) && !empty($order['customer_address'])) {
            $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($order['customer_address'] . ', Majene');
        }

        $proofStatus = !empty($order['payment_proof']) 
            ? "SUDAH DIUNGGAH VIA WEB" 
            : ($order['payment_method'] === 'COD' ? "BAYAR TUNAI SAAT SAMPAI (COD)" : "MENUNGGU BUKTI PEMBAYARAN");

        $message = "=========================================\n";
        $message .= "📦 PESANAN BARU - AKSARA UNSULBAR\n";
        $message .= "=========================================\n";
        $message .= "Kode Pesanan: #{$order['order_code']}\n";
        $message .= "Waktu Pemesanan: {$dateFormatted}\n\n";

        $message .= "👤 DATA PEMBELI:\n";
        $message .= "- Nama: {$order['customer_name']}\n";
        $message .= "- Kontak/WA: {$order['customer_phone']}\n";
        $message .= "- Alamat Lengkap: {$order['customer_address']}\n";
        if (!empty($order['notes'])) {
            $message .= "- Catatan: {$order['notes']}\n";
        }
        $message .= "🗺️ Link Google Maps (Navigasi Kurir):\n{$mapsUrl}\n\n";

        $message .= "🛒 RINCIAN PRODUK:\n";
        $i = 1;
        foreach ($items as $item) {
            $itemSubtotal = number_format($item['subtotal'], 0, ',', '.');
            $unitPrice = number_format($item['unit_price'], 0, ',', '.');
            $message .= "{$i}. {$item['product_name']} ({$item['quantity']}x @ Rp {$unitPrice}) = Rp {$itemSubtotal}\n";
            $i++;
        }

        $message .= "\n💰 RINCIAN LENGKAP STRUK PEMBAYARAN:\n";
        $message .= "- Subtotal Produk ({$totalPcs} pcs): Rp {$subtotal}\n";
        $message .= "- Biaya Pengantaran Kurir (Jarak: {$distance} km): Rp {$shipping}\n";
        $message .= "  (5 km pertama Rp 15.000, km selanjutnya +Rp 2.000/km)\n";
        $message .= "- Biaya Penanganan ({$totalPcs} pcs @ Rp 2.000): Rp {$handling}\n";
        $message .= "- TOTAL PEMBAYARAN: Rp {$total}\n";
        $message .= "- Metode Pembayaran: " . strtoupper($order['payment_method']) . "\n";
        $message .= "- Status Bukti Bayar: {$proofStatus}\n";
        $message .= "- Status Transaksi: " . strtoupper($order['payment_status'] ?? 'MENUNGGU') . "\n\n";

        $message .= "🚀 Status Pengantaran: MENUNGGU DISPATCH KURIR\n";
        $message .= "Silakan klik link Google Maps di atas untuk rute navigasi langsung. Terima kasih!\n";
        $message .= "=========================================";

        return $message;
    }

    /**
     * Buat URL wa.me aman terenkripsi untuk pemicu langsung dari browser
     */
    public static function buildCourierLink(string $phoneNumber, array $order, array $items): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $text = self::formatOrderMessage($order, $items);
        return "https://api.whatsapp.com/send?phone={$phone}&text=" . rawurlencode($text);
    }
}
