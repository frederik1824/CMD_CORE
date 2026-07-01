@extends('layouts.ars')

@section('title', 'Reportes Carnetización')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reporte e Indicadores de Carnetización</h2>
            <p class="text-xs text-slate-500 font-medium">Estadísticas operativas, censo de carnets impresos, despachados y mermas registradas por sucursal.</p>
        </div>
    </div>

    <!-- Indicadores Generales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Carnets Impresos</span>
                <span class="text-2xl font-black text-[#041e49]">{{ \App\Models\CarnetRequest::where('status', 'Impreso')->count() }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-blue-500 bg-blue-50 p-2.5 rounded-2xl">credit_card</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Entregas Completadas</span>
                <span class="text-2xl font-black text-emerald-600">{{ \App\Models\CarnetRequest::where('status', 'Entregado')->count() }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-emerald-500 bg-emerald-50 p-2.5 rounded-2xl">check_circle</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Mermas (Malogrados)</span>
                <span class="text-2xl font-black text-rose-600">{{ abs(\App\Models\CarnetAdjustment::where('adjustment_type', 'Merma')->sum('quantity')) }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-rose-500 bg-rose-50 p-2.5 rounded-2xl">delete_forever</span>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs flex items-center justify-between">
            <div>
                <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px] mb-1">Insumo Plásticos Disp.</span>
                <span class="text-2xl font-black text-purple-600">{{ \App\Models\PrintingSupply::where('supply_family', 'plastico')->sum('current_stock') }}</span>
            </div>
            <span class="material-symbols-outlined text-3xl text-purple-500 bg-purple-50 p-2.5 rounded-2xl">layers</span>
        </div>
    </div>

    <!-- Centros y Suministros -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Inventario y Distribución de Insumos por Sucursal</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Insumo / Suministro</th>
                        <th class="px-4 py-3 text-left">Familia</th>
                        <th class="px-4 py-3 text-mono text-center">Stock Inicial</th>
                        <th class="px-4 py-3 text-mono text-center">Stock Actual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse(\App\Models\PrintingSupply::all() as $s)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-bold text-[#041e49]">{{ $s->name }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 uppercase tracking-wider text-[9px]">{{ $s->supply_family }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $s->initial_stock }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-[#041e49]">{{ $s->current_stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No hay insumos registrados en el inventario.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
