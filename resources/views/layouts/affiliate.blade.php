<!DOCTYPE html>
<html lang="es" class="h-full bg-[#f9fafb]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plataforma Virtual') - ARS CMD</title>
    
    <!-- Google Fonts: Rubik & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- Lector Native Stylesheets -->
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/bootstrap-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/flaticon/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/style.css') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lector: {
                            title: '#403663',
                            desc: 'rgba(64, 54, 99, 0.8)',
                            coral: '#49bcf7', // Replaced with Lector Blue
                            sky: '#49bcf7',
                            border: '#ecf0f3',
                            ash: '#f9fafb',
                            white: '#fefefe',
                            green: '#0be881',
                            red: '#f53b57',
                            yellow: '#dec32b'
                        }
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                        rubik: ['Rubik', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f9fafb;
            color: rgba(64, 54, 99, 0.8);
            font-family: 'Roboto', sans-serif;
        }
        
        /* Lector Custom Dashboard Styles */
        .lector-card {
            background-color: #fefefe;
            border: 1px solid #ecf0f3;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(64, 54, 99, 0.03);
            transition: all 0.3s ease;
        }
        .lector-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(64, 54, 99, 0.08);
            border-color: rgba(73, 188, 247, 0.3); /* Replaced with Blue */
        }
        
        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.3s ease;
            margin-bottom: 4px;
        }
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff;
            padding-left: 24px;
        }
        .sidebar-item-active {
            background-color: #49bcf7 !important; /* Replaced with Lector Blue */
            color: #fff !important;
            box-shadow: 0 5px 15px rgba(73, 188, 247, 0.4);
            padding-left: 24px;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full flex flex-col bg-[#f9fafb] text-slate-700 antialiased" x-data="{ sidebarOpen: false }">

    @php
        $nss_safe = isset($afiliado) ? $afiliado->nss : '—';
        $consumo_real = isset($consumidoMED) ? $consumidoMED : 0;
        $tope_total = 12000.00;
        $porcentaje_consumo = min(100, ($consumo_real / $tope_total) * 100);
    @endphp

    <!-- HEADER (LECTOR STYLE) -->
    <header class="bg-white border-b border-[#ecf0f3] h-16 flex items-center justify-between px-6 shrink-0 z-30 shadow-xs">
        <div class="flex items-center space-x-3">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-slate-50 rounded-full text-slate-500 transition lg:hidden" aria-label="Menú">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <a href="{{ route('affiliate.dashboard') }}" class="flex items-center space-x-2.5 transition">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
                <div class="border-l border-[#ecf0f3] pl-3 hidden sm:block">
                    <span class="text-xs font-rubik font-extrabold text-[#00368c] tracking-tight block">ARS CMD</span>
                    <span class="text-[9px] text-[#49bcf7] font-bold block -mt-0.5 tracking-wider uppercase">Afiliados</span>
                </div>
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-bold bg-[#f9fafb] text-[#403663] border border-[#ecf0f3] font-rubik">
                Plan Contributivo
            </span>
            <div class="text-right hidden sm:block text-xs">
                <div class="font-bold text-[#403663]">{{ $afiliado->nombre_completo }}</div>
                <div class="text-[9px] text-slate-400 font-mono mt-0.5">Contrato: {{ $afiliado->numero_contrato }}</div>
            </div>
            
            <a href="{{ route('affiliate.logout') }}" class="text-slate-400 hover:text-[#f53b57] transition p-2 rounded-full hover:bg-rose-50" title="Cerrar Sesión">
                <i class="fas fa-sign-out-alt text-base"></i>
            </a>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        <!-- SIDEBAR -->
        <aside 
            class="w-64 bg-white flex flex-col shrink-0 overflow-y-auto py-6 pr-4 select-none z-20 fixed inset-y-16 left-0 lg:static lg:flex border-r border-slate-200"
            :class="sidebarOpen ? 'flex' : 'hidden lg:flex'"
            @click.away="sidebarOpen = false"
        >
            <nav class="space-y-6 flex-1 flex flex-col pl-4">
                <!-- GRUPO: MI CUENTA -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
                        Mi Cuenta
                    </div>
                    
                    <a href="{{ route('affiliate.dashboard') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.dashboard') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">dashboard</span>
                        <span>Mi Panel</span>
                    </a>
                    
                    <a href="{{ route('affiliate.dependientes') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.dependientes') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.dependientes') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">group</span>
                        <span>Núcleo Familiar</span>
                    </a>
                    
                    <a href="{{ route('affiliate.carnet') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.carnet') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.carnet') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">badge</span>
                        <span>Carnet Digital</span>
                    </a>
                </div>

                <!-- GRUPO: SERVICIOS -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
                        Servicios y Autogestión
                    </div>
                    
                    <a href="{{ route('affiliate.autorizaciones') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.autorizaciones') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.autorizaciones') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">clinical_notes</span>
                        <span>Autorizaciones</span>
                    </a>
                    
                    <a href="{{ route('affiliate.prestadores') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.prestadores') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.prestadores') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">domain</span>
                        <span>Red de Prestadores</span>
                    </a>
                    
                    <a href="{{ route('affiliate.solicitudes') }}" 
                       class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('affiliate.solicitudes') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
                        <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('affiliate.solicitudes') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">edit_document</span>
                        <span>Solicitar Servicio</span>
                    </a>
                </div>
            </nav>
            
            <!-- PROGRESS BAR CARD (LIGHT THEME) -->
            <div class="mx-4 p-4.5 bg-slate-50 border border-slate-200 rounded-2xl mt-6 space-y-2.5 text-[10px] text-slate-500 shadow-sm">
                <div class="flex items-center justify-between font-bold">
                    <span class="flex items-center gap-1.5 text-slate-700"><span class="material-symbols-outlined text-[16px] text-blue-600">pill</span> Medicamentos</span>
                    <span class="font-mono text-blue-600">{{ number_format($porcentaje_consumo, 0) }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-1.5 mt-1 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $porcentaje_consumo }}%"></div>
                </div>
                <span class="block text-[9px] text-slate-400 font-mono">DOP {{ number_format($consumo_real, 2) }} / DOP 12,000</span>
            </div>

            <div class="pt-6 text-[9.5px] text-slate-450 space-y-0.5 font-mono text-center pl-4">
                <div>Afiliado Titular Activo</div>
                <div>NSS: {{ $nss_safe }}</div>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="flex-grow overflow-y-auto p-6 md:p-8 bg-[#f9fafb]">
            <!-- Flash messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3 shadow-xs text-xs font-semibold">
                    <i class="fas fa-check-circle text-[#0be881] text-sm mt-0.5"></i>
                    <span class="text-[#403663]">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 shadow-xs text-xs font-semibold">
                    <i class="fas fa-times-circle text-[#f53b57] text-sm mt-0.5"></i>
                    <span class="text-[#403663]">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Lector JS Files -->
    <script src="{{ asset('themes/lector/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/functions.js') }}"></script>

</body>
</html>
