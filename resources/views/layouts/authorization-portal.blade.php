<!DOCTYPE html>
<html lang="es" class="h-full bg-[#f9fafb]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Prestadores PSS') - ARS CMD</title>
    
    <!-- Google Fonts: Rubik & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Lector Native Stylesheets (Only icons/animation to prevent layout distortion) -->
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/lector/assets/flaticon/flaticon.css') }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: {
                            50: 'rgba(73, 188, 247, 0.12)', // Lector primary-color light
                            600: '#49bcf7', // Lector primary-color
                            700: '#403663', // Lector title-color
                            650: '#49bcf7', // Replaced with Lector Blue
                            500: '#49bcf7'
                        },
                        brand: {
                            navy: '#403663',
                            blue: '#49bcf7',
                            accent: '#49bcf7'
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
            border-color: rgba(73, 188, 247, 0.3);
        }
        
        /* Force Tables to render natively and respect Tailwind alignments */
        table.min-w-full {
            display: table !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }
        table.min-w-full thead {
            display: table-header-group !important;
        }
        table.min-w-full tbody {
            display: table-row-group !important;
        }
        table.min-w-full tr {
            display: table-row !important;
        }
        table.min-w-full th, table.min-w-full td {
            display: table-cell !important;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full flex flex-col bg-[#f9fafb] text-slate-700 antialiased" x-data="{ mobileOpen: false }">
    <!-- Header / Navbar Superior PSS (LECTOR WHITE HEADER) -->
    <header class="bg-white border-b border-[#ecf0f3] text-slate-800 h-16 flex items-center justify-between px-6 shrink-0 z-30 shadow-xs">
        <div class="flex items-center space-x-3">
            <button @click="mobileOpen = !mobileOpen" class="p-2 hover:bg-slate-100 rounded-full text-slate-500 transition md:hidden">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <a href="{{ route('pss.dashboard') }}" class="flex items-center space-x-2">
                <svg class="h-8 w-8 text-[#0be881]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" class="fill-[#49bcf7]" />
                    <path d="M12 7V17M7 12H17" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <div class="border-l border-[#ecf0f3] pl-3">
                    <span class="font-rubik font-extrabold text-xs text-[#403663] tracking-tight block">PORTAL AUTORIZACIONES</span>
                    <span class="text-[9px] text-[#49bcf7] font-bold block -mt-0.5 uppercase tracking-wider">PRESTADORES PSS</span>
                </div>
            </a>
        </div>

        @php
            $accessType = session('active_access_type', 'medical_center');
            $pssId = session('active_pss_id', 1);
            $activePssObj = \App\Models\Pss::find($pssId);
            $pssProfiles = collect();
            if (Auth::check()) {
                $pssProfiles = \App\Models\PssUser::where('user_id', Auth::id())->where('status', 'activo')->get();
                // Fallback de demostración si la base de datos está vacía o el seed no se completó
                if ($pssProfiles->isEmpty() && Auth::user()->role === 'Usuario PSS') {
                    $pssProfiles = collect([
                        (object)['access_type' => 'medical_center', 'pss_id' => 1, 'pss' => (object)['nombre' => 'Clínica Central Demo']],
                        (object)['access_type' => 'pharmacy', 'pss_id' => 11, 'pss' => (object)['nombre' => 'Farmacias GBC Demo']],
                        (object)['access_type' => 'laboratory', 'pss_id' => 12, 'pss' => (object)['nombre' => 'Amadita Laboratorio Demo']]
                    ]);
                }
            }
        @endphp

        <!-- Desktop Navigation Menu (White style matching core/dashboard style) -->
        <nav class="hidden md:flex space-x-1 text-xs font-bold font-rubik">
            <a href="{{ route('pss.dashboard') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.dashboard') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Inicio</a>
            
            @if($accessType === 'pharmacy')
                <a href="{{ route('pss.buscar') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.buscar') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Validar Afiliado</a>
                <a href="{{ route('pss.farmacia.nueva_dispensacion') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.farmacia.nueva_dispensacion') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Nueva Autorización</a>
                <a href="{{ route('pss.farmacia.recetas') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.farmacia.recetas') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Recetas</a>
            @elseif($accessType === 'laboratory')
                <a href="{{ route('pss.buscar') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.buscar') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Validar Afiliado</a>
                <a href="{{ route('pss.laboratorio.nueva_orden') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.laboratorio.nueva_orden') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Nueva Autorización</a>
                <a href="{{ route('pss.laboratorio.ordenes') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.laboratorio.ordenes') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Autorizaciones</a>
                <a href="{{ route('pss.laboratorio.resultados') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.laboratorio.resultados') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Resultados</a>
            @else
                <a href="{{ route('pss.buscar') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.buscar') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Buscar Afiliado</a>
                <a href="{{ route('pss.autorizaciones.nueva') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.autorizaciones.nueva') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Nueva Autorización</a>
                <a href="{{ route('pss.solicitudes') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.solicitudes') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Mis Solicitudes</a>
                <a href="{{ route('pss.autorizaciones.cancelar') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.autorizaciones.cancelar') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Cancelar</a>
            @endif

            <a href="{{ route('pss.reclamaciones.index') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.reclamaciones.*') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Reclamaciones</a>
            <a href="{{ route('pss.pagos.index') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.pagos.index') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Pagos</a>
            <a href="{{ route('pss.perfil') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.perfil') ? 'bg-[#49bcf7] text-white shadow-sm' : 'text-slate-650 hover:bg-slate-100 hover:text-[#49bcf7]' }}">Tarifario PSS</a>
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" class="px-4 py-2.5 rounded-full text-rose-600 hover:bg-rose-50 transition flex items-center gap-1.5"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión (Salir)</a>
        </nav>
        
        <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- User profile & Logout -->
        <div class="flex items-center space-x-4 text-slate-800">
            <div class="text-right hidden sm:block">
                @if(Auth::check())
                    <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    
                    @if(count($pssProfiles) > 1)
                        <form action="{{ route('pss.perfil.switch') }}" method="POST" class="mt-0.5" id="switch-profile-form">
                            @csrf
                            <select name="access_type" onchange="document.getElementById('switch-profile-form').submit()" 
                                    class="text-[10px] text-[#49bcf7] bg-blue-50 border border-blue-100 rounded-full py-0.5 px-3 font-bold focus:ring-0 cursor-pointer">
                                @foreach($pssProfiles as $prof)
                                    <option value="{{ $prof->access_type }}" {{ $accessType === $prof->access_type ? 'selected' : '' }}>
                                        {{ $prof->access_type === 'pharmacy' ? 'Farmacia' : ($prof->access_type === 'laboratory' ? 'Laboratorio' : 'Centro Médico') }} ({{ $prof->pss->nombre ?? 'Prestadora Demo' }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <div class="text-[9px] text-[#49bcf7] font-bold uppercase tracking-wider">
                            {{ $accessType === 'pharmacy' ? 'Farmacia' : ($accessType === 'laboratory' ? 'Laboratorio' : 'Centro Médico') }} ({{ $activePssObj->nombre ?? 'Clínica Abreu' }})
                        </div>
                    @endif
                @else
                    <div class="text-xs font-bold text-slate-800">Clínica Abreu (Demo)</div>
                    <div class="text-[9px] text-[#49bcf7] font-medium font-mono">RNC: 101002034</div>
                @endif
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-1.5 text-xs text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-full transition font-semibold" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Mobile Navigation menu -->
    <div x-show="mobileOpen" @click.away="mobileOpen = false" class="md:hidden bg-white text-slate-700 border-b border-[#ecf0f3] px-6 py-4 space-y-2 shadow-inner" x-cloak>
        <a href="{{ route('pss.dashboard') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Inicio</a>
        
        @if($accessType === 'pharmacy')
            <a href="{{ route('pss.buscar') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Validar Afiliado</a>
            <a href="{{ route('pss.farmacia.nueva_dispensacion') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Nueva Autorización</a>
            <a href="{{ route('pss.farmacia.recetas') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Recetas</a>
        @elseif($accessType === 'laboratory')
            <a href="{{ route('pss.buscar') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Validar Afiliado</a>
            <a href="{{ route('pss.laboratorio.nueva_orden') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Nueva Autorización</a>
            <a href="{{ route('pss.laboratorio.ordenes') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Autorizaciones</a>
            <a href="{{ route('pss.laboratorio.resultados') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Resultados</a>
        @else
            <a href="{{ route('pss.buscar') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Buscar Afiliado</a>
            <a href="{{ route('pss.autorizaciones.nueva') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Nueva Autorización</a>
            <a href="{{ route('pss.solicitudes') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Mis Solicitudes</a>
            <a href="{{ route('pss.autorizaciones.cancelar') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Cancelar</a>
        @endif

        <a href="{{ route('pss.reclamaciones.index') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Reclamaciones</a>
        <a href="{{ route('pss.pagos.index') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Pagos</a>
        <a href="{{ route('pss.perfil') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-slate-100 hover:text-[#49bcf7]">Tarifario PSS</a>

        <!-- Sección de Perfil Móvil y Logout -->
        <div class="border-t border-[#ecf0f3] pt-4 mt-2">
            <div class="px-3 py-2">
                @if(Auth::check())
                    <span class="block text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                    @if(count($pssProfiles) > 1)
                        <form action="{{ route('pss.perfil.switch') }}" method="POST" class="mt-2" id="switch-profile-form-mobile">
                            @csrf
                            <label class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-1">Cambiar de Prestador:</label>
                            <select name="access_type" onchange="document.getElementById('switch-profile-form-mobile').submit()" 
                                    class="text-xs text-[#49bcf7] bg-blue-50 border border-blue-100 rounded-xl py-1 px-3 w-full font-bold focus:ring-0 cursor-pointer">
                                @foreach($pssProfiles as $prof)
                                    <option value="{{ $prof->access_type }}" {{ $accessType === $prof->access_type ? 'selected' : '' }}>
                                        {{ $prof->access_type === 'pharmacy' ? 'Farmacia' : ($prof->access_type === 'laboratory' ? 'Laboratorio' : 'Centro Médico') }} ({{ $prof->pss->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="block text-[10px] text-[#49bcf7] font-bold uppercase tracking-wider mt-0.5">
                            {{ $accessType === 'pharmacy' ? 'Farmacia' : ($accessType === 'laboratory' ? 'Laboratorio' : 'Centro Médico') }} ({{ $activePssObj->nombre ?? 'Clínica Abreu' }})
                        </span>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 bg-rose-50 border border-rose-100 text-[#f53b57] hover:bg-rose-100 py-2.5 rounded-xl text-xs font-bold transition">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Cerrar Sesión</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- Content Canvas -->
    <div class="flex-grow max-w-7xl w-full mx-auto p-6 md:p-8 flex flex-col">
        <div class="flex-grow bg-white border border-[#ecf0f3] rounded-2xl md:rounded-3xl shadow-xs p-6 md:p-8 overflow-y-auto">
            
            <!-- Flash message alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-start gap-3 shadow-xs">
                    <i class="fas fa-check-circle text-[#0be881] text-sm mt-0.5"></i>
                    <span class="text-xs font-semibold text-[#403663]">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 shadow-xs">
                    <i class="fas fa-times-circle text-[#f53b57] text-sm mt-0.5"></i>
                    <span class="text-xs font-semibold text-[#403663]">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-[#ecf0f3] py-4 px-6 text-center text-xs text-slate-400 mt-auto shrink-0 z-20">
        <span>Portal de Autorizaciones para Clínicas y Centros de Salud (PSS) · ARS CMD &copy; {{ date('Y') }}</span>
    </footer>

    <!-- Role Switcher Floating Panel -->
    <div x-data="{ open: false }" class="fixed bottom-5 right-5 z-50">
        <button @click="open = !open" class="flex items-center space-x-2 bg-[#49bcf7] hover:bg-[#31a3e6] text-white px-4 py-3 rounded-full shadow-lg hover:shadow-xl hover:scale-102 transition-all duration-200">
            <i class="fas fa-user-cog text-xs"></i>
            <span class="text-xs font-semibold tracking-wide">ROLES DEMO</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 w-80 bg-white border border-[#ecf0f3] rounded-2xl shadow-xl p-4 space-y-3" x-cloak>
            <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-rubik">Selector de Roles</h4>
                <span class="text-[10px] bg-blue-50 text-[#49bcf7] px-2 py-0.5 rounded-full font-medium">Demo Mode</span>
            </div>
            <div class="grid grid-cols-1 gap-1 max-h-80 overflow-y-auto">
                @php
                    $roleList = [
                        'Administrador ARS' => 'admin@ars.com',
                        'Supervisor Afiliación' => 'supervisor@ars.com',
                        'Analista Afiliación' => 'analista@ars.com',
                        'Auditor Médico' => 'auditor@ars.com',
                        'Autorizaciones Médicas' => 'autorizaciones@ars.com',
                        'Usuario PSS' => 'pss@ars.com',
                        'Consulta' => 'consulta@ars.com'
                    ];
                @endphp
                @foreach($roleList as $rlName => $rlEmail)
                    <a href="{{ route('switch.role', $rlEmail) }}" class="flex items-center justify-between p-2.5 rounded-xl text-left text-xs font-medium transition-all {{ Auth::check() && Auth::user()->role === $rlName ? 'bg-blue-50 text-[#49bcf7] border border-blue-100 shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 border border-transparent' }}">
                        <span>{{ $rlName }}</span>
                        @if(Auth::check() && Auth::user()->role === $rlName)
                            <i class="fas fa-check-circle text-[#49bcf7] text-xs"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Lector JS Files -->
    <script src="{{ asset('themes/lector/assets/js/jquery.js') }}"></script>
    <script src="{{ asset('themes/lector/assets/js/bootstrap.min.js') }}"></script>

</body>
</html>
