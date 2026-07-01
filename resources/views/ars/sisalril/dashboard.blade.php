@extends('layouts.ars')

@section('title', 'SISALRIL Dashboard')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Regulatorio SISALRIL</h2>
            <p class="text-xs text-slate-500 font-medium">Consola de monitoreo de archivos regulatorios y reportes mensuales obligatorios para el sistema SIMON.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('sisalril.generar') }}" class="bg-[#041e49] text-white px-4 py-2 rounded-full font-bold hover:bg-slate-800 transition flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Generar Reporte
            </a>
        </div>
    </div>

    <!-- KPIs Regulatorios -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Esquemas Activos</span>
                <span class="text-2xl font-black text-[#041e49]">{{ $stats['total_schemas'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-blue-500 bg-blue-50 p-2.5 rounded-2xl">clinical_notes</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Corridas del Mes</span>
                <span class="text-2xl font-black text-slate-800 font-mono">{{ $stats['runs_this_month'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-slate-500 bg-slate-50 p-2.5 rounded-2xl">analytics</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Envíos Aprobados</span>
                <span class="text-2xl font-black text-emerald-600 font-mono">{{ $stats['approved_runs'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-emerald-500 bg-emerald-50 p-2.5 rounded-2xl">check_circle</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Envíos Rechazados</span>
                <span class="text-2xl font-black text-rose-600 font-mono">{{ $stats['rejected_runs'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-rose-500 bg-rose-50 p-2.5 rounded-2xl">cancel</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Pendientes Envío</span>
                <span class="text-2xl font-black text-amber-600 font-mono">{{ $stats['pending_runs'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-amber-500 bg-amber-50 p-2.5 rounded-2xl">pending</span>
        </div>
    </div>

    <!-- Lista de Esquemas -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Estado de Informes Regulatorios</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Esquema</th>
                        <th class="px-4 py-3 text-left">Frecuencia / Origen</th>
                        <th class="px-4 py-3 text-left">Última Corrida</th>
                        <th class="px-4 py-3 text-center">Estatus Presentación</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($esquemas as $e)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-900 block text-xs">Esquema {{ $e->schema_code }}</span>
                                <span class="text-[10px] text-slate-450 block font-normal leading-relaxed">{{ $e->name }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="block font-semibold text-slate-700">{{ $e->periodicity }}</span>
                                <span class="block text-[10px] text-slate-400">Fuente: {{ $e->module_source }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 font-mono">
                                @if($e->last_run)
                                    <span class="block font-semibold">{{ $e->last_run->run_number }}</span>
                                    <span class="block text-[9px] text-slate-400">{{ $e->last_run->generated_at }}</span>
                                @else
                                    <span class="text-slate-400 italic">No generado aún</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($e->last_run)
                                    @if($e->last_run->status === 'aprobado')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">Aprobado</span>
                                    @elseif($e->last_run->status === 'con_errores')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[9px] font-bold text-rose-700 border border-rose-220">Con Errores</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">Generado</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-[9px] font-bold text-slate-500 border border-slate-200">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('sisalril.show', $e->schema_code) }}" class="text-[#041e49] font-bold hover:underline">Ver Historial</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
