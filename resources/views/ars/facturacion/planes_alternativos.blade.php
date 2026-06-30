@extends('layouts.ars')
@section('title', 'Facturación Planes Alternativos')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Facturación de Planes Voluntarios y Alternativos</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de facturas asociadas a afiliados con planes independientes.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Histórico de Planes Alternativos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">No. Factura</th>
                        <th class="px-4 py-3 text-left">Plan de Salud</th>
                        <th class="px-4 py-3 text-left font-mono">Comprobante NCF</th>
                        <th class="px-4 py-3 text-right">Monto Facturado</th>
                        <th class="px-4 py-3 text-center">Fecha Emisión</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $inv->invoice_number }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $inv->plan->name }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-650">{{ $inv->ncf }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">DOP {{ number_format($inv->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center font-mono text-slate-450">{{ $inv->issued_at }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">{{ $inv->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400 font-semibold">No hay facturas registradas para planes alternativos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection