@extends('layouts.core')

@section('title', 'Prevalidación — Simulador Unipago')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#041e49]/10 text-[#041e49] border border-[#041e49]/20 tracking-wider uppercase">
                    🔌 Simulador Unipago / Unisigma
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Prevalidación de Afiliados</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Consulta individual por cédula o prevalidación masiva de lotes contra el Maestro de Ciudadanos JCE y nóminas TSS.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.unipago.lotes') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
                Cargar Lote
            </a>
            <a href="{{ route('ars.unipago.dashboard') }}" class="bg-[#041e49] text-white rounded-full px-4 py-2 text-xs font-bold hover:bg-slate-800 transition shadow-xs">
                ← Central
            </a>
        </div>
    </div>

    {{-- ============================== SECCIÓN 1: BÚSQUEDA INDIVIDUAL AJAX ============================== --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        {{-- Header terminal --}}
        <div class="bg-[#041e49] px-6 py-4 flex items-center space-x-3">
            <div class="flex space-x-1.5">
                <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
            </div>
            <span class="text-slate-300 font-mono text-xs ml-2">UNIPAGO :: Maestro Nacional de Ciudadanos — Consulta Individual</span>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Panel Izquierdo: Input --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="relative">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Número de Cédula</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </span>
                        <input type="text" id="cedula-input" maxlength="13" placeholder="07900175907"
                               class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 bg-slate-50/60 font-mono text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#041e49]/30 transition-all"
                               autocomplete="off">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Sin guiones. Ej: 07900175907</p>
                </div>

                <button id="btn-consultar" onclick="consultarCedula()"
                        class="w-full flex items-center justify-center space-x-2 bg-[#041e49] text-white rounded-2xl py-3 font-bold hover:bg-slate-800 active:scale-[0.98] transition-all shadow-sm">
                    <svg id="icon-search" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg id="icon-loader" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span id="btn-texto">Consultar en Unipago</span>
                </button>

                {{-- Cédulas de demo --}}
                <div class="p-3 bg-[#041e49]/5 rounded-2xl space-y-2">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Prueba con afiliados registrados:</p>
                    <div id="demo-links" class="space-y-1 text-[11px]">
                        <p class="text-slate-400 italic">Cargando afiliados de demostración...</p>
                    </div>
                    <div class="pt-2 border-t border-slate-100 mt-2">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Reglas de simulación (externos):</p>
                        <div class="grid grid-cols-2 gap-1 text-[10px] text-slate-400 font-mono">
                            <span>Termina en <b class="text-rose-500">9</b>: No existe JCE</span>
                            <span>Termina en <b class="text-rose-500">8</b>: Fallecido</span>
                            <span>Termina en <b class="text-amber-600">7</b>: Otra ARS</span>
                            <span>Termina en <b class="text-purple-600">6</b>: Subsidiado</span>
                            <span>Termina en <b class="text-amber-600">5</b>: Sin nómina</span>
                            <span>Termina en <b class="text-amber-600">4</b>: Sin aporte</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Derecho: Resultado --}}
            <div class="lg:col-span-3" id="resultado-panel">
                {{-- Estado inicial --}}
                <div id="estado-vacio" class="flex flex-col items-center justify-center h-full min-h-[240px] text-center py-8">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                    </div>
                    <p class="text-slate-400 text-sm font-semibold">Ingresa una cédula para consultar</p>
                    <p class="text-slate-300 text-xs mt-1">Los datos se cargarán aquí en tiempo real</p>
                </div>

                {{-- Card resultado (oculto por defecto) --}}
                <div id="resultado-card" class="hidden space-y-4">

                    {{-- Badge de fuente --}}
                    <div class="flex items-center justify-between">
                        <div id="badge-fuente" class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider border"></div>
                        <div id="badge-codigo" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black tracking-wider border font-mono"></div>
                    </div>

                    {{-- Nombre principal --}}
                    <div class="bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Ciudadano Identificado</p>
                        <p id="res-nombre" class="text-lg font-black text-[#041e49] leading-tight"></p>
                        <div class="flex flex-wrap gap-3 mt-2">
                            <span class="text-[11px] text-slate-500 font-mono">NSS: <b id="res-nss" class="text-slate-700"></b></span>
                            <span class="text-[11px] text-slate-500 font-mono">Cédula: <b id="res-cedula" class="text-slate-700"></b></span>
                        </div>
                    </div>

                    {{-- Grid datos --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sexo / Edad</p>
                            <p id="res-sexo-edad" class="font-bold text-slate-700"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fecha Nac.</p>
                            <p id="res-fnac" class="font-bold text-slate-700 font-mono"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Régimen</p>
                            <p id="res-regimen" class="font-bold text-slate-700"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Activo Nómina</p>
                            <p id="res-nomina" class="font-bold"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tiene Aporte</p>
                            <p id="res-aporte" class="font-bold"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Estado Afiliación</p>
                            <p id="res-estado" class="font-bold text-slate-700 font-mono"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Contrato</p>
                            <p id="res-contrato" class="font-bold text-slate-700 font-mono text-[10px]"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Dependientes</p>
                            <p id="res-deps" class="font-bold text-slate-700"></p>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 space-y-0.5">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Tipo</p>
                            <p id="res-tipo" class="font-bold text-slate-700"></p>
                        </div>
                    </div>

                    {{-- Clasificación Unipago --}}
                    <div id="res-clasificacion-box" class="rounded-2xl p-4 border">
                        <div class="flex items-start space-x-3">
                            <div id="res-icon" class="mt-0.5 flex-shrink-0 text-xl"></div>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-wider opacity-60 mb-0.5">Clasificación Unipago</p>
                                <p id="res-clasificacion" class="font-black text-sm leading-tight"></p>
                                <p id="res-motivo" class="text-xs mt-1 opacity-70 font-medium"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Error --}}
                <div id="resultado-error" class="hidden flex flex-col items-center justify-center h-full min-h-[200px] text-center py-8">
                    <div class="w-12 h-12 bg-rose-50 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p id="error-msg" class="text-rose-500 text-sm font-bold"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================== SECCIÓN 2: PREVALIDACIÓN MASIVA ============================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- Formulario (Izquierda 2/5) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
            <div class="flex items-center space-x-2 border-b border-slate-50 pb-3">
                <div class="w-7 h-7 rounded-xl bg-teal-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Prevalidación Masiva de Lote</h3>
            </div>

            <form action="{{ route('ars.unipago.prevalidar.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">
                        Pegar registros — formato: Cédula, Nombre, Apellido
                    </label>
                    <textarea name="raw_data" rows="10" required
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 font-mono text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-200 transition-all"
                              placeholder="07900175907,PEDRO,JIMÉNEZ&#10;22500756150,CARMEN,REYES&#10;22500756159,LUIS,PÉREZ&#10;22500756158,JUAN,GÓMEZ">{{ $inputData }}</textarea>
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl text-[10px] text-slate-500 space-y-1">
                    <p class="font-bold text-slate-600">💡 Tip: Puedes pegar solo cédulas, una por línea. El sistema buscará el nombre en la base de datos si existe.</p>
                </div>

                <button type="submit" class="w-full bg-teal-700 text-white rounded-full py-2.5 font-bold hover:bg-teal-800 transition shadow-xs text-center block flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Prevalidar Lote Completo</span>
                </button>
            </form>
        </div>

        {{-- Resultados del lote (Derecha 3/5) --}}
        <div class="lg:col-span-3 bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
            @if(!empty($prevalidated))
                {{-- Resumen del lote --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="text-center bg-slate-50 rounded-2xl p-3">
                        <p class="text-xl font-black text-slate-800">{{ $summary['total'] }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total</p>
                    </div>
                    <div class="text-center bg-emerald-50 rounded-2xl p-3">
                        <p class="text-xl font-black text-emerald-700">{{ $summary['aptos'] }}</p>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider mt-0.5">Aptos</p>
                    </div>
                    <div class="text-center bg-amber-50 rounded-2xl p-3">
                        <p class="text-xl font-black text-amber-700">{{ $summary['pendientes'] }}</p>
                        <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider mt-0.5">Pendientes</p>
                    </div>
                    <div class="text-center bg-rose-50 rounded-2xl p-3">
                        <p class="text-xl font-black text-rose-700">{{ $summary['rechazados'] }}</p>
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mt-0.5">Rechazados</p>
                    </div>
                </div>

                {{-- Indicador BD --}}
                @if($summary['en_bd'] > 0)
                <div class="flex items-center space-x-2 bg-blue-50 rounded-xl px-4 py-2 text-xs">
                    <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                    <span class="text-blue-700 font-semibold">{{ $summary['en_bd'] }} de {{ $summary['total'] }} ciudadanos encontrados en la base de datos local — datos reales cargados.</span>
                </div>
                @endif

                {{-- Tabla enriquecida --}}
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Cédula</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nombre</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sexo/Edad</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Nómina/Aporte</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Régimen</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Código</th>
                                    <th class="px-3 py-3 text-left text-[9px] font-bold text-slate-400 uppercase tracking-wider">Clasificación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @foreach($prevalidated as $item)
                                    @php
                                        $code = $item['codigo_respuesta'] ?? '';
                                        $rowBg = $code === 'OK' ? 'bg-emerald-50/30' : ($code === 'RE' ? 'bg-rose-50/30' : 'bg-amber-50/20');
                                        $codeBadge = $code === 'OK'
                                            ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                                            : ($code === 'RE' ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-amber-100 text-amber-800 border-amber-200');
                                        $classBadge = $code === 'OK'
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-150'
                                            : ($code === 'RE' ? 'bg-rose-50 text-rose-700 border-rose-150' : 'bg-amber-50 text-amber-700 border-amber-150');
                                    @endphp
                                    <tr class="{{ $rowBg }} hover:bg-slate-50/70 transition-colors" title="{{ $item['motivo'] ?? '' }}">
                                        <td class="px-3 py-2.5 font-mono font-bold text-slate-600 whitespace-nowrap">
                                            {{ $item['cedula'] }}
                                            @if($item['found_in_db'] ?? false)
                                                <span class="ml-1 text-[8px] text-blue-500 font-bold" title="Encontrado en BD local">●DB</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 font-semibold text-slate-700 max-w-[140px] truncate">
                                            {{ $item['nombre_completo'] ?? ($item['nombres'] . ' ' . $item['apellidos']) }}
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-500 whitespace-nowrap">
                                            @if(($item['sexo'] ?? 'N/D') !== 'N/D' || ($item['edad'] ?? null))
                                                <span class="{{ ($item['sexo'] ?? '') === 'M' ? 'text-blue-600' : 'text-rose-500' }} font-bold">
                                                    {{ $item['sexo'] ?? '?' }}
                                                </span>
                                                @if($item['edad'] ?? null)
                                                    <span class="text-slate-400"> / {{ $item['edad'] }} años</span>
                                                @endif
                                            @else
                                                <span class="text-slate-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-[10px] {{ ($item['activo_nomina'] ?? false) ? 'text-emerald-600' : 'text-slate-300' }}" title="Nómina TSS">
                                                    {{ ($item['activo_nomina'] ?? false) ? '✔ NOM' : '✖ NOM' }}
                                                </span>
                                                <span class="text-slate-200">|</span>
                                                <span class="text-[10px] {{ ($item['tiene_aporte'] ?? false) ? 'text-emerald-600' : 'text-slate-300' }}" title="Aporte mínimo">
                                                    {{ ($item['tiene_aporte'] ?? false) ? '✔ APO' : '✖ APO' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap text-slate-500 text-[11px]">
                                            @if(($item['es_subsidiado'] ?? false))
                                                <span class="text-purple-600 font-bold">Subsidiado</span>
                                            @else
                                                {{ $item['regimen'] ?? '—' }}
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black tracking-wider border font-mono {{ $codeBadge }}">
                                                {{ $code ?: '?' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wide border {{ $classBadge }}">
                                                {{ $item['clasificacion'] ?? '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Acción post-prevalidación --}}
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <p class="text-[11px] text-slate-400 font-medium">
                        {{ $summary['aptos'] }} registro(s) aptos para cargar en lote de afiliación.
                    </p>
                    @if($summary['aptos'] > 0)
                        <a href="{{ route('ars.unipago.lotes') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-[#041e49] text-white text-xs font-bold hover:bg-slate-800 transition shadow-xs space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>Cargar Lote de Afiliación</span>
                        </a>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full min-h-[300px] text-center py-12">
                    <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-teal-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <p class="text-slate-500 font-bold text-sm">Prevalidación de Lote</p>
                    <p class="text-slate-300 text-xs mt-1 max-w-sm">Pega un listado de cédulas en el panel izquierdo y haz clic en "Prevalidar Lote Completo". Los resultados aparecerán aquí con datos enriquecidos.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ========================= JAVASCRIPT AJAX ========================= --}}
@push('scripts')
<script>
const CONSULTAR_URL = "{{ route('ars.unipago.consultar_cedula') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

// Cargar cédulas demo desde afiliados reales
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Buscar algunos afiliados usando un endpoint existente... o solo mostrar demos estáticas
        const demoLinks = document.getElementById('demo-links');
        const demos = [
            { cedula: '07900175907', label: 'Buscar cédula de prueba' },
        ];

        // Intentar cargar afiliados reales con datos del servidor via PHP inline
        @php
            $demoAfiliados = \App\Models\Afiliado::select('cedula','nombres','primer_apellido','estado_afiliacion')
                ->whereNotNull('cedula')->limit(5)->get();
        @endphp

        const realDemos = [
            @foreach($demoAfiliados as $af)
            {
                cedula: "{{ $af->cedula }}",
                label: "{{ $af->nombres }} {{ $af->primer_apellido }}",
                estado: "{{ $af->estado_afiliacion }}"
            },
            @endforeach
        ];

        if (realDemos.length > 0) {
            demoLinks.innerHTML = realDemos.map(d => `
                <button onclick="consultarCedula('${d.cedula}')" class="w-full text-left flex items-center justify-between group px-2 py-1.5 rounded-lg hover:bg-[#041e49]/10 transition">
                    <span class="font-mono text-[10px] text-[#041e49] font-bold">${d.cedula}</span>
                    <span class="text-[10px] text-slate-500 truncate ml-2 group-hover:text-slate-700">${d.label}</span>
                </button>
            `).join('');
        } else {
            demoLinks.innerHTML = '<p class="text-[10px] text-slate-400 italic">Sin afiliados registrados aún.</p>';
        }
    } catch(e) {
        console.warn('Error cargando demos:', e);
    }

    // Permitir Enter en el input
    document.getElementById('cedula-input').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') consultarCedula();
    });
});

async function consultarCedula(cedulaParam) {
    const input = document.getElementById('cedula-input');
    const cedula = cedulaParam || input.value.trim().replace(/[^0-9]/g, '');

    if (!cedula) {
        mostrarError('Por favor ingresa una cédula válida.');
        return;
    }

    if (cedulaParam) input.value = cedulaParam;

    setLoading(true);
    ocultarPaneles();

    try {
        const resp = await fetch(`${CONSULTAR_URL}?cedula=${encodeURIComponent(cedula)}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        });

        if (!resp.ok) throw new Error('Error de conexión');
        const data = await resp.json();

        if (data.error) {
            mostrarError(data.error);
            return;
        }

        renderResultado(data);

    } catch (err) {
        mostrarError('Error al consultar el servicio Unipago. Intenta de nuevo.');
    } finally {
        setLoading(false);
    }
}

function renderResultado(d) {
    // Badge fuente
    const badgeFuente = document.getElementById('badge-fuente');
    if (d.found_in_db) {
        badgeFuente.className = 'inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider border bg-blue-50 text-blue-700 border-blue-200';
        badgeFuente.innerHTML = `🗄 Encontrado en BD Local — ${d.type === 'dependiente' ? 'Dependiente' : 'Titular'}`;
    } else {
        badgeFuente.className = 'inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold tracking-wider border bg-slate-50 text-slate-500 border-slate-200';
        badgeFuente.innerHTML = '🌐 Simulado — Externo JCE/TSS';
    }

    // Badge código de respuesta
    const badgeCodigo = document.getElementById('badge-codigo');
    const codeMaps = {
        'OK':   { cls: 'bg-emerald-100 text-emerald-800 border-emerald-300', icon: '✔' },
        'RE':   { cls: 'bg-rose-100 text-rose-800 border-rose-300', icon: '✖' },
        'PE64': { cls: 'bg-amber-100 text-amber-800 border-amber-300', icon: '⚠' },
        'PE75': { cls: 'bg-purple-100 text-purple-800 border-purple-300', icon: '?' },
        'ERR':  { cls: 'bg-slate-100 text-slate-600 border-slate-300', icon: '!' },
    };
    const cm = codeMaps[d.codigo_respuesta] || codeMaps['ERR'];
    badgeCodigo.className = `inline-flex items-center space-x-1 px-3 py-1 rounded-full text-xs font-black tracking-wider border font-mono ${cm.cls}`;
    badgeCodigo.innerHTML = `<span>${cm.icon}</span><span>${d.codigo_respuesta || '?'}</span>`;

    // Datos principales
    document.getElementById('res-nombre').textContent = d.nombre_completo || '—';
    document.getElementById('res-nss').textContent = d.nss || 'N/D';
    document.getElementById('res-cedula').textContent = d.cedula || '—';
    document.getElementById('res-fnac').textContent = d.fecha_nacimiento || 'N/D';

    // Sexo / Edad
    const sexoEdad = document.getElementById('res-sexo-edad');
    const sexoColor = d.sexo === 'M' ? 'text-blue-600' : (d.sexo === 'F' ? 'text-rose-500' : 'text-slate-400');
    sexoEdad.className = `font-bold ${sexoColor}`;
    sexoEdad.textContent = [d.sexo !== 'N/D' ? d.sexo : '?', d.edad ? `${d.edad} años` : null].filter(Boolean).join(' / ') || 'N/D';

    // Régimen
    const regimenEl = document.getElementById('res-regimen');
    regimenEl.className = d.es_subsidiado ? 'font-bold text-purple-700' : 'font-bold text-slate-700';
    regimenEl.textContent = d.regimen || '—';

    // Nómina y Aporte
    const nominaEl = document.getElementById('res-nomina');
    nominaEl.className = d.activo_nomina ? 'font-bold text-emerald-600' : 'font-bold text-rose-500';
    nominaEl.textContent = d.activo_nomina ? '✔ Activo en TSS' : '✖ No registrado';

    const aporteEl = document.getElementById('res-aporte');
    aporteEl.className = d.tiene_aporte ? 'font-bold text-emerald-600' : 'font-bold text-rose-500';
    aporteEl.textContent = d.tiene_aporte ? '✔ Cumple mínimo' : '✖ No cumple';

    // Estado, contrato, deps, tipo
    document.getElementById('res-estado').textContent = d.estado_afiliacion || 'N/D';
    document.getElementById('res-contrato').textContent = d.numero_contrato || '—';
    document.getElementById('res-deps').textContent = d.dependientes_count !== undefined ? `${d.dependientes_count} dep.` : '—';
    document.getElementById('res-tipo').textContent = d.type === 'titular' ? '👤 Titular' : (d.type === 'dependiente' ? '👶 Dependiente' : '🌐 Externo');

    // Clasificación Unipago
    const clasificacionBox = document.getElementById('res-clasificacion-box');
    const clasificacion = d.clasificacion || 'Desconocido';
    const code = d.codigo_respuesta;

    const classMaps = {
        'OK':   { box: 'bg-emerald-50 border-emerald-200 text-emerald-900', icon: '✅' },
        'RE':   { box: 'bg-rose-50 border-rose-200 text-rose-900', icon: '🚫' },
        'PE64': { box: 'bg-amber-50 border-amber-200 text-amber-900', icon: '⚠️' },
        'PE75': { box: 'bg-purple-50 border-purple-200 text-purple-900', icon: '❓' },
        'ERR':  { box: 'bg-slate-50 border-slate-200 text-slate-600', icon: '⛔' },
    };
    const cmap = classMaps[code] || classMaps['ERR'];
    clasificacionBox.className = `rounded-2xl p-4 border ${cmap.box}`;
    document.getElementById('res-icon').textContent = cmap.icon;
    document.getElementById('res-clasificacion').textContent = clasificacion;
    document.getElementById('res-motivo').textContent = d.motivo || '';

    // Mostrar card
    document.getElementById('estado-vacio').classList.add('hidden');
    document.getElementById('resultado-error').classList.add('hidden');
    document.getElementById('resultado-card').classList.remove('hidden');
}

function mostrarError(msg) {
    document.getElementById('estado-vacio').classList.add('hidden');
    document.getElementById('resultado-card').classList.add('hidden');
    document.getElementById('error-msg').textContent = msg;
    document.getElementById('resultado-error').classList.remove('hidden');
}

function ocultarPaneles() {
    document.getElementById('estado-vacio').classList.add('hidden');
    document.getElementById('resultado-card').classList.add('hidden');
    document.getElementById('resultado-error').classList.add('hidden');
}

function setLoading(on) {
    document.getElementById('icon-search').classList.toggle('hidden', on);
    document.getElementById('icon-loader').classList.toggle('hidden', !on);
    document.getElementById('btn-texto').textContent = on ? 'Consultando Unipago...' : 'Consultar en Unipago';
    document.getElementById('btn-consultar').disabled = on;
}
</script>
@endpush
@endsection
