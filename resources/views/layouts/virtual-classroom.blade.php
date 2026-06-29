<!DOCTYPE html>
<html lang="es" class="h-full bg-[#f9fafb]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aula Virtual') - ARS CMD</title>
    
    <!-- Google Fonts: Rubik & Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;750;900&family=Rubik:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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
                            white: '#fefefe'
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

    <!-- HEADER (LECTOR INDIGO HEADER) -->
    <header class="bg-white border-b border-[#ecf0f3] h-16 flex items-center justify-between px-6 shrink-0 z-30 shadow-xs">
        <div class="flex items-center space-x-3">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-slate-50 rounded-full text-slate-500 transition lg:hidden">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <a href="{{ route('classroom.dashboard') }}" class="flex items-center space-x-2">
                <svg class="h-8 w-8 text-[#0be881]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="20" rx="5" class="fill-[#49bcf7]" />
                    <path d="M12 7V17M7 12H17" stroke="white" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <div class="border-l border-[#ecf0f3] pl-3">
                    <span class="font-rubik font-extrabold text-sm text-[#403663] tracking-tight block">AULA VIRTUAL</span>
                    <span class="text-[9px] text-[#49bcf7] font-bold block -mt-0.5 uppercase tracking-wider">CAPACITACIÓN</span>
                </div>
            </a>
        </div>

        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                @if(Auth::check())
                    <div class="text-xs font-bold text-[#403663]">{{ Auth::user()->name }}</div>
                    <div class="text-[9px] text-[#49bcf7] font-medium font-rubik">Estudiante Activo</div>
                @else
                    <div class="text-xs font-bold text-[#403663]">Estudiante Demo</div>
                    <div class="text-[9px] text-[#49bcf7] font-medium font-rubik">Invitado</div>
                @endif
            </div>
            
            <a href="{{ route('classroom.logout') }}" class="text-slate-400 hover:text-[#f53b57] transition p-2 rounded-full hover:bg-rose-50" title="Cerrar Sesión">
                <i class="fas fa-sign-out-alt text-base"></i>
            </a>
        </div>
    </header>

    <div class="flex-1 flex overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside 
            x-show="sidebarOpen" 
            class="w-64 bg-[#403663] flex flex-col shrink-0 overflow-y-auto py-6 px-4 select-none z-20 fixed inset-y-16 left-0 lg:static lg:block border-r border-[#ecf0f3]/10"
            :class="sidebarOpen ? 'block' : 'hidden lg:block'"
            @click.away="sidebarOpen = false"
        >
            <nav class="space-y-1.5">
                <a href="{{ route('classroom.dashboard') }}" class="sidebar-item {{ Route::is('classroom.dashboard') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-th-large w-5 text-center mr-3 text-xs"></i>
                    <span>Mi Panel</span>
                </a>
                <a href="{{ route('classroom.cursos') }}" class="sidebar-item {{ Route::is('classroom.cursos') || Route::is('classroom.curso') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-graduation-cap w-5 text-center mr-3 text-xs"></i>
                    <span>Catálogo de Cursos</span>
                </a>
                <a href="{{ route('classroom.certificados') }}" class="sidebar-item {{ Route::is('classroom.certificados') ? 'sidebar-item-active' : '' }}">
                    <i class="fas fa-certificate w-5 text-center mr-3 text-xs"></i>
                    <span>Mis Certificados</span>
                </a>
            </nav>
            
            <div class="mt-auto p-4 border-t border-white/10 text-[10px] text-white/40 space-y-1 font-mono">
                <div>Ecosistema Formativo</div>
                <div>ARS CMD Academy</div>
                <div>RD · Educación Integral</div>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="flex-grow overflow-y-auto p-6 md:p-8 bg-[#f9fafb]">
            <!-- Flash messages -->
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
        </main>
    </div>

    <!-- Floating switcher panel for estudiantes -->
    <div x-data="{ open: false }" class="fixed bottom-5 right-5 z-50">
        <button @click="open = !open" class="flex items-center space-x-2 bg-[#49bcf7] hover:bg-[#31a3e6] text-white px-4 py-3 rounded-full shadow-lg hover:shadow-xl hover:scale-102 transition-all duration-200">
            <i class="fas fa-user-graduate text-xs"></i>
            <span class="text-xs font-semibold tracking-wide">ESTUDIANTES DEMO</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 w-80 bg-white border border-[#ecf0f3] rounded-2xl shadow-xl p-4 space-y-3" x-cloak>
            <div class="flex justify-between items-center border-b border-[#ecf0f3] pb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider font-rubik">Cuentas Estudiantes</h4>
                <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Demo Mode</span>
            </div>
            <div class="grid grid-cols-1 gap-1 max-h-80 overflow-y-auto">
                @php
                    $studList = [
                        'Estudiante Demo 1' => 'estudiante1@ars.com',
                        'Estudiante Demo 2' => 'estudiante2@ars.com',
                        'Estudiante Demo 3' => 'estudiante3@ars.com',
                    ];
                @endphp
                @foreach($studList as $sName => $sEmail)
                    <a href="{{ route('switch.role', $sEmail) }}" class="flex items-center justify-between p-2.5 rounded-xl text-left text-xs font-medium transition-all {{ Auth::check() && Auth::user()->email === $sEmail ? 'bg-orange-50 text-[#49bcf7] border border-orange-100 shadow-xs font-semibold' : 'text-slate-600 hover:bg-slate-50 border border-transparent' }}">
                        <span>{{ $sName }}</span>
                        @if(Auth::check() && Auth::user()->email === $sEmail)
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
