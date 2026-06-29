@extends('layouts.ars')

@section('title', 'Dashboard Ejecutivo')

@section('content')
<div class="space-y-8 animate-fade-in font-sans">
    
    <!-- Welcome Banner (Adaptado de design.txt con PHP) -->
    <section class="relative w-full rounded-[32px] overflow-hidden mb-8 glass-card p-8 md:p-12 flex flex-col justify-center min-h-[220px]">
        <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none">
            <svg fill="currentColor" viewBox="0 0 24 24" class="w-full h-full text-secondary">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2-2 0-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
            </svg>
        </div>
        <div class="relative z-10 max-w-2xl space-y-3">
            <span class="px-4 py-1.5 bg-secondary/10 text-secondary rounded-full font-sans text-xs font-bold mb-2 inline-block">System Operational</span>
            <h2 class="font-title text-primary text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                Bienvenido al Centro de Comando ARS, {{ Auth::user()->name }}.
            </h2>
            <p class="font-sans text-on-surface-variant text-sm md:text-base leading-relaxed">
                Monitoreo en tiempo real de operaciones, afiliaciones y reclamaciones para una gestión eficiente de la red de salud.
            </p>
        </div>
        <div class="absolute right-12 top-1/2 -translate-y-1/2 flex gap-4 hidden lg:flex">
            <div class="flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 flex items-center justify-center shadow-lg shadow-primary/5 text-secondary">
                    <span class="material-symbols-outlined text-3xl">trending_up</span>
                </div>
                <span class="mt-2 text-xs font-semibold text-primary">Rendimiento</span>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 flex items-center justify-center shadow-lg shadow-primary/5 text-secondary">
                    <span class="material-symbols-outlined text-3xl">security</span>
                </div>
                <span class="mt-2 text-xs font-semibold text-primary">Seguridad</span>
            </div>
        </div>
    </section>

    <!-- Statistics KPI Section (Alineado con design.txt) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Afiliados activos -->
        <div class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-[180px]">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-3xl">groups</span>
                </div>
                <span class="text-green-600 text-xs font-bold flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-sm">arrow_upward</span>
                    +12.5%
                </span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-xs font-bold uppercase tracking-wider mb-1">Afiliados Activos</h3>
                <p class="font-title text-primary text-3xl font-extrabold leading-none">{{ number_format($kpis['afiliados_activos']) }}</p>
                <span class="text-[10px] text-slate-400 block mt-1">+{{ number_format($kpis['dependientes']) }} dependientes</span>
            </div>
        </div>

        <!-- Reclamaciones pendientes -->
        <div class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-[180px]">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-secondary/5 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-3xl">pending_actions</span>
                </div>
                <span class="text-error text-xs font-bold flex items-center gap-0.5 animate-pulse">
                    <span class="material-symbols-outlined text-sm">priority_high</span>
                    Atención
                </span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-xs font-bold uppercase tracking-wider mb-1">Reclamaciones Pendientes</h3>
                <p class="font-title text-primary text-3xl font-extrabold leading-none">{{ number_format($kpis['solicitudes_pendientes']) }}</p>
                <span class="text-[10px] text-slate-400 block mt-1">Revisión requerida</span>
            </div>
        </div>

        <!-- Prestadores acreditados -->
        <div class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-[180px]">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 flex items-center justify-center text-teal-600">
                    <span class="material-symbols-outlined text-3xl">fact_check</span>
                </div>
                <span class="text-teal-650 text-xs font-semibold">Red PSS</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-xs font-bold uppercase tracking-wider mb-1">Prestadores Acreditados</h3>
                <p class="font-title text-primary text-3xl font-extrabold leading-none">{{ number_format($kpis['pss_activas']) }}</p>
                <span class="text-[10px] text-slate-400 block mt-1">Médicos e Institutos</span>
            </div>
        </div>

        <!-- Pre-aut pendientes -->
        <div class="glass-card rounded-[24px] p-6 flex flex-col justify-between h-[180px]">
            <div class="flex justify-between items-start">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <span class="material-symbols-outlined text-3xl">hourglass_empty</span>
                </div>
                <span class="text-amber-650 text-xs font-semibold">Auditoría</span>
            </div>
            <div>
                <h3 class="text-on-surface-variant text-xs font-bold uppercase tracking-wider mb-1">Pre-aut Pendientes</h3>
                <p class="font-title text-primary text-3xl font-extrabold leading-none">{{ number_format($kpis['auditorias_pendientes']) }}</p>
                <span class="text-[10px] text-slate-400 block mt-1">Bandeja de Auditoría</span>
            </div>
        </div>

    </div>

    <!-- Módulos de Gestión / Accesos Rápidos -->
    <div class="space-y-4">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Módulos de Gestión</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            
            <!-- Module 1: Nuevo Afiliado -->
            <a href="{{ route('ars.titulares.create') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">person_add</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Nuevo Afiliado</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Registro de cotizantes titulares y dependientes.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Acceder</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Module 2: Pre-autorizar -->
            <a href="{{ route('ars.autorizaciones.nueva') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-white shadow-lg shadow-secondary/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Pre-autorizar</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Generar autorizaciones de servicios médicos.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Registrar</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Module 3: Reclamación -->
            <a href="{{ route('ars.autorizaciones.index') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-teal-600 flex items-center justify-center text-white shadow-lg shadow-teal-600/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">medical_services</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Reclamaciones</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Histórico y bandeja global de solicitudes.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Ver Bandeja</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Module 4: Facturar -->
            <a href="{{ route('ars.lotes.index') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-[#1a237e] flex items-center justify-center text-white shadow-lg shadow-indigo-900/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">inventory_2</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Carga de Lotes</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Procesamiento masivo de facturaciones y archivos.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Procesar</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Module 5: Prestadores -->
            <a href="{{ route('ars.pss.index') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">corporate_fare</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Prestadores (PSS)</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Acreditación e información de prestadoras médicas.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Gestionar</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

            <!-- Module 6: Reportes -->
            <a href="{{ route('ars.reportes.index') }}" class="glass-card rounded-[28px] p-6 flex flex-col justify-between min-h-[220px] group text-left">
                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center text-white shadow-lg shadow-slate-750/20 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                </div>
                <div class="mt-4">
                    <h4 class="font-title text-primary font-bold text-sm mb-1">Reportes</h4>
                    <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">Análisis de producción, auditorías y coberturas.</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center mt-3">
                    <span class="text-[10px] font-semibold text-secondary">Consultar</span>
                    <span class="material-symbols-outlined text-secondary text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </div>
            </a>

        </div>
    </div>

    <!-- Charts and Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart 1: Afiliaciones -->
        <div class="glass-card p-6 shadow-sm rounded-3xl">
            <h3 class="text-xs font-bold text-primary uppercase tracking-wider mb-4">Crecimiento de Afiliaciones</h3>
            <div class="h-60 relative">
                <canvas id="chartAfiliaciones"></canvas>
            </div>
        </div>

        <!-- Chart 2: Estados Autorizaciones -->
        <div class="glass-card p-6 shadow-sm rounded-3xl">
            <h3 class="text-xs font-bold text-primary uppercase tracking-wider mb-4">Autorizaciones por Estado</h3>
            <div class="h-60 relative">
                <canvas id="chartEstados"></canvas>
            </div>
        </div>

        <!-- Chart 3: Prioridad de Solicitudes -->
        <div class="glass-card p-6 shadow-sm rounded-3xl">
            <h3 class="text-xs font-bold text-primary uppercase tracking-wider mb-4">Distribución por Prioridad</h3>
            <div class="h-60 relative">
                <canvas id="chartPrioridades"></canvas>
            </div>
        </div>

    </div>

    <!-- Recent Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Últimas Solicitudes -->
        <div class="glass-card shadow-sm rounded-3xl overflow-hidden">
            <div class="px-6 py-5 border-b border-white/40 flex items-center justify-between">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider">Últimas Solicitudes de Autorización</h3>
                <a href="{{ route('ars.autorizaciones.index') }}" class="text-[10px] font-bold text-secondary hover:underline transition">Ver bandeja completa &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[360px] overflow-y-auto">
                @forelse($ultimasAutorizaciones as $aut)
                    <div class="p-4 flex items-center justify-between hover:bg-white/50 transition">
                        <div class="flex items-center space-x-3.5">
                            <span class="p-2.5 rounded-xl bg-primary/5 text-primary font-bold text-[10px] tracking-wide border border-primary/10">AUT</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $aut->numero_autorizacion }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $aut->pss->nombre }} - {{ optional($aut->servicio)->descripcion ?? optional($aut->servicioPdss)->coverage_description ?? $aut->procedimiento ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-800 block">${{ number_format($aut->monto_solicitado, 2) }}</span>
                            <span class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wide border {{ 
                                $aut->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : (
                                $aut->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-250' : (
                                $aut->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-250' : 'bg-amber-50 text-amber-700 border-amber-250'))
                            }}">{{ $aut->estado }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">No hay solicitudes recientes registradas.</div>
                @endforelse
            </div>
        </div>

        <!-- Últimos Lotes Procesados -->
        <div class="glass-card shadow-sm rounded-3xl overflow-hidden">
            <div class="px-6 py-5 border-b border-white/40 flex items-center justify-between">
                <h3 class="text-xs font-bold text-primary uppercase tracking-wider">Lotes de Afiliación & Novedades</h3>
                <a href="{{ route('ars.lotes.index') }}" class="text-[10px] font-bold text-secondary hover:underline transition">Ver todos los lotes &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 max-h-[360px] overflow-y-auto">
                @forelse($ultimosLotes as $lote)
                    <div class="p-4 flex items-center justify-between hover:bg-white/50 transition">
                        <div class="flex items-center space-x-3.5">
                            <span class="p-2.5 rounded-xl bg-secondary/5 text-secondary font-bold text-[10px] tracking-wide border border-secondary/10 font-mono">LOT</span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Tipo: <span class="capitalize">{{ str_replace('_', ' ', $lote->tipo_lote) }}</span> - Reg: {{ $lote->total_registros }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wide border {{ 
                                $lote->estado_lote === 'EV' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : (
                                $lote->estado_lote === 'PC' ? 'bg-blue-50 text-blue-700 border-blue-250 animate-pulse' : (
                                $lote->estado_lote === 'PE' ? 'bg-amber-50 text-amber-700 border-amber-250' : (
                                $lote->estado_lote === 'RE' ? 'bg-rose-50 text-rose-700 border-rose-250' : 'bg-slate-50 text-slate-650 border-slate-250')))
                            }}">
                                {{ $lote->estado_lote === 'EV' ? 'Procesado OK' : ($lote->estado_lote === 'PC' ? 'Procesando' : ($lote->estado_lote === 'PE' ? 'Errores' : ($lote->estado_lote === 'RE' ? 'Rechazado' : 'Espera (VE)'))) }}
                            </span>
                            <span class="text-[9px] text-slate-400 block mt-1 font-mono">{{ $lote->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">No hay lotes recientes registrados.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Chart.js Config -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxAfil = document.getElementById('chartAfiliaciones').getContext('2d');
        new Chart(ctxAfil, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartAfiliaciones['labels']) !!},
                datasets: [{
                    label: 'Afiliados Nuevos',
                    data: {!! json_encode($chartAfiliaciones['data']) !!},
                    borderColor: '#0056c5',
                    backgroundColor: 'rgba(0, 86, 197, 0.04)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#0056c5'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });

        const ctxEst = document.getElementById('chartEstados').getContext('2d');
        new Chart(ctxEst, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartEstados['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartEstados['data']) !!},
                    backgroundColor: ['#0f6df3', '#ba1a1a', '#bdc2ff', '#00a7bb', '#767683'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { family: 'Inter', size: 9 } } }
                },
                cutout: '75%'
            }
        });

        const ctxPrio = document.getElementById('chartPrioridades').getContext('2d');
        new Chart(ctxPrio, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartPrioridades['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartPrioridades['data']) !!},
                    backgroundColor: ['#d9e2ff', '#0f6df3', '#000666'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    });
</script>
@endsection
