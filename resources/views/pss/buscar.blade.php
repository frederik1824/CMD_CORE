@extends('layouts.pss')

@section('title', 'Consultar Afiliado')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="border-b border-slate-200 pb-5">
        <h2 class="text-2xl font-bold leading-7 text-slate-800 tracking-tight">Verificación de Cobertura</h2>
        <p class="mt-1 text-sm text-slate-500 font-medium">Ingresa el número de identificación (Cédula o NSS) para validar el estado de cobertura del paciente.</p>
    </div>

    <!-- Buscador -->
    <div class="bg-white p-6 shadow-sm rounded-3xl border border-slate-200">
        <form action="{{ route('pss.buscar') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="identificacion" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 font-semibold">Identificación del Paciente</label>
                <input type="text" name="identificacion" id="identificacion" required value="{{ $identificacion }}" class="block w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs font-mono text-slate-800 focus:outline-none" placeholder="Cédula o NSS del paciente...">
            </div>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-2xl shadow-md text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 transition">
                Consultar Cobertura
            </button>
        </form>
    </div>

    <!-- Alerta Error -->
    @if($error)
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl flex items-start space-x-3 shadow-sm animate-pulse">
            <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <div>
                <p class="font-bold">Error de Consulta:</p>
                <p class="mt-0.5 leading-relaxed">{{ $error }}</p>
            </div>
        </div>
    @endif

    <!-- Resultado si es encontrado -->
    @if($afiliado)
        <div class="bg-white shadow-sm rounded-3xl border border-slate-200 overflow-hidden hover:shadow-md transition duration-300">
            <!-- Header Ficha -->
            <div class="p-6 bg-gradient-to-tr from-teal-900 to-teal-850 text-white flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center font-bold text-lg uppercase border border-white/20">
                        {{ substr($afiliado->nombres, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold">{{ $afiliado->nombre_completo }}</h3>
                        <span class="text-[10px] text-teal-300 font-mono">Tipo: <span class="capitalize">{{ $afiliadoType }}</span></span>
                    </div>
                </div>
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide border {{ 
                    $afiliado->estado_afiliacion === 'OK' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30' : 'bg-rose-500/20 text-rose-300 border-rose-400/30'
                }}">
                    {{ $afiliado->estado_afiliacion === 'OK' ? 'COBERTURA ACTIVA' : 'SIN COBERTURA / INACTIVO' }}
                </span>
            </div>

            <!-- Datos Ficha -->
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-slate-100">
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider block">Identificación JCE</span>
                    <span class="text-sm font-bold text-slate-700 font-mono">{{ $afiliado->cedula ?? 'N/A' }}</span>
                </div>
                
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider block">Número NSS</span>
                    <span class="text-sm font-bold text-slate-700 font-mono">{{ $afiliado->nss ?? 'N/A' }}</span>
                </div>

                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider block">Datos Demográficos</span>
                    <span class="text-slate-600 block">Edad: {{ $afiliado->edad }} años ({{ $afiliado->sexo === 'M' ? 'Masc' : 'Fem' }})</span>
                    <span class="text-slate-600 block">Provincia: {{ $afiliado->provincia ?? 'N/A' }}</span>
                </div>

                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider block">Plan de Salud</span>
                    <span class="text-slate-600 font-semibold block">{{ $afiliado->regimen_actual ?? 'Régimen Contributivo' }}</span>
                </div>
            </div>

            <!-- Footer Ficha - Acción -->
            @if($afiliado->estado_afiliacion === 'OK')
                <div class="p-6 bg-slate-50 flex justify-end">
                    <a href="{{ route('pss.autorizaciones.nueva', ['afiliado_id' => $afiliado->id, 'afiliado_type' => $afiliadoType]) }}" class="inline-flex items-center px-5 py-3 border border-transparent rounded-2xl shadow-md text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 active:scale-95 transition-all">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Solicitar Autorización Médica
                    </a>
                </div>
            @else
                <div class="p-6 bg-rose-50/50 border-t border-slate-100 text-xs text-rose-700 leading-normal">
                    <span class="font-bold">Advertencia de Cobertura:</span> No es posible tramitar autorizaciones médicas para este afiliado debido a que su estado en el padrón de afiliados de la ARS es INACTIVO/RECHAZADO. Razón: <span class="font-semibold">{{ $afiliado->motivo_estado }}</span>.
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
