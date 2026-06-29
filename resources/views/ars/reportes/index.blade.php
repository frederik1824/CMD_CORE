@extends('layouts.ars')

@section('title', 'Reportería Ejecutiva')

@section('content')
<div class="space-y-6 animate-fade-in text-xs">
    <!-- Encabezado Corporativo Premium -->
    <div class="bg-gradient-to-r from-[#000666] via-[#00368c] to-[#0056c5] rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
        
        <div class="flex items-center space-x-4 z-10">
            <div class="p-2.5 bg-white rounded-2xl shadow-sm flex-shrink-0">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
            </div>
            <div class="space-y-1">
                <span class="text-[9px] bg-white/20 text-white border border-white/20 px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider">Módulo de Analítica</span>
                <h2 class="text-xl font-bold tracking-tight text-white">Reportes & Analíticas de Salud</h2>
                <p class="text-xs text-blue-100 font-medium">Consola analítica institucional para evaluar tasas de respuesta, auditorías médicas y transacciones de lotes.</p>
            </div>
        </div>
    </div>

    <!-- Filtro de Fechas -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100/85 shadow-sm">
        <form action="{{ route('ars.reportes.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end max-w-2xl font-bold text-slate-500 uppercase tracking-wider text-[9px]">
            <div class="flex-1 w-full">
                <label for="fecha_inicio" class="block mb-1.5">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                       class="block w-full rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <div class="flex-1 w-full">
                <label for="fecha_fin" class="block mb-1.5">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}"
                       class="block w-full rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 font-medium text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-[#0056c5] hover:bg-blue-700 text-white font-bold rounded-full transition shadow-md normal-case text-xs">
                Aplicar Rango
            </button>
        </form>
    </div>

    <!-- KPIs del Reporte -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Tiempo Promedio de Respuesta -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 flex items-center justify-between hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Tiempo de Respuesta</span>
                <span class="text-3xl font-bold text-slate-800 block mt-2 font-mono">{{ $tiempoPromedioMinutos }} min</span>
                <span class="text-[10px] text-slate-450 mt-1 block font-semibold">Promedio envío PSS a resolución</span>
            </div>
            <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl border border-purple-100">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Lotes Procesados -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 flex items-center justify-between hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Lotes Transmitidos</span>
                <span class="text-3xl font-bold text-slate-800 block mt-2 font-mono">{{ $lotesTipo->sum('total') }}</span>
                <span class="text-[10px] text-slate-450 mt-1 block font-semibold">Modificaciones TSS / Unipago</span>
            </div>
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl border border-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2v-3M8 7h4m-4 8h4m-4-4h4"/>
                </svg>
            </div>
        </div>

        <!-- Tasa de Aprobación Automática (Logo Blue) -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 flex items-center justify-between hover:scale-[1.01] hover:shadow-md transition-all duration-200">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Aprobaciones del Período</span>
                @php
                    $totalAut = $autorizacionesEstado->sum('total');
                    $aprobadas = $autorizacionesEstado->where('estado', 'Aprobada')->first()->total ?? 0;
                    $tasa = $totalAut > 0 ? round(($aprobadas / $totalAut) * 100, 1) : 0;
                @endphp
                <span class="text-3xl font-bold text-[#0056c5] block mt-2 font-mono">{{ $tasa }}%</span>
                <span class="text-[10px] text-slate-450 mt-1 block font-semibold">Tasa de aprobación directa</span>
            </div>
            <div class="p-4 bg-blue-50 text-[#0056c5] rounded-2xl border border-blue-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Gráficos de Reportes -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Gráfico: Autorizaciones por Estado -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="font-extrabold text-slate-800 mb-6 uppercase tracking-wider text-[10px]">Autorizaciones por Estado (Período)</h3>
            <div class="h-64 relative">
                <canvas id="chartAutEstado"></canvas>
            </div>
        </div>

        <!-- Gráfico: Autorizaciones por PSS (Top 5) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="font-extrabold text-slate-800 mb-6 uppercase tracking-wider text-[10px]">Top 5 Prestadoras (Cantidad Solicitudes)</h3>
            <div class="h-64 relative">
                <canvas id="chartAutPss"></canvas>
            </div>
        </div>

        <!-- Gráfico: Afiliados vs Dependientes -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="font-extrabold text-slate-800 mb-6 uppercase tracking-wider text-[10px]">Estado de Afiliaciones (Titulares vs Dependientes)</h3>
            <div class="h-64 relative">
                <canvas id="chartAfilAprobados"></canvas>
            </div>
        </div>

        <!-- Tabla: Rechazos por Motivo -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 mb-4 uppercase tracking-wider text-[10px]">Top Motivos de Rechazo (Autorizaciones)</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($rechazosMotivo as $rech)
                        <div class="py-3.5 flex items-center justify-between">
                            <span class="text-slate-700 font-bold max-w-xs truncate" title="{{ $rech->motivo_estado }}">{{ $rech->motivo_estado }}</span>
                            <span class="bg-rose-50 text-rose-700 border border-rose-200 font-extrabold px-3 py-0.5 rounded-full font-mono text-[10px]">{{ $rech->total }}</span>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 italic">No se han registrado rechazos en el rango de fechas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de Gráficos con Colores de Marca -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Autorizaciones por Estado
        const ctxAut = document.getElementById('chartAutEstado').getContext('2d');
        new Chart(ctxAut, {
            type: 'pie',
            data: {
                labels: {!! json_encode($autorizacionesEstado->pluck('estado')->toArray()) !!},
                datasets: [{
                    data: {!! json_encode($autorizacionesEstado->pluck('total')->toArray()) !!},
                    backgroundColor: [
                        '#0056c5', // Aprobada: Royal Blue (Logo Blue)
                        '#f43f5e', // Rechazada: Rose
                        '#8b5cf6', // Auditoría: Purple
                        '#f59e0b', // Pendiente: Amber
                        '#64748b'  // Otros: Slate
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'right', 
                        labels: { 
                            font: { family: 'Inter, sans-serif', size: 10, weight: '600' }, 
                            color: '#475569' 
                        } 
                    }
                }
            }
        });

        // 2. Autorizaciones por PSS (Top 5)
        const ctxPss = document.getElementById('chartAutPss').getContext('2d');
        new Chart(ctxPss, {
            type: 'bar',
            data: {
                labels: {!! json_encode($autorizacionesPss->map(fn($item) => $item->pss ? $item->pss->nombre : 'OTRO')->toArray()) !!},
                datasets: [{
                    label: 'Solicitudes',
                    data: {!! json_encode($autorizacionesPss->pluck('total')->toArray()) !!},
                    backgroundColor: '#0056c5', // Logo Blue
                    borderRadius: 20,
                    barThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' }, 
                        ticks: { color: '#64748b', font: { family: 'Inter, sans-serif', size: 9, weight: '500' } } 
                    },
                    x: { 
                        grid: { display: false }, 
                        ticks: { 
                            color: '#64748b', 
                            font: { family: 'Inter, sans-serif', size: 9, weight: '600' },
                            callback: function(val, index) {
                                let label = this.getLabelForValue(val);
                                return label.length > 15 ? label.substring(0, 15) + '...' : label;
                            }
                        } 
                    }
                }
            }
        });

        // 3. Afiliados vs Dependientes Aprobados
        const ctxAfil = document.getElementById('chartAfilAprobados').getContext('2d');
        new Chart(ctxAfil, {
            type: 'doughnut',
            data: {
                labels: ['Titulares', 'Dependientes'],
                datasets: [{
                    data: [
                        {{ $afiliacionesEstado->where('estado_afiliacion', 'OK')->first()->total ?? 0 }},
                        {{ $dependientesEstado->where('estado_afiliacion', 'OK')->first()->total ?? 0 }}
                    ],
                    backgroundColor: ['#000666', '#0056c5'], // Navy Blue & Royal Blue
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            font: { family: 'Inter, sans-serif', size: 10, weight: '600' }, 
                            color: '#475569' 
                        } 
                    }
                },
                cutout: '75%'
            }
        });
    });
</script>
@endsection
