@extends('layouts.app')

@section('title', 'Detail Proyek: ' . $order->order_number . ' - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header & Order Banner -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-brand-500/20 text-brand-300 border border-brand-500/30">
                    {{ $order->order_number }}
                </span>
                
                @php
                    $statusBadges = [
                        'WAITING_DP' => ['bg' => 'bg-amber-500/20', 'text' => 'text-amber-300', 'label' => 'Menunggu Pembayaran DP (50%)'],
                        'BRIEF_PHASE' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-300', 'label' => 'Fase Pengisian & Review Brief'],
                        'IN_PROGRESS' => ['bg' => 'bg-indigo-500/20', 'text' => 'text-indigo-300', 'label' => 'Sedang Dikerjakan'],
                        'STAGING_REVIEW' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-300', 'label' => 'Evaluasi Server Staging'],
                        'COMPLETED' => ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-300', 'label' => 'Proyek Selesai & Handover'],
                        'CANCELLED' => ['bg' => 'bg-rose-500/20', 'text' => 'text-rose-300', 'label' => 'Dibatalkan'],
                    ];
                    $badge = $statusBadges[$order->status] ?? ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-300', 'label' => $order->status];
                @endphp
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $badge['bg'] }} {{ $badge['text'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">
                {{ $order->package->name }} &bull; {{ $order->client->company_name ?? 'Klien' }}
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Klien: <strong class="text-slate-200">{{ $order->client->user->name ?? '-' }}</strong> ({{ $order->client->whatsapp_no ?? '-' }}) &bull; Target Selesai: <span class="text-brand-300 font-mono">{{ $order->target_completion_date ? $order->target_completion_date->format('d M Y') : '-' }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($order->isSettlementPaid())
                <a href="{{ route('vault.bast', $order->id) }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-500/25 flex items-center gap-2 transition-all">
                    <ion-icon name="document-text-outline" class="text-base"></ion-icon>
                    <span>Cetak BAST Digital</span>
                </a>
            @endif

            @if(!$order->isDpPaid() && $order->dpInvoice)
                <a href="{{ route('invoices.show', $order->dpInvoice->id) }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-bold text-xs shadow-lg shadow-amber-500/25 flex items-center gap-2 transition-all">
                    <ion-icon name="wallet-outline" class="text-base"></ion-icon>
                    <span>Bayar DP Termin 1</span>
                </a>
            @elseif(!$order->isSettlementPaid() && $order->settlementInvoice && $order->status === 'STAGING_REVIEW')
                <a href="{{ route('invoices.show', $order->settlementInvoice->id) }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 flex items-center gap-2 transition-all animate-pulse">
                    <ion-icon name="card-outline" class="text-base"></ion-icon>
                    <span>Pelunasan (Termin 2)</span>
                </a>
            @endif

            @if($order->isDpPaid() && (!$order->brief || $order->brief->status === 'NEED_REVISION'))
                <a href="{{ route('briefs.create', $order->id) }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs shadow-lg shadow-indigo-500/25 flex items-center gap-2 transition-all">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                    <span>{{ $order->brief ? 'Revisi Brief Kebutuhan' : 'Lengkapi Brief Kebutuhan' }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Progress Milestones 5 Fase (SRS FR-WF-01 & FR-SIGNOFF-01) -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-6">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <ion-icon name="git-branch-outline" class="text-brand-400 text-xl"></ion-icon>
                    <span>Kemajuan Milestone & Persetujuan Digital (Sign-Off)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Alur pengerjaan berurutan. Setiap tahapan mensyaratkan persetujuan sebelum fase berikutnya terbuka.</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400">Total Progres:</span>
                @php
                    $approvedCount = $order->milestones->where('status', 'APPROVED')->count();
                    $percent = round(($approvedCount / 5) * 100);
                @endphp
                <span class="text-sm font-extrabold text-brand-400 ml-1">{{ $percent }}%</span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-brand-600 via-indigo-500 to-emerald-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
        </div>

        <!-- 5 Milestone Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 pt-2">
            @foreach($order->milestones->sortBy('stage_number') as $m)
                @php
                    $isLocked = $m->status === 'LOCKED';
                    $isActive = $m->status === 'ACTIVE';
                    $isInReview = $m->status === 'IN_REVIEW';
                    $isApproved = $m->status === 'APPROVED';

                    $cardBorder = $isApproved ? 'border-emerald-500/40 bg-emerald-500/5' : ($isActive ? 'border-brand-500/50 bg-brand-500/10' : ($isInReview ? 'border-amber-500/50 bg-amber-500/10' : 'border-white/5 bg-slate-900/30 opacity-60'));
                    $badgeColor = $isApproved ? 'bg-emerald-500/20 text-emerald-300' : ($isActive ? 'bg-brand-500/20 text-brand-300 animate-pulse' : ($isInReview ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-400'));
                @endphp
                <div class="rounded-2xl p-4 border {{ $cardBorder }} flex flex-col justify-between transition-all">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold font-mono px-2 py-0.5 rounded-full {{ $badgeColor }}">
                                Fase {{ $m->stage_number }}
                            </span>
                            @if($isApproved)
                                <ion-icon name="checkmark-circle" class="text-emerald-400 text-lg"></ion-icon>
                            @elseif($isLocked)
                                <ion-icon name="lock-closed" class="text-slate-500 text-base"></ion-icon>
                            @else
                                <ion-icon name="time-outline" class="text-brand-400 text-base"></ion-icon>
                            @endif
                        </div>
                        <h3 class="text-xs font-bold text-white mb-1">{{ $m->title }}</h3>
                        <p class="text-[11px] text-slate-400 leading-snug">{{ Str::limit($m->description, 80) }}</p>

                        @if($m->staging_url)
                            <div class="mt-2 pt-2 border-t border-white/5">
                                <a href="{{ $m->staging_url }}" target="_blank" class="text-[11px] text-brand-400 hover:underline flex items-center gap-1 font-mono">
                                    <ion-icon name="open-outline"></ion-icon>
                                    <span>Tinjau Staging</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-white/5 text-[10px]">
                        @if($isApproved)
                            <div class="text-emerald-400 font-semibold flex items-center gap-1">
                                <ion-icon name="shield-checkmark"></ion-icon>
                                <span>Disetujui Digital</span>
                            </div>
                            <div class="text-slate-500 font-mono">{{ $m->approved_at ? $m->approved_at->format('d/m/Y H:i') : '' }}</div>
                        @elseif($isActive || $isInReview)
                            <form action="{{ route('milestones.signoff', $m->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmSignoff(this, 'Fase {{ $m->stage_number }}: {{ $m->title }}')" class="w-full py-1.5 px-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition-all flex items-center justify-center gap-1">
                                    <ion-icon name="finger-print-outline"></ion-icon>
                                    <span>Sign-Off Digital</span>
                                </button>
                            </form>
                        @else
                            <span class="text-slate-500 italic">Menunggu fase sebelumnya</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2 Kolom: Vault Kredensial & Tiket Revisi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Brankas Kredensial Digital (SRS FR-SEC-01 & FR-SEC-02: AES-256 Gated Release) -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-6">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <ion-icon name="lock-closed" class="text-amber-400 text-lg"></ion-icon>
                        <span>Brankas Kredensial Website (Vault AES-256)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Aset kredensial production (cPanel, WordPress Admin, Database) terenkripsi tingkat militer.</p>
                </div>
                <div>
                    @if($order->isSettlementPaid())
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            UNLOCKED
                        </span>
                    @else
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            GATED LOCKED
                        </span>
                    @endif
                </div>
            </div>

            @if(!$order->isSettlementPaid())
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs leading-relaxed flex items-start gap-3">
                    <ion-icon name="shield-outline" class="text-2xl text-amber-400 flex-shrink-0 mt-0.5"></ion-icon>
                    <div>
                        <strong class="font-bold">Gated Release Protocol Aktif:</strong>
                        Seluruh sandi kredensial dikunci oleh sistem sampai pembayaran Termin 2 (Pelunasan 50%) terkonfirmasi secara lunas.
                    </div>
                </div>
            @endif

            <!-- List Kredensial Tersimpan -->
            <div class="space-y-3">
                @forelse($order->credentialVaults as $vault)
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-white/5 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-white">{{ $vault->service_label }}</div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                User: <span class="text-slate-200">{{ $vault->username }}</span> &bull; URL: <a href="{{ $vault->login_url }}" target="_blank" class="text-brand-400 hover:underline">{{ Str::limit($vault->login_url, 30) }}</a>
                            </div>
                        </div>

                        <div>
                            @if($order->isSettlementPaid() || $vault->is_unlocked)
                                <button type="button" onclick="revealCredential({{ $vault->id }}, '{{ $vault->service_label }}', '{{ $vault->username }}')" class="px-3 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold flex items-center gap-1 transition-all">
                                    <ion-icon name="eye-outline"></ion-icon>
                                    <span>Lihat Sandi</span>
                                </button>
                            @else
                                <button type="button" onclick="showAlertModal('Akses Terkunci', 'Kredensial {{ $vault->service_label }} masih terkunci. Silakan selesaikan pembayaran invoice Termin 2 (Pelunasan) untuk membuka brankas.', 'danger')" class="px-3 py-1.5 rounded-lg bg-white/5 text-slate-500 border border-white/5 text-xs font-semibold flex items-center gap-1 cursor-not-allowed">
                                    <ion-icon name="lock-closed"></ion-icon>
                                    <span>Terkunci</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                        Belum ada aset kredensial yang didaftarkan oleh tim developer pada brankas ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Developer Menambahkan Kredensial Baru (Bila role developer/admin/pm) -->
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'pm', 'developer']))
                <div class="pt-4 border-t border-white/10">
                    <button type="button" onclick="document.getElementById('addVaultForm').classList.toggle('hidden')" class="text-xs font-bold text-brand-400 hover:text-brand-300 flex items-center gap-1">
                        <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                        <span>+ Tambah Kredensial Baru ke Brankas (AES-256)</span>
                    </button>

                    <form id="addVaultForm" action="{{ route('vault.store', $order->id) }}" method="POST" class="mt-4 space-y-3 hidden p-4 rounded-xl bg-slate-900 border border-white/10">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Nama Layanan / Akun</label>
                                <input type="text" name="service_label" required placeholder="Contoh: Admin WordPress" class="w-full px-3 py-1.5 rounded-lg glass-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">URL Login</label>
                                <input type="url" name="login_url" required placeholder="https://domain.com/wp-admin" class="w-full px-3 py-1.5 rounded-lg glass-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Username / Akun</label>
                                <input type="text" name="username" required placeholder="admin_webcraft" class="w-full px-3 py-1.5 rounded-lg glass-input text-xs">
                            </div>
                            <div>
                                <label class="text-[11px] text-slate-400 block mb-1">Password (Akan dienkripsi)</label>
                                <input type="text" name="password" required placeholder="Sandi aman..." class="w-full px-3 py-1.5 rounded-lg glass-input text-xs font-mono">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2 rounded-lg bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs transition-all">
                            Simpan & Enkripsi AES-256
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Tiket Revisi & Evaluasi Staging (SRS FR-REV-01 & FR-REV-02) -->
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-6">
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <ion-icon name="repeat-outline" class="text-indigo-400 text-lg"></ion-icon>
                        <span>Tiket Revisi & Umpan Balik Klien</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Siklus perbaikan terukur dengan batas kuota transparan.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Kuota Terpakai:</span>
                    <span class="text-xs font-bold text-white font-mono ml-1">{{ $order->revisions_used }} / {{ $order->package->revision_quota ?? 2 }}</span>
                </div>
            </div>

            <!-- List Tiket Revisi -->
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                @forelse($order->revisionTickets as $ticket)
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-white/5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-indigo-400">{{ $ticket->ticket_number }}</span>
                            @php
                                $tStatus = [
                                    'open' => ['bg' => 'bg-amber-500/20 text-amber-300', 'lbl' => 'Diajukan'],
                                    'approved_for_work' => ['bg' => 'bg-blue-500/20 text-blue-300', 'lbl' => 'Disetujui PM'],
                                    'in_progress' => ['bg' => 'bg-purple-500/20 text-purple-300', 'lbl' => 'Dikerjakan Dev'],
                                    'resolved' => ['bg' => 'bg-emerald-500/20 text-emerald-300', 'lbl' => 'Selesai'],
                                    'rejected' => ['bg' => 'bg-rose-500/20 text-rose-300', 'lbl' => 'Ditolak (Di Luar Scope)'],
                                ];
                                $tBadge = $tStatus[$ticket->status] ?? ['bg' => 'bg-slate-700 text-slate-400', 'lbl' => $ticket->status];
                            @endphp
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $tBadge['bg'] }}">
                                {{ $tBadge['lbl'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $ticket->issue_description }}</p>

                        @if($ticket->developer_response)
                            <div class="p-2.5 rounded-lg bg-white/5 text-[11px] text-slate-300 border-l-2 border-emerald-400">
                                <strong class="text-emerald-400 block">Tanggapan Tim Dev:</strong>
                                {{ $ticket->developer_response }}
                            </div>
                        @endif

                        @if($ticket->is_paid_addon)
                            <div class="text-[10px] text-amber-400 flex items-center gap-1 font-mono">
                                <ion-icon name="alert-circle-outline"></ion-icon>
                                <span>Revisi Berbayar (Invoice Rp 200.000)</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 italic border border-dashed border-white/10 rounded-2xl">
                        Belum ada tiket revisi yang diajukan pada pesanan ini.
                    </div>
                @endforelse
            </div>

            <!-- Form Ajukan Revisi -->
            <div class="pt-4 border-t border-white/10">
                <button type="button" onclick="document.getElementById('addRevisionForm').classList.toggle('hidden')" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                    <ion-icon name="create-outline" class="text-base"></ion-icon>
                    <span>+ Ajukan Catatan / Tiket Revisi Baru</span>
                </button>

                <form id="addRevisionForm" action="{{ route('revisions.store', $order->id) }}" method="POST" class="mt-4 space-y-3 hidden p-4 rounded-xl bg-slate-900 border border-white/10">
                    @csrf
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-1">Pilih Milestone Target Revisi *</label>
                        <select name="milestone_id" required class="w-full px-3 py-2 rounded-lg glass-input text-xs">
                            @foreach($order->milestones as $ms)
                                <option value="{{ $ms->id }}">Fase {{ $ms->stage_number }}: {{ $ms->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-1">Rincian Perbaikan / Masalah *</label>
                        <textarea name="issue_description" required rows="3" placeholder="Tuliskan detail kendala, bagian yang perlu disesuaikan, atau perbaikan teks/tampilan..." class="w-full px-3 py-2 rounded-lg glass-input text-xs"></textarea>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-1">URL Target Halaman / Link Screenshot Aset (Opsional)</label>
                        <input type="url" name="target_url" placeholder="https://staging.link/page atau Google Drive link" class="w-full px-3 py-1.5 rounded-lg glass-input text-xs">
                    </div>

                    @if($order->revisions_used >= ($order->package->revision_quota ?? 2))
                        <div class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[11px]">
                            <ion-icon name="information-circle-outline"></ion-icon>
                            <span>Perhatian: Kuota revisi gratis telah habis. Pengajuan ini akan otomatis menerbitkan invoice add-on revisi sebesar Rp 200.000.</span>
                        </div>
                    @endif

                    <button type="submit" class="w-full py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all">
                        Kirim Tiket Revisi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Faktur Tagihan (Split Invoicing Termin 1 & 2) -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-6">
        <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
            <ion-icon name="receipt-outline" class="text-brand-400 text-lg"></ion-icon>
            <span>Daftar Faktur Pembayaran (Split Billing)</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($order->invoices as $inv)
                <div class="p-5 rounded-2xl bg-slate-900/60 border border-white/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-slate-300">{{ $inv->invoice_number }}</span>
                            @if($inv->isPaid())
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    LUNAS
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    BELUM DIBAYAR
                                </span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <div class="text-xs text-slate-400">
                                @if($inv->term_type === 'TERM_1_DP')
                                    Termin 1 (DP 50% Mulai Pengerjaan)
                                @elseif($inv->term_type === 'TERM_2_SETTLEMENT')
                                    Termin 2 (Pelunasan 50% & Buka Kredensial)
                                @else
                                    Faktur Tambahan / Revisi Ekstra
                                @endif
                            </div>
                            <div class="text-2xl font-extrabold text-white mt-0.5">
                                Rp {{ number_format($inv->amount, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">
                                Jatuh Tempo: {{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-white/5 flex items-center justify-between">
                        @if($inv->isPaid())
                            <div class="text-[11px] text-emerald-400 font-mono flex items-center gap-1">
                                <ion-icon name="checkmark-done-circle"></ion-icon>
                                <span>Lunas ({{ $inv->paid_at ? $inv->paid_at->format('d/m/Y') : '-' }})</span>
                            </div>
                            <a href="{{ route('invoices.show', $inv->id) }}" class="text-xs text-slate-300 hover:text-white underline">Lihat Bukti</a>
                        @else
                            <a href="{{ route('invoices.show', $inv->id) }}" class="w-full py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs text-center transition-all flex items-center justify-center gap-1">
                                <span>Buka Pembayaran / Simulasi</span>
                                <ion-icon name="arrow-forward"></ion-icon>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmSignoff(btn, milestoneTitle) {
        showConfirmModal(
            'Konfirmasi Digital Sign-Off',
            `Apakah Anda menyetujui hasil pengerjaan pada <strong>${milestoneTitle}</strong>?<br><br>Persetujuan digital ini akan dicatat ke dalam audit trail (Timestamp & IP) dan membuka tahapan berikutnya.`,
            function() {
                btn.closest('form').submit();
            },
            {
                confirmText: 'Ya, Setujui Tahapan Ini',
                cancelText: 'Batal',
                icon: 'finger-print-outline'
            }
        );
    }

    function revealCredential(vaultId, serviceLabel, username) {
        fetch(`/vault/${vaultId}/reveal`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlertModal(
                        `Kredensial: ${serviceLabel}`,
                        `<div class="space-y-2 font-mono text-xs bg-slate-900 p-3 rounded-xl border border-white/10 text-left">
                            <div><strong class="text-slate-400">Username:</strong> <span class="text-white">${data.username}</span></div>
                            <div><strong class="text-slate-400">Password:</strong> <span class="text-emerald-400 font-bold select-all">${data.password}</span></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">Kredensial didekripsi secara aman dengan AES-256-CBC dari brankas agensi.</p>`
                    );
                } else {
                    showAlertModal('Akses Ditolak', data.message, 'danger');
                }
            })
            .catch(err => {
                showAlertModal('Gagal Mengambil Kredensial', 'Terjadi kesalahan sistem dalam proses dekripsi kredensial.', 'danger');
            });
    }
</script>
@endpush
