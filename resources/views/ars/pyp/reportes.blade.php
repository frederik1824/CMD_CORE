@extends('layouts.ars')

@section('title', 'Indicadores PyP')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Indicadores y Reportes Preventivos (PyP)</h2>
            <p class="text-xs text-slate-500 font-medium">Estadísticas operativas, efectividad de captación de pacientes en riesgo y censo de afiliados.</p>
        </div>
    </div>

    <!-- KPIs de Programas Preventivos -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Total Inscritos</span>
                <span class="text-2xl font-black text-[#041e49]">{{ $stats['total_enrollments'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-blue-500 bg-blue-50 p-2.5 rounded-2xl">people</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Afiliados Activos</span>
                <span class="text-2xl font-black text-emerald-600">{{ $stats['active_enrollments'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-emerald-500 bg-emerald-50 p-2.5 rounded-2xl">check_circle</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Candidatos Detectados</span>
                <span class="text-2xl font-black text-amber-600">{{ $stats['candidates'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-amber-500 bg-amber-50 p-2.5 rounded-2xl">troubleshoot</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Programas Activos</span>
                <span class="text-2xl font-black text-purple-600">{{ $stats['programs_count'] }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-purple-500 bg-purple-50 p-2.5 rounded-2xl">clinical_notes</span>
        </div>
    </div>

    <!-- Inscritos por Programa -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Distribución de Inscripciones por Programa</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Programa</th>
                        <th class="px-4 py-3 text-left">Grupo Riesgo Objetivo</th>
                        <th class="px-4 py-3 text-mono text-center">Afiliados Inscritos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($programEnrollments as $pe)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-bold text-[#041e49]">{{ $pe->name }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-850">{{ $pe->riskGroup?->name ?? 'General' }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-slate-800">{{ $pe->enrollments_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400 font-semibold">No hay estadísticas de programas registradas en el censo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
