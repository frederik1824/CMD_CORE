@extends('layouts.pss')

@section('title', 'Cuentas y Pagos Recibidos')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 pb-4 border-b border-slate-100">
        <div class="bg-gradient-to-tr from-teal-600 to-teal-400 text-white p-2 rounded-xl shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="leading-none flex-1">
            <h2 class="text-base font-bold text-slate-800">Cuentas por Pagar y Liquidación de Pagos</h2>
            <p class="text-[11px] text-slate-400 font-medium">Bandeja de verificación de facturas auditadas, liberación de pagos y comprobantes bancarios.</p>
        </div>
        <a href="{{ route('pss.dashboard') }}" class="text-slate-500 hover:text-slate-700 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition">
            Volver al panel
        </a>
    </div>

    <!-- Tabla Cuentas / Pagos -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">No. Cuenta CXP</th>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">Factura / Reclamación</th>
                        <th class="px-6 py-3.5 text-right text-[9px] uppercase tracking-wider">Monto Bruto</th>
                        <th class="px-6 py-3.5 text-right text-[9px] uppercase tracking-wider">Objeciones</th>
                        <th class="px-6 py-3.5 text-right text-[9px] uppercase tracking-wider">Monto Neto Autorizado</th>
                        <th class="px-6 py-3.5 text-left text-[9px] uppercase tracking-wider">Fecha Estimada</th>
                        <th class="px-6 py-3.5 text-center text-[9px] uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                    @forelse($cxpList as $ap)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3 font-mono font-bold text-[#0f766e]">
                                {{ $ap->payable_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Aut: {{ $ap->authorization->numero_autorizacion }}</span>
                            </td>
                            <td class="px-6 py-3">
                                {{ $ap->claim->invoice_number }}
                                <span class="block text-[10px] text-slate-400 font-normal">Recl: {{ $ap->claim->claim_number }}</span>
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-slate-400 font-mono">
                                DOP {{ number_format($ap->amount, 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-rose-500 font-mono">
                                DOP {{ number_format($ap->retained_amount, 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-extrabold text-[#0f766e] font-mono bg-[#eaf1fb]/10">
                                DOP {{ number_format($ap->net_amount, 2) }}
                            </td>
                            <td class="px-6 py-3 font-semibold text-slate-500">
                                {{ $ap->due_date ? $ap->due_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold tracking-wider
                                    {{ $ap->status === 'Pagada' || $ap->status === 'Conciliada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : 
                                       ($ap->status === 'En lote de pago' ? 'bg-blue-50 text-blue-700 border border-blue-150' : 'bg-amber-50 text-amber-700 border border-amber-150') }}">
                                    {{ $ap->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400 font-medium">
                                No se registran pagos liquidados o cuentas por pagar emitidas para esta prestadora.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $cxpList->links() }}
        </div>
    </div>
</div>
@endsection
