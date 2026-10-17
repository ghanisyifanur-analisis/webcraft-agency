<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Client;
use App\Models\ServicePackage;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\Invoice;
use App\Models\ProjectMilestone;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $packageId = $request->query('package');
        $package = ServicePackage::where('slug', $packageId)->orWhere('id', $packageId)->firstOrFail();
        
        $selectedAddons = (array) $request->query('addons', []);

        $allAddons = [
            'addon_domain_com' => ['name' => 'Domain .com (1 Tahun)', 'price' => 165000, 'days' => 0],
            'addon_domain_id' => ['name' => 'Domain .id Resmi PANDI (1 Tahun)', 'price' => 250000, 'days' => 1],
            'addon_hosting_nvme' => ['name' => 'Upgrade Hosting NVMe SSD 10GB + Cloudflare Pro', 'price' => 350000, 'days' => 0],
            'addon_payment_gateway' => ['name' => 'Integrasi Payment Gateway Otomatis (VA & QRIS)', 'price' => 450000, 'days' => 2],
            'addon_multilingual' => ['name' => 'Fitur Multi-Bahasa (Bilingual ID & EN)', 'price' => 500000, 'days' => 2],
            'addon_content_upload' => ['name' => 'Input Produk / Konten Artikel Massal (Hingga 50 Item)', 'price' => 350000, 'days' => 2],
            'addon_seo_setup' => ['name' => 'Advanced Technical SEO & Schema Markup', 'price' => 400000, 'days' => 2],
        ];

        return view('orders.checkout', compact('package', 'selectedAddons', 'allAddons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'client_name' => 'required|string|max:150',
            'client_email' => 'required|email|max:150',
            'company_name' => 'required|string|max:150',
            'business_sector' => 'required|string|max:100',
            'whatsapp_no' => 'required|string|max:25',
            'address' => 'nullable|string',
            'agreement' => 'accepted',
        ]);

        $package = ServicePackage::findOrFail($request->package_id);

        // 1. Resolve or Create User
        $user = Auth::user();
        if (!$user) {
            $user = User::where('email', $request->client_email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'phone_number' => $request->whatsapp_no,
                    'status' => 'active',
                ]);
            }
            Auth::login($user);
        }

        // 2. Resolve or Create Client Profile
        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $request->company_name,
                'business_sector' => $request->business_sector,
                'whatsapp_no' => $request->whatsapp_no,
                'address' => $request->address,
            ]
        );

        // 3. Calculate Addons & Total Price
        $allAddonList = [
            'addon_domain_com' => ['name' => 'Domain .com (1 Tahun)', 'price' => 165000, 'days' => 0],
            'addon_domain_id' => ['name' => 'Domain .id Resmi PANDI (1 Tahun)', 'price' => 250000, 'days' => 1],
            'addon_hosting_nvme' => ['name' => 'Upgrade Hosting NVMe SSD 10GB + Cloudflare Pro', 'price' => 350000, 'days' => 0],
            'addon_payment_gateway' => ['name' => 'Integrasi Payment Gateway Otomatis (VA & QRIS)', 'price' => 450000, 'days' => 2],
            'addon_multilingual' => ['name' => 'Fitur Multi-Bahasa (Bilingual ID & EN)', 'price' => 500000, 'days' => 2],
            'addon_content_upload' => ['name' => 'Input Produk / Konten Artikel Massal (Hingga 50 Item)', 'price' => 350000, 'days' => 2],
            'addon_seo_setup' => ['name' => 'Advanced Technical SEO & Schema Markup', 'price' => 400000, 'days' => 2],
        ];

        $addonCost = 0;
        $extraDays = 0;
        $selectedAddons = (array) $request->input('addons', []);

        foreach ($selectedAddons as $addonKey) {
            if (isset($allAddonList[$addonKey])) {
                $addonCost += $allAddonList[$addonKey]['price'];
                $extraDays += $allAddonList[$addonKey]['days'];
            }
        }

        $totalAmount = $package->base_price + $addonCost;
        $totalEstimatedDays = $package->estimated_days + $extraDays;

        // 4. Generate Unique Order Number: ORD-YYYYMMDD-XXXX
        $dateStr = date('Ymd');
        $randomSeq = str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $orderNumber = "ORD-{$dateStr}-{$randomSeq}";

        // 5. Create Order
        $order = Order::create([
            'order_number' => $orderNumber,
            'client_id' => $client->id,
            'package_id' => $package->id,
            'total_amount' => $totalAmount,
            'status' => 'WAITING_DP',
            'target_completion_date' => now()->addDays($totalEstimatedDays),
            'revisions_used' => 0,
        ]);

        // 6. Save Addons
        foreach ($selectedAddons as $addonKey) {
            if (isset($allAddonList[$addonKey])) {
                OrderAddon::create([
                    'order_id' => $order->id,
                    'addon_name' => $allAddonList[$addonKey]['name'],
                    'price' => $allAddonList[$addonKey]['price'],
                    'additional_days' => $allAddonList[$addonKey]['days'],
                ]);
            }
        }

        // 7. Seed 5 Milestones for this Order
        $milestoneTemplates = [
            1 => ['title' => 'Fase 1: Review Brief & Konsep Sitemap', 'desc' => 'Pengumpulan brief, verifikasi aset brand, dan pemetaan struktur website.'],
            2 => ['title' => 'Fase 2: Desain UI/UX & Wireframing', 'desc' => 'Pembuatan prototype visual layout, palet warna, tipografi, dan wireframe responsif.'],
            3 => ['title' => 'Fase 3: Slicing Kode & Pengembangan Sistem', 'desc' => 'Implementasi frontend HTML/Tailwind CSS dan integrasi fitur backend database.'],
            4 => ['title' => 'Fase 4: Pengujian di Server Staging & Evaluasi Klien', 'desc' => 'Pementasan website di subdomain staging untuk pengujian langsung oleh klien.'],
            5 => ['title' => 'Fase 5: Pelunasan, Migrasi ke Server Live, & Final Handover', 'desc' => 'Pelunasan invoice Termin 2, migrasi ke domain utama, dan penyerahan akun kredensial.'],
        ];

        foreach ($milestoneTemplates as $stageNum => $tmpl) {
            ProjectMilestone::create([
                'order_id' => $order->id,
                'title' => $tmpl['title'],
                'stage_number' => $stageNum,
                'description' => $tmpl['desc'],
                'status' => 'LOCKED',
            ]);
        }

        // 8. Generate Termin 1 Invoice (DP 50%)
        $dpAmount = round($totalAmount * 0.5);
        $dpInvoiceNumber = 'INV-' . date('Ym') . '-' . substr($orderNumber, -4) . '-DP';
        
        $invoiceDp = Invoice::create([
            'invoice_number' => $dpInvoiceNumber,
            'order_id' => $order->id,
            'term_type' => 'TERM_1_DP',
            'amount' => $dpAmount,
            'due_date' => now()->addDays(2),
            'status' => 'UNPAID',
        ]);

        // 9. Generate Termin 2 Invoice (Pelunasan 50%)
        $settlementAmount = $totalAmount - $dpAmount;
        $settlementInvoiceNumber = 'INV-' . date('Ym') . '-' . substr($orderNumber, -4) . '-PELUNASAN';

        Invoice::create([
            'invoice_number' => $settlementInvoiceNumber,
            'order_id' => $order->id,
            'term_type' => 'TERM_2_SETTLEMENT',
            'amount' => $settlementAmount,
            'due_date' => now()->addDays($totalEstimatedDays),
            'status' => 'UNPAID',
        ]);

        return redirect()->route('invoices.show', $invoiceDp->id)->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran Termin 1 (DP 50%).');
    }

    public function show($id)
    {
        $order = Order::with(['client.user', 'package', 'addons', 'brief', 'milestones.revisionTickets', 'invoices.payments', 'credentialVaults'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
