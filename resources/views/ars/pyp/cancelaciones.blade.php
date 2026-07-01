@extends('layouts.ars')

@section('title', 'Egresos PyP')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Egresos y Cancelaciones de Programas Preventivos</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja histórica de afiliados egresados de programas por mejoría, voluntario o inasistencia.</p>
        </div>
    </div>

    <!-- Listado de Egresos -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Afiliados Egresados</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left">Programa Preventivo</th>
                        <th class="px-4 py-3 text-left">Fecha Egreso</th>
                        <th class="px-4 py-3 text-left">Motivo / Causa Egreso</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($cancelaciones as $c)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-850 block">{{ $c->affiliate?->nombre_completo ?? 'N/A' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Céd: {{ $c->affiliate?->cedula ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $c->program?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $c->cancelled_at ? \Carbon\Carbon::parse($c->cancelled_at)->format('d/m/Y') : 'N/A' }}</td>
                            <td class="px-4 py-3 text-rose-700 font-medium leading-relaxed">{{ $c->cancellation_reason ?? 'No especificado' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[9px] font-bold text-rose-700 border border-rose-220">
                                    {{ $c->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se registran egresos formales de programas preventivos en el sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
