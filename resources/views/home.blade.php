@extends('layouts.app')

@section('title', 'WebCraft Agency - Solusi Pembuatan Website & Aplikasi Bisnis')

@section('content')
<div class="relative overflow-hidden pt-12 pb-20">
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-300 text-xs font-semibold backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse"></span>
                <span>ISO/IEC/IEEE 29148 Standard Compliant Platform</span>
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Transformasi Digital Bisnis Anda dengan <span class="bg-gradient-to-r from-brand-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">Website Kelas Industri</span>
            </h1>

            <p class="text-slate-300 text-base sm:text-lg leading-relaxed">
                Platform agensi pembuatan website modern dengan alur kerja transparan 5 fase milestone, split invoice (DP 50% & Pelunasan), staging review interaktif, serta penyerahan kredensial aman terenkripsi AES-256.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="#kalkulator" class="px-7 py-3.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-sm shadow-xl shadow-brand-500/25 transition-all flex items-center gap-2">
                    <ion-icon name="calculator-outline" class="text-lg"></ion-icon>
                    <span>Kalkulator Estimasi Biaya</span>
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-xl glass-panel hover:bg-white/10 text-slate-200 font-semibold text-sm transition-all flex items-center gap-2 border border-white/10">
                    <ion-icon name="key-outline" class="text-lg text-brand-400"></ion-icon>
                    <span>Demo 5 Akun Multi-Role</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 5 Fase Workflow Diagram -->
    <div id="alur-kerja" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24">
        <div class="text-center mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-brand-400">Transparansi Proyek</h2>
            <p class="text-2xl sm:text-3xl font-bold text-white mt-1">Alur Kerja 5 Milestone Terstandarisasi</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Phase 1 -->
            <div class="glass-panel p-5 rounded-2xl relative border-brand-500/20 group hover:border-brand-500/50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold text-sm mb-4 border border-brand-500/30">01</div>
                <h3 class="text-base font-bold text-white mb-2">Brief & Sitemap</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Klien input brief, aset logo, palet warna, dan referensi setelah DP 50% terverifikasi.</p>
            </div>

            <!-- Phase 2 -->
            <div class="glass-panel p-5 rounded-2xl relative border-brand-500/20 group hover:border-brand-500/50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-sm mb-4 border border-indigo-500/30">02</div>
                <h3 class="text-base font-bold text-white mb-2">UI/UX Wireframe</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Perancangan tata letak antarmuka, prototype interaktif Figma, dan persetujuan klien.</p>
            </div>

            <!-- Phase 3 -->
            <div class="glass-panel p-5 rounded-2xl relative border-brand-500/20 group hover:border-brand-500/50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-sm mb-4 border border-purple-500/30">03</div>
                <h3 class="text-base font-bold text-white mb-2">Coding & Slicing</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Pengembangan kode responsif Tailwind/Laravel, database, dan logika fungsionalitas sistem.</p>
            </div>

            <!-- Phase 4 -->
            <div class="glass-panel p-5 rounded-2xl relative border-brand-500/20 group hover:border-brand-500/50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm mb-4 border border-emerald-500/30">04</div>
                <h3 class="text-base font-bold text-white mb-2">Staging & Evaluasi</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Peninjauan langsung pada server staging, pengujian fitur, dan pengajuan tiket revisi terukur.</p>
            </div>

            <!-- Phase 5 -->
            <div class="glass-panel p-5 rounded-2xl relative border-brand-500/20 group hover:border-brand-500/50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm mb-4 border border-amber-500/30">05</div>
                <h3 class="text-base font-bold text-white mb-2">Pelunasan & Handover</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Pelunasan Termin 2, auto-unlock Vault Kredensial AES-256, penerbitan BAST resmi.</p>
            </div>
        </div>
    </div>

    <!-- Katalog Layanan -->
    <div id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24">
        <div class="text-center mb-12">
            <h2 class="text-xs font-bold uppercase tracking-wider text-brand-400">Pilihan Paket</h2>
            <p class="text-2xl sm:text-3xl font-bold text-white mt-1">Paket Pembuatan Website Siap Pakai</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($packages as $pkg)
                <div class="glass-panel rounded-2xl p-6 flex flex-col justify-between border border-white/10 hover:border-brand-500/50 transition-all relative overflow-hidden group">
                    @if($pkg->slug === 'custom-app')
                        <div class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30 text-[10px] font-bold uppercase">
                            Enterprise
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $pkg->name }}</h3>
                        <p class="text-xs text-slate-400 mt-1 min-h-[36px]">{{ $pkg->description }}</p>

                        <div class="mt-4 pt-4 border-t border-white/5">
                            <span class="text-xs text-slate-400">Mulai dari</span>
                            <div class="text-2xl font-extrabold text-white">
                                Rp {{ number_format($pkg->base_price, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-brand-400 mt-1 flex items-center gap-1">
                                <ion-icon name="time-outline"></ion-icon>
                                <span>Estimasi {{ $pkg->estimated_days }} Hari Kerja</span>
                            </div>
                        </div>

                        <ul class="mt-6 space-y-2.5 text-xs text-slate-300">
                            @foreach($pkg->features as $feat)
                                <li class="flex items-center gap-2">
                                    <ion-icon name="checkmark-circle" class="text-emerald-400 text-sm flex-shrink-0"></ion-icon>
                                    <span>{{ $feat }}</span>
                                </li>
                            @endforeach
                            <li class="flex items-center gap-2 text-indigo-300 font-medium">
                                <ion-icon name="repeat-outline" class="text-indigo-400 text-sm flex-shrink-0"></ion-icon>
                                <span>Gratis {{ $pkg->revision_quota }}x Siklus Revisi</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <button type="button" onclick="selectPackageForCalculator('{{ $pkg->slug }}', {{ $pkg->base_price }}, {{ $pkg->estimated_days }})" class="w-full py-2.5 rounded-xl bg-white/5 hover:bg-brand-600 text-slate-200 hover:text-white border border-white/10 hover:border-brand-500 text-xs font-semibold transition-all text-center">
                            Pilih Paket Ini
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Interactive Pricing & Quotation Calculator (FR-EST-01, FR-EST-02) -->
    <div id="kalkulator" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-24">
        <div class="glass-panel rounded-3xl p-6 sm:p-10 border border-brand-500/30 relative shadow-2xl">
            <div class="text-center max-w-xl mx-auto mb-8">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 text-xs font-semibold mb-2">
                    <ion-icon name="calculator-outline"></ion-icon>
                    <span>Dynamic Cost Estimator (SRS FR-EST-01)</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Kalkulator Estimasi Biaya & Termin</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-2">Pilih paket dasar dan add-on kebutuhan proyek Anda. Sistem menghitung subtotal dan split termin DP 50% secara otomatis.</p>
            </div>

            <form action="{{ route('orders.checkout') }}" method="GET" id="calcForm" class="space-y-8">
                <!-- 1. Pilihan Paket Dasar -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">1. Pilih Paket Layanan Dasar</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach($packages as $p)
                            <label class="cursor-pointer">
                                <input type="radio" name="package" value="{{ $p->slug }}" data-price="{{ $p->base_price }}" data-days="{{ $p->estimated_days }}" {{ $loop->first ? 'checked' : '' }} class="peer hidden package-radio">
                                <div class="p-4 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/10 transition-all">
                                    <div class="font-bold text-sm text-white">{{ $p->name }}</div>
                                    <div class="text-xs text-brand-400 font-semibold mt-1">Rp {{ number_format($p->base_price, 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->estimated_days }} Hari Kerja</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Pilihan Add-on Tambahan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">2. Tambahan Fitur & Layanan (Add-ons)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($addons as $addon)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="addons[]" value="{{ $addon['id'] }}" data-price="{{ $addon['price'] }}" data-days="{{ $addon['days'] }}" class="peer hidden addon-checkbox">
                                <div class="p-3.5 rounded-xl glass-panel border border-white/10 peer-checked:border-indigo-400 peer-checked:bg-indigo-500/10 transition-all flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-md border border-white/20 peer-checked:bg-indigo-600 flex items-center justify-center text-white text-xs">
                                            <ion-icon name="checkmark" class="hidden"></ion-icon>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-white">{{ $addon['name'] }}</div>
                                            @if($addon['days'] > 0)
                                                <div class="text-[10px] text-slate-400">+{{ $addon['days'] }} Hari Kerja</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-xs font-bold text-indigo-300">+Rp {{ number_format($addon['price'], 0, ',', '.') }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Rangkuman Kalkulasi Real-time -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-white/10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-white/10">
                        <!-- Total Biaya Proyek -->
                        <div class="pt-4 md:pt-0">
                            <span class="text-xs text-slate-400">Total Biaya Investasi</span>
                            <div class="text-2xl sm:text-3xl font-extrabold text-white mt-1" id="displayTotal">Rp 0</div>
                            <div class="text-xs text-slate-400 mt-1" id="displayDays">Estimasi Selesai: 0 Hari</div>
                        </div>

                        <!-- Termin 1 (DP 50%) -->
                        <div class="pt-4 md:pt-0 md:pl-6">
                            <div class="inline-flex items-center gap-1 text-xs text-emerald-400 font-semibold mb-1">
                                <ion-icon name="wallet-outline"></ion-icon>
                                <span>Termin 1 (DP 50%)</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-emerald-300" id="displayDp">Rp 0</div>
                            <div class="text-[11px] text-slate-400 mt-1">Dibayar untuk mulai pengerjaan & pengisian brief</div>
                        </div>

                        <!-- Termin 2 (Pelunasan 50%) -->
                        <div class="pt-4 md:pt-0 md:pl-6">
                            <div class="inline-flex items-center gap-1 text-xs text-amber-400 font-semibold mb-1">
                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                <span>Termin 2 (Pelunasan 50%)</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-bold text-amber-300" id="displaySettlement">Rp 0</div>
                            <div class="text-[11px] text-slate-400 mt-1">Dibayar saat evaluasi staging lolos & unlock kredensial</div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-400 flex items-center gap-2">
                            <ion-icon name="lock-closed-outline" class="text-brand-400 text-base"></ion-icon>
                            <span>Tanpa biaya tersembunyi &bull; Invoice resmi diterbitkan otomatis</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold text-sm shadow-xl shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                            <span>Lanjut ke Formulir Pemesanan</span>
                            <ion-icon name="arrow-forward"></ion-icon>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function calculateEstimate() {
        const selectedPackage = document.querySelector('.package-radio:checked');
        const addonCheckboxes = document.querySelectorAll('.addon-checkbox:checked');

        let basePrice = selectedPackage ? parseInt(selectedPackage.dataset.price) : 0;
        let baseDays = selectedPackage ? parseInt(selectedPackage.dataset.days) : 0;

        let addonTotal = 0;
        let addonDays = 0;

        addonCheckboxes.forEach(cb => {
            addonTotal += parseInt(cb.dataset.price);
            addonDays += parseInt(cb.dataset.days);
        });

        const totalCost = basePrice + addonTotal;
        const totalDays = baseDays + addonDays;
        const dpAmount = Math.round(totalCost * 0.5);
        const settlementAmount = totalCost - dpAmount;

        document.getElementById('displayTotal').textContent = formatRupiah(totalCost);
        document.getElementById('displayDays').textContent = `Estimasi Selesai: ${totalDays} Hari Kerja`;
        document.getElementById('displayDp').textContent = formatRupiah(dpAmount);
        document.getElementById('displaySettlement').textContent = formatRupiah(settlementAmount);
    }

    function selectPackageForCalculator(slug, price, days) {
        const radio = document.querySelector(`.package-radio[value="${slug}"]`);
        if (radio) {
            radio.checked = true;
            calculateEstimate();
            document.getElementById('kalkulator').scrollIntoView({ behavior: 'smooth' });
        }
    }

    document.querySelectorAll('.package-radio').forEach(el => el.addEventListener('change', calculateEstimate));
    document.querySelectorAll('.addon-checkbox').forEach(el => el.addEventListener('change', calculateEstimate));

    // Run on init
    calculateEstimate();
</script>
@endpush
