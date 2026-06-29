@extends('layouts.core')

@section('title', 'Detalle de Asiento Contable')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    📄 Transacción # {{ $entry->entry_number }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Comprobante de Diario</h2>
            <p class="text-xs text-slate-500 font-medium">Asiento contable por partida doble e imputaciones a auxiliares y terceros.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.contabilidad.asientos') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Regresar a Diario
            </a>
        </div>
    </div>

    <!-- Info General -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Fecha de Asiento</span>
            <span class="font-mono font-bold text-slate-700">{{ $entry->entry_date->format('d/m/Y') }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Período Contable</span>
            <span class="font-mono font-bold text-slate-700">{{ $entry->period }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Diario Asiento</span>
            <span class="font-mono font-bold text-slate-700">{{ $entry->journal->name }} ({{ $entry->journal->code }})</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Módulo Origen</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                {{ ucfirst($entry->source_module) }}
            </span>
        </div>
        <div class="md:col-span-4 border-t border-slate-50 pt-4">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Concepto / Descripción General</span>
            <p class="text-sm font-semibold text-slate-700 leading-relaxed">{{ $entry->description }}</p>
        </div>
    </div>

    <!-- Partida Doble -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cuentas Afectadas (Débitos & Créditos)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Código Cuenta</th>
                        <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nombre Cuenta</th>
                        <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tercero / Auxiliar</th>
                        <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Débito (Debe)</th>
                        <th class="px-6 py-3 text-right text-[9px] font-bold text-slate-400 uppercase tracking-wider">Crédito (Haber)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($entry->lines as $line)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5 font-mono text-slate-600 whitespace-nowrap font-bold">
                                {{ $line->account->code }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-700 font-semibold">
                                {{ $line->account->name }}
                            </td>
                            <td class="px-6 py-3.5 text-slate-400 whitespace-nowrap font-medium">
                                @if($line->third_party_type)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-slate-50 border border-slate-150 text-slate-500 font-bold uppercase font-mono">
                                        👤 {{ $line->third_party_type }}: #{{ $line->third_party_id }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-800 text-sm">
                                {{ $line->debit > 0 ? ('DOP ' . number_format($line->debit, 2)) : '—' }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-slate-800 text-sm">
                                {{ $line->credit > 0 ? ('DOP ' . number_format($line->credit, 2)) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/80 font-mono font-black border-t border-slate-150">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-slate-500 uppercase tracking-wider text-[10px] font-bold">
                            Total General Cuadrado
                        </td>
                        <td class="px-6 py-4 text-right text-slate-900 text-sm">
                            DOP {{ number_format($entry->total_debit, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-slate-900 text-sm">
                            DOP {{ number_format($entry->total_credit, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
