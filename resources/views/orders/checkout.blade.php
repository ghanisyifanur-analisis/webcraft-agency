@extends('layouts.app')

@section('title', 'Formulir Pemesanan Website - WebCraft Agency')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <a href="{{ route('home') }}#kalkulator" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>Kembali ke Kalkulator</span>
        </a>
        <h1 class="text-3xl font-extrabold text-white mt-2">Formulir Pemesanan Proyek</h1>
        <p class="text-sm text-slate-400 mt-1">Lengkapi rincian profil perusahaan Anda untuk memulai proses pembuatan website.</p>
    </div>

    @php
        $addonCost = 0;
        $addonDays = 0;
        foreach($selectedAddons as $ak) {
            if(isset($allAddons[$ak])) {
                $addonCost += $allAddons[$ak]['price'];
                $addonDays += $allAddons[$ak]['days'];
            }
        }
        $totalCost = $package->base_price + $addonCost;
        $totalDays = $package->estimated_days + $addonDays;
        $dpAmount = round($totalCost * 0.5);
        $settlementAmount = $totalCost - $dpAmount;
    @endphp

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
        @csrf
        <input type="hidden" name="package_id" value="{{ $package->id }}">
        @foreach($selectedAddons as $addonKey)
            <input type="hidden" name="addons[]" value="{{ $addonKey }}">
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Input Data Klien -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Klien & Bisnis -->
                <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                        <ion-icon name="business-outline" class="text-brand-400 text-lg"></ion-icon>
                        <span>Informasi Klien & Entitas Bisnis</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap Penanggung Jawab *</label>
                            <input type="text" name="client_name" required value="{{ old('client_name', auth()->user()->name ?? '') }}" placeholder="Contoh: Hermawan" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email *</label>
                            <input type="email" name="client_email" required value="{{ old('client_email', auth()->user()->email ?? '') }}" placeholder="nama@perusahaan.com" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Perusahaan / Merek Usaha *</label>
                            <input type="text" name="company_name" required value="{{ old('company_name') }}" placeholder="PT / CV / Toko Anda" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Bidang Usaha / Industri *</label>
                            <input type="text" name="business_sector" required value="{{ old('business_sector') }}" placeholder="F&B, Edukasi, Retail, dll" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor WhatsApp Aktif *</label>
                        <input type="text" name="whatsapp_no" required value="{{ old('whatsapp_no', auth()->user()->phone_number ?? '') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                        <span class="text-[11px] text-slate-500">Notifikasi status pengerjaan akan disinkronkan ke nomor ini.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Domisili / Kantor (Opsional)</label>
                        <textarea name="address" rows="2" placeholder="Alamat lengkap perusahaan..." class="w-full px-4 py-2 rounded-xl glass-input text-sm">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Ketentuan Layanan & Digital Signoff Agreement -->
                <div class="glass-panel p-6 rounded-3xl border border-white/10">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="agreement" value="1" required class="mt-1 rounded bg-slate-800 border-slate-700 text-brand-600 focus:ring-brand-500">
                        <div class="text-xs text-slate-300 leading-relaxed">
                            Saya menyetujui <span class="text-brand-400 font-semibold">Syarat & Ketentuan WebCraft Agency</span>:
                            Sistem split billing memberlakukan DP 50% sebelum pengerjaan brief dimulai, serta pelunasan 50% setelah peninjauan server staging selesai untuk membuka brankas kredensial digital (AES-256).
                        </div>
                    </label>
                </div>
            </div>

            <!-- Kolom Ringkasan Pemesanan -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 rounded-3xl border border-brand-500/30 sticky top-24 space-y-6">
                    <h3 class="text-base font-bold text-white border-b border-white/10 pb-3 flex items-center justify-between">
                        <span>Ringkasan Order</span>
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-brand-500/20 text-brand-300">Draft</span>
                    </h3>

                    <!-- Paket Dipilih -->
                    <div>
                        <span class="text-xs text-slate-400">Paket Layanan:</span>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-sm font-bold text-white">{{ $package->name }}</span>
                            <span class="text-sm font-semibold text-slate-200">Rp {{ number_format($package->base_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[11px] text-brand-400 mt-0.5">{{ $package->estimated_days }} Hari Kerja &bull; {{ $package->revision_quota }}x Revisi</div>
                    </div>

                    <!-- Add-on Dipilih -->
                    @if(count($selectedAddons) > 0)
                        <div class="pt-3 border-t border-white/10">
                            <span class="text-xs text-slate-400">Add-on Terpilih:</span>
                            <div class="space-y-2 mt-2">
                                @foreach($selectedAddons as $ak)
                                    @if(isset($allAddons[$ak]))
                                        <div class="flex items-start justify-between text-xs">
                                            <span class="text-slate-300 pr-2">{{ $allAddons[$ak]['name'] }}</span>
                                            <span class="text-slate-200 font-mono whitespace-nowrap">+{{ number_format($allAddons[$ak]['price'], 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Total & Split Payment -->
                    <div class="pt-4 border-t border-white/10 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Estimasi Total Waktu</span>
                            <span class="font-bold text-white">{{ $totalDays }} Hari Kerja</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-bold text-slate-300">Total Investasi</span>
                            <span class="font-extrabold text-white text-base">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Termin DP 50% Highlight -->
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <div class="text-[11px] text-emerald-400 font-semibold uppercase tracking-wider">Tagihan Termin 1 (DP 50%)</div>
                        <div class="text-2xl font-black text-emerald-300 mt-0.5">Rp {{ number_format($dpAmount, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-slate-400 mt-1">Invoice DP akan diterbitkan seketika setelah formulir dikirim.</div>
                    </div>

                    <button type="button" onclick="confirmOrderSubmit()" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold text-sm shadow-xl shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                        <ion-icon name="checkmark-done-circle-outline" class="text-lg"></ion-icon>
                        <span>Konfirmasi & Buat Pesanan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function confirmOrderSubmit() {
        const form = document.getElementById('orderForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        showConfirmModal(
            'Konfirmasi Pemesanan Website',
            'Apakah seluruh data profil entitas usaha dan paket layanan yang Anda pilih sudah tepat? Invoice Termin 1 (DP 50%) akan diterbitkan.',
            function() {
                form.submit();
            },
            {
                confirmText: 'Ya, Buat Pesanan',
                cancelText: 'Periksa Kembali',
                icon: 'bag-check-outline'
            }
        );
    }
</script>
@endpush
