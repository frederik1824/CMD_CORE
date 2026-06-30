@extends('layouts.ars')

@section('title', 'Prevalidación Masiva Unipago')

@section('content')
<div class="space-y-6 font-sans text-xs">
    
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Prevalidación Masiva ante Unipago</h2>
            <p class="text-xs text-slate-500 font-medium">Validador estructural y pre-auditoría de lotes de afiliados contra el padrón.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
            Simulador SUIR
        </span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        <!-- Formulario (1/3) -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Pegar Registros del Lote</h3>
            <form action="{{ route('ars.unipago.prevalidar.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Formato: Cédula, Nombres, Apellidos (Línea por línea)</label>
                    <textarea name="raw_data" rows="10" required
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-[11px] text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                              placeholder="07900175907,Juan,Perez&#10;40200001001,Maria,Gomez&#10;00109283742,Pedro,Ramirez">{{ $inputData ?? '' }}</textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-850 transition flex items-center justify-center space-x-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-sm">rule</span>
                    <span>Prevalidar Lote</span>
                </button>
            </form>
        </div>

        <!-- Resultados (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            @if(!empty($prevalidated))
                <!-- Summary Bento Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-xs">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Total</span>
                        <span class="text-xl font-bold text-slate-900 block font-mono mt-1">{{ $summary['total'] }}</span>
                    </div>
                    <div class="bg-emerald-50/30 rounded-2xl border border-emerald-100 p-4 shadow-xs">
                        <span class="text-[9px] font-bold text-emerald-600 uppercase">Aptos (OK)</span>
                        <span class="text-xl font-bold text-emerald-700 block font-mono mt-1">{{ $summary['aptos'] }}</span>
                    </div>
                    <div class="bg-amber-50/30 rounded-2xl border border-amber-100 p-4 shadow-xs">
                        <span class="text-[9px] font-bold text-amber-600 uppercase">Pendientes</span>
                        <span class="text-xl font-bold text-amber-700 block font-mono mt-1">{{ $summary['pendientes'] }}</span>
                    </div>
                    <div class="bg-rose-50/30 rounded-2xl border border-rose-100 p-4 shadow-xs">
                        <span class="text-[9px] font-bold text-rose-600 uppercase">Rechazados</span>
                        <span class="text-xl font-bold text-rose-700 block font-mono mt-1">{{ $summary['rechazados'] }}</span>
                    </div>
                    <div class="bg-blue-50/30 rounded-2xl border border-blue-100 p-4 shadow-xs">
                        <span class="text-[9px] font-bold text-blue-600 uppercase">En ARS</span>
                        <span class="text-xl font-bold text-blue-700 block font-mono mt-1">{{ $summary['en_bd'] }}</span>
                    </div>
                </div>

                <!-- Detalle -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
                    <h3 class="font-bold text-slate-800">Resultado de la Prevalidación</h3>
                    <div class="overflow-x-auto max-h-[350px]">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50 font-bold text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 text-left">Cédula</th>
                                    <th class="px-4 py-3 text-left">Nombre Registrado</th>
                                    <th class="px-4 py-3 text-center">Código Resp.</th>
                                    <th class="px-4 py-3 text-left">Detalle / Diagnóstico</th>
                                    <th class="px-4 py-3 text-center">En DB</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($prevalidated as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-mono font-bold text-slate-650">{{ $item['cedula'] }}</td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $item['nombres'] }} {{ $item['apellidos'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold
                                                {{ $item['codigo_respuesta'] === 'OK' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 
                                                   (str_starts_with($item['codigo_respuesta'], 'PE') ? 'bg-amber-50 text-amber-700 border border-amber-250' : 'bg-rose-50 text-rose-700 border border-rose-250') }}">
                                                {{ $item['codigo_respuesta'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-500 font-semibold">{{ $item['motivo'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="material-symbols-outlined text-sm {{ $item['found_in_db'] ? 'text-blue-600' : 'text-slate-300' }}">
                                                {{ $item['found_in_db'] ? 'check_circle' : 'cancel' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-100 p-10 text-center text-slate-400 space-y-3 min-h-[320px] flex flex-col justify-center items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300">rule_folder</span>
                    <h4 class="font-bold text-slate-700 text-sm">Esperando Datos para Validar</h4>
                    <p class="text-xs text-slate-450 max-w-sm">Use el formulario de la izquierda para pegar los datos y analizar la viabilidad de la carga del lote de afiliados.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
