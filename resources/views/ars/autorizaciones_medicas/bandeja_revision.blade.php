@extends('layouts.ars')

@section('title', 'Bandeja de Revisión Administrativa')

@section('content')
<div class="space-y-6">
    <!-- Encabezado de la Bandeja -->
    <div class="pb-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Bandeja de Revisión Administrativa</h2>
            <p class="text-xs text-slate-400 font-medium">Casos desviados del flujo automático por discrepancias en tarifas contractuales o estatus de prestadora.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-[10px] font-black text-amber-700 bg-amber-50 border border-amber-250 px-3 py-1 rounded-full uppercase tracking-wider font-mono flex items-center space-x-1.5 animate-pulse">
                <span>Revisión Convenios</span>
            </span>
        </div>
    </div>

    <!-- Lista de Revisión -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden text-xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">No. Autorización</th>
                        <th scope="col" class="px-6 py-4 text-left">Afiliado</th>
                        <th scope="col" class="px-6 py-4 text-left">Prestador PSS</th>
                        <th scope="col" class="px-6 py-4 text-left">Alerta de Convenio</th>
                        <th scope="col" class="px-6 py-4 text-right">Solicitado</th>
                        <th scope="col" class="px-6 py-4 text-right">Tarifa Pactada</th>
                        <th scope="col" class="px-6 py-4 text-center w-24">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                    @forelse($autorizaciones as $a)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <!-- No. Caso -->
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-slate-800 font-mono block text-xs">{{ $a->numero_autorizacion }}</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-0.5 block">{{ $a->channel ?? 'llamada' }}</span>
                            </td>
                            <!-- Afiliado -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block text-[11px]">{{ $a->afiliado->nombres }} {{ $a->afiliado->primer_apellido }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5 font-semibold">Cédula: {{ $a->afiliado->cedula }}</span>
                            </td>
                            <!-- Prestador -->
                            <td class="px-6 py-4 font-bold text-slate-700">
                                {{ $a->pss->nombre }}
                            </td>
                            <!-- Alerta de Convenio -->
                            <td class="px-6 py-4">
                                @if(!$a->pss_contract_id)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[8px] font-black bg-rose-50 text-rose-700 border border-rose-250 uppercase tracking-wide">
                                        🔴 PSS Sin Contrato Vigente
                                    </span>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[8px] font-black bg-amber-50 text-amber-700 border border-amber-250 uppercase tracking-wide">
                                            🟡 Monto Excede Tarifa
                                        </span>
                                        <span class="text-[10px] text-rose-600 block font-bold font-mono">
                                            + DOP {{ number_format($a->monto_solicitado - $a->contracted_amount_snapshot, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <!-- Monto Solicitado -->
                            <td class="px-6 py-4 text-right font-bold text-slate-900 font-mono text-xs">
                                DOP {{ number_format($a->monto_solicitado, 2) }}
                            </td>
                            <!-- Tarifa Pactada -->
                            <td class="px-6 py-4 text-right font-bold text-slate-450 font-mono text-xs">
                                DOP {{ number_format($a->contracted_amount_snapshot ?? 0.00, 2) }}
                            </td>
                            <!-- Acción -->
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('ars.autorizaciones_medicas.show', $a->id) }}" 
                                   class="text-amber-700 hover:text-amber-900 font-bold border border-amber-150 hover:border-amber-300 px-3.5 py-1.5 rounded-full bg-white transition shadow-2xs text-[10px] inline-block">
                                    Revisar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-semibold italic">
                                No se registran discrepancias o desvíos en la bandeja de revisión administrativa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($autorizaciones->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $autorizaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
