@extends('layouts.ars')

@section('title', 'Expediente Maestro de Afiliado')

@section('content')
@php
    $totalDeps = $titular->dependientes->count();
    $totalAutos = $autorizaciones->count();
    $autosActivas = $autorizaciones->whereIn('estado', ['Aprobado', 'Pendiente', 'Aprobada', 'Auditoría'])->count();
    $totalReclamos = $titular->claims->count();
    $reclamosPendientes = $titular->claims->whereNotIn('status', ['Pagada', 'Cerrada'])->count();
    $totalNotif = $titular->capitationNotifications->count();
    $totalDisp = $titular->dispersionCutDetails->count();
@endphp

<div class="max-w-7xl mx-auto space-y-6 animate-fade-in" x-data="{ 
    activeTab: 'nucleo', 
    addDepModal: false, 
    showCarnet: false,
    copiedNss: false,
    copiedCedula: false,
    searchDep: '',
    searchAut: '',
    searchClaim: '',
    searchNov: '',
    searchDoc: '',
    copyToClipboard(text, type) {
        navigator.clipboard.writeText(text);
        if (type === 'nss') {
            this.copiedNss = true;
            setTimeout(() => this.copiedNss = false, 2000);
        } else {
            this.copiedCedula = true;
            setTimeout(() => this.copiedCedula = false, 2000);
        }
    }
}">
        <!-- Encabezado & Perfil Central Ultra Premium -->
    <div class="relative bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
        <!-- Decoración de fondo abstracta y de alta gama -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-50/60 via-indigo-50/20 to-transparent rounded-full -mr-20 -mt-20 pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-emerald-50/10 blur-3xl rounded-full pointer-events-none"></div>
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <!-- Sección Izquierda: Avatar y Nombres -->
            <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                <!-- Avatar Médico Estilizado con Aro de Estado Pulsante -->
                <div class="relative">
                    <div class="w-22 h-22 rounded-3xl bg-gradient-to-tr from-[#0b57d0] to-[#1a73e8] text-white font-extrabold flex items-center justify-center text-3xl shadow-lg border-4 border-white transform transition-transform duration-300 hover:scale-105 select-none">
                        {{ substr($titular->nombres, 0, 1) }}{{ substr($titular->primer_apellido, 0, 1) }}
                    </div>
                    <span class="absolute -bottom-1.5 -right-1.5 w-6.5 h-6.5 rounded-full border-4 border-white flex items-center justify-center shadow-md {{
                        $titular->estado_afiliacion === 'OK' ? 'bg-emerald-500' : 'bg-amber-500'
                    }}" title="{{ $titular->estado_afiliacion === 'OK' ? 'Activo' : 'Pendiente' }}">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    </span>
                </div>
 
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight leading-none">{{ $titular->nombre_completo }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black tracking-widest uppercase {{ 
                            $titular->estado_afiliacion === 'OK' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60'
                        }}">
                            {{ $titular->estado_afiliacion === 'OK' ? 'Activo' : 'Pendiente' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-450 font-semibold font-mono flex items-center justify-center sm:justify-start gap-2.5">
                        <span>PLAN: <strong class="text-[#0b57d0] font-sans font-black tracking-tight">BÁSICO DE SALUD CMD</strong></span>
                        <span class="text-slate-300">|</span>
                        <span>Contrato: <strong class="text-slate-700 font-bold font-mono">{{ $titular->numero_contrato ?? 'N/A' }}</strong></span>
                    </p>
                    
                    <!-- Botones Rápidos Identificaciones -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1 text-[10px] text-slate-500 font-mono">
                        <span class="bg-slate-50/80 px-3 py-1.5 rounded-xl border border-slate-150 flex items-center gap-2 shadow-2xs transition hover:bg-slate-100/50">
                            <span class="text-slate-400 font-semibold">NSS:</span>
                            <strong class="text-slate-750 font-bold">{{ $titular->nss ?? 'N/D' }}</strong>
                            <button @click="copyToClipboard('{{ $titular->nss }}', 'nss')" class="text-slate-450 hover:text-[#0b57d0] transition focus:outline-none" title="Copiar NSS">
                                <svg class="w-3.5 h-3.5" :class="copiedNss ? 'text-emerald-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!copiedNss" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    <path x-show="copiedNss" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </span>
 
                        <span class="bg-slate-50/80 px-3 py-1.5 rounded-xl border border-slate-150 flex items-center gap-2 shadow-2xs transition hover:bg-slate-100/50">
                            <span class="text-slate-450 font-semibold">Cédula:</span>
                            <strong class="text-slate-750 font-bold">{{ $titular->cedula ?? 'N/D' }}</strong>
                            <button @click="copyToClipboard('{{ $titular->cedula }}', 'cedula')" class="text-slate-450 hover:text-[#0b57d0] transition focus:outline-none" title="Copiar Cédula">
                                <svg class="w-3.5 h-3.5" :class="copiedCedula ? 'text-emerald-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!copiedCedula" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    <path x-show="copiedCedula" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
 
            <!-- Botones de Acción de Flujo -->
            <div class="flex items-center gap-2.5 self-center shrink-0">
                <a href="{{ route('ars.titulares.index') }}" class="p-3 rounded-2xl hover:bg-slate-50 transition text-slate-450 hover:text-slate-700 border border-slate-200 bg-white shadow-2xs hover:shadow-xs" title="Volver a la bandeja">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <a href="{{ route('ars.novedades.create', ['afiliado_id' => $titular->id, 'afiliado_type' => 'titular']) }}" 
                    class="inline-flex items-center px-4.5 py-3 border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs hover:shadow-xs">
                    <svg class="mr-1.5 h-4.5 w-4.5 text-slate-450" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Novedad
                </a>
                <a href="{{ route('ars.titulares.edit', $titular->id) }}" 
                    class="inline-flex items-center px-5 py-3 rounded-2xl text-xs font-bold text-white transition hover:shadow-md active:scale-98"
                    style="background: linear-gradient(135deg, #0b57d0 0%, #1a73e8 100%);">
                    <svg class="mr-1.5 h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Editar Datos
                </a>
            </div>
        </div>
    </div>
 
    <!-- Bento Grid de KPIs del Afiliado -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @php
            $kpis = [
                ['label' => 'Dependientes', 'val' => $totalDeps, 'sub' => 'Núcleo familiar', 'color' => 'bg-indigo-50/75 text-indigo-700', 'border' => 'border-indigo-100/50 hover:border-indigo-300', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Autorizaciones', 'val' => $totalAutos, 'sub' => "$autosActivas activas", 'color' => 'bg-cyan-50/75 text-cyan-700', 'border' => 'border-cyan-100/50 hover:border-cyan-300', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Reclamaciones', 'val' => $totalReclamos, 'sub' => "$reclamosPendientes pendientes", 'color' => 'bg-amber-50/75 text-amber-700', 'border' => 'border-amber-100/50 hover:border-amber-300', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['label' => 'Notificaciones', 'val' => $totalNotif, 'sub' => 'Lotes de TSS', 'color' => 'bg-violet-50/75 text-violet-700', 'border' => 'border-violet-100/50 hover:border-violet-300', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                ['label' => 'Antigüedad', 'val' => $diasAfiliado > 365 ? round($diasAfiliado / 365, 1) . ' años' : $diasAfiliado . ' días', 'sub' => 'Tiempo Afiliado ARS', 'color' => 'bg-emerald-50/75 text-emerald-700', 'border' => 'border-emerald-100/50 hover:border-emerald-300', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z']
            ];
        @endphp
        @foreach($kpis as $kp)
        <div class="bg-white rounded-2xl border {{ $kp['border'] }} p-4 flex items-center justify-between shadow-xs hover:shadow-sm hover:-translate-y-0.5 transition duration-200 cursor-pointer select-none">
            <div class="space-y-0.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $kp['label'] }}</span>
                <span class="text-xl font-black text-slate-800 tracking-tight">{{ $kp['val'] }}</span>
                <span class="text-[10px] font-semibold text-slate-550 block mt-0.5">{{ $kp['sub'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl {{ $kp['color'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $kp['icon'] }}"/></svg>
            </div>
        </div>
        @endforeach
    </div>
 
    <!-- Perfil Demográfico & Clínico Horizontal (Bento Grid Premium) -->
    <div class="bg-white shadow-xs rounded-2xl border border-slate-200/80 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Expediente Demográfico & Clínico Consolidado</h3>
            </div>
            <span class="text-[9px] text-slate-500 font-extrabold uppercase font-mono bg-slate-100/80 px-2.5 py-1 rounded-lg border border-slate-200">Fisiológico & TSS</span>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
            <!-- 1. Datos Fisiológicos -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fisiología Básica</span>
                <div class="space-y-0.5">
                    <span class="text-slate-800 font-extrabold block text-xs font-mono">{{ $titular->fecha_nacimiento->format('d/m/Y') }}</span>
                    <span class="text-[10px] text-slate-500 block font-semibold">{{ $titular->edad }} años | 
                        @if($titular->sexo === 'M' || $titular->sexo === 'Masculino')
                            <span class="text-blue-600 font-bold">M</span>
                        @else
                            <span class="text-rose-600 font-bold">F</span>
                        @endif
                    </span>
                </div>
            </div>
 
            <!-- 2. Afiliación & Entrada -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Afiliación & Régimen</span>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between">
                        <span class="text-slate-500 font-medium">Entrada:</span>
                        <span class="text-slate-800 font-bold font-mono">{{ $titular->fecha_afiliacion ? $titular->fecha_afiliacion->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between pt-0.5 border-t border-slate-200/30 mt-0.5">
                        <span class="text-slate-500 font-medium">Régimen:</span>
                        <span class="text-slate-850 font-bold">{{ $titular->regimen_actual ?? 'Contributivo' }}</span>
                    </div>
                </div>
            </div>
 
            <!-- 3. Contacto & Residencia -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Contacto Principal</span>
                <div class="space-y-0.5 text-[11px] leading-tight">
                    <span class="text-slate-850 font-extrabold block font-mono">{{ $titular->telefono ?? 'N/A' }}</span>
                    <span class="text-slate-450 block truncate text-[10px]" title="{{ $titular->correo }}">{{ $titular->correo ?? 'N/A' }}</span>
                </div>
            </div>
 
            <!-- 4. Estatus Documental -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Expediente Físico</span>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-450 font-medium">Carnet:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold {{ $titular->esta_carnetizado ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                            {{ $titular->esta_carnetizado ? 'ENTREGADO' : 'PENDIENTE' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-0.5 border-t border-slate-200/30 mt-0.5">
                        <span class="text-[10px] text-slate-450 font-medium">Formulario:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-bold {{ $titular->tiene_formulario ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                            {{ $titular->tiene_formulario ? 'RECIBIDO' : 'PENDIENTE' }}
                        </span>
                    </div>
                </div>
            </div>
 
            <!-- 5. Estatus TSS & Laboral -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Nómina TSS & Aportes</span>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-450 font-medium">Activo TSS:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-bold {{ $titular->activo_nomina ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                            {{ $titular->activo_nomina ? 'SÍ' : 'NO' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pt-0.5 border-t border-slate-200/30 mt-0.5">
                        <span class="text-[10px] text-slate-450 font-medium">Aportes TSS:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-bold {{ $titular->tiene_aporte ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                            {{ $titular->tiene_aporte ? 'SÍ' : 'NO' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 6. Control Formulario Unipago -->
            <div class="bg-slate-50/50 rounded-xl p-3.5 border border-slate-100 flex flex-col justify-between space-y-1.5 transition hover:bg-slate-50/80">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Contrato / Formulario</span>
                <div class="space-y-1 text-[11px]">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] text-slate-450 font-medium">Número:</span>
                        <strong class="font-mono text-slate-800">{{ $titular->contract_number ?: 'Sin Asignar' }}</strong>
                    </div>
                    <div class="flex justify-between items-center pt-0.5 border-t border-slate-200/30 mt-0.5">
                        <span class="text-[10px] text-slate-450 font-medium">Estado:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[8px] font-bold uppercase {{ 
                            optional($titular->contractNumber)->status === 'ok' ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' : (
                            optional($titular->contractNumber)->status === 're' ? 'bg-rose-50 text-rose-700 border border-rose-150' : 'bg-slate-100 text-slate-500 border border-slate-200')
                        }}">
                            {{ optional($titular->contractNumber)->status ?: 'Disponible' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ubicación Geográfica Completa en el Cintillo Base -->
        @if($titular->direccion || $titular->provincia)
        <div class="px-5 py-3 bg-slate-50/55 border-t border-slate-150 text-[10px] text-slate-600 flex flex-col sm:flex-row sm:items-center gap-2.5 font-mono">
            <span class="font-bold text-slate-450 shrink-0 uppercase tracking-wider text-[9px] font-sans">Ubicación Residencial Segregada:</span>
            <span class="font-bold text-slate-800">
                {{ $titular->provincia ?? 'N/A' }} &raquo; {{ $titular->municipio ?? 'N/A' }} @if($titular->sector) &raquo; {{ $titular->sector }} @endif @if($titular->direccion) &raquo; <span class="font-sans font-semibold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-150 leading-normal">{{ $titular->direccion }}</span> @endif
            </span>
            @if($titular->tiene_formulario && $titular->ubicacion_formulario)
                <div class="sm:ml-auto flex items-center gap-1.5 text-[9px] bg-white px-2.5 py-1 rounded-lg border border-slate-200/80 shadow-2xs font-sans">
                    <span class="font-bold text-slate-400 uppercase">Ubicación Formulario:</span>
                    <strong class="text-blue-750 font-extrabold">{{ $titular->ubicacion_formulario }}</strong>
                </div>
            @endif
        </div>
        @endif

        <!-- Widget Premium de Consumo de Coberturas Anuales del Plan -->
        <div class="px-5 py-4.5 bg-slate-50/25 border-t border-slate-150/70 text-xs">
            <span class="font-extrabold text-slate-450 uppercase tracking-wider text-[9px] block mb-3.5">
                Consumo y Disponibilidad de Coberturas Anuales del Plan
            </span>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 1. Farmacia -->
                @php
                    $pctFarmacia = min(100, round(($consumoFarmacia / 8000) * 100, 1));
                @endphp
                <div class="space-y-2 bg-white rounded-xl p-3 border border-slate-150 shadow-2xs hover:shadow-xs transition duration-200">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-bold text-slate-700 uppercase">Farmacia (Medicamentos)</span>
                        <span class="font-mono font-bold text-blue-650">{{ $pctFarmacia }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full" style="width: {{ $pctFarmacia }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[9px] text-slate-450 font-mono">
                        <span>Consumido: DOP {{ number_format($consumoFarmacia, 2) }}</span>
                        <span>Tope: DOP 8,000.00</span>
                    </div>
                </div>

                <!-- 2. Ambulatorio -->
                @php
                    $pctAmbulatorio = min(100, round(($consumoAmbulatorio / 150000) * 100, 1));
                @endphp
                <div class="space-y-2 bg-white rounded-xl p-3 border border-slate-150 shadow-2xs hover:shadow-xs transition duration-200">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-bold text-slate-700 uppercase">Ambulatorio (Consultas/Lab)</span>
                        <span class="font-mono font-bold text-cyan-650">{{ $pctAmbulatorio }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-2.5 rounded-full" style="width: {{ $pctAmbulatorio }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[9px] text-slate-450 font-mono">
                        <span>Consumido: DOP {{ number_format($consumoAmbulatorio, 2) }}</span>
                        <span>Tope: DOP 150,000.00</span>
                    </div>
                </div>

                <!-- 3. Hospitalización -->
                @php
                    $pctHospitalizacion = min(100, round(($consumoHospitalizacion / 1000000) * 100, 1));
                @endphp
                <div class="space-y-2 bg-white rounded-xl p-3 border border-slate-150 shadow-2xs hover:shadow-xs transition duration-200">
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="font-bold text-slate-700 uppercase">Hospitalización & Cirugía</span>
                        <span class="font-mono font-bold text-violet-650">{{ $pctHospitalizacion }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-violet-500 to-indigo-600 h-2.5 rounded-full" style="width: {{ $pctHospitalizacion }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[9px] text-slate-450 font-mono">
                        <span>Consumido: DOP {{ number_format($consumoHospitalizacion, 2) }}</span>
                        <span>Tope: DOP 1,000,000.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Contenido Principal en Dos Columnas (Optimizado 1/4 y 3/4) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        
        <!-- Columna Izquierda (Carnet Ocultable y Causa de Rechazo) -->
        <div class="space-y-6">
            
            <!-- Acordeón para Carnet de Afiliación Digital Ultra Premium -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs space-y-3">
                <button @click="showCarnet = !showCarnet" 
                        class="w-full flex items-center justify-between p-3.5 bg-slate-50/50 hover:bg-slate-50 border border-slate-100 rounded-xl transition duration-150 text-left focus:outline-none">
                    <span class="flex items-center gap-3">
                        <span class="w-8.5 h-8.5 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v1"/></svg>
                        </span>
                        <div>
                            <span class="block font-bold text-slate-800 text-xs">Carnet de Afiliación Digital</span>
                            <span class="block text-[9px] text-slate-450 font-medium">Ver credencial oficial del afiliado</span>
                        </div>
                    </span>
                    <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-250" :class="showCarnet ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                </button>
 
                <!-- Cuerpo del Carnet Digital Ocultable Premium -->
                <div x-show="showCarnet" x-collapse x-cloak class="pt-2 border-t border-slate-100">
                    <div class="relative w-full aspect-[1.586/1] rounded-2xl p-5 text-white overflow-hidden shadow-lg flex flex-col justify-between"
                         style="background: linear-gradient(135deg, #0b57d0 0%, #1a237e 100%);">
                        
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white via-transparent to-transparent pointer-events-none"></div>
                        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
                        
                        <div class="flex items-start justify-between z-10">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 backdrop-blur-xs">
                                    <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="leading-none">
                                    <span class="text-xs font-black tracking-tight block">ARS CMD</span>
                                    <span class="text-[7px] text-white/70 tracking-wider font-extrabold">PLAN BÁSICO DE SALUD</span>
                                </div>
                            </div>
                            <span class="text-[7px] px-2.5 py-0.5 rounded-full bg-white/20 border border-white/30 font-black uppercase tracking-widest backdrop-blur-xs">
                                {{ $titular->estado_afiliacion === 'OK' ? 'Activo' : 'Pendiente' }}
                            </span>
                        </div>
 
                        <div class="flex items-center gap-4.5 z-10 my-1">
                            <!-- Chip Dorado Realista -->
                            <div class="w-9 h-7 rounded-lg bg-gradient-to-tr from-amber-400 via-yellow-150 to-amber-500 border border-amber-300 relative overflow-hidden shadow-inner">
                                <div class="absolute inset-x-2 top-0 h-full border-x border-amber-600/30"></div>
                                <div class="absolute inset-y-1.5 left-0 w-full border-y border-amber-600/30"></div>
                                <div class="absolute w-2 h-2 rounded-full bg-amber-600/20 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
                            </div>
                            <svg class="w-5 h-5 text-white/50 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
 
                        <div class="z-10 mt-auto space-y-1.5">
                            <div class="text-xs font-black uppercase tracking-wider line-clamp-1 filter drop-shadow-xs">
                                {{ $titular->nombre_completo }}
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[8px] text-white/85 font-mono">
                                <div>
                                    <span class="block text-[6px] text-white/60 font-sans uppercase font-bold">NSS del Afiliado</span>
                                    <strong class="font-bold text-white tracking-widest block text-[9px]">{{ $titular->nss ?? 'N/D' }}</strong>
                                </div>
                                <div>
                                    <span class="block text-[6px] text-white/60 font-sans uppercase font-bold">Cédula Identidad</span>
                                    <strong class="font-bold text-white tracking-widest block text-[9px]">{{ $titular->cedula ?? 'N/D' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    <button onclick="window.print()" class="mt-3.5 w-full flex items-center justify-center gap-2 py-2.5 border border-slate-200 rounded-xl text-[10px] font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs hover:shadow-xs active:scale-98">
                        <svg class="w-4 h-4 text-slate-450" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimir Credencial de Salud
                    </button>
 
                    @if($titular->estado_afiliacion === 'RE')
                        <div class="bg-rose-50/80 border border-rose-200/80 rounded-2xl p-4 shadow-2xs space-y-2 mt-3">
                            <h4 class="text-xs font-black text-rose-800 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Rechazo Unipago
                            </h4>
                            <p class="text-xs text-rose-700 leading-relaxed font-semibold">{{ $titular->motivo_estado }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha (Tabs y Detalle - Optimizado 3/4) -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Barra de Pestañas Workspace Style -->
            <div class="bg-slate-150/70 p-1.5 rounded-2xl border border-slate-200/60 flex items-center overflow-x-auto gap-1">
                @php
                    $tabs = [
                        ['id' => 'nucleo', 'label' => 'Familiar', 'badge' => $totalDeps, 'color' => 'bg-indigo-600'],
                        ['id' => 'autorizaciones', 'label' => 'Autorizaciones', 'badge' => $totalAutos, 'color' => 'bg-cyan-600'],
                        ['id' => 'reclamaciones', 'label' => 'Reclamaciones', 'badge' => $totalReclamos, 'color' => 'bg-amber-600'],
                        ['id' => 'novedades', 'label' => 'Novedades', 'badge' => $novedades->count(), 'color' => 'bg-purple-600'],
                        ['id' => 'documentos', 'label' => 'Documentos', 'badge' => $documentos->count(), 'color' => 'bg-slate-600'],
                        ['id' => 'notificaciones', 'label' => 'TSS Notif.', 'badge' => $totalNotif, 'color' => 'bg-pink-600'],
                        ['id' => 'dispersiones', 'label' => 'Dispersión', 'badge' => $totalDisp, 'color' => 'bg-emerald-600'],
                        ['id' => 'cronologia', 'label' => 'Cronología', 'badge' => 0, 'color' => 'bg-slate-500']
                    ];
                @endphp
                @foreach($tabs as $tb)
                <button @click="activeTab = '{{ $tb['id'] }}'" 
                    :class="{ 'bg-white text-[#0b57d0] shadow-sm font-bold': activeTab === '{{ $tb['id'] }}', 'text-slate-500 hover:text-slate-800 hover:bg-white/50': activeTab !== '{{ $tb['id'] }}' }" 
                    class="flex items-center gap-1.5 whitespace-nowrap px-4 py-2 rounded-xl text-xs font-semibold tracking-wide transition duration-150 shrink-0">
                    {{ $tb['label'] }}
                    @if($tb['badge'] > 0)
                        <span class="inline-flex items-center px-1.5 py-0.25 rounded-full text-[9px] font-bold text-white {{ $tb['color'] }}">
                            {{ $tb['badge'] }}
                        </span>
                    @endif
                </button>
                @endforeach
            </div>

            <!-- ============================== -->
            <!-- TAB: NÚCLEO FAMILIAR            -->
            <!-- ============================== -->
            <div x-show="activeTab === 'nucleo'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Integrantes del Núcleo Familiar</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalDeps }} familiar(es) cotizante(s)/dependiente(s)</p>
                        </div>
                        
                        <div class="flex items-center gap-3.5 w-full sm:w-auto">
                            <!-- Input de Búsqueda Predictiva -->
                            <div class="relative w-full sm:w-60">
                                <input type="text" x-model="searchDep" placeholder="Buscar familiar por nombre/cédula..." class="w-full rounded-xl border border-slate-250 bg-white py-1.5 pl-8 pr-3 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
 
                            <button @click="addDepModal = true" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-xs font-bold text-white transition active:scale-95 shrink-0"
                                style="background: linear-gradient(135deg, #0b57d0 0%, #1a73e8 100%);">
                                <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Agregar Familiar
                            </button>
                        </div>
                    </div>
                    
                    @if($totalDeps > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-xs">
                                <thead class="bg-slate-50 font-bold text-slate-500 text-[10px] uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left">Familiar / Beneficiario</th>
                                        <th class="px-6 py-3.5 text-left">Relación</th>
                                        <th class="px-6 py-3.5 text-left">Edad</th>
                                        <th class="px-6 py-3.5 text-left">Seguridad Social</th>
                                        <th class="px-6 py-3.5 text-left">Estado</th>
                                        <th class="px-6 py-3.5 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($titular->dependientes as $dep)
                                        <tr x-show="searchDep === '' || '{{ strtolower($dep->nombre_completo) }}'.includes(searchDep.toLowerCase()) || '{{ $dep->cedula }}'.includes(searchDep) || '{{ $dep->nss }}'.includes(searchDep)"
                                            class="hover:bg-slate-50/65 transition duration-150 align-middle">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8.5 w-8.5 rounded-full bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-xs uppercase shrink-0">
                                                        {{ substr($dep->nombres, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-xs font-bold text-slate-800 block">{{ $dep->nombre_completo }}</span>
                                                        <span class="text-[9px] text-slate-400 block mt-0.5">{{ $dep->tipo_dependiente }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-slate-600">{{ $dep->parentesco->descripcion }}</td>
                                            <td class="px-6 py-4 text-slate-600">{{ $dep->edad }} años</td>
                                            <td class="px-6 py-4">
                                                <span class="font-mono text-slate-600 block">Ced: {{ $dep->cedula ?? 'N/A' }}</span>
                                                <span class="font-mono text-[9px] text-slate-400 block mt-0.5">NSS: {{ $dep->nss ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ 
                                                    $dep->estado_afiliacion === 'OK' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'
                                                }}">
                                                    {{ $dep->estado_afiliacion === 'OK' ? 'Activo' : 'Pendiente' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('ars.dependientes.edit', $dep->id) }}" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 inline-flex transition" title="Editar dependiente">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No hay dependientes registrados en este núcleo familiar.</p>
                            <button @click="addDepModal = true" class="mt-3 inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Agregar Primer Familiar
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: AUTORIZACIONES            -->
            <!-- ============================== -->
            <div x-show="activeTab === 'autorizaciones'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Historial de Autorizaciones</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalAutos }} solicitud(es) médica(s) registrada(s)</p>
                        </div>
                        
                        <div class="flex items-center gap-3.5 w-full sm:w-auto">
                            <!-- Input de Búsqueda Predictiva -->
                            <div class="relative w-full sm:w-60">
                                <input type="text" x-model="searchAut" placeholder="Buscar por número/PSS/procedimiento..." class="w-full rounded-xl border border-slate-250 bg-white py-1.5 pl-8 pr-3 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400">
                                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
 
                            <a href="{{ route('ars.autorizaciones_medicas.create') }}?afiliado_id={{ $titular->id }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-xs font-bold text-white transition active:scale-95 shrink-0"
                                style="background: linear-gradient(135deg, #008b8b 0%, #00a8a8 100%);">
                                <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Nueva Autorización
                            </a>
                        </div>
                    </div>
                    
                    @if($autorizaciones->count() > 0)
                        <div class="divide-y divide-slate-150/60 max-h-[550px] overflow-y-auto">
                            @foreach($autorizaciones as $aut)
                                <div x-show="searchAut === '' || '{{ strtolower($aut->numero_autorizacion) }}'.includes(searchAut.toLowerCase()) || '{{ strtolower($aut->pss->nombre ?? '') }}'.includes(searchAut.toLowerCase()) || '{{ strtolower($aut->procedimiento ?? '') }}'.includes(searchAut.toLowerCase()) || '{{ strtolower($aut->diagnostico ?? '') }}'.includes(searchAut.toLowerCase())"
                                     class="p-5 hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-800 font-mono">#{{ $aut->numero_autorizacion }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ 
                                                    $aut->estado === 'Aprobada' || $aut->estado === 'Aprobado' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : (
                                                    $aut->estado === 'Rechazada' || $aut->estado === 'Rechazado' ? 'bg-rose-50 text-rose-700 border border-rose-100' : 'bg-amber-50 text-amber-700 border border-amber-100')
                                                }}">{{ $aut->estado }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600">{{ $aut->prioridad }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium">
                                                <span class="text-slate-800 font-bold">{{ $aut->pss->nombre ?? 'N/A' }}</span>
                                                @if($aut->servicio) &middot; {{ $aut->servicio->descripcion }} @endif
                                            </p>
                                            <p class="text-xs text-slate-400 italic line-clamp-1">Diagnóstico: {{ $aut->diagnostico }}</p>
                                            <div class="flex items-center gap-4 text-[10px] text-slate-400 font-mono pt-1">
                                                <span>Solicitado: <strong class="text-slate-600">${{ number_format($aut->monto_solicitado, 2) }}</strong></span>
                                                <span>Aprobado: <strong class="text-emerald-700">${{ number_format($aut->monto_contratado, 2) }}</strong></span>
                                                <span>Fecha: {{ $aut->fecha_solicitud->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('ars.autorizaciones.show', $aut->id) }}" 
                                            class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50/50 shadow-sm transition shrink-0" 
                                            title="Ver Expediente de Autorización">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">Este afiliado no registra solicitudes de autorizaciones médicas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: RECLAMACIONES             -->
            <!-- ============================== -->
            <div x-show="activeTab === 'reclamaciones'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Reclamaciones y Obligaciones (CXP)</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalReclamos }} reclamaciones recibidas para este afiliado</p>
                        </div>
                        
                        <!-- Input de Búsqueda Predictiva -->
                        <div class="relative w-full sm:w-60">
                            <input type="text" x-model="searchClaim" placeholder="Buscar por número/PSS/factura..." class="w-full rounded-xl border border-slate-250 bg-white py-1.5 pl-8 pr-3 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    
                    @if($titular->claims->count() > 0)
                        <div class="divide-y divide-slate-150/60 max-h-[550px] overflow-y-auto">
                            @foreach($titular->claims as $claim)
                                <div x-show="searchClaim === '' || '{{ strtolower($claim->claim_number) }}'.includes(searchClaim.toLowerCase()) || '{{ strtolower($claim->pss->nombre ?? '') }}'.includes(searchClaim.toLowerCase()) || '{{ strtolower($claim->invoice_number ?? '') }}'.includes(searchClaim.toLowerCase()) || '{{ strtolower($claim->ncf ?? '') }}'.includes(searchClaim.toLowerCase())"
                                     class="p-5 hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-800 font-mono">#{{ $claim->claim_number }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ 
                                                    $claim->status === 'Pagada' || $claim->status === 'Cerrada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-100'
                                                }}">{{ $claim->status }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium">
                                                <span class="text-slate-800 font-bold">{{ $claim->pss->nombre ?? 'N/A' }}</span>
                                                @if($claim->authorization) &middot; Autorización origen #{{ $claim->authorization->numero_autorizacion }} @endif
                                            </p>
                                            <div class="flex items-center gap-4 text-[10px] text-slate-400 font-mono pt-1">
                                                <span>Sometido: <strong class="text-slate-650">${{ number_format($claim->claimed_amount, 2) }}</strong></span>
                                                <span>Aprobado: <strong class="text-emerald-700">${{ number_format($claim->approved_amount, 2) }}</strong></span>
                                                @if($claim->objected_amount > 0)
                                                    <span>Objetado (Glosa): <strong class="text-rose-600">${{ number_format($claim->objected_amount, 2) }}</strong></span>
                                                @endif
                                                <span>Servicio: {{ $claim->service_date->format('d/m/Y') }}</span>
                                            </div>
                                            @if($claim->invoice_number)
                                                <p class="text-[9px] text-slate-400 font-mono">Factura PSS: {{ $claim->invoice_number }} @if($claim->ncf) | NCF: {{ $claim->ncf }} @endif</p>
                                            @endif
                                        </div>
                                        <a href="{{ route('ars.reclamaciones.show', $claim->id) }}" 
                                            class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50/50 shadow-sm transition shrink-0" 
                                            title="Ver Detalle de Reclamación">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No se registran reclamaciones de salud para esta póliza.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: NOVEDADES                 -->
            <!-- ============================== -->
            <div x-show="activeTab === 'novedades'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Historial de Novedades</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $novedades->count() }} novedad(es) tramitada(s) en JCE / TSS</p>
                        </div>
                        
                        <!-- Input de Búsqueda Predictiva -->
                        <div class="relative w-full sm:w-60">
                            <input type="text" x-model="searchNov" placeholder="Buscar novedad por tipo/estado..." class="w-full rounded-xl border border-slate-250 bg-white py-1.5 pl-8 pr-3 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    
                    @if($novedades->count() > 0)
                        <div class="divide-y divide-slate-150/60 max-h-[500px] overflow-y-auto">
                            @foreach($novedades as $nov)
                                <div x-show="searchNov === '' || '{{ strtolower($nov->tipoNovedad->descripcion ?? '') }}'.includes(searchNov.toLowerCase()) || '{{ strtolower($nov->estado ?? '') }}'.includes(searchNov.toLowerCase())"
                                     class="p-5 hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-bold text-slate-800">{{ $nov->tipoNovedad->descripcion }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ 
                                                $nov->estado === 'OK' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100'
                                            }}">{{ $nov->estado }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $nov->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed">{{ $nov->motivo_estado ?? 'Tramitada y validada por la TSS.' }}</p>
                                    
                                    @if($nov->campos_modificados && is_array($nov->campos_modificados))
                                        <div class="mt-3 p-3 bg-slate-50 rounded-xl flex flex-wrap gap-2 text-[10px] border border-slate-100">
                                            @foreach($nov->campos_modificados as $c => $v)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-500 font-mono">
                                                    {{ $c }}: <strong class="text-slate-800 ml-1">{{ $v }}</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No se han registrado modificaciones o novedades en esta cuenta.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: DOCUMENTOS                -->
            <!-- ============================== -->
            <div x-show="activeTab === 'documentos'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Expediente de Documentos Adjuntos</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $documentos->count() }} archivo(s) digitalizado(s) de soporte</p>
                        </div>
                        
                        <!-- Input de Búsqueda Predictiva -->
                        <div class="relative w-full sm:w-60">
                            <input type="text" x-model="searchDoc" placeholder="Buscar documento por nombre/tipo..." class="w-full rounded-xl border border-slate-250 bg-white py-1.5 pl-8 pr-3 text-[11px] text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 placeholder:text-slate-400">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                    
                    @if($documentos->count() > 0)
                        <div class="divide-y divide-slate-150/60 bg-white">
                            @foreach($documentos as $doc)
                                <div x-show="searchDoc === '' || '{{ strtolower($doc->nombre_archivo) }}'.includes(searchDoc.toLowerCase()) || '{{ strtolower($doc->tipo_documento ?? '') }}'.includes(searchDoc.toLowerCase())"
                                     class="p-5 flex items-center justify-between hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-800 block">{{ $doc->nombre_archivo }}</span>
                                            <p class="text-xs text-slate-450 mt-0.5">
                                                <span class="font-bold text-slate-500">{{ $doc->tipo_documento }}</span>
                                                &middot; Cargado el {{ $doc->fecha_carga->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="#" class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 bg-white hover:bg-blue-50/50 shadow-sm transition" title="Descargar Documento">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No se han cargado actas o documentos de soporte clínico.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: NOTIFICACIONES CAPITACIÓN -->
            <!-- ============================== -->
            <div x-show="activeTab === 'notificaciones'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Notificaciones de Capitación TSS</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalNotif }} registro(s) devengado(s) e individualizado(s)</p>
                    </div>
                    
                    @if($totalNotif > 0)
                        <div class="divide-y divide-slate-150/60 bg-white">
                            @foreach($titular->capitationNotifications as $notif)
                                <div class="p-5 hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-bold text-slate-800">Notif. #{{ $notif->notification_number }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ 
                                                    $notif->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-100'
                                                }}">{{ $notif->status }}</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium">Período: {{ $notif->period }} &middot; Tipo: {{ $notif->individualization_type }}</p>
                                        </div>
                                        <span class="text-sm font-black text-emerald-600 font-mono">DOP {{ number_format($notif->capitation_amount, 2) }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 mt-3 text-[10px] text-slate-450 border-t border-slate-100 pt-3">
                                        <div>
                                            <span class="block">Fecha Notificado:</span>
                                            <span class="font-semibold text-slate-700">{{ $notif->notified_at ? $notif->notified_at->format('d/m/Y') : '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="block">Confirmado JCE:</span>
                                            <span class="font-semibold text-slate-700">{{ $notif->confirmed_at ? $notif->confirmed_at->format('d/m/Y') : '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="block">Estado Trámite:</span>
                                            <span class="font-bold text-emerald-700">Conciliado OK</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No se registran pagos de cápitas para este número de seguridad social.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: DISPERSIONES              -->
            <!-- ============================== -->
            <div x-show="activeTab === 'dispersiones'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Cortes y Dispersión de Cápita</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalDisp }} cápitas dispersadas en el libro diario de la ARS</p>
                    </div>
                    
                    @if($totalDisp > 0)
                        <div class="divide-y divide-slate-150/60 bg-white">
                            @foreach($titular->dispersionCutDetails as $det)
                                <div class="p-5 hover:bg-slate-50/70 transition duration-150">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-bold text-slate-800">Corte ID: #{{ $det->cut->cut_number }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-100">Dispersado</span>
                                            </div>
                                            <p class="text-xs text-slate-500 font-medium">Período Fiscal: {{ $det->cut->period }} &middot; Lote: {{ ucfirst($det->cut->cut_type) }}</p>
                                        </div>
                                        <span class="text-sm font-black text-emerald-600 font-mono">DOP {{ number_format($det->amount, 2) }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 mt-3 text-[10px] text-slate-450 border-t border-slate-100 pt-3">
                                        <div>
                                            <span class="block">Cierre de Lote:</span>
                                            <span class="font-semibold text-slate-700">{{ $det->cut->closed_at ? $det->cut->closed_at->format('d/m/Y') : '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="block">Asiento Contable:</span>
                                            <span class="font-semibold text-blue-700 underline font-mono">Ver Diarios</span>
                                        </div>
                                        <div>
                                            <span class="block">Monto Distribuido:</span>
                                            <span class="font-bold text-emerald-700">DOP {{ number_format($det->amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p class="mt-3 text-sm text-slate-400 font-medium">No se registran dispersiones de cápitas para esta cuenta contable.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================== -->
            <!-- TAB: CRONOLOGÍA (TIMELINE)     -->
            <!-- ============================== -->
            <div x-show="activeTab === 'cronologia'" 
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 -translate-y-2" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 p-6 space-y-6">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight uppercase">Cronología y Vida del Afiliado</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Seguimiento de hitos, afiliación, novedades y consumos de servicios</p>
                    </div>

                    <div class="relative pl-6 border-l border-slate-150 space-y-6 ml-2 text-xs">
                        <!-- Hito: Afiliación Inicial -->
                        <div class="relative">
                            <span class="absolute -left-[29px] top-1.5 w-4 h-4 rounded-full border-2 bg-white border-blue-600 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            </span>
                            <div class="flex justify-between items-center font-bold text-slate-850">
                                <span>Ingreso Inicial al Sistema</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $titular->fecha_afiliacion ? $titular->fecha_afiliacion->format('d/m/Y') : $titular->created_at->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">Se formalizó el ingreso de <strong>{{ $titular->nombre_completo }}</strong> a la ARS con régimen {{ $titular->regimen_actual ?? 'Contributivo' }}.</p>
                        </div>

                        <!-- Hitos: Dependientes Vinculados -->
                        @foreach($titular->dependientes as $dep)
                        <div class="relative">
                            <span class="absolute -left-[29px] top-1.5 w-4 h-4 rounded-full border-2 bg-white border-indigo-500 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            </span>
                            <div class="flex justify-between items-center font-bold text-slate-850">
                                <span>Dependiente Incluido: {{ $dep->parentesco->descripcion }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $dep->created_at ? $dep->created_at->format('d/m/Y') : '—' }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">Se incluyó a <strong>{{ $dep->nombre_completo }}</strong> como beneficiario de salud del afiliado cotizante.</p>
                        </div>
                        @endforeach

                        <!-- Hitos: Últimas Autorizaciones -->
                        @foreach($autorizaciones->take(3) as $aut)
                        <div class="relative">
                            <span class="absolute -left-[29px] top-1.5 w-4 h-4 rounded-full border-2 bg-white flex items-center justify-center
                                {{ $aut->estado === 'Aprobada' || $aut->estado === 'Aprobado' ? 'border-emerald-500' : ($aut->estado === 'Rechazada' || $aut->estado === 'Rechazado' ? 'border-rose-500' : 'border-amber-500') }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $aut->estado === 'Aprobada' || $aut->estado === 'Aprobado' ? 'bg-emerald-500' : ($aut->estado === 'Rechazada' || $aut->estado === 'Rechazado' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                            </span>
                            <div class="flex justify-between items-center font-bold text-slate-850">
                                <span>Autorización Médica #{{ $aut->numero_autorizacion }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $aut->fecha_solicitud->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">
                                Solicitud de <strong>{{ $aut->procedimiento ?? 'Consulta general' }}</strong> vía {{ $aut->channel ?? 'portal' }}. 
                                Estatus: <strong class="uppercase text-[9px]">{{ $aut->estado }}</strong> @if($aut->motivo_estado) ({{ $aut->motivo_estado }}) @endif
                            </p>
                        </div>
                        @endforeach

                        <!-- Hitos: Últimas Reclamaciones -->
                        @foreach($titular->claims->take(3) as $claim)
                        <div class="relative">
                            <span class="absolute -left-[29px] top-1.5 w-4 h-4 rounded-full border-2 bg-white flex items-center justify-center
                                {{ $claim->status === 'Pagada' || $claim->status === 'Cerrada' ? 'border-emerald-500' : 'border-blue-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $claim->status === 'Pagada' || $claim->status === 'Cerrada' ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                            </span>
                            <div class="flex justify-between items-center font-bold text-slate-850">
                                <span>Reclamación Sometida #{{ $claim->claim_number }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $claim->service_date->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-0.5 leading-normal">
                                Reclamación por DOP {{ number_format($claim->claimed_amount, 2) }} de la PSS <strong>{{ $claim->pss->nombre ?? 'N/A' }}</strong>. 
                                Estado: <strong class="text-slate-700">{{ $claim->status }}</strong> (Aprobado: DOP {{ number_format($claim->approved_amount, 2) }}).
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Registrar Dependiente -->
    <div x-show="addDepModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-show="addDepModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-4" x-show="addDepModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Agregar Dependiente al Núcleo</h3>
                    <button @click="addDepModal = false" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('ars.dependientes.store', $titular->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_tipo_id" class="block text-xs font-semibold text-slate-500 mb-1">Tipo Identificación</label>
                            <select name="tipo_identificacion_id" id="dep_tipo_id" required class="block w-full rounded-xl border border-slate-300 py-2 px-3 text-xs text-slate-800 bg-white">
                                @foreach($tiposIdentificacion as $ti)
                                    <option value="{{ $ti->id }}">{{ $ti->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dep_parentesco_id" class="block text-xs font-semibold text-slate-500 mb-1">Parentesco</label>
                            <select name="parentesco_id" id="dep_parentesco_id" required class="block w-full rounded-xl border border-slate-300 py-2 px-3 text-xs text-slate-800 bg-white">
                                @foreach($parentescos as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_cedula" class="block text-xs font-semibold text-slate-500 mb-1">Cédula (Si aplica)</label>
                            <input type="text" name="cedula" id="dep_cedula" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 font-mono" placeholder="00100000000">
                        </div>
                        <div>
                            <label for="dep_nss" class="block text-xs font-semibold text-slate-500 mb-1">NSS</label>
                            <input type="text" name="nss" id="dep_nss" class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800 font-mono" placeholder="20000000000">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_nombres" class="block text-xs font-semibold text-slate-500 mb-1">Nombres</label>
                            <input type="text" name="nombres" id="dep_nombres" required class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800" placeholder="Nombres">
                        </div>
                        <div>
                            <label for="dep_apellidos" class="block text-xs font-semibold text-slate-500 mb-1">Apellidos</label>
                            <input type="text" name="apellidos" id="dep_apellidos" required class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800" placeholder="Apellidos">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_fecha_nacimiento" class="block text-xs font-semibold text-slate-500 mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="dep_fecha_nacimiento" required class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sexo</label>
                            <div class="flex items-center space-x-4 mt-1.5">
                                <label class="inline-flex items-center text-xs text-slate-700">
                                    <input type="radio" name="sexo" value="M" checked class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="ml-1.5">M</span>
                                </label>
                                <label class="inline-flex items-center text-xs text-slate-700">
                                    <input type="radio" name="sexo" value="F" class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="ml-1.5">F</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="dep_tipo" class="block text-xs font-semibold text-slate-500 mb-1">Tipo de Dependiente</label>
                            <select name="tipo_dependiente" id="dep_tipo" required class="block w-full rounded-xl border border-slate-300 py-2 px-3 text-xs text-slate-800 bg-white">
                                <option value="Directo">Directo</option>
                                <option value="Adicional">Adicional</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 mt-4">
                            <label class="inline-flex items-center text-xs font-medium text-slate-700">
                                <input type="checkbox" name="estudiante" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600">
                                <span class="ml-2">Es Estudiante</span>
                            </label>
                            <br>
                            <label class="inline-flex items-center text-xs font-medium text-slate-700">
                                <input type="checkbox" name="discapacitado" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600">
                                <span class="ml-2">Posee Discapacidad</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="documento_simulado" class="block text-xs font-semibold text-slate-500 mb-1">Documento Soporte</label>
                        <input type="file" name="documento_simulado" id="documento_simulado" class="block w-full text-xs text-slate-500 border border-slate-300 rounded-xl cursor-pointer bg-slate-50 p-2">
                    </div>
                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="addDepModal = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                            Validar & Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection