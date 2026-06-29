<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ARS Core Demo') - Plataforma Institucional</title>
    
    <!-- Google Fonts: Sora and Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "outline-variant": "#c6c5d4",
                        "secondary": "#0056c5",
                        "primary-container": "#1a237e",
                        "on-primary": "#ffffff",
                        "primary-fixed": "#e0e0ff",
                        "on-secondary-container": "#fefcff",
                        "surface-container": "#eceef0",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a",
                        "primary": "#000666",
                        "surface-container-high": "#e6e8ea",
                        "on-surface": "#191c1e",
                        "on-primary-container": "#8690ee",
                        "surface": "#f7f9fb",
                        "on-primary-fixed-variant": "#343d96",
                        "secondary-container": "#0f6df3",
                        "tertiary-container": "#00363d",
                        "error": "#ba1a1a",
                        "on-tertiary-fixed-variant": "#004f58",
                        "on-secondary": "#ffffff",
                        "inverse-primary": "#bdc2ff",
                        "primary-fixed-dim": "#bdc2ff",
                        "surface-container-highest": "#e0e3e5",
                        "tertiary": "#001f24",
                        "inverse-on-surface": "#eff1f3",
                        "inverse-surface": "#2d3133",
                        "surface-tint": "#4c56af",
                        "surface-container-low": "#f2f4f6",
                        "on-error": "#ffffff",
                        "on-secondary-fixed-variant": "#00429b",
                        "surface-dim": "#d8dadc",
                        "on-secondary-fixed": "#001945",
                        "on-surface-variant": "#454652",
                        "secondary-fixed": "#d9e2ff",
                        "tertiary-fixed": "#9cf0ff",
                        "outline": "#767683",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#000767",
                        "secondary-fixed-dim": "#b0c6ff",
                        "on-tertiary-container": "#00a7bb",
                        "on-background": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "surface-bright": "#f7f9fb",
                        "tertiary-fixed-dim": "#00daf3",
                        "on-tertiary": "#ffffff",
                        "on-tertiary-fixed": "#001f24",
                        "background": "#f7f9fb",
                        "brand": {
                            50:  "#e8f0fe",
                            100: "#d2e3fc",
                            200: "#a8c7fa",
                            300: "#74a7f5",
                            400: "#4285f4",
                            500: "#1a73e8",
                            600: "#0b57d0",
                            700: "#0d3c80",
                            800: "#082563",
                            900: "#041645"
                        }
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        "margin-desktop": "48px",
                        "bento-gap": "24px",
                        "unit": "8px",
                        "gutter": "24px",
                        "margin-mobile": "16px",
                        "container-max": "1440px"
                    },
                    fontFamily: {
                        "title-md": ["Sora"],
                        "headline-lg-mobile": ["Sora"],
                        "caption": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "display-lg": ["Sora"],
                        "headline-lg": ["Sora"]
                    },
                    fontSize: {
                        "title-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "caption": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "label-sm": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "600"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #F8FAFC;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 24px -1px rgba(0, 6, 102, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px -4px rgba(0, 6, 102, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }
        .pulse-indicator {
            box-shadow: 0 0 0 0 rgba(186, 26, 26, 0.7);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(186, 26, 26, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(186, 26, 26, 0); }
            100% { box-shadow: 0 0 0 0 rgba(186, 26, 26, 0); }
        }
        .sidebar-active {
            position: relative;
        }
        .sidebar-active::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 4px;
            background-color: #0056c5;
            border-radius: 4px 0 0 4px;
        }
        /* Fix: Forzar color de texto en selects y options nativos del OS */
        select,
        select option {
            color: #1e293b !important; /* slate-800 */
            background-color: #ffffff;
        }
        select:focus {
            outline: none;
        }
        /* Fix general para inputs y selects con clase de tailwind */
        .form-field {
            color: #1e293b;
            background-color: #ffffff;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full font-sans text-slate-700 antialiased flex flex-col bg-[#f6f8fc]" x-data="{ sidebarOpen: false }">

    <!-- Header Superior de Ancho Completo -->
    <header class="flex h-16 flex-shrink-0 bg-[#f6f8fc] px-6 lg:px-8 items-center justify-between z-10">
        <!-- Izquierda: Logo y Toggle de Sidebar -->
        <div class="flex items-center space-x-3 lg:w-64 flex-shrink-0">
            <button type="button" @click="sidebarOpen = true" class="p-2 -ml-2 text-slate-500 lg:hidden focus:outline-none hover:bg-slate-200/50 rounded-full transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div class="flex items-center space-x-2">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
            </div>
        </div>

        <!-- Centro: Buscador estilo Gmail / Google Workspace -->
        <div class="flex-1 max-w-2xl mx-8 hidden md:block">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" placeholder="Buscar afiliados, novedades o autorizaciones..." 
                       class="block w-full rounded-full border-0 bg-[#eaf1fb] py-2 pl-12 pr-4 text-xs text-slate-800 placeholder-slate-500 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all duration-200 shadow-sm">
            </div>
        </div>

        <!-- Derecha: Perfil de usuario y Logout -->
        <div class="flex items-center space-x-4">
            <div class="font-medium text-slate-500 text-xs hidden lg:block mr-2">
                Rol: <span class="bg-[#e8f0fe] text-[#1a73e8] px-3 py-1 rounded-full text-[10px] font-bold border border-[#d2e3fc] ml-1">{{ Auth::user()->role }}</span>
            </div>
            <div class="text-right hidden sm:block">
                <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-400 font-medium">{{ Auth::user()->email }}</div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-600 transition p-2 rounded-full hover:bg-rose-50" title="Cerrar sesión">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </header>

    <!-- Contenedor Principal (Sidebar + Contenido) -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- Sidebar para móvil (Off-canvas) -->
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak>
            <div class="fixed inset-0 bg-slate-900/40 transition-opacity" x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <div class="fixed inset-0 flex">
                <div class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4 border-r border-slate-200 select-none" x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    <!-- Botón de cierre -->
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-4">
                        @include('layouts.partials.sidebar-nav')
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex lg:flex-shrink-0 lg:flex-col lg:w-64 bg-white border-r border-slate-200 py-6 pr-4 select-none">
            <div class="flex flex-1 flex-col overflow-y-auto pt-2">
                @include('layouts.partials.sidebar-nav')
            </div>
        </div>
        <!-- Contenedor del Panel de Contenido Principal -->
        <div class="flex flex-1 flex-col min-w-0 bg-white rounded-t-3xl lg:rounded-3xl border border-slate-100/80 shadow-sm lg:mr-6 lg:mb-6 overflow-hidden">
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <!-- Alertas Flash -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start space-x-3 shadow-sm transition-all duration-200">
                        <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <div>
                            <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-250 rounded-2xl flex items-start space-x-3 shadow-sm transition-all duration-200">
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
        <button @click="open = !open" class="flex items-center space-x-2 bg-[#0b57d0] hover:bg-[#0d3c80] text-white px-4 py-3 rounded-full shadow-lg hover:shadow-xl hover:scale-102 active:scale-98 transition-all duration-200">
            <svg class="w-4.5 h-4.5 animate-spin" style="animation-duration: 6s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-xs font-semibold tracking-wide">ROLES DEMO</span>
        </button>
        
        <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-16 right-0 w-80 bg-white border border-slate-100 rounded-2xl shadow-xl p-4 space-y-3" x-cloak>
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selector de Roles</h4>
                <span class="text-[10px] bg-blue-50 text-brand-700 px-2 py-0.5 rounded-full font-medium">Demo Mode</span>
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
                    <a href="{{ route('switch.role', $rl) }}" class="flex items-center justify-between p-2.5 rounded-xl text-left text-xs font-medium transition-all {{ Auth::user()->role === $rl ? 'bg-blue-50 text-[#0b57d0] border border-[#d2e3fc] shadow-sm' : 'text-slate-600 hover:bg-slate-50 border border-transparent' }}">
                        <span>{{ $rl }}</span>
                        @if(Auth::user()->role === $rl)
                            <svg class="w-4 h-4 text-[#0b57d0]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
