@extends('layouts.ars')
@section('title', 'Ajustes de Inventario')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Ajustes de Inventario</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de auditoría de mermas, plásticos dañados y conteos físicos.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800">Ajustes Registrados</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Insumo</th>
                        <th class="px-4 py-3 text-left">Centro Impresión</th>
                        <th class="px-4 py-3 text-left">Tipo Ajuste</th>
                        <th class="px-4 py-3 text-right">Cantidad</th>
                        <th class="px-4 py-3 text-left">Razón / Justificación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($ajustes as $aj)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $aj->supply->name }}</td>
                            <td class="px-4 py-3">{{ $aj->printingCenter->name }}</td>
                            <td class="px-4 py-3 text-blue-900 capitalize">{{ $aj->adjustment_type }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rose-500">{{ $aj->quantity }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $aj->reason }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se registran mermas ni ajustes extraordinarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection