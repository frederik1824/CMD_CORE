@extends('layouts.ars')

@section('title', 'Radicaciones y Aging')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Radicaciones y Aging</h2>
            <p class="text-xs text-slate-500 font-medium">Mesa de entrada y bandeja de radicaciones agrupadas por aging.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <!-- Aging KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">1 - 30 Días</span>
            <span class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $agingData['1_30'] ?? 0 }}</span>
            <span class="text-[9px] text-emerald-600 font-bold block mt-1">Tiempo óptimo</span>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">31 - 60 Días</span>
            <span class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $agingData['31_60'] ?? 0 }}</span>
            <span class="text-[9px] text-amber-600 font-bold block mt-1">Revisión requerida</span>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">61 - 90 Días</span>
            <span class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $agingData['61_90'] ?? 0 }}</span>
            <span class="text-[9px] text-rose-600 font-bold block mt-1">Prioridad de pago</span>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Más de 90 Días</span>
            <span class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $agingData['90_plus'] ?? 0 }}</span>
            <span class="text-[9px] text-slate-500 font-bold block mt-1">En disputa/glosado</span>
        </div>
    </div>

    <!-- Listado Radicaciones -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Radicaciones Recibidas</h3>
            <form action="{{ route('ars.reclamaciones.radicaciones') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por claim, PSS..." class="rounded-full border border-slate-200 bg-slate-50/50 px-4 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-200">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-4 py-1.5 font-bold">Buscar</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-150">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-left">No. Reclamación</th>
                        <th class="px-6 py-4 text-left">PSS</th>
                        <th class="px-6 py-4 text-right">Monto Reclamado</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Fecha Radicación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($radicaciones as $rad)
                        <tr class="hover:bg-slate-50/30 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-900">{{ $rad->claim_number }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ $rad->pss->nombre }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">DOP {{ number_format($rad->claimed_amount, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">{{ $rad->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-450 font-mono">{{ $rad->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-semibold">No se encontraron radicaciones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($radicaciones, 'links'))
        <div class="px-6 py-3 border-t border-slate-100">
            {{ $radicaciones->appends(request()->query())->links() }}
        </div>
        @endif
    </div>


</div>
@endsection
