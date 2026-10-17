@extends('layouts.app')

@section('title', 'Super Admin Overview - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-purple-300">Executive Control Panel</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1">Super Admin Overview</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemantauan metrik kinerja, profitabilitas, dan manajemen pesanan agensi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl glass-panel text-slate-300 hover:text-white text-xs font-semibold transition-all">
                &larr; Lihat Website Publik
            </a>
            <a href="{{ route('login.quick', 'pm') }}" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition-all">
                Ganti ke Modul PM &rarr;
            </a>
        </div>
    </div>

    <!-- 4 Metrik Kartu Finansial & Operasional -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pesanan -->
        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Total Pesanan Masuk</span>
                <ion-icon name="bag-handle-outline" class="text-lg text-brand-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-white mt-2">{{ $totalOrders }}</div>
            <div class="text-[11px] text-slate-400 mt-1">
                <span class="text-emerald-400 font-semibold">{{ $activeOrders }} aktif</span> &bull; {{ $completedOrders }} selesai
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Total Omzet / Revenue</span>
                <ion-icon name="cash-outline" class="text-lg text-emerald-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-emerald-300 mt-2 font-mono">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Dari pembayaran yang terverifikasi</div>
        </div>

        <!-- Beban Modal COGS -->
        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Total Beban Modal (COGS)</span>
                <ion-icon name="trending-down-outline" class="text-lg text-rose-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-rose-300 mt-2 font-mono">
                Rp {{ number_format($totalCogs, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Biaya domain, server, plugin</div>
        </div>

        <!-- Laba Kotor (Gross Profit) -->
        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Laba Bersih (Net Profit)</span>
                <ion-icon name="pie-chart-outline" class="text-lg text-purple-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-purple-300 mt-2 font-mono">
                Rp {{ number_format($grossProfit, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-400 mt-1">
                Margin: {{ $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Tabel Pesanan Terkini & Penugasan Staf -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <ion-icon name="list-outline" class="text-purple-400 text-xl"></ion-icon>
                    <span>Daftar Pesanan & Penugasan Staf Pelaksana</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Penetapan Project Manager (PM) dan Web Developer per proyek.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-white/5 rounded-2xl overflow-hidden">
                <thead class="bg-white/5 text-slate-300 uppercase text-[10px] tracking-wider border-b border-white/5">
                    <tr>
                        <th class="p-3.5">No. Order</th>
                        <th class="p-3.5">Klien / Perusahaan</th>
                        <th class="p-3.5">Paket Layanan</th>
                        <th class="p-3.5">Total Biaya</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">PM Ditugaskan</th>
                        <th class="p-3.5">Dev Ditugaskan</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentOrders as $ord)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-3.5 font-mono font-bold text-brand-300">
                                <a href="{{ route('orders.show', $ord->id) }}" class="hover:underline">{{ $ord->order_number }}</a>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-white">{{ $ord->client->company_name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $ord->client->user->name ?? '-' }}</div>
                            </td>
                            <td class="p-3.5 text-slate-300">{{ $ord->package->name }}</td>
                            <td class="p-3.5 font-mono font-bold text-white">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/10 text-slate-200">
                                    {{ $ord->status }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-300">
                                {{ $ord->assignedPm->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="p-3.5 text-slate-300">
                                {{ $ord->assignedDev->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('orders.show', $ord->id) }}" class="px-3 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-white text-[11px] font-semibold transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-xs text-slate-500 italic">Belum ada data pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
