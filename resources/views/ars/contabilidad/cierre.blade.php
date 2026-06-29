@extends('layouts.core')

@section('title', 'Cierre Contable Mensual')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider">
                    🔒 Control de Períodos
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Cierre de Período Contable</h2>
            <p class="text-xs text-slate-500 font-medium">Bloquee transacciones operativas y de diario en meses específicos para auditoría contable final.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.contabilidad.dashboard') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Panel Izquierdo: Ejecución de Cierre (2/5) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                Ejecutar Cierre Contable
            </h3>

            <form action="{{ route('ars.contabilidad.ejecutar_cierre') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5">Seleccionar Período Abierto</label>
                    <select name="period_code" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                        <option value="">-- Seleccionar período a cerrar --</option>
                        @foreach($periodos->where('is_closed', false) as $p)
                            <option value="{{ $p->period_code }}">{{ $p->period_code }} (Abierto)</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl text-[10px] text-amber-900 border border-amber-200 leading-relaxed font-medium space-y-2">
                    <p class="font-bold">⚠️ ADVERTENCIA DE CIERRE:</p>
                    <p>Al cerrar un período contable:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Se validará que no existan asientos en estado "Borrador".</li>
                        <li>Se bloqueará la inserción de nuevos asientos para este período.</li>
                        <li>Las autorizaciones, reclamaciones y reembolsos con fecha del período cerrado quedarán bloqueadas.</li>
                    </ul>
                </div>

                <button type="submit" class="w-full bg-[#ba1a1a] text-white rounded-full py-2.5 font-bold hover:bg-[#93000a] transition shadow-xs text-center block flex items-center justify-center space-x-2">
                    <span class="material-symbols-outlined text-sm">lock</span>
                    <span>Cerrar Período Contable</span>
                </button>
            </form>
        </div>

        <!-- Panel Derecho: Historial de Períodos (3/5) -->
        <div class="lg:col-span-3 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                Historial de Períodos Contables
            </h3>

            <div class="border border-slate-100 rounded-2xl overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Período</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha Inicio / Fin</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha Cierre</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @foreach($periodos as $per)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 font-mono text-slate-700 whitespace-nowrap font-bold">
                                    {{ $per->period_code }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 font-mono">
                                    {{ $per->start_date->format('d/m/Y') }} al {{ $per->end_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if($per->is_closed)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                            🔒 Cerrado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 animate-pulse">
                                            🔓 Abierto
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-slate-400 font-medium whitespace-nowrap">
                                    {{ $per->closed_at ? $per->closed_at->format('d/m/Y H:i') : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
