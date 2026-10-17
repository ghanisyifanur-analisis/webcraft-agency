@extends('layouts.app')

@section('title', 'Formulir Brief Kebutuhan Proyek - WebCraft Agency')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span>Kembali ke Detail Proyek</span>
        </a>
        <h1 class="text-3xl font-extrabold text-white mt-2">Formulir Brief Proyek Website</h1>
        <p class="text-sm text-slate-400 mt-1">
            Pesanan: <strong class="text-white font-mono">{{ $order->order_number }}</strong> &bull; Paket: <span class="text-brand-300 font-semibold">{{ $order->package->name }}</span>
        </p>
    </div>

    @if($order->brief && $order->brief->status === 'NEED_REVISION')
        <div class="p-5 rounded-2xl glass-panel border-amber-500/30 bg-amber-500/10 text-amber-200 text-xs mb-6 space-y-2">
            <div class="flex items-center gap-2 font-bold text-amber-300">
                <ion-icon name="alert-circle-outline" class="text-lg"></ion-icon>
                <span>Catatan Peninjauan dari Project Manager (Perlu Revisi Brief):</span>
            </div>
            <p class="leading-relaxed pl-6">{{ $order->brief->pm_feedback }}</p>
        </div>
    @endif

    <form action="{{ route('briefs.store', $order->id) }}" method="POST" enctype="multipart/form-data" id="briefForm" class="space-y-6">
        @csrf
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 space-y-6">
            <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-4">
                <ion-icon name="color-palette-outline" class="text-brand-400 text-lg"></ion-icon>
                <span>Identitas Brand & Preferensi Visual</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Brand / Judul Website *</label>
                    <input type="text" name="brand_name" required value="{{ old('brand_name', $order->brief->brand_name ?? $order->client->company_name ?? '') }}" placeholder="Contoh: Coffee Craft Nusantara" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Slogan / Tagline Bisnis</label>
                    <input type="text" name="tagline_description" value="{{ old('tagline_description', $order->brief->tagline_description ?? '') }}" placeholder="Contoh: Cita Rasa Kopi Asli Indonesia" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Preferensi Palet Warna Utama</label>
                    <input type="text" name="color_palette" value="{{ old('color_palette', $order->brief->color_palette ?? '#0F172A, #6366F1, #F8FAFC') }}" placeholder="Contoh: Biru Navy & Emas (#1e3a8a, #d97706)" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                    <span class="text-[11px] text-slate-500">Bisa berupa kode hex warna atau deskripsi warna brand Anda.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Audiens / Pengunjung Utama</label>
                    <input type="text" name="target_audience" value="{{ old('target_audience', $order->brief->target_audience ?? '') }}" placeholder="Contoh: Mahasiswa, Profesional usia 20-40 tahun" class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">URL Website Referensi / Kompetitor</label>
                <textarea name="competitor_references" rows="2" placeholder="Cantumkan tautan website yang Anda sukai sebagai acuan gaya tata letak atau fungsionalitas..." class="w-full px-4 py-2 rounded-xl glass-input text-sm">{{ old('competitor_references', $order->brief->competitor_references ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-white/5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Unggah Berkas Logo / Vektor Brand (Maks 25MB)</label>
                    <input type="file" name="logo_file" accept=".png,.jpg,.jpeg,.svg,.zip,.pdf" class="w-full px-3 py-2 rounded-xl glass-input text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-600 file:text-white hover:file:bg-brand-500 cursor-pointer">
                    @if($order->brief && $order->brief->logo_file_path)
                        <div class="text-[11px] text-emerald-400 mt-1 font-mono">
                            Logo tersimpan: {{ basename($order->brief->logo_file_path) }}
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Tautan Aset Pendukung (Google Drive / Figma / Dropbox)</label>
                    <input type="url" name="supporting_assets_url" value="{{ old('supporting_assets_url', $order->brief->supporting_assets_url ?? '') }}" placeholder="https://drive.google.com/drive/folders/..." class="w-full px-4 py-2.5 rounded-xl glass-input text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Catatan Spesifikasi Fitur Khusus</label>
                <textarea name="feature_notes" rows="4" placeholder="Tuliskan jika ada kebutuhan form khusus, integrasi API, atau alur kerja khusus yang diinginkan..." class="w-full px-4 py-2 rounded-xl glass-input text-sm">{{ old('feature_notes', $order->brief->feature_notes ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('orders.show', $order->id) }}" class="px-5 py-2.5 rounded-xl glass-panel text-slate-300 hover:text-white text-xs font-semibold transition-all">
                Batal
            </a>
            <button type="button" onclick="confirmBriefSubmit()" class="px-7 py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-lg shadow-brand-500/25 transition-all flex items-center gap-2">
                <ion-icon name="send-outline" class="text-base"></ion-icon>
                <span>Simpan & Kirim Brief ke Project Manager</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function confirmBriefSubmit() {
        const form = document.getElementById('briefForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        showConfirmModal(
            'Konfirmasi Penyerahan Brief',
            'Apakah seluruh informasi brief kebutuhan proyek website Anda sudah lengkap? Project Manager akan meninjau dan memulai Milestone 1.',
            function() {
                form.submit();
            },
            {
                confirmText: 'Ya, Serahkan Brief',
                cancelText: 'Periksa Lagi',
                icon: 'document-text-outline'
            }
        );
    }
</script>
@endpush
