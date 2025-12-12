{{-- resources/views/layouts/app.blade.php --}}
{{-- 1 SIDEBAR – TOMBOL OTOMATIS SESUAI ROLE --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SICIP POLRI')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-active { @apply bg-black text-white; }
    </style>
</head>
<body class="h-full bg-white font-sans antialiased">

<div class="flex h-full">

    <!-- SIDEBAR SATU – TERGANTUNG ROLE -->
    <div class="w-64 bg-white text-black flex flex-col shadow-xl">
        
        <!-- HEADER SICIP (SAMA UNTUK SEMUA ROLE) -->
        <div class="p-6 border-b border-black">
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <i class="fas fa-shield-alt text-yellow-400"></i>
                SICIP POLRI
            </h1>
            <p class="text-xs opacity-80 mt-1">Sistem Cuti & Izin Personel</p>
        </div>


        <!-- MENU SESUAI ROLE -->
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                
                <!-- PERSONEL -->
                @if(Auth::guard('personel')->check())
                    <li>
                        <a href="{{ route('personel.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 transition 
                                  {{ request()->routeIs('personel.dashboard') ? 'bg-gray-200' : '' }}">
                            <i class="fas fa-home text-lg "></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('personel.pengajuan.create') }}" 
                            class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 transition {{ request()->routeIs('personel.pengajuan.create') ? 'bg-gray-200' : '' }}">
                                <i class="fas fa-plus-circle text-black text-lg"></i>
                                <span class="">Pengajuan Baru</span>
                            </a>
                        </a>
                    </li>
                
                <!-- RENMIN -->
                @elseif(Auth::guard('renmin')->check())
                    <li>
                        <a href="{{ route('renmin.dashboard') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 transition 
                                  {{ request()->routeIs('renmin.dashboard') ? 'bg-gray-200' : '' }}">
                            <i class="fas fa-tachometer-alt text-lg"></i>
                            <span class="font-bold">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('renmin.validasi') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-200 transition 
                                  {{ request()->routeIs('renmin.validasi') ? 'bg-gray-200' : '' }}">
                            <i class="fas fa-clipboard-check text-lg"></i>
                            <span class="font-bold">Validasi Pengajuan</span>
                        </a>
                    </li>
                
                <!-- PIMPINAN -->
                @elseif(Auth::guard('pimpinan')->check())
                    <li>
                        <a href="{{ route('dashboard.pimpinan') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition 
                                  {{ request()->routeIs('dashboard.pimpinan') ? 'bg-indigo-700' : '' }}">
                            <i class="fas fa-crown text-lg"></i>
                            <span class="font-bold">DASHBOARD</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>

        <!-- LOGOUT SESUAI ROLE -->
        <div class="p-4 border-t border-black">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-3 px-6 py-4 rounded-xl font-bold w-full hover:bg-gray-500 hover:bg-opacity-30 transition text-center">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>LOGOUT</span>
                </button>
            </form>
            <p class="text-xs opacity-75 mt-4 text-center">© 2025 POLRI</p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- HEADER -->
        <header class="bg-white shadow-lg border-b border-gray-200">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-indigo-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-700">
                            @if(Auth::guard('personel')->check())
                                {{ Auth::guard('personel')->user()->name }} <span class="text-xs">(Personel)</span>
                            @elseif(Auth::guard('renmin')->check())
                                {{ Auth::guard('renmin')->user()->username }} <span class="text-xs">(Renmin)</span>
                            @elseif(Auth::guard('pimpinan')->check())
                                {{ Auth::guard('pimpinan')->user()->username }} <span class="text-xs">(Pimpinan)</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">{{ now()->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto px-4 py-8">

                @yield('content')
            </div>
        </main>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    // Toggle sidebar mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.querySelector('.w-64').classList.toggle('-translate-x-full');
    });

    // Logout confirmation
    document.querySelector('form[action*="logout"]')?.addEventListener('submit', (e) => {
        e.preventDefault();
        if (confirm('Yakin ingin keluar dari SICIP POLRI?')) {
            e.target.submit();
        }
    });
</script>
</body>
</html>