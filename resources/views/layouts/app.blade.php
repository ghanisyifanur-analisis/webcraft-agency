<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WebCraft Agency') - Professional Web Development Platform</title>
    
    <!-- Google Fonts & Tailwind CSS CDN -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        dark: {
                            base: '#0B0F19',
                            card: 'rgba(17, 24, 39, 0.75)',
                            border: 'rgba(255, 255, 255, 0.08)',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #07090E;
            color: #F3F4F6;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.10) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(14, 165, 233, 0.06) 0px, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-nav {
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #F3F4F6;
            transition: all 0.2s ease;
        }

        .glass-input:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0B0F19;
        }
        ::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4B5563;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen antialiased selection:bg-brand-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-400 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform duration-200">
                            <ion-icon name="code-slash-outline" class="text-xl font-bold"></ion-icon>
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-white via-slate-100 to-brand-400 bg-clip-text text-transparent">WebCraft</span>
                            <span class="text-xs font-semibold px-2 py-0.5 ml-1.5 rounded-full bg-brand-500/10 text-brand-400 border border-brand-500/20">Agency</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('home') }}#katalog" class="text-slate-300 hover:text-white transition-colors">Paket Layanan</a>
                    <a href="{{ route('home') }}#kalkulator" class="text-slate-300 hover:text-white transition-colors">Kalkulator Estimasi</a>
                    <a href="{{ route('home') }}#alur-kerja" class="text-slate-300 hover:text-white transition-colors">5 Fase Alur Kerja</a>
                    
                    @auth
                        <div class="h-4 w-px bg-slate-800"></div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                            <ion-icon name="speedometer-outline" class="text-base"></ion-icon>
                            <span>Dashboard ({{ strtoupper(auth()->user()->role) }})</span>
                        </a>
                    @endauth
                </nav>

                <!-- Action Button & User Role Switcher -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-2">
                            <div class="text-right hidden sm:block">
                                <div class="text-xs font-semibold text-white leading-tight">{{ auth()->user()->name }}</div>
                                <div class="text-[11px] text-brand-400 font-mono">{{ auth()->user()->role }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="button" onclick="confirmLogout(this)" class="p-2 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all text-sm flex items-center gap-1" title="Logout">
                                    <ion-icon name="log-out-outline" class="text-lg"></ion-icon>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors">Masuk</a>
                        <a href="{{ route('home') }}#kalkulator" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gradient-to-r from-brand-600 to-indigo-500 text-white shadow-md shadow-brand-500/20 hover:from-brand-500 hover:to-indigo-400 transition-all">Pesan Website</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl glass-panel border-emerald-500/30 bg-emerald-500/10 text-emerald-300 flex items-center justify-between text-sm">
                    <div class="flex items-center gap-3">
                        <ion-icon name="checkmark-circle" class="text-2xl text-emerald-400 flex-shrink-0"></ion-icon>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200 text-lg">
                        <ion-icon name="close"></ion-icon>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl glass-panel border-rose-500/30 bg-rose-500/10 text-rose-300 flex items-center justify-between text-sm">
                    <div class="flex items-center gap-3">
                        <ion-icon name="alert-circle" class="text-2xl text-rose-400 flex-shrink-0"></ion-icon>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200 text-lg">
                        <ion-icon name="close"></ion-icon>
                    </button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl glass-panel border-amber-500/30 bg-amber-500/10 text-amber-300 text-sm">
                    <div class="flex items-center gap-2 font-semibold mb-2">
                        <ion-icon name="warning-outline" class="text-xl text-amber-400"></ion-icon>
                        <span>Perhatian! Terdapat kendala input:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs text-amber-200/90 pl-2">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-white/5 py-8 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>WebCraft Agency Platform &bull; Academic Production Engine</span>
            </div>
            <div>
                &copy; {{ date('Y') }} MaoneArt &bull; Web Programming Project
            </div>
        </div>
    </footer>

    <!-- MaoneArt Glassmorphism Modal System (Rule 5 Compliant: 100% Symmetrical 2-Column Grid) -->
    <div id="maoneModalOverlay" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
        <div id="maoneModalBox" class="w-full max-w-md rounded-2xl glass-panel p-6 transform scale-95 transition-all duration-300 border border-white/10 shadow-2xl relative text-left">
            <div class="flex items-start gap-4">
                <div id="modalIconContainer" class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 bg-brand-500/20 text-brand-400 border border-brand-500/30">
                    <ion-icon id="modalIcon" name="information-circle"></ion-icon>
                </div>
                <div class="flex-grow">
                    <h3 id="modalTitle" class="text-lg font-bold text-white mb-1">Konfirmasi</h3>
                    <p id="modalMessage" class="text-sm text-slate-300 leading-relaxed">Pesan modal</p>
                </div>
            </div>

            <!-- Symmetrical 2-Column Action Buttons (w-full, grid grid-cols-2 gap-3) -->
            <div class="grid grid-cols-2 gap-3 w-full mt-6">
                <button type="button" id="modalBtnCancel" onclick="closeMaoneModal()" class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-all text-center flex items-center justify-center">
                    Batal
                </button>
                <button type="button" id="modalBtnConfirm" class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-500/25 transition-all text-center flex items-center justify-center">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
        // MaoneArt Glassmorphism Modal Implementation
        let modalConfirmCallback = null;

        function showConfirmModal(title, message, onConfirm, options = {}) {
            const overlay = document.getElementById('maoneModalOverlay');
            const box = document.getElementById('maoneModalBox');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalIconContainer = document.getElementById('modalIconContainer');
            const modalIcon = document.getElementById('modalIcon');
            const btnCancel = document.getElementById('modalBtnCancel');
            const btnConfirm = document.getElementById('modalBtnConfirm');

            modalTitle.textContent = title;
            modalMessage.innerHTML = message;
            btnCancel.textContent = options.cancelText || 'Batal';
            btnConfirm.textContent = options.confirmText || 'Lanjutkan';

            // Reset & Apply Button Colors
            btnConfirm.className = 'w-full py-2.5 px-4 rounded-xl text-sm font-semibold transition-all text-center flex items-center justify-center ';
            if (options.isDanger) {
                modalIcon.setAttribute('name', 'warning-outline');
                modalIconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 bg-rose-500/20 text-rose-400 border border-rose-500/30';
                btnConfirm.className += 'bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-500/25';
            } else {
                modalIcon.setAttribute('name', options.icon || 'shield-checkmark-outline');
                modalIconContainer.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 bg-brand-500/20 text-brand-400 border border-brand-500/30';
                btnConfirm.className += 'bg-brand-600 hover:bg-brand-500 text-white shadow-lg shadow-brand-500/25';
            }

            modalConfirmCallback = onConfirm;
            btnConfirm.onclick = function() {
                closeMaoneModal();
                if (modalConfirmCallback) modalConfirmCallback();
            };

            overlay.classList.remove('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-95');
            box.classList.add('scale-100');
        }

        function showAlertModal(title, message, type = 'info') {
            showConfirmModal(title, message, null, {
                confirmText: 'Mengerti',
                cancelText: 'Tutup',
                isDanger: type === 'danger',
                icon: type === 'success' ? 'checkmark-circle-outline' : (type === 'danger' ? 'alert-circle-outline' : 'information-circle-outline')
            });
        }

        function closeMaoneModal() {
            const overlay = document.getElementById('maoneModalOverlay');
            const box = document.getElementById('maoneModalBox');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            box.classList.remove('scale-100');
            box.classList.add('scale-95');
            modalConfirmCallback = null;
        }

        function confirmLogout(btn) {
            showConfirmModal(
                'Konfirmasi Keluar',
                'Apakah Anda yakin ingin mengakhiri sesi aktif ini?',
                function() {
                    btn.closest('form').submit();
                },
                { isDanger: true, confirmText: 'Keluar Sekarang' }
            );
        }
    </script>

    @stack('scripts')
</body>
</html>
