<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal PSS') - ARS Core Demo</title>
    
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        clinical: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans text-slate-700 antialiased flex flex-col bg-[#f6f8fc]" x-data="{ sidebarOpen: false }">

    <!-- Header / Navbar Superior PSS -->
    <header class="bg-[#f6f8fc] px-4 sm:px-6 lg:px-8 z-10 flex-shrink-0">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-16">
            <!-- Logotipo / Home -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
                <div>
                    <span class="font-extrabold text-sm tracking-wider text-slate-800 block">PORTAL PRESTADORES (PSS)</span>
                    <span class="text-[10px] text-[#49bcf7] font-semibold block -mt-0.5">ARS CMD</span>
                </div>
            </div>

            <!-- Menú Horizontal Escritorio (Estilo Workspace Pills) -->
            <nav class="hidden md:flex space-x-1.5 text-xs font-bold">
                <a href="{{ route('pss.dashboard') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.dashboard') ? 'bg-[#ccfbf1]/85 text-[#0f766e]' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-800' }}">Inicio</a>
                <a href="{{ route('pss.autorizaciones.nueva') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.autorizaciones.nueva') ? 'bg-[#ccfbf1]/85 text-[#0f766e]' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-800' }}">Nueva Autorización</a>
                <a href="{{ route('pss.solicitudes') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.solicitudes') ? 'bg-[#ccfbf1]/85 text-[#0f766e]' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-800' }}">Mis Solicitudes</a>
                <a href="{{ route('pss.autorizaciones.cancelar') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.autorizaciones.cancelar') ? 'bg-[#ccfbf1]/85 text-[#0f766e]' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-800' }}">Cancelar Autorización</a>
                <a href="{{ route('pss.perfil') }}" class="px-4 py-2.5 rounded-full transition {{ Route::is('pss.perfil') ? 'bg-[#ccfbf1]/85 text-[#0f766e]' : 'text-slate-600 hover:bg-slate-200/50 hover:text-slate-800' }}">Contrato y Tarifas</a>
            </nav>

            <!-- Menú de Usuario -->
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-bold text-slate-800">Clínica Abreu (Demo)</div>
                    <div class="text-[10px] text-teal-600 font-medium">RNC: 101002034</div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition p-2 rounded-full hover:bg-rose-50" title="Cerrar sesión">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
                
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-600 p-2 hover:bg-slate-200/50 rounded-full focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Sidebar Móvil -->
    <div x-show="sidebarOpen" @click.away="sidebarOpen = false" class="md:hidden bg-[#f6f8fc] text-slate-800 px-4 pt-2 pb-4 space-y-1 shadow-inner border-b border-slate-200/60" x-cloak>
        <a href="{{ route('pss.dashboard') }}" class="block px-3 py-2 rounded-full text-sm font-semibold hover:bg-slate-200/50">Inicio</a>
        <a href="{{ route('pss.autorizaciones.nueva') }}" class="block px-3 py-2 rounded-full text-sm font-semibold hover:bg-slate-200/50">Nueva Autorización</a>
        <a href="{{ route('pss.solicitudes') }}" class="block px-3 py-2 rounded-full text-sm font-semibold hover:bg-slate-200/50">Mis Solicitudes</a>
        <a href="{{ route('pss.autorizaciones.cancelar') }}" class="block px-3 py-2 rounded-full text-sm font-semibold hover:bg-slate-200/50">Cancelar Autorización</a>
        <a href="{{ route('pss.perfil') }}" class="block px-3 py-2 rounded-full text-sm font-semibold hover:bg-slate-200/50">Contrato y Tarifas</a>
    </div>

    <!-- Contenedor del Panel de Contenido Principal PSS -->
    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pb-6 overflow-hidden flex flex-col">
        <div class="flex-1 bg-white rounded-2xl lg:rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <!-- Alertas Flash -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start space-x-3 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-250 rounded-2xl flex items-start space-x-3 shadow-sm">
                        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="text-xs font-semibold text-rose-800">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Floating Role Switcher for Demo (Estilo Material Design) -->
    <div x-data="{ open: false }" class="fixed bottom-5 right-5 z-50">
        <button @click="open = !open" class="flex items-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-3 rounded-full shadow-lg hover:shadow-xl hover:scale-102 active:scale-98 transition-all duration-200">
            <svg class="w-4.5 h-4.5 animate-spin" style="animation-duration: 6s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-xs font-semibold tracking-wide">ROLES DEMO</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl p-4 space-y-3" x-cloak>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selector de Roles</h4>
                <span class="text-[10px] bg-teal-50 text-teal-700 px-2 py-0.5 rounded-full font-medium">Demo Mode</span>
            </div>
            <div class="grid grid-cols-1 gap-1 max-h-80 overflow-y-auto">
                @php
                    $roleList = [
                        'Administrador ARS',
                        'Supervisor Afiliación',
                        'Analista Afiliación',
                        'Auditor Médico',
                        'Autorizaciones Médicas',
                        'Usuario PSS',
                        'Consulta'
                    ];
                @endphp
                @foreach($roleList as $rl)
                    <a href="{{ route('switch.role', $rl) }}" class="flex items-center justify-between p-2.5 rounded-xl text-left text-xs font-medium transition-all {{ Auth::user()->role === $rl ? 'bg-teal-50 text-teal-700 border border-teal-100 shadow-sm' : 'text-slate-600 hover:bg-slate-50 border border-transparent' }}">
                        <span>{{ $rl }}</span>
                        @if(Auth::user()->role === $rl)
                            <svg class="w-4 h-4 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
