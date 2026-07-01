@extends('layouts.ars')

@section('title', 'Historial de Transacciones')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Historial de Transacciones de Afiliados</h2>
            <p class="text-xs text-slate-500 font-medium">Registro de auditoría transaccional de afiliaciones, egresos, traspasos y cambios de estatus.</p>
        </div>
    </div>

    <!-- Listado de Transacciones -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Auditoría Transaccional</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha / Hora</th>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left">Tipo Acción</th>
                        <th class="px-4 py-3 text-left">Concepto / Motivo</th>
                        <th class="px-4 py-3 text-left">Responsable</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($transacciones as $t)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-mono text-slate-500 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-850 block">{{ $t->affiliate?->nombre_completo ?? 'N/A' }}</span>
                                <span class="text-[10px] text-slate-450 font-mono">Céd: {{ $t->affiliate?->cedula ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                    {{ $t->transaction_type === 'traspaso' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-250' }}">
                                    {{ ucfirst($t->transaction_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 leading-relaxed">{{ $t->concept }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $t->user?->name ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se registran transacciones operativas en el log de afiliaciones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
