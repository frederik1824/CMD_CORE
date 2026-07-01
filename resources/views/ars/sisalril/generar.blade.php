@extends('layouts.ars')

@section('title', 'Generar Esquemas')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consola de Generación Regulatoria</h2>
            <p class="text-xs text-slate-500 font-medium">Extraiga, valide y estructure los datos operativos en archivos planos oficiales para envío a SIMON.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel de Generación -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Generar Nuevo Archivo</h3>
            <form action="{{ route('sisalril.generar_procesar') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Esquema SISALRIL <span class="text-rose-500">*</span></label>
                    <select name="regulatory_schema_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($esquemas as $es)
                            <option value="{{ $es->id }}">Esquema {{ $es->schema_code }} - {{ $es->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Período de Reporte <span class="text-rose-500">*</span></label>
                    <select name="period_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($periodos as $per)
                            <option value="{{ $per->id }}">{{ $per->period_code }} ({{ $per->status }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">
                    Generar y Validar Estructura
                </button>
            </form>
        </div>

        <!-- Historial de Últimas Corridas -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Últimas Corridas Generadas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Corrida / Archivo</th>
                            <th class="px-4 py-3 text-left">Esquema</th>
                            <th class="px-4 py-3 text-right">Registros (V / E)</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($corridas as $corr)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-900 block font-mono text-[10px]">{{ $corr->run_number }}</span>
                                    <span class="text-[9px] text-slate-450 block font-normal">{{ $corr->file_name }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="block text-slate-700">Esquema {{ $corr->schema?->schema_code }}</span>
                                    <span class="block text-[9px] font-normal text-slate-400">Período: {{ $corr->period?->period_code }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="block font-mono text-emerald-700 font-bold">{{ $corr->valid_records }}</span>
                                    <span class="block font-mono text-rose-600">{{ $corr->invalid_records }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($corr->status === 'aprobado')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[8px] font-bold text-emerald-700 border border-emerald-250">Aprobado</span>
                                    @elseif($corr->status === 'con_errores')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[8px] font-bold text-rose-700 border border-rose-220">Con Errores</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[8px] font-bold text-blue-700 border border-blue-200">Generado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sisalril.show', $corr->schema?->schema_code) }}" class="text-[#041e49] font-bold hover:underline">Ver</a>
                                        @if($corr->status !== 'aprobado' && !$corr->submission)
                                            <form action="{{ route('sisalril.enviar_simon', $corr->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 text-white rounded-full px-2.5 py-1 font-bold hover:bg-emerald-700 transition text-[9px]">
                                                    Enviar SIMON
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se registran corridas regulatorias en la consola.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
