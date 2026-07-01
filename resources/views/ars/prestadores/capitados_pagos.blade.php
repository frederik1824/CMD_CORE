@extends('layouts.ars')

@section('title', 'Liquidación de Cápitas')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Liquidación de Cápitas Mensuales</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de pagos y transferencias de cápitas mensuales liquidadas para PSS capitadas.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Pago Capitado -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Liquidar Pago de Cápita</h3>
            <form action="{{ route('ars.prestadores.guardar_capitado_pago') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Contrato Capitado Activo <span class="text-rose-500">*</span></label>
                    <select name="capitated_contract_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($contratos as $c)
                            <option value="{{ $c->id }}">{{ $c->pss?->nombre }} - {{ $c->contract_number }} (Mensual: DOP {{ number_format($c->total_monthly_amount, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período (Mes/Año) <span class="text-rose-500">*</span></label>
                    <input type="month" name="period" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Referencia de Pago (Transferencia / Cheque)</label>
                    <input type="text" name="payment_reference" placeholder="Ej. TRANSF-982828" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs font-mono">
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Confirmar y Liquidar Pago</button>
            </form>
        </div>

        <!-- Listado de Pagos de Cápitas -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Liquidaciones de Cápitas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Prestador (PSS)</th>
                            <th class="px-4 py-3 text-left">Contrato / Período</th>
                            <th class="px-4 py-3 text-mono text-right">Monto Liquidado</th>
                            <th class="px-4 py-3 text-left">Fecha Liquidación</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($pagos as $p)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49]">{{ $p->contract?->pss?->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-slate-600">
                                    <span class="block font-mono text-[10px] text-slate-450">{{ $p->contract?->contract_number }}</span>
                                    <span class="block">Período: {{ $p->period }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">DOP {{ number_format($p->amount_paid, 2) }}</td>
                                <td class="px-4 py-3 text-slate-500 font-mono">{{ $p->paid_at }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado liquidaciones de cápitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
