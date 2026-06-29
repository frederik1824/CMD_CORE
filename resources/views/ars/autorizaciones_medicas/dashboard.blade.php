@extends('layouts.ars')

@section('title', 'Dashboard de Autorizaciones Médicas')

@section('content')
<div class="space-y-6">
    <!-- Encabezado Corporativo Premium -->
    <div class="bg-gradient-to-r from-[#000666] via-[#00368c] to-[#0056c5] rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="flex items-center space-x-4 z-10">
            <div class="p-2.5 bg-white rounded-2xl shadow-sm flex-shrink-0">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
            </div>
            <div class="space-y-1">
                <span class="text-[9px] bg-white/20 text-white border border-white/20 px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">Consola Ejecutiva</span>
                <h2 class="text-xl font-bold tracking-tight text-white">Canal de Autorizaciones Médicas</h2>
                <p class="text-xs text-blue-100 font-medium">Monitoreo de auditorías clínicas, convenios y provisión técnica en tiempo real.</p>
            </div>
        </div>
        <div class="z-10">
            <a href="{{ route('ars.autorizaciones_medicas.create') }}" 
               class="inline-flex items-center space-x-2 bg-white text-[#000666] hover:bg-slate-50 font-bold px-5 py-2.5 rounded-full transition shadow-md hover:scale-102 active:scale-98 text-xs">
                <span>+ Crear Autorización</span>
            </a>
        </div>
    </div>

    <!-- Bento Grid de Indicadores del Día -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-xs">
        <!-- KPI 1 -->
        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4 border border-slate-100 bg-white/95">
            <div class="p-3 bg-[#000666]/5 text-[#000666] rounded-2xl border border-[#000666]/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">Solicitudes Hoy</span>
                <span class="text-lg font-black text-slate-800 mt-1 block">{{ $autorizacionesHoy }} casos</span>
            </div>
        </div>

        <!-- KPI 2 -->
        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4 border border-slate-100 bg-white/95">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl border border-blue-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">Aprobación Auto</span>
                <span class="text-lg font-black text-blue-600 mt-1 block">{{ $aprobadasAuto }} aprobadas</span>
            </div>
        </div>

        <!-- KPI 3 -->
        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4 border border-slate-100 bg-white/95">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl border border-purple-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">En Auditoría Médica</span>
                <span class="text-lg font-black text-purple-700 mt-1 block">{{ $enAuditoria }} pendientes</span>
            </div>
        </div>

        <!-- KPI 4 -->
        <div class="glass-card p-5 rounded-3xl flex items-center space-x-4 border border-slate-100 bg-white/95">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl border border-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="text-slate-400 font-bold block text-[10px] uppercase tracking-wider">Overrides Aplicados</span>
                <span class="text-lg font-black text-indigo-700 mt-1 block">{{ $overridesHoy }} forzados</span>
            </div>
        </div>
    </div>

    <!-- Sección de Gráficos y Listados Bento -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Gráfico y Alertas (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Gráfico de Dona: Distribución Operativa -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100/80 shadow-xs text-xs space-y-4">
                <div class="flex justify-between items-center border-b border-slate-50 pb-3">
                    <h3 class="font-extrabold text-slate-800 text-xs">Distribución Operativa del Día</h3>
                    <span class="text-[10px] text-slate-400 font-bold">Tiempo Real</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="relative h-44 w-full flex justify-center items-center">
                        <canvas id="opsDoughnutChart" class="max-h-full"></canvas>
                    </div>
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Resumen de Métricas</span>
                        <div class="grid grid-cols-2 gap-3 text-[11px] font-semibold text-slate-650">
                            <div class="p-2 border border-slate-50 bg-slate-50/20 rounded-xl">
                                <span class="text-slate-400 block text-[9px]">Aprobaciones Auto:</span>
                                <span class="text-blue-600 font-extrabold block" x-text="Number({{ $aprobadasAuto }}).toLocaleString()"></span>
                            </div>
                            <div class="p-2 border border-slate-50 bg-slate-50/20 rounded-xl">
                                <span class="text-slate-400 block text-[9px]">Aprobaciones Override:</span>
                                <span class="text-indigo-600 font-extrabold block" x-text="Number({{ $overridesHoy }}).toLocaleString()"></span>
                            </div>
                            <div class="p-2 border border-slate-50 bg-slate-50/20 rounded-xl">
                                <span class="text-slate-400 block text-[9px]">En Auditoría:</span>
                                <span class="text-purple-600 font-extrabold block" x-text="Number({{ $enAuditoria }}).toLocaleString()"></span>
                            </div>
                            <div class="p-2 border border-slate-50 bg-slate-50/20 rounded-xl">
                                <span class="text-slate-400 block text-[9px]">Rechazadas:</span>
                                <span class="text-rose-600 font-extrabold block" x-text="Number({{ $rechazadas }}).toLocaleString()"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de Reglas de Tarifas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100/80 shadow-xs text-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h3 class="font-extrabold text-slate-800 text-xs">Alertas de Contratos y Reglas de Convenios</h3>
                    <span class="bg-rose-50 text-rose-700 px-3 py-1 rounded-full text-[9px] font-black border border-rose-200 uppercase tracking-wider">Urgente</span>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl border border-slate-100/80 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center space-x-3.5">
                            <div class="p-2.5 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800">Reclamaciones fuera de tarifarios</h4>
                                <p class="text-[10px] text-slate-450 mt-0.5">Existen autorizaciones activas que superan las tarifas máximas pactadas.</p>
                            </div>
                        </div>
                        <a href="{{ route('ars.autorizaciones_medicas.bandeja_revision') }}" class="text-[#0056c5] hover:underline font-bold px-3 py-1 bg-blue-50/40 rounded-full border border-blue-100 text-[10px]">Revisar</a>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl border border-slate-100/80 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center space-x-3.5">
                            <div class="p-2.5 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800">Intentos de PSS Sin Contrato Activo</h4>
                                <p class="text-[10px] text-slate-450 mt-0.5">Se detectaron {{ $pssSinContratoCount }} prestadoras inactivas o sin tarifario vigente.</p>
                            </div>
                        </div>
                        <a href="{{ route('ars.autorizaciones_medicas.bandeja_revision') }}" class="text-[#0056c5] hover:underline font-bold px-3 py-1 bg-blue-50/40 rounded-full border border-blue-100 text-[10px]">Revisar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Gestión y Bandejas (1 col) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Gestión de Bandejas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100/80 shadow-xs text-xs space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2">Navegación del Módulo</h3>
                
                <div class="flex flex-col space-y-2 font-bold text-slate-700">
                    <a href="{{ route('ars.autorizaciones_medicas.index') }}" class="flex items-center justify-between p-3.5 hover:bg-[#eaf1fb]/30 hover:border-blue-200 rounded-2xl border border-slate-100 transition shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Bandeja General</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('ars.autorizaciones_medicas.bandeja_auditoria') }}" class="flex items-center justify-between p-3.5 hover:bg-[#eaf1fb]/30 hover:border-blue-200 rounded-2xl border border-slate-100 transition shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            <span>Bandeja de Auditoría Médica</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('ars.autorizaciones_medicas.bandeja_revision') }}" class="flex items-center justify-between p-3.5 hover:bg-[#eaf1fb]/30 hover:border-blue-200 rounded-2xl border border-slate-100 transition shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Bandeja de Revisión</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('ars.autorizaciones_medicas.config_reglas') }}" class="flex items-center justify-between p-3.5 hover:bg-[#eaf1fb]/30 hover:border-blue-200 rounded-2xl border border-slate-100 transition shadow-2xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                            <span>Reglas de Negocio</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Resumen Informativo Adicional -->
            <div class="bg-gradient-to-br from-[#000666] to-[#00368c] p-6 rounded-3xl text-white space-y-3 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                <span class="text-[9px] font-bold tracking-wider uppercase bg-white/10 px-2 py-0.5 rounded border border-white/20">Operación Core</span>
                <h4 class="font-extrabold text-xs">Cumplimiento de SLAs</h4>
                <p class="text-[10px] text-blue-150 leading-relaxed font-semibold">El 98.4% de los expedientes de urgencia de hoy han sido auditados en menos de 15 minutos. Continúa monitoreando.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('opsDoughnutChart').getContext('2d');
        const labels = ['Aprobadas Auto', 'Aprobadas Override', 'En Auditoría', 'Rechazadas'];
        const data = [
            {{ $aprobadasAuto }},
            {{ $overridesHoy }},
            {{ $enAuditoria }},
            {{ $rechazadas }}
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#0056c5', // Aprobadas Auto: Royal Blue (Logo Blue)
                        '#6366f1', // Overrides: Indigo
                        '#8b5cf6', // Auditoría: Purple
                        '#f43f5e'  // Rechazadas: Rose
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '75%'
            }
        });
    });
</script>
@endsection
