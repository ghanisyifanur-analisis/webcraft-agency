@extends('layouts.app')

@section('title', 'Project Manager Dashboard - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Operations & Sprint Control</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1">Project Manager Dashboard</h1>
            <p class="text-xs text-slate-400 mt-0.5">Peninjauan brief klien, alokasi pengembang, persetujuan scope tiket revisi.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login.quick', 'developer') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all">
                Ganti ke Modul Dev &rarr;
            </a>
        </div>
    </div>

    <!-- Brief Masuk yang Membutuhkan Review PM -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-blue-500/30 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <ion-icon name="document-text-outline" class="text-blue-400 text-xl"></ion-icon>
                    <span>Antrean Brief Klien Masuk (Menunggu Verifikasi PM)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Periksa kelengkapan brand, logo, palet warna, dan aset sebelum membuka Fase 2 UI/UX.</p>
            </div>
            <span class="text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-300">
                {{ count($pendingBriefs) }} Menunggu
            </span>
        </div>

        <div class="space-y-4">
            @forelse($pendingBriefs as $brf)
                <div class="p-5 rounded-2xl bg-slate-900/70 border border-white/10 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-mono font-bold text-blue-400">{{ $brf->order->order_number ?? '-' }}</span>
                            <h3 class="text-sm font-bold text-white mt-0.5">{{ $brf->brand_name }} ({{ $brf->order->package->name ?? '' }})</h3>
                        </div>
                        <div class="text-xs text-slate-400">
                            Diajukan: {{ $brf->updated_at->diffForHumans() }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-white/5 p-3 rounded-xl">
                        <div>
                            <strong class="text-slate-400 block">Slogan / Tagline:</strong>
                            <span class="text-slate-200">{{ $brf->tagline_description ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-400 block">Warna Brand:</strong>
                            <span class="text-slate-200">{{ $brf->color_palette ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-400 block">Aset Logo:</strong>
                            @if($brf->logo_file_path)
                                <a href="/{{ $brf->logo_file_path }}" target="_blank" class="text-brand-400 hover:underline">Lihat Berkas Logo</a>
                            @else
                                <span class="text-slate-500">Tidak ada file</span>
                            @endif
                        </div>
                    </div>

                    <!-- Form Aksi Review PM -->
                    <form action="{{ route('briefs.review', $brf->id) }}" method="POST" class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <input type="text" name="pm_feedback" placeholder="Catatan untuk klien (wajib diisi bila meminta revisi brief)..." class="w-full sm:flex-grow px-3 py-2 rounded-xl glass-input text-xs">
                        
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit" name="status" value="NEED_REVISION" class="w-1/2 sm:w-auto px-4 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-semibold transition-all">
                                Minta Revisi
                            </button>
                            <button type="submit" name="status" value="ACCEPTED" class="w-1/2 sm:w-auto px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md">
                                &check; Terima Brief (Mulai Dev)
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                    Tidak ada antrean brief klien yang memerlukan peninjauan saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tiket Revisi Terbuka (Open Revision Tickets) -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <ion-icon name="repeat-outline" class="text-indigo-400 text-xl"></ion-icon>
                    <span>Tiket Revisi Klien Baru (Verifikasi Scope Proyek)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Tinjau apakah tiket perbaikan sesuai ruang lingkup paket atau di luar kesepakatan.</p>
            </div>
            <span class="text-xs font-bold font-mono px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300">
                {{ count($openTickets) }} Tiket Baru
            </span>
        </div>

        <div class="space-y-3">
            @forelse($openTickets as $tkt)
                <div class="p-4 rounded-xl bg-slate-900/60 border border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-indigo-400">{{ $tkt->ticket_number }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">({{ $tkt->order->order_number ?? '' }})</span>
                            @if($tkt->is_paid_addon)
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold">Paid Add-on</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-300">{{ $tkt->issue_description }}</p>
                    </div>

                    <form action="{{ route('revisions.update', $tkt->id) }}" method="POST" class="flex items-center gap-2 flex-shrink-0">
                        @csrf
                        <button type="submit" name="status" value="rejected" class="px-3 py-1.5 rounded-lg bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 text-xs font-semibold">
                            Tolak (Out of Scope)
                        </button>
                        <button type="submit" name="status" value="approved_for_work" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md">
                            Setujui untuk Dev
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                    Semua tiket revisi klien telah diverifikasi.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
