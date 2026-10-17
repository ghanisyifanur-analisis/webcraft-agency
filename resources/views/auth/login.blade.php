@extends('layouts.app')

@section('title', 'Masuk Akun & Demo Multi-Role - WebCraft Agency')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center max-w-xl mx-auto mb-8">
        <h1 class="text-3xl font-extrabold text-white">Akses Portal Agensi</h1>
        <p class="text-sm text-slate-400 mt-2">Masuk ke ruang kerja terintegrasi WebCraft Agency sesuai peran dan hak akses Anda.</p>
    </div>

    <!-- Quick Role Switcher (SRS FR-AUTH Evaluator Access) -->
    <div class="glass-panel p-6 rounded-3xl border border-brand-500/30 mb-8">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-300">Akses Pengujian Dosen & Evaluator (1-Click Login)</span>
        </div>
        <p class="text-xs text-slate-300 mb-5">Klik tombol di bawah ini untuk langsung masuk ke dashboard modul peran yang ingin diuji tanpa mengetik kredensial:</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <!-- 1. Admin -->
            <a href="{{ route('login.quick', 'admin') }}" class="p-3 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-center transition-all group">
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-300 mx-auto flex items-center justify-center text-lg mb-1 group-hover:scale-110 transition-transform">
                    <ion-icon name="shield-outline"></ion-icon>
                </div>
                <div class="text-xs font-bold text-white">Super Admin</div>
                <div class="text-[10px] text-purple-300 font-mono mt-0.5">Overview & Staff</div>
            </a>

            <!-- 2. Project Manager -->
            <a href="{{ route('login.quick', 'pm') }}" class="p-3 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-center transition-all group">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-300 mx-auto flex items-center justify-center text-lg mb-1 group-hover:scale-110 transition-transform">
                    <ion-icon name="briefcase-outline"></ion-icon>
                </div>
                <div class="text-xs font-bold text-white">Project Manager</div>
                <div class="text-[10px] text-blue-300 font-mono mt-0.5">Brief & Sprint</div>
            </a>

            <!-- 3. Developer -->
            <a href="{{ route('login.quick', 'developer') }}" class="p-3 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-center transition-all group">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 mx-auto flex items-center justify-center text-lg mb-1 group-hover:scale-110 transition-transform">
                    <ion-icon name="code-working-outline"></ion-icon>
                </div>
                <div class="text-xs font-bold text-white">Developer / Dev</div>
                <div class="text-[10px] text-emerald-300 font-mono mt-0.5">Tasks & Staging</div>
            </a>

            <!-- 4. Finance -->
            <a href="{{ route('login.quick', 'finance') }}" class="p-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-center transition-all group">
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 mx-auto flex items-center justify-center text-lg mb-1 group-hover:scale-110 transition-transform">
                    <ion-icon name="receipt-outline"></ion-icon>
                </div>
                <div class="text-xs font-bold text-white">Finance</div>
                <div class="text-[10px] text-amber-300 font-mono mt-0.5">Invoice & COGS</div>
            </a>

            <!-- 5. Client -->
            <a href="{{ route('login.quick', 'client') }}" class="p-3 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 text-center transition-all group col-span-2 sm:col-span-1">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-300 mx-auto flex items-center justify-center text-lg mb-1 group-hover:scale-110 transition-transform">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div class="text-xs font-bold text-white">Klien Portal</div>
                <div class="text-[10px] text-indigo-300 font-mono mt-0.5">Order & Vault</div>
            </a>
        </div>
    </div>

    <!-- Manual Login Form -->
    <div class="max-w-md mx-auto glass-panel p-8 rounded-3xl border border-white/10 shadow-2xl">
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-lg">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl glass-input text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-lg">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 rounded-xl glass-input text-sm">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-400">
                    <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-700 text-brand-600 focus:ring-brand-500">
                    <span>Ingat Saya</span>
                </label>
                <a href="#" onclick="showAlertModal('Reset Sandi', 'Untuk mengatur ulang kata sandi, silakan hubungi tim administrator agensi melalui kontak resmi WhatsApp.')" class="text-brand-400 hover:text-brand-300">Lupa sandi?</a>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <ion-icon name="log-in-outline" class="text-lg"></ion-icon>
            </button>
        </form>

        <!-- Default Credentials Hint -->
        <div class="mt-6 pt-5 border-t border-white/5 text-center">
            <span class="text-[11px] text-slate-500">Kredensial Default Uji Coba:</span>
            <div class="mt-1 font-mono text-[11px] text-slate-400">
                admin@webcraft.id / admin123
            </div>
        </div>
    </div>
</div>
@endsection
