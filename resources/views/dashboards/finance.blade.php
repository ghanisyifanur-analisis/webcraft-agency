@extends('layouts.app')

@section('title', 'Finance & Accounting Dashboard - WebCraft Agency')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Billing & COGS Financial Control</span>
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1">Finance & Accounting</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemantauan arus kas masuk faktur, pencatatan biaya pokok penjualan (COGS), dan kalkulasi laba.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login.quick', 'client') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all">
                Ganti ke Klien Portal &rarr;
            </a>
        </div>
    </div>

    <!-- 4 Metrik Keuangan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Total Dana Diterima (Cash In)</span>
                <ion-icon name="cash-outline" class="text-lg text-emerald-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-emerald-300 mt-2 font-mono">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Pembayaran lunas terverifikasi</div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Piutang / Pending Invoices</span>
                <ion-icon name="time-outline" class="text-lg text-amber-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-amber-300 mt-2 font-mono">
                Rp {{ number_format($totalPending, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Menunggu pelunasan klien</div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Total Modal Proyek (COGS)</span>
                <ion-icon name="cart-outline" class="text-lg text-rose-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-rose-300 mt-2 font-mono">
                Rp {{ number_format($totalExpenses, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Pembelian domain, hosting, lisensi</div>
        </div>

        <div class="glass-panel p-5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span>Laba Bersih Agensi (Net)</span>
                <ion-icon name="wallet-outline" class="text-lg text-indigo-400"></ion-icon>
            </div>
            <div class="text-2xl font-black text-indigo-300 mt-2 font-mono">
                Rp {{ number_format($netIncome, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Cash in dikurangi total COGS</div>
        </div>
    </div>

    <!-- Form Input Pengeluaran Modal Proyek (COGS) -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-amber-500/30 space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <ion-icon name="add-circle-outline" class="text-amber-400 text-xl"></ion-icon>
                    <span>Catat Pengeluaran Modal Proyek (COGS / HPP)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Catat biaya riil pembelian domain, sewa server/hosting, atau pembelian lisensi plugin per order.</p>
            </div>
        </div>

        <form action="{{ route('finance.logs.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Target Pesanan Proyek *</label>
                <select name="order_id" required class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                    @foreach($orders as $ord)
                        <option value="{{ $ord->id }}">{{ $ord->order_number }} - {{ $ord->client->company_name ?? 'Klien' }} ({{ $ord->package->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Kategori Pengeluaran *</label>
                <select name="category" required class="w-full px-3 py-2 rounded-xl glass-input text-xs">
                    <option value="domain">Registrasi Domain (.com / .id)</option>
                    <option value="hosting">Sewa Hosting / Cloud VPS</option>
                    <option value="plugin">Lisensi Tema / Plugin / Addon</option>
                    <option value="freelance">Jasa Freelance Spesialis</option>
                    <option value="other">Lainnya / Operasional</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nama Item Pengeluaran *</label>
                <input type="text" name="expense_title" required placeholder="Contoh: Domain namabisnis.com" class="w-full px-3 py-2 rounded-xl glass-input text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nominal Biaya (Rp) *</label>
                <input type="number" name="expense_amount" required min="0" placeholder="Contoh: 165000" class="w-full px-3 py-2 rounded-xl glass-input text-xs font-mono">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nama Vendor / Penyedia</label>
                <input type="text" name="vendor_name" placeholder="Niagahoster, DomaiNesia, Envato, dll" class="w-full px-3 py-2 rounded-xl glass-input text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Tanggal Transaksi *</label>
                <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl glass-input text-xs">
            </div>

            <div class="sm:col-span-2 lg:col-span-3 flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2">
                    <ion-icon name="save-outline"></ion-icon>
                    <span>Simpan Pengeluaran Modal</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Faktur Invoice & Arus Kas Masuk -->
    <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-4">
        <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
            <ion-icon name="receipt-outline" class="text-amber-400 text-xl"></ion-icon>
            <span>Daftar Seluruh Faktur Invoice Agensi</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-white/5 rounded-2xl overflow-hidden">
                <thead class="bg-white/5 text-slate-300 uppercase text-[10px] tracking-wider border-b border-white/5">
                    <tr>
                        <th class="p-3.5">No. Invoice</th>
                        <th class="p-3.5">Pesanan Terkait</th>
                        <th class="p-3.5">Tipe Tagihan</th>
                        <th class="p-3.5">Nominal</th>
                        <th class="p-3.5">Jatuh Tempo</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-3.5 font-mono font-bold text-brand-300">{{ $inv->invoice_number }}</td>
                            <td class="p-3.5 text-slate-300">{{ $inv->order->order_number ?? '-' }}</td>
                            <td class="p-3.5 font-mono text-slate-400">{{ $inv->term_type }}</td>
                            <td class="p-3.5 font-mono font-bold text-white">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-slate-400">{{ $inv->due_date ? $inv->due_date->format('d/m/Y') : '-' }}</td>
                            <td class="p-3.5">
                                @if($inv->isPaid())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300">LUNAS</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300">PENDING</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="text-xs text-brand-400 hover:underline">
                                    Buka Faktur &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-xs text-slate-500 italic">Belum ada invoice diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
