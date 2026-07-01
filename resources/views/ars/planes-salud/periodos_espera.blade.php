@extends('layouts.ars')

@section('title', 'Tiempos de Espera')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Tiempos de Espera (Carencias)</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de configuración e inspección de tiempos de carencia reglamentarios requeridos por cobertura.</p>
        </div>
    </div>

    <!-- Carencias por Cobertura -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Tiempos de Carencia Activos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Plan de Salud</th>
                        <th class="px-4 py-3 text-left">Servicio / Cobertura</th>
                        <th class="px-4 py-3 text-center">Días de Espera</th>
                        <th class="px-4 py-3 text-center">Excepciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($coberturas as $c)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-bold text-[#041e49]">{{ $c->plan?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->service?->coverage_description ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-rose-700 bg-rose-50/30 rounded-xl px-2.5 py-1">{{ $c->waiting_period_days }} días</td>
                            <td class="px-4 py-3 text-center text-slate-500">Excluye accidentes de tránsito y emergencias vitales</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado carencias con días de espera activos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
