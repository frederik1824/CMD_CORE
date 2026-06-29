@php
$personas = [
    ['rol' => 'Administrador ARS',       'email' => 'admin@ars.com',         'icon' => 'A', 'color' => 'bg-[#0b57d0]',  'desc' => 'Dashboard general, configuraciones, bitácora de auditoría y reportes gráficos.'],
    ['rol' => 'Supervisor Afiliación',  'email' => 'supervisor@ars.com',    'icon' => 'S', 'color' => 'bg-[#12b886]',  'desc' => 'Gestión de novedades de afiliación, aprobación de lotes Unipago y control.'],
    ['rol' => 'Analista Afiliación',    'email' => 'analista@ars.com',      'icon' => 'N', 'color' => 'bg-[#15aabf]',  'desc' => 'Carga masiva por CSV, registro de dependientes y ficha de afiliados.'],
    ['rol' => 'Auditor Médico',         'email' => 'auditor@ars.com',       'icon' => 'M', 'color' => 'bg-[#fa5252]',  'desc' => 'Evaluación de autorizaciones de alto costo, tarifas excedidas y diagnóstico.'],
    ['rol' => 'Autorizaciones Médicas', 'email' => 'autorizaciones@ars.com','icon' => 'U', 'color' => 'bg-[#7950f2]',  'desc' => 'Bandeja de solicitudes de PSS, creación de autorizaciones internas y búsqueda.'],
    ['rol' => 'Usuario PSS (Clínica)',  'email' => 'pss@ars.com',           'icon' => 'P', 'color' => 'bg-[#495057]',  'desc' => 'Simulador de prestadores, consultas de cobertura, emisión y cancelación.'],
];

$todosModulos = collect($categorias)->flatMap(fn($c) => $c['modulos']);
@endphp
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ARS CMD | Operations Portal</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "outline-variant": "#c6c5d4",
                        "secondary": "#0b57d0",
                        "primary-container": "#e8f0fe",
                        "on-primary": "#ffffff",
                        "primary-fixed": "#d2e3fc",
                        "on-secondary-container": "#0d3c80",
                        "surface-container": "#f1f3f4",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#ba1a1a",
                        "primary": "#041645",
                        "surface-container-high": "#e8eaed",
                        "on-surface": "#202124",
                        "on-primary-container": "#1a73e8",
                        "surface": "#f8f9fa",
                        "secondary-container": "#1a73e8",
                        "error": "#d93025",
                        "background": "#f8f9fa"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "48px",
                        "bento-gap": "24px",
                        "unit": "8px",
                        "gutter": "24px",
                        "margin-mobile": "16px",
                        "container-max": "1440px"
                    },
                    "fontFamily": {
                        "title-md": ["Sora"],
                        "headline-lg-mobile": ["Sora"],
                        "caption": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "display-lg": ["Sora"],
                        "headline-lg": ["Sora"]
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(218, 220, 224, 0.6);
            box-shadow: 0 1px 3px rgba(60, 64, 67, 0.1), 0 1px 2px rgba(60, 64, 67, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(60, 64, 67, 0.12), 0 2px 6px rgba(60, 64, 67, 0.08);
            border-color: rgba(26, 115, 232, 0.4);
        }
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }
        .pulse-indicator {
            box-shadow: 0 0 0 0 rgba(217, 48, 37, 0.7);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(217, 48, 37, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(217, 48, 37, 0); }
            100% { box-shadow: 0 0 0 0 rgba(217, 48, 37, 0); }
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
            background-color: #1a73e8;
            border-radius: 4px 0 0 4px;
        }
        .glow-effect {
            position: relative;
            overflow: hidden;
        }
        .glow-effect::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 100%);
            transform: skewX(-25deg);
            transition: 0.75s;
        }
        .glow-effect:hover::before {
            left: 150%;
        }
    </style>
