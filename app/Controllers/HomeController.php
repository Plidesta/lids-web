<?php
// app/Controllers/HomeController.php
namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController
{
    public function index(): void
    {
        $categories = Category::all();
        $featuredProducts = Product::featured(9);

        // Galeri dokumentasi kegiatan mahasiswa
        $activities = [
            [
                'title' => 'Budidaya & Inokulasi Bibit Jamur Tiram',
                'description' => 'Mahasiswa Unsulbar melakukan proses sterilisasi media tanam serbuk kayu dan inokulasi bibit F2 jamur tiram putih di kumbung kampus.',
                'tag' => 'Produksi Pangan',
                'image' => 'assets/images/branding/activity-1.svg',
            ],
            [
                'title' => 'Pemanenan Jamur Tiram Berkualitas Super',
                'description' => 'Hasil panen jamur tiram segar dengan tudung mekar sempurna, siap disortir untuk bahan baku nugget, bakso, kaldu, dan rendang.',
                'tag' => 'Panen Segar',
                'image' => 'assets/images/branding/activity-2.svg',
            ],
            [
                'title' => 'Inovasi Pupuk Kompos Dari Limbah Baglog',
                'description' => 'Pengolahan sisa baglog pasca panen menjadi kompos hayati dan POC kaya unsur hara makro untuk mendukung pertanian sirkular.',
                'tag' => 'Sirkular Ekonomi',
                'image' => 'assets/images/branding/activity-3.svg',
            ],
            [
                'title' => 'Edukasi & Pendampingan Masyarakat Petani Majene',
                'description' => 'Sosialisasi pemanfaatan paket budidaya baglog dan pupuk organik hayati kepada kelompok tani lokal di Sulawesi Barat.',
                'tag' => 'Pengabdian',
                'image' => 'assets/images/branding/activity-4.svg',
            ]
        ];

        // Banner Promo Menarik
        $promos = [
            [
                'title' => 'Paket Budidaya Jamur Mandiri!',
                'subtitle' => 'Dapatkan Baglog Berkualitas + Bibit Unggul F2 Siap Tumbuh Mulai Rp 6.000',
                'badge' => 'PAKET EDUKASI',
                'link' => '?page=catalog&category=produk-paket-budidaya',
            ],
           
        ];

        $pageTitle = 'Dashboard - Aksara Mahasiswa Berdampak Unsulbar';
        $activeTab = 'dashboard';

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/dashboard.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }
}
