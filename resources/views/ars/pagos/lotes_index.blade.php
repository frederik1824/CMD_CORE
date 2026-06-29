@extends('layouts.core')

@section('title', 'Lotes de Pago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lotes de Pago Consolidados</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de programación, dispersión de fondos y conciliación extractaria de lotes bancarios.</p>
        </div>
        <a href="{{ route('ars.pagos.cxp') }}" class="bg-[#041e49] text-white rounded-full px-4 py-2.5 text-xs font-bold hover:bg-slate-800 transition shadow-xs flex items-center space-x-1.5">
            <span>Nueva Consolidación CXP</span>
        </a>
    </div>

    <!-- Tabla Lotes -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">No. Lote</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">Creado Por</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Cantidad CXP</th>
                        <th class="px-6 py-4 text-right text-[9px] uppercase tracking-wider">Monto Total</th>
                        <th class="px-6 py-4 text-left text-[9px] uppercase tracking-wider">Fecha Programada</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-[9px] uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-[#041e49]">
                                {{ $lote->batch_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Creado: {{ $lote->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $lote->creator->name ?? 'Sistema' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-600 font-mono">
                                {{ $lote->total_items }}
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-[#041e49] font-mono">
                                DOP {{ number_format($lote->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-600">
                                {{ $lote->scheduled_payment_date ? $lote->scheduled_payment_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold tracking-wider
                                    {{ $lote->status === 'Borrador' ? 'bg-slate-50 text-slate-600 border border-slate-200' : 
                                       ($lote->status === 'Programado' ? 'bg-amber-50 text-amber-700 border border-amber-250' : 
                                       ($lote->status === 'Pagado' ? 'bg-blue-50 text-blue-700 border border-blue-250' : 
                                       ($lote->status === 'Conciliado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 'bg-rose-50 text-rose-700 border border-rose-250'))) }}">
                                    {{ $lote->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('ars.lotes.show', $lote->id) }}" 
                                   class="text-[#041e49] hover:text-blue-900 font-bold border border-slate-250 rounded-full px-3.5 py-1.5 hover:bg-slate-50 transition">
                                    Ver Lote
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se encontraron lotes de pago consolidados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $lotes->links() }}
        </div>
    </div>
</div>
@endsection
