<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima (BAST) - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 20mm !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-6 sm:p-12">

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            &larr; Kembali ke Detail Pesanan
        </a>
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-md transition-all flex items-center gap-2">
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Lembar Surat BAST Fisik / Dokumen -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl p-8 sm:p-14 border border-slate-200 shadow-xl page-sheet">
        <!-- Kop Surat -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-6 mb-8">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">WEBCRAFT AGENCY</h1>
                <p class="text-xs text-slate-600">Digital Agency & Software Engineering Platform</p>
                <p class="text-[11px] text-slate-500 font-mono mt-0.5">https://webcraft.id &bull; support@webcraft.id</p>
            </div>
            <div class="text-right">
                <div class="text-xs font-mono font-bold text-slate-500">DOKUMEN RESMI</div>
                <div class="text-sm font-bold text-slate-900 uppercase">BERITA ACARA SERAH TERIMA</div>
                <div class="text-xs font-mono text-indigo-600 mt-1">BAST/{{ date('Ym') }}/{{ substr($order->order_number, -4) }}</div>
            </div>
        </div>

        <!-- Badan Surat -->
        <div class="space-y-6 text-xs text-slate-800 leading-relaxed">
            <p>
                Pada hari ini, <strong class="text-slate-900">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>, telah dilakukan serah terima hasil pekerjaan pembuatan website dan aplikasi berbasis web dengan rincian sebagai berikut:
            </p>

            <!-- Pihak Pertama & Kedua -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Pihak Pertama (Penyedia Jasa):</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5">WebCraft Agency Platform</div>
                    <div class="text-slate-600">Perwakilan Tim Pengembang & Project Management</div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Pihak Kedua (Klien / Pemilik Proyek):</span>
                    <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $order->client->company_name ?? 'Klien' }}</div>
                    <div class="text-slate-600">Penanggung Jawab: {{ $order->client->user->name ?? '-' }} ({{ $order->client->whatsapp_no ?? '-' }})</div>
                </div>
            </div>

            <!-- Rincian Proyek -->
            <div>
                <h3 class="font-bold text-slate-900 mb-2">1. Lingkup Pekerjaan & Spesifikasi Paket</h3>
                <table class="w-full text-left border border-slate-200 rounded-lg overflow-hidden text-xs">
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <td class="p-2.5 font-semibold text-slate-700 w-1/3">Nomor Kontrak Order</td>
                        <td class="p-2.5 font-mono font-bold text-slate-900">{{ $order->order_number }}</td>
                    </tr>
                    <tr class="border-b border-slate-200">
                        <td class="p-2.5 font-semibold text-slate-700">Paket Layanan</td>
                        <td class="p-2.5 font-semibold text-slate-900">{{ $order->package->name }} (Base: Rp {{ number_format($order->package->base_price, 0, ',', '.') }})</td>
                    </tr>
                    <tr class="border-b border-slate-200">
                        <td class="p-2.5 font-semibold text-slate-700">Fitur & Add-on</td>
                        <td class="p-2.5 text-slate-700">
                            @forelse($order->addons as $adn)
                                <span class="inline-block bg-slate-200 px-2 py-0.5 rounded text-[11px] mr-1 mb-1 font-mono">{{ $adn->addon_name }}</span>
                            @empty
                                Standar Paket
                            @endforelse
                        </td>
                    </tr>
                    <tr class="bg-slate-50">
                        <td class="p-2.5 font-semibold text-slate-700">Status Penyelesaian Pembayaran</td>
                        <td class="p-2.5 font-bold text-emerald-600 font-mono">LUNAS 100% (Termin 1 DP 50% & Termin 2 Pelunasan 50%)</td>
                    </tr>
                </table>
            </div>

            <!-- Audit Trail Milestone -->
            <div>
                <h3 class="font-bold text-slate-900 mb-2">2. Riwayat Sign-Off Digital 5 Milestone</h3>
                <div class="space-y-1.5">
                    @foreach($order->milestones->sortBy('stage_number') as $m)
                        <div class="flex items-center justify-between p-2 rounded bg-slate-50 border border-slate-200 text-[11px]">
                            <div>
                                <span class="font-bold text-slate-900">Fase {{ $m->stage_number }}: {{ $m->title }}</span>
                            </div>
                            <div class="text-right font-mono text-emerald-700 font-semibold">
                                &check; Approved {{ $m->approved_at ? $m->approved_at->format('d/m/Y H:i') : 'System Verified' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Penyerahan Kredensial & Hak Akses -->
            <div>
                <h3 class="font-bold text-slate-900 mb-2">3. Penyerahan Hak Akses Kredensial (Vault AES-256)</h3>
                <p class="text-slate-700 leading-relaxed mb-2">
                    Seluruh kredensial hak akses administratif website telah didekripsi dan diserahkan secara elektronik melalui Brankas Digital Portal Klien, mencakup:
                </p>
                <ul class="list-disc list-inside space-y-1 pl-2 text-slate-800 font-mono text-[11px]">
                    @foreach($order->credentialVaults as $v)
                        <li>{{ $v->service_label }} &mdash; URL: {{ $v->login_url }} (Akun: {{ $v->username }})</li>
                    @endforeach
                </ul>
            </div>

            <p class="pt-2 text-slate-700">
                Demikian Berita Acara Serah Terima ini diterbitkan dengan sebenarnya secara digital dan memiliki kekuatan hukum yang sah sebagai bukti tuntasnya seluruh kewajiban pengerjaan proyek.
            </p>
        </div>

        <!-- Tanda Tangan Kedua Belah Pihak -->
        <div class="mt-14 pt-8 border-t border-slate-200 grid grid-cols-2 text-center text-xs">
            <div>
                <span class="text-slate-500 font-semibold block mb-1">PIHAK PERTAMA</span>
                <span class="text-[10px] text-slate-400 block mb-16">WebCraft Agency Platform</span>
                <div class="font-bold text-slate-900 uppercase underline">Hermawan (MaoneArt)</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Project Director / Lead Engineer</div>
            </div>

            <div>
                <span class="text-slate-500 font-semibold block mb-1">PIHAK KEDUA</span>
                <span class="text-[10px] text-slate-400 block mb-16">{{ $order->client->company_name ?? 'Klien' }}</span>
                <div class="font-bold text-slate-900 uppercase underline">{{ $order->client->user->name ?? 'Klien Resmi' }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Authorized Signatory</div>
            </div>
        </div>
    </div>

</body>
</html>
