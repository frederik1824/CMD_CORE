@extends('layouts.ars')

@section('title', 'Indicadores y Reportes de Reclamaciones')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Tablero de Reportes & Desviación Financiera</h2>
            <p class="text-xs text-slate-400 font-medium">Indicadores consolidados y comportamiento financiero de glosas y facturación PSS</p>
        </div>
        <div class="flex space-x-2 text-xs">
            <button onclick="window.print()" class="px-4 py-2 border border-slate-200 rounded-full font-bold text-slate-600 bg-white hover:bg-slate-50 transition shadow-xs flex items-center space-x-1">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Imprimir Reporte</span>
            </button>
        </div>
    </div>

    <!-- Bento Grid de KPIs Financieros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta 1: Desviación General de Montos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4 md:col-span-2">
            <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Monto Reclamado vs Aprobado vs Objetado</span>
            </h3>

            @php
                $totalClaimed = \App\Models\AuthorizationClaim::sum('claimed_amount');
                $totalApproved = \App\Models\AuthorizationClaim::sum('approved_amount');
                $totalObjected = \App\Models\AuthorizationClaim::sum('objected_amount');

                $pctApproved = $totalClaimed > 0 ? ($totalApproved / $totalClaimed) * 100 : 0;
                $pctObjected = $totalClaimed > 0 ? ($totalObjected / $totalClaimed) * 100 : 0;
            @endphp

            <div class="grid grid-cols-3 gap-4 text-xs font-mono">
                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <span class="text-[8px] font-bold text-slate-450 uppercase block">Total Reclamado</span>
                    <span class="text-base font-extrabold text-slate-800 mt-1 block">DOP {{ number_format($totalClaimed, 2) }}</span>
                </div>
                <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                    <span class="text-[8px] font-bold text-emerald-600 uppercase block">Total Aprobado</span>
                    <span class="text-base font-extrabold text-emerald-700 mt-1 block">DOP {{ number_format($totalApproved, 2) }}</span>
                    <span class="text-[9px] font-bold text-emerald-500 mt-0.5 block">({{ round($pctApproved, 1) }}%)</span>
                </div>
                <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-100">
                    <span class="text-[8px] font-bold text-rose-600 uppercase block">Total Objetado (Glosas)</span>
                    <span class="text-base font-extrabold text-rose-700 mt-1 block">DOP {{ number_format($totalObjected, 2) }}</span>
                    <span class="text-[9px] font-bold text-rose-500 mt-0.5 block">({{ round($pctObjected, 1) }}%)</span>
                </div>
            </div>

            <!-- Gráfico de Barra Horizontal Simulado -->
            <div class="space-y-1">
                <div class="flex justify-between text-[10px] font-bold text-slate-400">
                    <span>Distribución de Glosas sobre lo Facturado</span>
                    <span>100%</span>
                </div>
                <div class="h-4 w-full bg-slate-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $pctApproved }}%"></div>
                    <div class="h-full bg-rose-500 transition-all duration-500" style="width: {{ $pctObjected }}%"></div>
                </div>
                <div class="flex items-center space-x-4 text-[9px] font-bold text-slate-400 pt-1">
                    <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span><span>Aprobado ({{ round($pctApproved, 1) }}%)</span></div>
                    <div class="flex items-center space-x-1.5"><span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span><span>Objetado/Glosa ({{ round($pctObjected, 1) }}%)</span></div>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Tiempos de Procesamiento -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <h3 class="text-xs font-bold text-slate-850 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Duración de Auditoría</span>
            </h3>

            <div class="space-y-4 my-auto py-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-semibold text-slate-650">Promedio Radicación a Aprobación</span>
                    <span class="font-bold text-purple-700 bg-purple-50 border border-purple-150 px-3 py-1 rounded-full font-mono">
                        {{ $stats['average_days'] }} días
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="font-semibold text-slate-650">Promedio Aprobación a Pago</span>
                    <span class="font-bold text-teal-700 bg-teal-50 border border-teal-150 px-3 py-1 rounded-full font-mono">
                        4.2 días
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="font-semibold text-slate-650">Plazo promedio de respuesta PSS</span>
                    <span class="font-bold text-amber-700 bg-amber-50 border border-amber-150 px-3 py-1 rounded-full font-mono">
                        7.8 días
                    </span>
                </div>
            </div>

            <p class="text-[9px] text-slate-400 italic">Datos simulados en base al ciclo de vida histórico del total de radicaciones.</p>
        </div>
    </div>

    <!-- Comportamiento por Prestador (PSS) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="p-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Top 5 Prestadores con Mayor Facturación</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left">Prestador PSS</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Monto Sometido</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Monto Aprobado</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Monto Objetado</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Tasa de Glosa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($pssData as $row)
                        @php
                            $rate = $row->total_claimed > 0 ? ($row->total_objected / $row->total_claimed) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/30 transition-colors font-semibold text-slate-700">
                            <td class="px-6 py-3.5 text-slate-800 font-bold">{{ $row->nombre }}</td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold">DOP {{ number_format($row->total_claimed, 2) }}</td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-emerald-700">DOP {{ number_format($row->total_approved, 2) }}</td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-rose-700">DOP {{ number_format($row->total_objected, 2) }}</td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border
                                    {{ $rate > 20 ? 'bg-rose-50 text-rose-700 border-rose-200' : 
                                       ($rate > 10 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                    {{ round($rate, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
