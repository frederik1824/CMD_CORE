@extends('layouts.ars')
@section('title', 'Emisión de Facturas')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Facturación General</h2>
            <p class="text-xs text-slate-500 font-medium">Gestión y emisión de facturas para afiliados y empleadores (NCF).</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Facturas Emitidas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">No. Factura</th>
                            <th class="px-4 py-3 text-left">Plan / Grupo</th>
                            <th class="px-4 py-3 text-left font-mono">Comprobante NCF</th>
                            <th class="px-4 py-3 text-right">Monto Facturado</th>
                            <th class="px-4 py-3 text-center">Fecha Emisión</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($invoices as $inv)
                            <tr class="hover:bg-slate-50/55 transition">
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-3 text-slate-700">
                                    @if($inv->plan)
                                        <span class="font-semibold block">Plan: {{ $inv->plan->name }}</span>
                                    @elseif($inv->group)
                                        <span class="font-semibold block">Grupo: {{ $inv->group->name }}</span>
                                    @else
                                        <span class="text-slate-400 font-semibold block">Suscripción Core</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-slate-650">{{ $inv->ncf ?? 'No asignado' }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">DOP {{ number_format($inv->amount, 2) }}</td>
                                <td class="px-4 py-3 text-center font-mono text-slate-450">{{ $inv->issued_at }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">{{ $inv->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 font-sans">Emitir Nueva Factura</h3>
            <form action="{{ route('ars.facturacion.emitir') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Plan de Salud Asociado (Opcional)</label>
                    <select name="health_plan_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none">
                        <option value="">-- Seleccionar Plan --</option>
                        @foreach($planes as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Grupo Corporativo (Opcional)</label>
                    <select name="affiliate_group_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none">
                        <option value="">-- Seleccionar Grupo --</option>
                        @foreach($grupos as $gr)
                            <option value="{{ $gr->id }}">{{ $gr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Monto a Facturar (DOP)</label>
                    <input type="number" step="0.01" name="amount" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. 15000.00" required>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Generar Factura (NCF)</button>
            </form>
        </div>
    </div>
</div>
@endsection