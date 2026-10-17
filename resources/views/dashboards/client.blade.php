@extends('layouts.app')

@section('title', 'Portal Klien - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-400"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-300">Client Portal</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1">Portal Klien: {{ $client->company_name ?? $user->name }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola pesanan website, isi brief proyek, pantau evaluasi staging, dan akses brankas kredensial.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}#kalkulator" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold shadow-lg shadow-brand-500/25 transition-all flex items-center gap-2">
                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                <span>Pesan Proyek Baru</span>
            </a>
        </div>
    </div>

    <!-- Daftar Proyek Klien -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-white flex items-center gap-2">
            <ion-icon name="folder-open-outline" class="text-indigo-400 text-xl"></ion-icon>
            <span>Proyek Website Anda</span>
        </h2>

        <div class="grid grid-cols-1 gap-4">
            @forelse($orders as $ord)
                <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:border-brand-500/30 transition-all">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-brand-500/20 text-brand-300">
                                {{ $ord->order_number }}
                            </span>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-white/10 text-slate-200">
                                {{ $ord->status }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-white">{{ $ord->package->name }}</h3>
                        <div class="text-xs text-slate-400">
                            Total Investasi: <strong class="text-slate-200 font-mono">Rp {{ number_format($ord->total_amount, 0, ',', '.') }}</strong> &bull; Target Selesai: <span class="text-brand-300 font-mono">{{ $ord->target_completion_date ? $ord->target_completion_date->format('d M Y') : '-' }}</span>
                        </div>

                        <!-- Progress 5 Milestone -->
                        @php
                            $approvedCount = $ord->milestones->where('status', 'APPROVED')->count();
                            $percent = round(($approvedCount / 5) * 100);
                        @endphp
                        <div class="w-full max-w-md space-y-1">
                            <div class="flex justify-between text-[11px] text-slate-400">
                                <span>Kemajuan Milestone ({{ $approvedCount }}/5 Selesai)</span>
                                <span class="font-bold text-brand-400">{{ $percent }}%</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row md:flex-col items-stretch md:items-end justify-center gap-2 flex-shrink-0">
                        <a href="{{ route('orders.show', $ord->id) }}" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-md transition-all text-center flex items-center justify-center gap-1.5">
                            <span>Buka Ruang Kerja Proyek</span>
                            <ion-icon name="arrow-forward"></ion-icon>
                        </a>

                        @if(!$ord->isDpPaid() && $ord->dpInvoice)
                            <a href="{{ route('invoices.show', $ord->dpInvoice->id) }}" class="px-5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 font-semibold text-xs text-center transition-all">
                                Bayar DP (Termin 1)
                            </a>
                        @elseif($ord->isSettlementPaid())
                            <a href="{{ route('vault.bast', $ord->id) }}" target="_blank" class="px-5 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 font-semibold text-xs text-center transition-all">
                                Cetak BAST &check;
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass-panel p-12 rounded-3xl border border-dashed border-white/10 text-center space-y-4">
                    <ion-icon name="bag-handle-outline" class="text-4xl text-slate-500 mx-auto"></ion-icon>
                    <div>
                        <h3 class="text-base font-bold text-white">Belum Ada Proyek Aktif</h3>
                        <p class="text-xs text-slate-400 mt-1">Anda belum memiliki pesanan pembuatan website di WebCraft Agency.</p>
                    </div>
                    <a href="{{ route('home') }}#kalkulator" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-lg shadow-brand-500/25 transition-all">
                        <span>Pesan Website Sekarang</span>
                        <ion-icon name="arrow-forward"></ion-icon>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