</head>
<body class="bg-surface" x-data="landingApp()">

    <!-- SIDE NAVIGATION -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-white border-r border-slate-200 shadow-sm flex flex-col py-8 z-50 select-none">
        <div class="px-8 mb-10 flex items-center gap-3">
            <span class="material-symbols-outlined text-[32px] text-secondary">admin_panel_settings</span>
            <div>
                <p class="font-bold text-slate-800 text-sm tracking-tight">ARS CMD</p>
                <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Centro de Mando</p>
            </div>
        </div>
        
        <nav class="flex-1 space-y-1.5 px-4 overflow-y-auto">
            <a class="flex items-center gap-4 px-4 py-2.5 rounded-r-full mr-2 transition-all cursor-pointer"
               :class="!filterCategory ? 'bg-blue-50 text-blue-600 font-bold sidebar-active' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-medium'"
               @click="filterCategory = null; searchQuery = ''">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                <span class="text-xs">Todos los módulos</span>
            </a>
            
            <a class="flex items-center gap-4 px-4 py-2.5 rounded-r-full mr-2 transition-all cursor-pointer"
               :class="filterCategory === 'fav' ? 'bg-blue-50 text-blue-600 font-bold sidebar-active' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-medium'"
               @click="filterCategory = 'fav'; searchQuery = ''">
                <span class="material-symbols-outlined text-[18px] text-yellow-500 fill-current">star</span>
                <span class="text-xs flex-1">Favoritos</span>
                <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-0.5 rounded-full font-bold" x-text="favorites.length" x-show="favorites.length > 0"></span>
            </a>

            <!-- Divider -->
            <hr class="my-4 border-slate-100">

            <div class="px-4 py-1 text-[9px] font-bold text-slate-400 tracking-wider uppercase">
                Categorías
            </div>

            @foreach($categorias as $cat)
            <a class="flex items-center gap-4 px-4 py-2.5 rounded-r-full mr-2 transition-all cursor-pointer"
               :class="filterCategory === '{{ $cat['nombre'] }}' ? 'bg-blue-50 text-blue-600 font-bold sidebar-active' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-medium'"
               @click="filterCategory = '{{ $cat['nombre'] }}'; searchQuery = ''">
                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat['color'] }}"></span>
                <span class="text-xs truncate flex-1">{{ $cat['nombre'] }}</span>
                <span class="text-[10px] text-slate-400 font-semibold" x-show="!searchQuery">{{ count($cat['modulos']) }}</span>
            </a>
            @endforeach
        </nav>
        
        <div class="px-6 mb-4">
            <button @click="scrollToPersonas()" class="w-full py-3 px-4 bg-secondary text-white rounded-xl text-xs font-semibold shadow-md shadow-secondary/15 hover:scale-95 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">login</span>
                <span>Ingreso Demo Rápido</span>
            </button>
        </div>

        <div class="px-6 space-y-3 border-t border-slate-100 pt-6 text-[10px] text-slate-500">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>SQLite DB: Activa</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Prevalidación: En línea</span>
            </div>
            <div class="text-[9px] text-slate-400 font-mono">
                CMD_CORE v13.17.0
            </div>
        </div>
    </aside>

    <!-- TOP NAVIGATION -->
    <header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-white/80 backdrop-blur-md border-b border-slate-200 flex justify-between items-center px-8 z-40">
        <div class="flex items-center gap-4 flex-1">
            <div class="relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" style="font-size: 18px;">search</span>
                <input x-ref="search" x-model="searchQuery" class="w-full pl-12 pr-4 py-2 bg-slate-50 rounded-full border border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-100 text-xs outline-none transition-all" placeholder="Buscar herramientas, afiliados o módulos... (Pulsar /)" type="text" @keydown.slash.window.prevent="$refs.search.focus()"/>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Servicios Online
                </span>
                
                <button class="p-2 text-slate-500 hover:text-secondary hover:bg-slate-50 rounded-full transition-all relative" @click="showAlerts = !showAlerts" title="Alertas de Producción">
                    <span class="material-symbols-outlined" style="font-size: 20px;">notifications</span>
                    @if(count($alertas) > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full ring-2 ring-white"></span>
                    @endif
                </button>
            </div>
            
            <div class="h-6 w-[1px] bg-slate-200"></div>
            
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-800">Invitado de Control</p>
                    <p class="text-[9px] text-slate-400 font-medium">Consola de Desarrollo</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center font-bold text-xs text-blue-700 shadow-sm select-none">
                    IC
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT CANVAS -->
    <main class="ml-64 pt-24 px-8 pb-12 min-h-screen">
        
        <!-- Welcome Banner -->
        <section class="relative w-full rounded-[24px] overflow-hidden mb-8 bg-gradient-to-r from-[#041645] to-[#0d3c80] p-10 flex flex-col justify-center shadow-sm">
            <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none" fill="currentColor" class="text-white">
                    <path d="M0 100 C 20 80, 40 80, 60 100 C 80 80, 100 80, 100 100 Z"/>
                </svg>
            </div>
            <div class="relative z-10 max-w-2xl">
                <span class="px-2.5 py-1 bg-blue-500/20 text-blue-300 rounded-full text-[10px] font-bold uppercase tracking-wider mb-3 inline-block">CMD Operations Control</span>
                <h2 class="font-bold text-white text-[32px] tracking-tight leading-tight mb-2">Centro de Comando & Control ARS</h2>
                <p class="text-xs text-slate-200 leading-relaxed">Consola ejecutiva integrada para analistas, auditores y administradores. Administre en un solo entorno el motor de autorización médica, carga TSS, catálogo de cobertura y contabilidad corporativa.</p>
            </div>
        </section>

        <!-- Bento Grid: KPIs + Alertas/Consola -->
        <div class="bento-grid">
            
            <!-- KPI Grid - Column Span 8 -->
            <div class="col-span-8 grid grid-cols-2 gap-6">
                <!-- Afiliados Activos -->
                <div class="glass-card rounded-[20px] p-6 flex flex-col justify-between h-[180px] relative overflow-hidden bg-white">
                    <div class="flex justify-between items-start z-10">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <span class="material-symbols-outlined" style="font-size: 24px;">groups</span>
                        </div>
                        <span class="text-emerald-600 text-[10px] font-bold bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">trending_up</span>
                            +{{ number_format($kpis['dependientes'] ?? 0) }} dep
                        </span>
                    </div>
                    <div class="mt-2 z-10">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Afiliados Activos</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-0.5">{{ number_format($kpis['afiliados_activos']) }}</p>
                    </div>
                    <!-- Sparkline SVG -->
                    <div class="absolute bottom-0 left-0 right-0 h-12 opacity-40">
                        <svg class="w-full h-full" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M 0 10 L 0 8 Q 15 3, 30 6 T 60 2 T 85 5 L 100 1 L 100 10 Z" fill="url(#blue-grad)" />
                            <defs>
                                <linearGradient id="blue-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#1a73e8" />
                                    <stop offset="100%" stop-color="#ffffff" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- Solicitudes Pendientes -->
                <div class="glass-card rounded-[20px] p-6 flex flex-col justify-between h-[180px] relative overflow-hidden bg-white">
                    <div class="flex justify-between items-start z-10">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600">
                            <span class="material-symbols-outlined" style="font-size: 24px;">pending_actions</span>
                        </div>
                        <span class="text-rose-600 text-[10px] font-bold bg-rose-50 px-2 py-0.5 rounded-full flex items-center gap-0.5 animate-pulse">
                            <span class="material-symbols-outlined text-[12px]">priority_high</span>
                            Requiere Atención
                        </span>
                    </div>
                    <div class="mt-2 z-10">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sol. Pendientes</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-0.5">{{ number_format($kpis['solicitudes_pendientes']) }}</p>
                    </div>
                    <!-- Sparkline SVG -->
                    <div class="absolute bottom-0 left-0 right-0 h-12 opacity-40">
                        <svg class="w-full h-full" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M 0 10 L 0 5 Q 20 8, 40 4 T 80 7 L 100 2 L 100 10 Z" fill="url(#rose-grad)" />
                            <defs>
                                <linearGradient id="rose-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#d93025" />
                                    <stop offset="100%" stop-color="#ffffff" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- Autorizaciones Hoy -->
                <div class="glass-card rounded-[20px] p-6 flex flex-col justify-between h-[180px] relative overflow-hidden bg-white">
                    <div class="flex justify-between items-start z-10">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <span class="material-symbols-outlined" style="font-size: 24px;">fact_check</span>
                        </div>
                        <span class="text-emerald-700 text-[10px] font-bold bg-emerald-50 px-2 py-0.5 rounded-full">Hoy</span>
                    </div>
                    <div class="mt-2 z-10">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Autorizaciones Hoy</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-0.5">{{ number_format($kpis['autorizaciones_dia']) }}</p>
                    </div>
                    <!-- Sparkline SVG -->
                    <div class="absolute bottom-0 left-0 right-0 h-12 opacity-40">
                        <svg class="w-full h-full" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M 0 10 L 0 7 Q 25 3, 50 6 T 75 1 L 100 4 L 100 10 Z" fill="url(#emerald-grad)" />
                            <defs>
                                <linearGradient id="emerald-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#12b886" />
                                    <stop offset="100%" stop-color="#ffffff" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- Lotes Procesados -->
                <div class="glass-card rounded-[20px] p-6 flex flex-col justify-between h-[180px] relative overflow-hidden bg-white">
                    <div class="flex justify-between items-start z-10">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                            <span class="material-symbols-outlined" style="font-size: 24px;">inventory_2</span>
                        </div>
                        <span class="text-amber-700 text-[10px] font-bold bg-amber-50 px-2 py-0.5 rounded-full">TSS / Lotes</span>
                    </div>
                    <div class="mt-2 z-10">
                        <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Lotes Procesados</h3>
                        <p class="text-3xl font-extrabold text-slate-800 mt-0.5">{{ number_format($kpis['lotes_processed'] ?? 24) }}</p>
                    </div>
                    <!-- Sparkline SVG -->
                    <div class="absolute bottom-0 left-0 right-0 h-12 opacity-40">
                        <svg class="w-full h-full" viewBox="0 0 100 10" preserveAspectRatio="none">
                            <path d="M 0 10 L 0 9 Q 30 7, 50 8 T 80 4 L 100 1 L 100 10 Z" fill="url(#amber-grad)" />
                            <defs>
                                <linearGradient id="amber-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#f59f00" />
                                    <stop offset="100%" stop-color="#ffffff" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Panel Lateral de Alertas & Actividad Consola (col-span-4) -->
            <div class="col-span-4 flex flex-col gap-6">
                <!-- Alertas Operativas -->
                <div class="glass-card rounded-[20px] p-6 flex-1 bg-white flex flex-col justify-between" x-show="showAlerts">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-error rounded-full pulse-indicator"></span>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alertas Operativas</h3>
                            </div>
                            <span class="text-[10px] text-slate-400 font-semibold" x-text="alertas.length + ' incidentes'"></span>
                        </div>
                        <div class="space-y-2.5 max-h-[160px] overflow-y-auto pr-1">
                            @forelse($alertas as $alerta)
                            <div class="p-3 rounded-xl border flex flex-col justify-between transition-all
                                @if($alerta['tipo'] === 'error') bg-rose-50/50 border-rose-100 hover:bg-rose-50 text-rose-800
                                @elseif($alerta['tipo'] === 'warning') bg-amber-50/50 border-amber-100 hover:bg-amber-50 text-amber-800
                                @else bg-slate-50 border-slate-100 hover:bg-slate-100/50 text-slate-700
                                @endif">
                                <p class="text-[11px] font-medium leading-relaxed">{{ $alerta['texto'] }}</p>
                                <div class="flex justify-between items-center mt-2 pt-2 border-t border-dashed border-black/5">
                                    <span class="text-[9px] uppercase font-bold tracking-wider opacity-70">{{ $alerta['tipo'] }}</span>
                                    <a href="{{ $alerta['accion'] }}" class="text-[10px] font-bold text-blue-600 hover:underline flex items-center gap-0.5">
                                        <span>Resolver</span>
                                        <span class="material-symbols-outlined text-[10px]" style="font-size: 10px;">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="p-6 rounded-xl bg-slate-50 border border-dashed border-slate-200 flex flex-col items-center justify-center text-center">
                                <span class="material-symbols-outlined text-slate-400 text-3xl mb-1.5">check_circle</span>
                                <span class="text-xs text-slate-500 font-medium">Servidor operativo sin alertas</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Consola de Eventos en Tiempo Real -->
                <div class="glass-card rounded-[20px] p-6 bg-slate-900 text-slate-200 font-mono text-[10px] h-[220px] flex flex-col justify-between border-slate-800">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="flex items-center gap-1.5 text-blue-400 font-bold"><span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-ping"></span> AUDIT LOG FEED</span>
                        <span class="text-slate-500 text-[9px]">LIVE STACKS</span>
                    </div>
                    <div class="flex-1 overflow-y-auto space-y-1.5 py-3 pr-1">
                        <template x-for="(log, idx) in logs" :key="idx">
                            <div class="leading-relaxed flex gap-1">
                                <span class="text-slate-500 shrink-0" x-text="'[' + log.time + ']'"></span>
                                <span :class="{
                                    'text-emerald-400': log.type === 'success',
                                    'text-blue-400': log.type === 'info',
                                    'text-amber-400': log.type === 'warning',
                                    'text-rose-400': log.type === 'error'
                                }" x-text="log.text"></span>
                            </div>
                        </template>
                    </div>
                    <div class="border-t border-slate-800 pt-2 text-[9px] text-slate-500 flex justify-between">
                        <span>SYS STAT: STABLE</span>
                        <span>CONN: 127.0.0.1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN: INGRESO RÁPIDO DE USUARIOS DEMO -->
        <section id="personas-section" class="mt-12 space-y-6">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[28px]">supervised_user_circle</span>
                <h2 class="text-lg font-bold text-slate-800">Acceso Rápido a Perfiles de Simulación</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($personas as $per)
                <a href="{{ route('switch.role', $per['email']) }}" 
                   class="glass-card rounded-[20px] p-6 flex flex-col justify-between h-[160px] group cursor-pointer bg-white">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full text-white {{ $per['color'] }} flex items-center justify-center font-bold text-sm shadow-sm group-hover:scale-105 transition-transform select-none shrink-0">
                            {{ $per['icon'] }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-slate-800 group-hover:text-secondary transition-colors truncate">{{ $per['rol'] }}</h4>
                            <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">{{ $per['email'] }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                        {{ $per['desc'] }}
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-[10px] font-bold text-secondary group-hover:text-blue-700">
                        <span>Iniciar simulación</span>
                        <span class="material-symbols-outlined text-xs">login</span>
                    </div>
                </a>
                @endforeach
            </div>
        </section>

        <!-- DYNAMIC FAVORITES BAR -->
        <div x-show="favorites.length > 0 && !searchQuery && filterCategory !== 'fav'" x-cloak class="mt-12 space-y-6">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-yellow-500 fill-current text-[24px]">star</span>
                <h3 class="text-base font-bold text-slate-800">Módulos Favoritos</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($todosModulos as $mod)
                <div 
                    x-show="favorites.includes('{{ $mod['id'] }}')"
                    class="glass-card rounded-[20px] p-6 aspect-square flex flex-col justify-between group bg-white glow-effect"
                >
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-105 transition-transform select-none">
                            <span class="material-symbols-outlined text-2xl">{{ $mod['icon'] === 'chart-bar' ? 'analytics' : ($mod['icon'] === 'user' ? 'person' : ($mod['icon'] === 'user-group' ? 'groups' : ($mod['icon'] === 'upload' ? 'upload_file' : ($mod['icon'] === 'layers' ? 'layers' : ($mod['icon'] === 'bell' ? 'notifications' : ($mod['icon'] === 'clipboard-check' ? 'fact_check' : ($mod['icon'] === 'building-office' ? 'domain' : ($mod['icon'] === 'map' ? 'map' : ($mod['icon'] === 'document-text' ? 'receipt_long' : ($mod['icon'] === 'list-bullet' ? 'list' : ($mod['icon'] === 'clock' ? 'history' : 'settings'))))))))))) }}</span>
                        </div>
                        <button class="text-yellow-500 hover:text-slate-400 p-1 transition-colors" @click="toggleFav('{{ $mod['id'] }}')" title="Quitar de favoritos">
                            <span class="material-symbols-outlined text-[20px] fill-current">star</span>
                        </button>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-xs font-bold text-slate-800 mb-1 group-hover:text-secondary transition-colors">{{ $mod['nombre'] }}</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $mod['descripcion'] }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center mt-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded border 
                                @if($mod['estado'] === 'activo') bg-emerald-50 text-emerald-700 border-emerald-100
                                @elseif($mod['estado'] === 'demo') bg-blue-50 text-blue-700 border-blue-100
                                @else bg-amber-50 text-amber-700 border-amber-100
                                @endif">{{ $mod['estado'] }}</span>
                        </div>
                        <a href="{{ $mod['ruta'] }}" class="text-[11px] font-bold text-secondary hover:underline flex items-center gap-0.5">
                            <span>Abrir</span>
                            <span class="material-symbols-outlined text-xs">north_east</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- MODULES GRID BY CATEGORY -->
        @foreach($categorias as $cat)
        <div 
            class="mt-12 space-y-6"
            x-show="!filterCategory || filterCategory === '{{ $cat['nombre'] }}' || (filterCategory === 'fav' && hasFavsInCategory('{{ $cat['nombre'] }}'))"
        >
            <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat['color'] }}"></span>
                    <h2 class="text-base font-bold text-slate-800">{{ $cat['nombre'] }}</h2>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ count($cat['modulos']) }} Módulos</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($cat['modulos'] as $mod)
                <div 
                    x-show="(filterCategory !== 'fav' || favorites.includes('{{ $mod['id'] }}')) && (!searchQuery || '{{ strtolower($mod['nombre'].' '.$mod['descripcion']) }}'.includes(searchQuery.toLowerCase()))"
                    class="glass-card rounded-[20px] p-6 aspect-square flex flex-col justify-between group bg-white glow-effect"
                >
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm group-hover:scale-105 transition-transform select-none">
                            <span class="material-symbols-outlined text-2xl">{{ $mod['icon'] === 'chart-bar' ? 'analytics' : ($mod['icon'] === 'user' ? 'person' : ($mod['icon'] === 'user-group' ? 'groups' : ($mod['icon'] === 'upload' ? 'upload_file' : ($mod['icon'] === 'layers' ? 'layers' : ($mod['icon'] === 'bell' ? 'notifications' : ($mod['icon'] === 'clipboard-check' ? 'fact_check' : ($mod['icon'] === 'building-office' ? 'domain' : ($mod['icon'] === 'map' ? 'map' : ($mod['icon'] === 'document-text' ? 'receipt_long' : ($mod['icon'] === 'list-bullet' ? 'list' : ($mod['icon'] === 'clock' ? 'history' : 'settings'))))))))))) }}</span>
                        </div>
                        <button class="p-1 transition-colors" 
                            :class="favorites.includes('{{ $mod['id'] }}') ? 'text-yellow-500' : 'text-slate-300 hover:text-yellow-500'"
                            @click="toggleFav('{{ $mod['id'] }}')" 
                            title="Marcar como favorito">
                            <span class="material-symbols-outlined text-[20px]" :class="favorites.includes('{{ $mod['id'] }}') ? 'fill-current' : ''">star</span>
                        </button>
                    </div>
                    <div class="mt-4">
                        <h4 class="text-xs font-bold text-slate-800 mb-1 group-hover:text-secondary transition-colors">{{ $mod['nombre'] }}</h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $mod['descripcion'] }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center mt-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded border 
                                @if($mod['estado'] === 'activo') bg-emerald-50 text-emerald-700 border-emerald-100
                                @elseif($mod['estado'] === 'demo') bg-blue-50 text-blue-700 border-blue-100
                                @else bg-amber-50 text-amber-700 border-amber-100
                                @endif">{{ $mod['estado'] }}</span>
                            @if($mod['badge'])
                            <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-medium">{{ $mod['badge'] }}</span>
                            @endif
                        </div>
                        <a href="{{ $mod['ruta'] }}" class="text-[11px] font-bold text-secondary hover:underline flex items-center gap-0.5">
                            <span>Abrir</span>
                            <span class="material-symbols-outlined text-xs">north_east</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Secondary Grid - Row 3 -->
        <div class="bento-grid mt-12">
            <div class="col-span-4 glass-card rounded-[20px] p-6 h-[280px] bg-white">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-6">Distribución por Plan</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                            <span>Plan Platino</span>
                            <span>45%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="w-[45%] h-full bg-blue-600"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                            <span>Plan Oro</span>
                            <span>30%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="w-[30%] h-full bg-[#041645]"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1">
                            <span>Plan Complementario</span>
                            <span>25%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="w-[25%] h-full bg-[#7950f2]"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-span-8 glass-card rounded-[20px] p-0 overflow-hidden h-[280px] flex bg-white">
                <div class="w-1/2 p-8 flex flex-col justify-center">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cobertura Nacional</h3>
                    <p class="text-xs text-slate-500 mb-6 leading-relaxed">Nivel de disponibilidad y respuesta de servicios de la red de clínicas y farmacias prestadoras contratadas.</p>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                            <span class="text-[11px] font-medium text-slate-600">Óptimo</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-[11px] font-medium text-slate-600">Alerta</span>
                        </div>
                    </div>
                </div>
                <div class="w-1/2 relative bg-slate-100 overflow-hidden select-none">
                    <img class="w-full h-full object-cover" alt="Visualización de Mapa de Cobertura Nacional" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsE_ainNt-0J6PZ_E4r2whwC3OBoxO29j006vIyh2UvUJq3eURXWTSW7g85mCPGhOzwul8KW5THWT_DZsLaT5RPJm_7sQIaOFnHI6jtwvLC1dzMjbL046wuQVhDpsVAOah5hPcpSgXtkK8ydthWa0uRdkVzoKToSpk3g-VOhlHvgrl3bVHOnVILi38YGc4lQbiabZECcNEToL84_842feLAZ3c2zfS4jnMIiTop85cXlLAspq-bUPG2-AAjRHFX8lQ6iIAg7kMAxvr"/>
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-white/70 pointer-events-none"></div>
                </div>
            </div>
        </div>

    </main>

    <!-- FAB for quick actions -->
    <button @click="scrollToPersonas()" class="fixed bottom-12 right-12 w-14 h-14 bg-secondary text-white rounded-full shadow-lg flex items-center justify-center hover:scale-115 transition-transform z-50 group" title="Iniciar Sesión Rápida">
        <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform duration-300">account_circle</span>
    </button>

    <!-- Alpine.js Application Script -->
    <script>
        function landingApp() {
            return {
                searchQuery: '',
                filterCategory: null,
                showAlerts: true,
                favorites: JSON.parse(localStorage.getItem('ars_fav') || '[]'),
                
                // Simulación de Logs de auditoría
                logs: [],
                logTemplates: [
                    { text: 'Lote #3928 TSS importado por Analista de Afiliación', type: 'success' },
                    { text: 'Autorización #8591 aprobada con cobertura del 80%', type: 'info' },
                    { text: 'Nueva PSS registrada: Clínica Corominas Pepín', type: 'warning' },
                    { text: 'Auditoría médica completada para NSS ***-****312-3', type: 'success' },
                    { text: 'Prevalidación Unipago masiva completada: 432 registros', type: 'info' },
                    { text: 'Alerta del motor: Regla PDSS sobrepasó el tope en medicamentos', type: 'warning' },
                    { text: 'Cierre mensual contable de Junio procesado exitosamente', type: 'success' },
                    { text: 'Intento de ingreso bloqueado: IP no registrada en VPN', type: 'error' },
                    { text: 'Contrato de prestador actualizado: Hosp. Metropolitano Santiago', type: 'info' }
                ],

                init() {
                    // Inicializar con 4 logs
                    for (let i = 0; i < 4; i++) {
                        this.addRandomLog();
                    }

                    // Ciclo de nuevos logs en tiempo real cada 6 segundos
                    setInterval(() => {
                        this.addRandomLog();
                    }, 6000);
                },

                addRandomLog() {
                    const time = new Date().toLocaleTimeString('es-ES', { hour12: false });
                    const template = this.logTemplates[Math.floor(Math.random() * this.logTemplates.length)];
                    this.logs.unshift({
                        time: time,
                        text: template.text,
                        type: template.type
                    });
                    
                    // Limitar a máximo 8 logs en consola
                    if (this.logs.length > 8) {
                        this.logs.pop();
                    }
                },
                
                // Mapear módulos por categorías
                categoriesMap: {
                    @foreach($categorias as $cat)
                    '{{ $cat['nombre'] }}': [
                        @foreach($cat['modulos'] as $mod)
                        '{{ $mod['id'] }}',
                        @endforeach
                    ],
                    @endforeach
                },

                toggleFav(id) {
                    if (this.favorites.includes(id)) {
                        this.favorites = this.favorites.filter(f => f !== id);
                    } else {
                        this.favorites = [...this.favorites, id];
                    }
                    localStorage.setItem('ars_fav', JSON.stringify(this.favorites));
                },

                hasFavsInCategory(catName) {
                    const modIds = this.categoriesMap[catName] || [];
                    return modIds.some(id => this.favorites.includes(id));
                },

                scrollToPersonas() {
                    const el = document.getElementById('personas-section');
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }
        }
    </script>
</body>
</html>
