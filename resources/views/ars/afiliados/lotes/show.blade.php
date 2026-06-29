@extends('layouts.ars')

@section('title', 'Detalle de Lote')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex items-center space-x-4">
            <a href="{{ route('ars.lotes.index') }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold leading-7 text-slate-900 font-mono tracking-tight">{{ $lote->numero_lote }}</h2>
                <p class="mt-1 text-sm text-slate-500">Tipo: <span class="capitalize font-semibold">{{ str_replace('_', ' ', $lote->tipo_lote) }}</span> • Creado por: {{ $lote->creador->name }}</p>
            </div>
        </div>
        
        <!-- Botón Procesar Simulación -->
        @if($lote->estado_lote === 'VE')
            <div class="mt-4 sm:mt-0 sm:ml-4">
                <form action="{{ route('ars.lotes.procesar', $lote->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-3 border border-transparent rounded-xl shadow-lg text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 hover:shadow-indigo-500/20 active:scale-95 transition-all">
                        <svg class="mr-2 h-4 w-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Procesar Simulación Unipago
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Panel de Métricas del Lote -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 bg-white p-6 shadow-sm rounded-2xl border border-slate-200">
        <!-- Registros Totales -->
        <div class="text-center sm:text-left sm:border-r border-slate-100 sm:pr-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Registros Totales</span>
            <span class="text-2xl font-bold text-slate-800 block mt-1">{{ $lote->total_registros }}</span>
        </div>
        <!-- Registros Aprobados (OK) -->
        <div class="text-center sm:text-left sm:border-r border-slate-100 sm:px-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Procesados OK</span>
            <span class="text-2xl font-bold text-emerald-600 block mt-1">{{ $lote->registros_ok }}</span>
        </div>
        <!-- Registros Rechazados (RE) -->
        <div class="text-center sm:text-left sm:border-r border-slate-100 sm:px-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rechazados Unipago</span>
            <span class="text-2xl font-bold text-rose-600 block mt-1">{{ $lote->registros_re }}</span>
        </div>
        <!-- Estado Lote -->
        <div class="text-center sm:text-left sm:pl-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Estado Lote</span>
            <span class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ 
                $lote->estado_lote === 'EV' ? 'bg-emerald-50 text-emerald-700' : (
                $lote->estado_lote === 'PC' ? 'bg-blue-50 text-blue-700 animate-pulse' : (
                $lote->estado_lote === 'PE' ? 'bg-amber-50 text-amber-700' : (
                $lote->estado_lote === 'RE' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600')))
            }}">
                {{ $lote->estado_lote === 'EV' ? 'Procesado OK' : ($lote->estado_lote === 'PC' ? 'Procesando' : ($lote->estado_lote === 'PE' ? 'Errores' : ($lote->estado_lote === 'RE' ? 'Rechazado' : 'Espera (VE)'))) }}
            </span>
        </div>
    </div>

    <!-- Detalles de Registros -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Integrantes del Lote</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Afiliado</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Identificación</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tipo Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Respuesta Unipago</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Resultado / Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($lote->detalles as $det)
                        @php $entidad = $det->entidad; @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($entidad)
                                    @if($lote->tipo_lote === 'novedades')
                                        <!-- Entidad es novedad, mostramos el afiliado de la novedad -->
                                        @php $target = $entidad->afiliado; @endphp
                                        @if($target)
                                            <div class="text-sm font-bold text-slate-800">{{ $target->nombre_completo }}</div>
                                            <span class="text-xs text-slate-400 font-semibold bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded mt-1 inline-block">Novedad: {{ $entidad->tipoNovedad->codigo }}</span>
                                        @else
                                            <span class="text-slate-400 text-xs">Sin afiliado</span>
                                        @endif
                                    @else
                                        <!-- Entidad es titular o dependiente -->
                                        <div class="text-sm font-bold text-slate-800">{{ $entidad->nombre_completo }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">Registro huérfano</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-600">
                                @if($entidad)
                                    @if($lote->tipo_lote === 'novedades' && $target)
                                        Ced: {{ $target->cedula }}<br>NSS: {{ $target->nss }}
                                    @else
                                        Ced: {{ $entidad->cedula ?? 'N/A' }}<br>NSS: {{ $entidad->nss ?? 'N/A' }}
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-500 capitalize">
                                {{ $det->entidad_type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ 
                                    $det->estado === 'OK' ? 'bg-emerald-50 text-emerald-700' : (
                                    $det->estado === 'PE' ? 'bg-slate-100 text-slate-600' : 'bg-rose-50 text-rose-700')
                                }}">
                                    {{ $det->estado === 'OK' ? 'Aprobado (OK)' : ($det->estado === 'PE' ? 'Pendiente' : 'Rechazado (' . $det->estado . ')') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400 leading-normal max-w-sm">
                                @if($det->estado === 'OK')
                                    <span class="text-emerald-600 font-semibold">Validación exitosa, afiliación activa.</span>
                                @else
                                    {{ $det->motivo_rechazo ?? 'En espera de procesamiento' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
