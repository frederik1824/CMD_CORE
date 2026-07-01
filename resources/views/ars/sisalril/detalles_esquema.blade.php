@extends('layouts.ars')

@section('title', 'Historial de Esquema')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Esquema {{ $esquema->schema_code }} - {{ $esquema->name }}</h2>
            <p class="text-xs text-slate-500 font-medium">Historial de generaciones, estructura de campos obligatorios y bitácora técnica de auditoría.</p>
        </div>
        <div>
            <a href="{{ route('sisalril.generar') }}" class="bg-[#041e49] text-white px-4 py-2 rounded-full font-bold hover:bg-slate-800 transition text-xs flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">replay</span>
                Nueva Generación
            </a>
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
        <!-- Campos y Anchos de Ancho Fijo -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Especificación Técnica de Columnas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-2 py-2 text-left">Campo</th>
                            <th class="px-2 py-2 text-center">Tipo</th>
                            <th class="px-2 py-2 text-center">Rango</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($esquema->fields as $field)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-2 py-2">
                                    <span class="font-bold block text-slate-800 text-[10px]">{{ $field->field_label }}</span>
                                    <span class="text-[9px] text-slate-400 font-mono block">{{ $field->field_name }}</span>
                                </td>
                                <td class="px-2 py-2 text-center font-mono text-[9px]">{{ $field->data_type }}({{ $field->length }})</td>
                                <td class="px-2 py-2 text-center font-mono text-[9px] text-slate-500">{{ $field->start_position }}-{{ $field->end_position }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Historial de Corridas y Validación -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Corridas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código Corrida</th>
                            <th class="px-4 py-3 text-mono text-center">Fecha Corrida</th>
                            <th class="px-4 py-3 text-right">Registros</th>
                            <th class="px-4 py-3 text-center">Estatus</th>
                            <th class="px-4 py-3 text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($corridas as $corr)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-bold text-[#041e49] font-mono">{{ $corr->run_number }}</td>
                                <td class="px-4 py-3 text-slate-500 text-center font-mono">{{ $corr->generated_at }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">{{ $corr->total_records }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($corr->status === 'aprobado')
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[8px] font-bold text-emerald-700 border border-emerald-250">Aprobado</span>
                                    @elseif($corr->status === 'con_errores')
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[8px] font-bold text-rose-700 border border-rose-220">Con Errores</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[8px] font-bold text-blue-700 border border-blue-200">Generado</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($corr->submission)
                                        <a href="{{ route('sisalril.submission_detalle', $corr->submission->id) }}" class="text-[#041e49] font-bold hover:underline">Ver Envío</a>
                                    @else
                                        <span class="text-slate-400 italic">No enviado</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han generado corridas de este esquema aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
