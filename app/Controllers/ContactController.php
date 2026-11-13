<?php
// app/Controllers/ContactController.php
namespace App\Controllers;

class ContactController
{
    public function view(): void
    {
        $pageTitle = 'Kontak & Informasi Tim - Aksara Mahasiswa Berdampak Unsulbar';
        $activeTab = 'contact';

        $teamMember = [
            [
                'name' => 'HIMAPSI FT-UNSULBAR',
                'role' => 'IoT & Website',
                'dept' => 'Fakultas Teknik Universitas Sulawesi Barat ',
                'avatar' => 'assets/images/branding/himapsi.png'
            ],
            [
                'name' => 'HIMALOGISTA UNSULBAR',
                'role' => 'Pengolahan Produk Pangan jamur Dan Pupuk Dari Limbah Baglog',
                'dept' => 'Fakultas Pertanian & Kehutanan Universitas Sulawesi Barat',
                'avatar' => 'assets/images/branding/himalogista.png'
            ],
            [
                'name' => 'HIMAGRI UNSULBAR',
                'role' => 'Strategi Pemasaran',
                'dept' => 'Fakultas Pertanian & Kehutanan Universitas Sulawesi Barat',
                'avatar' => 'assets/images/branding/himagri.png'
            ],
            [
                'name' => 'HIMAGRO UNSULBAR',
                'role' => 'pengelolaan Lahan dan Kumbung',
                'dept' => 'Fakultas Pertanian & Kehutanan Universitas Sulawesi Barat',
                'avatar' => 'assets/images/branding/himagro.png'
            ],
             [
                'name' => 'HIMABIOTEK UNSULBAR',
                'role' => 'Pengolahan Media Tanam Dan Pembibitan',
                'dept' => 'Fakultas Matematika & Dan Ilmu Pengetahuan Alam Universitas Sulawesi Barat',
                'avatar' => 'assets/images/branding/himabio.jpeg'
            ],
        ];

        require_once dirname(__DIR__, 2) . '/views/layouts/header.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/subheader.php';
        require_once dirname(__DIR__, 2) . '/views/pages/contact.php';
        require_once dirname(__DIR__, 2) . '/views/layouts/footer.php';
    }
}
