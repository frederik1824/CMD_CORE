@extends('layouts.core')

@section('title', 'Libro Mayor y Auxiliar')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    📖 Mayorización
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Libro Mayor Auxiliar</h2>
            <p class="text-xs text-slate-500 font-medium">Consulte los movimientos históricos y el saldo final acumulado de una cuenta contable específica.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.contabilidad.dashboard') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Buscador y Filtros -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('ars.contabilidad.mayor') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Seleccionar Cuenta Contable</label>
                <select name="account_id" onchange="this.form.submit()" required class="w-full rounded-2xl border-slate-200 text-xs text-slate-600 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all">
                    <option value="">-- Seleccione una cuenta posteable --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->nature) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Período Contable</label>
                <select name="period" onchange="this.form.submit()" class="w-full rounded-2xl border-slate-200 text-xs text-slate-600 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all">
                    @foreach($periodos as $per)
                        <option value="{{ $per->period_code }}" {{ $period == $per->period_code ? 'selected' : '' }}>
                            {{ $per->period_code }} ({{ $per->is_closed ? 'Cerrado' : 'Abierto' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#0b57d0] text-white text-xs font-bold py-3 rounded-2xl hover:bg-[#083d91] transition shadow-sm">
                    Consultar Movimientos
                </button>
            </div>
        </form>
    </div>

    <!-- Resultados -->
    @if($selectedAccount)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden space-y-4 p-6">
            <!-- Resumen Saldos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-slate-100 pb-5">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Inicial Período</span>
                    <p class="text-base font-black text-slate-700 font-mono mt-1">DOP {{ number_format($saldoInicial, 2) }}</p>
                </div>
                <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-50 text-center">
                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Movimientos Netos</span>
                    @php
                        $movNetos = $lines->sum('debit') - $lines->sum('credit');
                        if ($selectedAccount->nature === 'credito') {
                            $movNetos = $lines->sum('credit') - $lines->sum('debit');
                        }
                    @endphp
                    <p class="text-base font-black text-[#0b57d0] font-mono mt-1">DOP {{ number_format($movNetos, 2) }}</p>
                </div>
                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 text-center font-bold">
                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Saldo Final Acumulado</span>
                    <p class="text-base font-black text-emerald-700 font-mono mt-1">DOP {{ number_format($saldoFinal, 2) }}</p>
                </div>
            </div>

            <!-- Tabla de Detalle -->
            <div class="border border-slate-100 rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Asiento</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Módulo / Origen</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Concepto</th>
                            <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Débito</th>
                            <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Crédito</th>
                            <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Saldo Acum.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @php
                            $saldoTemp = $saldoInicial;
                        @endphp
                        <tr class="bg-slate-50/30">
                            <td colspan="4" class="px-6 py-2 text-xs font-bold text-slate-500 italic">
                                Saldo anterior de apertura...
                            </td>
                            <td colspan="2" class="px-6 py-2 text-right"></td>
                            <td class="px-6 py-2 text-right font-mono font-bold text-slate-700">
                                DOP {{ number_format($saldoInicial, 2) }}
                            </td>
                        </tr>
                        @forelse($lines as $line)
                            @php
                                if ($selectedAccount->nature === 'debito') {
                                    $saldoTemp += ($line->debit - $line->credit);
                                } else {
                                    $saldoTemp += ($line->credit - $line->debit);
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-500 font-mono">
                                    {{ $line->entry->entry_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-[#0b57d0] font-bold whitespace-nowrap">
                                    <a href="{{ route('ars.contabilidad.asiento_show', $line->entry->id) }}" class="hover:underline">
                                        {{ $line->entry->entry_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3.5 text-slate-400 font-bold whitespace-nowrap font-mono">
                                    {{ ucfirst($line->entry->source_module) }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-600 font-semibold max-w-[200px] truncate" title="{{ $line->description }}">
                                    {{ $line->description }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-800">
                                    {{ $line->debit > 0 ? ('DOP ' . number_format($line->debit, 2)) : '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-800">
                                    {{ $line->credit > 0 ? ('DOP ' . number_format($line->credit, 2)) : '—' }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-700">
                                    DOP {{ number_format($saldoTemp, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-slate-400 font-medium">
                                    No se registran movimientos en el período seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center min-h-[300px] bg-white border border-slate-100 rounded-3xl text-center py-12">
            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">auto_stories</span>
            <p class="text-slate-500 font-bold text-sm">Libro Mayor Auxiliar</p>
            <p class="text-slate-300 text-xs mt-1 max-w-sm">Seleccione una cuenta contable en los filtros superiores para ver los movimientos correspondientes.</p>
        </div>
    @endif
</div>
@endsection
