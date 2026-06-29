@extends('layouts.core')

@section('title', 'Detalle Lote de Pago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lote: {{ $lote->batch_number }}</h2>
            <p class="text-xs text-slate-500 font-medium">Consolidación de cuentas a pagar. Estado: <strong class="text-blue-900">{{ $lote->status }}</strong></p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('ars.lotes.index') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
                Volver a la bandeja
            </a>
        </div>
    </div>

    <!-- Grid de Info y Acciones -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lista de CXPs (Izquierda 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Listado de Facturas Incluidas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-4.5 h-4.5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Cuentas por Pagar Consolidadas ({{ $lote->total_items }} ítems)</span>
                </h3>

                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 font-bold text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Obligación (CXP)</th>
                                <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Prestadora PSS</th>
                                <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">No. Factura / Recl</th>
                                <th class="px-4 py-3 text-right text-[9px] uppercase tracking-wider">Monto Neto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                            @foreach($lote->items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-mono font-bold text-[#041e49]">
                                        {{ $item->payable->payable_number }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $item->payable->pss->nombre }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-500">
                                        {{ $item->payable->claim->invoice_number }}
                                        <span class="block text-[10px] text-slate-400 font-normal">Recl: {{ $item->payable->claim->claim_number }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-extrabold text-slate-900 font-mono">
                                        DOP {{ number_format($item->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50/50 font-bold text-slate-800">
                                <td colspan="3" class="px-4 py-3 text-[10px] uppercase">Monto Total del Lote</td>
                                <td class="px-4 py-3 text-right font-mono text-[#041e49]">DOP {{ number_format($lote->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Datos de Conciliación Bancaria (si aplica) -->
            @if($lote->status === 'Conciliado')
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                        <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Detalles de Conciliación Bancaria</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs border border-slate-100 p-4 rounded-2xl bg-emerald-50/20">
                        @php
                            $rec = $lote->reconciliations->first();
                        @endphp
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Referencia Bancaria</span>
                            <span class="font-bold text-slate-800 block mt-0.5">{{ $rec->bank_reference ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fecha de Conciliación</span>
                            <span class="font-semibold text-slate-600 block mt-0.5">{{ $rec->payment_date ? $rec->payment_date->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Resultado Conciliación</span>
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-bold text-emerald-800 mt-1 border border-emerald-200">
                                {{ $rec->status ?? 'Conciliado' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel de Operaciones (Derecha 1/3) -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-4.5 h-4.5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>Panel de Control Operativo</span>
                </h3>

                <div class="text-xs space-y-3">
                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                        <span class="text-slate-400">Total Items:</span>
                        <span class="font-bold text-slate-800">{{ $lote->total_items }} cuentas</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                        <span class="text-slate-400">Monto Lote:</span>
                        <span class="font-bold text-slate-800">DOP {{ number_format($lote->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                        <span class="text-slate-400">Fecha Estimada:</span>
                        <span class="font-semibold text-slate-700">{{ $lote->scheduled_payment_date ? $lote->scheduled_payment_date->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Botones y Formularios de Estados -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    @if($lote->status === 'Borrador')
                        <!-- Aprobar Lote -->
                        <form action="{{ route('ars.lotes.aprobar', $lote->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-xs">
                                Aprobar y Programar Lote
                            </button>
                        </form>
                    @endif

                    @if($lote->status === 'Programado')
                        <!-- Pagar Lote -->
                        <form action="{{ route('ars.lotes.pagar', $lote->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white rounded-full py-2.5 font-bold hover:bg-blue-700 transition shadow-xs text-xs">
                                Procesar Desembolso Bancario
                            </button>
                        </form>
                    @endif

                    @if($lote->status === 'Pagado')
                        <!-- Conciliar Lote -->
                        <form action="{{ route('ars.lotes.conciliar', $lote->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-500 mb-1 uppercase tracking-wider text-[9px]">No. Referencia Extracto Bancario</label>
                                <input type="text" name="bank_reference" required 
                                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-200 transition-all"
                                       placeholder="Ej: REF-009893921">
                            </div>
                            <button type="submit" class="w-full bg-teal-600 text-white rounded-full py-2.5 font-bold hover:bg-teal-700 transition shadow-xs text-xs">
                                Conciliar contra Banco & Cerrar Lote
                            </button>
                        </form>
                    @endif

                    @if($lote->status === 'Conciliado')
                        <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-2 text-xs text-emerald-850">
                            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Lote cerrado. Todas las facturas y obligaciones han sido saldadas y conciliadas correctamente.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
