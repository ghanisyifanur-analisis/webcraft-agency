@extends('layouts.app')

@section('title', 'Developer & Designer Tasks - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-300">Engineering & Implementation</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1">Developer Tasks & Staging Portal</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pengembangan fitur kode, pementasan server staging, dan penyerahan kredensial terenkripsi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login.quick', 'finance') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition-all">
                Ganti ke Modul Finance &rarr;
            </a>
        </div>
    </div>

    <!-- Tiket Revisi Yang Perlu Dikerjakan Developer -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-emerald-500/30 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <ion-icon name="hammer-outline" class="text-emerald-400 text-xl"></ion-icon>
                    <span>Antrean Tiket Revisi Aktif untuk Pengembang</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Tiket yang telah disetujui PM untuk diperbaiki pada codebase atau server staging.</p>
            </div>
            <span class="text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300">
                {{ count($activeTickets) }} Tiket Aktif
            </span>
        </div>

        <div class="space-y-4">
            @forelse($activeTickets as $tkt)
                <div class="p-5 rounded-2xl bg-slate-900/70 border border-white/10 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-400">{{ $tkt->ticket_number }}</span>
                            <span class="text-xs font-bold text-white ml-2">{{ $tkt->order->package->name ?? '' }} ({{ $tkt->milestone->title ?? '' }})</span>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono">
                            Status Saat Ini: <strong class="text-amber-300 uppercase">{{ $tkt->status }}</strong>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-white/5 text-xs text-slate-300 leading-relaxed">
                        <strong class="text-slate-400 block mb-1">Catatan Klien:</strong>
                        {{ $tkt->issue_description }}
                    </div>

                    <!-- Form Tindak Lanjut Developer -->
                    <form action="{{ route('revisions.update', $tkt->id) }}" method="POST" class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <input type="text" name="developer_response" required placeholder="Jelaskan perbaikan yang telah diterapkan (misal: 'Sudah disesuaikan pada commit abc')..." class="w-full sm:flex-grow px-3 py-2 rounded-xl glass-input text-xs">
                        
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit" name="status" value="in_progress" class="w-1/2 sm:w-auto px-4 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/30 text-xs font-semibold">
                                Sedang Dikerjakan
                            </button>
                            <button type="submit" name="status" value="resolved" class="w-1/2 sm:w-auto px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                                &check; Selesaikan Tiket
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                    Tidak ada tiket revisi aktif yang menunggu penanganan pengembang.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Proyek Dalam Pengerjaan & Input Staging URL -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-4">
        <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
            <ion-icon name="code-working-outline" class="text-emerald-400 text-xl"></ion-icon>
            <span>Daftar Proyek & Pembaruan Server Staging</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($assignedOrders as $ord)
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-white/10 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-brand-300">{{ $ord->order_number }}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $ord->status }}</span>
                        </div>
                        <h3 class="text-sm font-bold text-white mt-1">{{ $ord->package->name }}</h3>
                        <p class="text-xs text-slate-400">Klien: {{ $ord->client->company_name ?? '-' }} ({{ $ord->client->user->name ?? '-' }})</p>
                    </div>

                    <!-- Form Update Staging URL Milestone 4 -->
                    @php
                        $m4 = $ord->milestones->where('stage_number', 4)->first();
                    @endphp
                    @if($m4)
                        <form action="{{ route('milestones.update', $m4->id) }}" method="POST" class="space-y-2 pt-2 border-t border-white/5">
                            @csrf
                            <label class="text-[11px] text-slate-400 block font-semibold">Tautan Demo Server Staging (Fase 4):</label>
                            <div class="flex items-center gap-2">
                                <input type="url" name="staging_url" value="{{ $m4->staging_url ?? '' }}" placeholder="https://staging.domain.com" class="w-full px-3 py-1.5 rounded-lg glass-input text-xs font-mono">
                                <input type="hidden" name="status" value="IN_REVIEW">
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex-shrink-0">
                                    Publish
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="pt-2 border-t border-white/5 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 font-mono">{{ count($ord->credentialVaults) }} Kredensial di Vault</span>
                        <a href="{{ route('orders.show', $ord->id) }}" class="text-xs text-brand-400 hover:underline">
                            Buka Detail Proyek &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                    Belum ada penugasan proyek untuk Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
