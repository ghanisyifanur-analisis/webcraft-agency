@extends('layouts.app')

@section('title', 'Faktur Tagihan: ' . $invoice->invoice_number . ' - WebCraft Agency')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('orders.show', $invoice->order_id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>Kembali ke Detail Proyek ({{ $invoice->order->order_number ?? '' }})</span>
        </a>

        @if($invoice->isPaid())
            <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5">
                <ion-icon name="checkmark-circle"></ion-icon>
                <span>STATUS: LUNAS (PAID)</span>
            </span>
        @else
            <span class="text-xs font-mono font-bold px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 flex items-center gap-1.5">
                <ion-icon name="time-outline"></ion-icon>
                <span>STATUS: MENUNGGU PEMBAYARAN</span>
            </span>
        @endif
    </div>

    <!-- Lembar Faktur Digital (Printable) -->
    <div class="glass-panel p-6 sm:p-10 rounded-3xl border border-white/10 shadow-2xl space-y-8" id="invoiceSheet">
        <!-- Header Faktur -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/10 pb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white text-2xl shadow-lg shadow-brand-500/30">
                    <ion-icon name="receipt-outline"></ion-icon>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">FAKTUR TAGIHAN RESMI</h1>
                    <div class="text-xs text-brand-300 font-mono mt-0.5">{{ $invoice->invoice_number }}</div>
                </div>
            </div>

            <div class="text-left sm:text-right text-xs text-slate-400 space-y-0.5">
                <div>Tanggal Terbit: <strong class="text-slate-200">{{ $invoice->created_at->format('d M Y') }}</strong></div>
                <div>Jatuh Tempo: <strong class="text-rose-400">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</strong></div>
            </div>
        </div>

        <!-- Info Penerima & Agensi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
                <span class="text-slate-500 uppercase font-bold text-[10px] tracking-wider block mb-1">Ditagihkan Kepada:</span>
                <div class="text-sm font-bold text-white">{{ $invoice->order->client->company_name ?? 'Klien' }}</div>
                <div class="text-slate-300 mt-1">U.P. {{ $invoice->order->client->user->name ?? '-' }}</div>
                <div class="text-slate-400 mt-0.5">WhatsApp: {{ $invoice->order->client->whatsapp_no ?? '-' }}</div>
                <div class="text-slate-400 mt-0.5">Email: {{ $invoice->order->client->user->email ?? '-' }}</div>
            </div>

            <div class="sm:text-right">
                <span class="text-slate-500 uppercase font-bold text-[10px] tracking-wider block mb-1">Penyedia Layanan:</span>
                <div class="text-sm font-bold text-white">WebCraft Agency Platform</div>
                <div class="text-slate-300 mt-1">Divisi Keuangan & Billing Terintegrasi</div>
                <div class="text-slate-400 mt-0.5">Automated Midtrans Gateway Simulator</div>
                <div class="text-brand-400 mt-0.5 font-mono">finance@webcraft.id</div>
            </div>
        </div>

        <!-- Tabel Item Tagihan -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-white/5 rounded-2xl overflow-hidden">
                <thead class="bg-white/5 text-slate-300 uppercase text-[10px] tracking-wider border-b border-white/5">
                    <tr>
                        <th class="p-3.5">Deskripsi Item</th>
                        <th class="p-3.5 text-center">Tipe Termin</th>
                        <th class="p-3.5 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <tr>
                        <td class="p-3.5">
                            <div class="font-bold text-white">{{ $invoice->order->package->name ?? 'Layanan Website' }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                @if($invoice->term_type === 'TERM_1_DP')
                                    Termin 1 (Uang Muka / Down Payment 50%). Membuka fase pengisian brief & perancangan sistem.
                                @elseif($invoice->term_type === 'TERM_2_SETTLEMENT')
                                    Termin 2 (Pelunasan 50%). Membuka akses brankas kredensial (AES-256) & penyerahan BAST.
                                @else
                                    Add-on Biaya Tiket Revisi Tambahan di luar kuota gratis paket.
                                @endif
                            </div>
                        </td>
                        <td class="p-3.5 text-center font-mono text-indigo-300">
                            {{ $invoice->term_type }}
                        </td>
                        <td class="p-3.5 text-right font-extrabold text-white text-sm font-mono">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-white/5 border-t border-white/10">
                    <tr>
                        <td colspan="2" class="p-3.5 font-bold text-right text-slate-300">Total Tagihan Bersih:</td>
                        <td class="p-3.5 text-right font-black text-brand-400 text-lg font-mono">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->isPaid() && count($invoice->payments) > 0)
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-xs">
                <div class="font-bold text-emerald-400 flex items-center gap-1.5 mb-2">
                    <ion-icon name="checkmark-done-circle" class="text-base"></ion-icon>
                    <span>Informasi Pembayaran Terverifikasi</span>
                </div>
                @foreach($invoice->payments as $p)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] font-mono text-slate-300 pt-1 border-t border-emerald-500/20">
                        <div>Kanal: <span class="text-white">{{ $p->payment_channel }}</span></div>
                        <div>Referensi: <span class="text-white">{{ $p->reference_code }}</span></div>
                        <div>Nominal: <span class="text-white">Rp {{ number_format($p->amount_paid, 0, ',', '.') }}</span></div>
                        <div>Waktu: <span class="text-white">{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '-' }}</span></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Simulator Pembayaran Instan (SRS FR-PAY-01 & FR-PAY-02) -->
    @if(!$invoice->isPaid())
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-brand-500/30 space-y-6">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <h2 class="text-sm font-bold uppercase tracking-wider text-brand-300">Simulator Gateway Pembayaran Otomatis</h2>
            </div>
            <p class="text-xs text-slate-300">
                Untuk kemudahan evaluasi dan pengujian dosen, Anda dapat memilih saluran pembayaran simulasi berikut untuk menyelesaikan invoice ini seketika (Idempotent Webhook Simulation):
            </p>

            <form action="{{ route('invoices.simulate', $invoice->id) }}" method="POST" id="simPaymentForm">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
                    <!-- QRIS -->
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_channel" value="QRIS" checked class="peer hidden">
                        <div class="p-3 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/20 text-center transition-all">
                            <div class="w-8 h-8 rounded-lg bg-white/10 mx-auto flex items-center justify-center text-lg mb-1 text-white">
                                <ion-icon name="qr-code-outline"></ion-icon>
                            </div>
                            <div class="text-xs font-bold text-white">QRIS Instant</div>
                            <div class="text-[10px] text-slate-400">Semua E-Wallet</div>
                        </div>
                    </label>

                    <!-- BCA VA -->
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_channel" value="BCA_VA" class="peer hidden">
                        <div class="p-3 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/20 text-center transition-all">
                            <div class="w-8 h-8 rounded-lg bg-white/10 mx-auto flex items-center justify-center text-lg mb-1 text-white">
                                <ion-icon name="card-outline"></ion-icon>
                            </div>
                            <div class="text-xs font-bold text-white">BCA VA</div>
                            <div class="text-[10px] text-slate-400">Virtual Account</div>
                        </div>
                    </label>

                    <!-- Mandiri VA -->
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_channel" value="MANDIRI_VA" class="peer hidden">
                        <div class="p-3 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/20 text-center transition-all">
                            <div class="w-8 h-8 rounded-lg bg-white/10 mx-auto flex items-center justify-center text-lg mb-1 text-white">
                                <ion-icon name="wallet-outline"></ion-icon>
                            </div>
                            <div class="text-xs font-bold text-white">Mandiri VA</div>
                            <div class="text-[10px] text-slate-400">Livin' Mandiri</div>
                        </div>
                    </label>

                    <!-- BNI VA -->
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_channel" value="BNI_VA" class="peer hidden">
                        <div class="p-3 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/20 text-center transition-all">
                            <div class="w-8 h-8 rounded-lg bg-white/10 mx-auto flex items-center justify-center text-lg mb-1 text-white">
                                <ion-icon name="cash-outline"></ion-icon>
                            </div>
                            <div class="text-xs font-bold text-white">BNI VA</div>
                            <div class="text-[10px] text-slate-400">Virtual Account</div>
                        </div>
                    </label>

                    <!-- BRI VA -->
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_channel" value="BRI_VA" class="peer hidden">
                        <div class="p-3 rounded-xl glass-panel border border-white/10 peer-checked:border-brand-500 peer-checked:bg-brand-500/20 text-center transition-all">
                            <div class="w-8 h-8 rounded-lg bg-white/10 mx-auto flex items-center justify-center text-lg mb-1 text-white">
                                <ion-icon name="business-outline"></ion-icon>
                            </div>
                            <div class="text-xs font-bold text-white">BRI VA</div>
                            <div class="text-[10px] text-slate-400">BRIVA</div>
                        </div>
                    </label>
                </div>

                <button type="button" onclick="confirmPaymentSimulation()" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-xl shadow-emerald-500/25 transition-all flex items-center justify-center gap-2">
                    <ion-icon name="checkmark-done-circle-outline" class="text-xl"></ion-icon>
                    <span>Simulasikan Pembayaran Lunas Sekarang</span>
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmPaymentSimulation() {
        const selectedChannel = document.querySelector('input[name="payment_channel"]:checked').value;
        const totalRp = 'Rp {{ number_format($invoice->amount, 0, ',', '.') }}';

        showConfirmModal(
            'Simulasi Pelunasan Invoice',
            `Apakah Anda ingin mengkonfirmasi simulasi pembayaran <strong>${totalRp}</strong> via kanal <strong>${selectedChannel}</strong>?<br><br>Status invoice akan seketika diubah menjadi LUNAS dan alur proyek akan diaktifkan secara otomatis.`,
            function() {
                document.getElementById('simPaymentForm').submit();
            },
            {
                confirmText: 'Ya, Bayar Lunas',
                cancelText: 'Batal',
                icon: 'wallet-outline'
            }
        );
    }
</script>
@endpush
