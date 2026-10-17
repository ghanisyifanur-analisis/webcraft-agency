<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServicePackage;

class HomeController extends Controller
{
    public function index()
    {
        $packages = ServicePackage::all();
        
        $addons = [
            ['id' => 'addon_domain_com', 'name' => 'Domain .com (1 Tahun)', 'price' => 165000, 'days' => 0],
            ['id' => 'addon_domain_id', 'name' => 'Domain .id Resmi PANDI (1 Tahun)', 'price' => 250000, 'days' => 1],
            ['id' => 'addon_hosting_nvme', 'name' => 'Upgrade Hosting NVMe SSD 10GB + Cloudflare Pro', 'price' => 350000, 'days' => 0],
            ['id' => 'addon_payment_gateway', 'name' => 'Integrasi Payment Gateway Otomatis (VA & QRIS)', 'price' => 450000, 'days' => 2],
            ['id' => 'addon_multilingual', 'name' => 'Fitur Multi-Bahasa (Bilingual ID & EN)', 'price' => 500000, 'days' => 2],
            ['id' => 'addon_content_upload', 'name' => 'Input Produk / Konten Artikel Massal (Hingga 50 Item)', 'price' => 350000, 'days' => 2],
            ['id' => 'addon_seo_setup', 'name' => 'Advanced Technical SEO & Schema Markup', 'price' => 400000, 'days' => 2],
        ];

        return view('home', compact('packages', 'addons'));
    }
}
