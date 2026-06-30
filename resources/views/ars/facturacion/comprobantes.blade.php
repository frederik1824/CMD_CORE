@extends('layouts.ars')
@section('title', 'Comprobantes Fiscales NCF')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Comprobantes Fiscales (NCF)</h2>
            <p class="text-xs text-slate-500 font-medium">Log de asignaciones de NCF para control fiscal de la DGII.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Control de NCF Emitidos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 font-mono text-[11px]">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left font-sans">No. Factura</th>
                        <th class="px-4 py-3 text-left">Código NCF</th>
                        <th class="px-4 py-3 text-right">Monto Transacción</th>
                        <th class="px-4 py-3 text-center font-sans">Fecha Emisión</th>
                        <th class="px-4 py-3 text-center font-sans">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="px-4 py-3 font-bold text-blue-900 font-sans">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 font-bold text-slate-800">{{ $inv->ncf }}</td>
                            <td class="px-4 py-3 text-right text-slate-900">DOP {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center text-slate-450">{{ $inv->issued_at }}</td>
                            <td class="px-4 py-3 text-center font-sans">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">Asignado</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400 font-semibold font-sans">No hay comprobantes fiscales asignados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection