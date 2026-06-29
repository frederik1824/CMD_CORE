@extends('layouts.ars')

@section('title', 'Control de Plazos y Antigüedad (Aging)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Antigüedad de Cuentas & Plazos (Aging)</h2>
            <p class="text-xs text-slate-400 font-medium">Análisis de expedientes radicados por tiempo transcurrido en el flujo administrativo</p>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <span class="font-bold text-slate-500">Tiempo promedio de liquidación:</span>
            <span class="bg-teal-50 text-teal-700 border border-teal-200 rounded-full px-3 py-1 font-bold">
                {{ $stats['average_days'] }} días
            </span>
        </div>
    </div>

    <!-- Widgets de Antigüedad (Semáforos) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Rango 1-30 días -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="leading-tight text-xs">
                <span class="text-slate-400 font-semibold block">1 a 30 días</span>
                <span class="text-xl font-black text-slate-850 mt-1 block">{{ $stats['bucket_1_30'] }} <span class="text-[10px] font-normal text-slate-400">casos</span></span>
                <span class="text-[10px] font-bold text-emerald-650 block mt-1 font-mono">DOP {{ number_format($stats['claimed_by_bucket']['1-30'], 2) }}</span>
            </div>
        </div>

        <!-- Rango 31-60 días -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="leading-tight text-xs">
                <span class="text-slate-400 font-semibold block">31 a 60 días</span>
                <span class="text-xl font-black text-slate-850 mt-1 block">{{ $stats['bucket_31_60'] }} <span class="text-[10px] font-normal text-slate-400">casos</span></span>
                <span class="text-[10px] font-bold text-amber-650 block mt-1 font-mono">DOP {{ number_format($stats['claimed_by_bucket']['31-60'], 2) }}</span>
            </div>
        </div>

        <!-- Rango 61-90 días -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 rounded-xl bg-orange-50 text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="leading-tight text-xs">
                <span class="text-slate-400 font-semibold block">61 a 90 días</span>
                <span class="text-xl font-black text-slate-850 mt-1 block">{{ $stats['bucket_61_90'] }} <span class="text-[10px] font-normal text-slate-400">casos</span></span>
                <span class="text-[10px] font-bold text-orange-650 block mt-1 font-mono">DOP {{ number_format($stats['claimed_by_bucket']['61-90'], 2) }}</span>
            </div>
        </div>

        <!-- Rango +90 días -->
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 rounded-xl bg-rose-50 text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="leading-tight text-xs">
                <span class="text-slate-400 font-semibold block">Más de 90 días</span>
                <span class="text-xl font-black text-slate-850 mt-1 block">{{ $stats['bucket_90_plus'] }} <span class="text-[10px] font-normal text-slate-400">casos</span></span>
                <span class="text-[10px] font-bold text-rose-650 block mt-1 font-mono">DOP {{ number_format($stats['claimed_by_bucket']['90+'], 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Listado con Semáforo de Colores -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="p-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Gros del Expediente Radicado</h3>
            <div class="flex items-center space-x-3 text-[10px] font-bold text-slate-400">
                <div class="flex items-center space-x-1"><span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span><span>1-30d</span></div>
                <div class="flex items-center space-x-1"><span class="w-2.5 h-2.5 bg-amber-500 rounded-full"></span><span>31-60d</span></div>
                <div class="flex items-center space-x-1"><span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span><span>61-90d</span></div>
                <div class="flex items-center space-x-1"><span class="w-2.5 h-2.5 bg-rose-500 rounded-full"></span><span>+90d</span></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left w-12"></th>
                        <th scope="col" class="px-6 py-3.5 text-left">Código / NCF</th>
                        <th scope="col" class="px-6 py-3.5 text-left">Prestador PSS</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Días en Proceso</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Monto Sometido</th>
                        <th scope="col" class="px-6 py-3.5 text-center">Etapa Actual</th>
                        <th scope="col" class="px-6 py-3.5 text-center w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($claimsWithAging as $claim)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-block w-3.5 h-3.5 rounded-full border border-white shadow-xs
                                    {{ $claim->aging->color_class === 'emerald' ? 'bg-emerald-500' : 
                                       ($claim->aging->color_class === 'amber' ? 'bg-amber-500' : 
                                       ($claim->aging->color_class === 'orange' ? 'bg-orange-500' : 
                                       ($claim->aging->color_class === 'rose' ? 'bg-rose-500' : 'bg-slate-450'))) }}">
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-extrabold text-slate-800 font-mono block">{{ $claim->claim_number }}</span>
                                <span class="text-slate-400 font-medium block mt-0.5">NCF: {{ $claim->ncf }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-semibold text-slate-700 block">{{ $claim->pss->nombre }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Entrada: {{ \Carbon\Carbon::parse($claim->aging->official_entry_date)->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold font-mono">
                                <span class="text-xs text-slate-800 block">{{ $claim->aging->current_age_days }} días</span>
                                @if($claim->aging->is_overdue)
                                    <span class="text-[8px] font-black uppercase text-rose-600 block mt-0.5 tracking-wide">Vencido</span>
                                @else
                                    <span class="text-[8px] font-bold text-slate-400 block mt-0.5">{{ $claim->aging->days_remaining }} días restantes</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold">
                                <span class="text-slate-900 block">DOP {{ number_format($claim->claimed_amount, 2) }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Aut.: DOP {{ number_format($claim->authorized_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider border
                                    {{ $claim->status === 'Pagada' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                       ($claim->status === 'Devuelta por documentos' ? 'bg-orange-50 text-orange-700 border-orange-200' : 
                                       ($claim->status === 'En auditoría de reclamación' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-600 border-slate-200')) }}">
                                    {{ $claim->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <a href="{{ route('ars.reclamaciones.show', $claim->id) }}" class="text-teal-600 hover:text-teal-800 font-bold border border-teal-200 hover:border-teal-400 px-3 py-1.5 rounded-full transition text-[10px] shadow-2xs bg-white">
                                    Auditar / Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-450 font-semibold">
                                No se encontraron registros de reclamaciones activas en el core.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
