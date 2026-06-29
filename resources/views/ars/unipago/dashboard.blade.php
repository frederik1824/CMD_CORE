@extends('layouts.core')

@section('title', 'Simulador Unipago / Unisigma')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Simulador Unipago - Central de Afiliaciones y Cápitas</h2>
            <p class="text-xs text-slate-500 font-medium">Panel interactivo que simula la interacción con la base nacional de afiliados y la dispersión monetaria.</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('ars.unipago.prevalidar') }}" class="bg-[#041e49] text-white rounded-full px-4 py-2 text-xs font-bold hover:bg-slate-800 transition shadow-xs flex items-center space-x-1.5">
                <span>Prevalidación Masiva</span>
            </a>
            <a href="{{ route('ars.unipago.lotes') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
                Cargar Lote Afiliaciones
            </a>
        </div>
    </div>

    <!-- KPIs del Simulador -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Tarjeta 1 -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-blue-50 text-blue-800 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Afiliados Totales</span>
                <span class="text-xl font-black text-[#041e49] mt-0.5">{{ $stats['titulares_count'] + $stats['dependientes_count'] }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">T: {{ $stats['titulares_count'] }} | D: {{ $stats['dependientes_count'] }}</span>
            </div>
        </div>

        <!-- Tarjeta 2 -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-800 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lotes Unipago</span>
                <span class="text-xl font-black text-[#041e49] mt-0.5">{{ $stats['lotes_enviados'] }} lotes</span>
                <span class="block text-[9px] text-slate-400 font-medium">Procesados: {{ $stats['lotes_procesados'] }}</span>
            </div>
        </div>

        <!-- Tarjeta 3 -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-teal-50 text-teal-800 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aprobaciones OK</span>
                <span class="text-xl font-black text-[#041e49] mt-0.5">{{ $stats['solicitudes_ok'] }} registros</span>
                <span class="block text-[9px] text-slate-400 font-medium">Rechazados: {{ $stats['solicitudes_re'] }}</span>
            </div>
        </div>

        <!-- Tarjeta 4 -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-800 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Monto Dispersado</span>
                <span class="text-xl font-black text-emerald-700 mt-0.5">DOP {{ number_format($stats['monto_dispersado'], 2) }}</span>
                <span class="block text-[9px] text-slate-400 font-medium">Cortes: {{ count($corteMonto) }} cortes</span>
            </div>
        </div>
    </div>

    <!-- Sección de Gráficos y Tablas Bento -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Resultados de Solicitudes (Izquierda 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Distribución de Solicitudes de Lotes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                    Distribución de Solicitudes en Lotes
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                        <span class="text-emerald-800 font-black text-lg font-mono">{{ $stats['solicitudes_ok'] }}</span>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Aceptadas (OK)</p>
                    </div>
                    <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100">
                        <span class="text-amber-800 font-black text-lg font-mono">{{ $stats['solicitudes_pe64'] }}</span>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Nómina/Aporte (PE64)</p>
                    </div>
                    <div class="bg-purple-50/50 p-4 rounded-2xl border border-purple-100">
                        <span class="text-purple-800 font-black text-lg font-mono">{{ $stats['solicitudes_pe75'] }}</span>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Inexistentes (PE75)</p>
                    </div>
                    <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100">
                        <span class="text-rose-800 font-black text-lg font-mono">{{ $stats['solicitudes_re'] }}</span>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Rechazadas (RE)</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Dispersión por Período -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                    Dispersión Histórica de Cápitas
                </h3>
                <div class="space-y-3">
                    @foreach($corteMonto as $periodo => $monto)
                        <div>
                            <div class="flex justify-between text-[11px] font-bold text-slate-600 mb-1">
                                <span>Período: {{ $periodo }}</span>
                                <span class="font-mono text-[#041e49]">DOP {{ number_format($monto, 2) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-teal-600 h-2 rounded-full" style="width: {{ min(100, ($monto / 200000) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Panel Lateral: Calendario & Acciones Rápidas -->
        <div class="space-y-6">
            <!-- Programación de Cortes -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-4.5 h-4.5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Calendario de Cortes de Dispersión</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="border border-slate-100 p-3 rounded-2xl bg-slate-50/50 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-700">Primer Corte Mensual</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Procesamiento de cápitas regular</p>
                        </div>
                        <span class="font-bold text-slate-500">Día 10</span>
                    </div>

                    <div class="border border-slate-100 p-3 rounded-2xl bg-slate-50/50 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-slate-700">Segundo Corte Operativo</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Novedades y retroactivos</p>
                        </div>
                        <span class="font-bold text-slate-500">Día 25</span>
                    </div>
                </div>

                <!-- Formulario Rápido de Corte -->
                <form action="{{ route('ars.unipago.cortes.generar') }}" method="POST" class="pt-4 border-t border-slate-100 space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Período de Dispersión</label>
                        <select name="period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:bg-white focus:outline-none transition-all">
                            <option value="{{ date('Ym') }}">Mes Actual ({{ date('Y/m') }})</option>
                            <option value="{{ date('Ym', strtotime('-1 month')) }}">Mes Anterior</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Corte</label>
                        <select name="cut_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:bg-white focus:outline-none transition-all">
                            <option value="primer corte">Primer Corte (Capitación)</option>
                            <option value="segundo corte">Segundo Corte (Novedades)</option>
                            <option value="operativo">Corte Operativo Especial</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-center block">
                        Ejecutar Corte de Dispersión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
