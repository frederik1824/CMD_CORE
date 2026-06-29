@extends('layouts.ars')

@section('title', 'Auditar Autorización')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
        <div class="flex items-center space-x-4">
            <a href="{{ route('ars.autorizaciones.index') }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight">Expediente de Auditoría Médica</h2>
                <p class="mt-1 text-sm text-slate-500">Número de Solicitud: <span class="font-mono font-bold" style="color:#0b57d0;">{{ $autorizacion->numero_autorizacion }}</span></p>
            </div>
        </div>
        {{-- Botón Imprimir --}}
        <a href="{{ route('ars.autorizaciones.imprimir', $autorizacion->id) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white shadow-md transition-all hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0"
            style="background: linear-gradient(135deg, #0b57d0, #1a73e8);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimir / Firmar
        </a>
    </div>


    <!-- Contenido Principal -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Columna Izquierda: Información Solicitud y Paciente -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Detalles de Solicitud -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detalles de la Solicitud</h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 gap-6 sm:grid-cols-2 text-xs">
                    <div>
                        <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Paciente (Afiliado)</span>
                        @if($afiliado)
                            <h4 class="text-sm font-bold text-slate-800">{{ $afiliado->nombre_completo }}</h4>
                            <span class="text-slate-400 block font-mono mt-0.5">Ced: {{ $afiliado->cedula }} | NSS: {{ $afiliado->nss }}</span>
                            <span class="text-slate-400 block mt-0.5">Plan/Régimen: {{ $afiliado->regimen_actual }} | Tipo: <span class="capitalize font-semibold text-slate-600">{{ $autorizacion->afiliado_type }}</span></span>
                        @else
                            <span class="text-slate-400">Desconocido</span>
                        @endif
                    </div>

                    <div>
                        <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Prestadora de Salud (PSS)</span>
                        <h4 class="text-sm font-bold text-slate-800">{{ $autorizacion->pss->nombre }}</h4>
                        <span class="text-slate-400 block font-mono mt-0.5">RNC: {{ $autorizacion->pss->rnc }}</span>
                        <span class="text-slate-400 block mt-0.5">Dirección: {{ $autorizacion->pss->direccion }}</span>
                    </div>

                    <div class="sm:col-span-2 border-t border-slate-100 pt-4">
                        <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Servicio y Procedimiento</span>
                        <h4 class="text-sm font-bold text-slate-800">{{ optional($autorizacion->servicio)->descripcion ?? optional($autorizacion->servicioPdss)->coverage_description ?? $autorizacion->procedimiento ?? '—' }}</h4>
                        <p class="text-slate-500 mt-1">Procedimiento: {{ $autorizacion->procedimiento ?? 'Consulta Médica General' }}</p>
                        <p class="text-slate-500 mt-1">Diagnóstico (CIE-10): <span class="font-semibold text-slate-700">{{ $autorizacion->diagnostico }}</span></p>
                        <p class="text-slate-500 mt-0.5">Médico Solicitante: <span class="font-semibold text-slate-700">{{ $autorizacion->medico_solicitante }}</span></p>
                    </div>

                    <div class="sm:col-span-2 border-t border-slate-100 pt-4 grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Monto Solicitado</span>
                            <span class="text-lg font-bold text-slate-800">${{ number_format($autorizacion->monto_solicitado, 2) }}</span>
                        </div>
                        <div>
                            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Monto Contratado (Tarifado)</span>
                            <span class="text-lg font-bold text-emerald-600">${{ number_format($autorizacion->monto_contratado, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Reclamación y CXP -->
            @if($autorizacion->claims->isNotEmpty())
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Historial de Reclamación y CXP</h3>
                    </div>
                    <div class="p-6 divide-y divide-slate-100 text-xs space-y-3">
                        @foreach($autorizacion->claims as $claim)
                            <div class="py-3 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Reclamación</span>
                                    <a href="{{ route('ars.reclamaciones.show', $claim->id) }}" class="font-bold text-[#041e49] underline block mt-0.5">{{ $claim->claim_number }}</a>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">NCF / Factura</span>
                                    <span class="font-semibold text-slate-700 block mt-0.5">{{ $claim->invoice_number }} ({{ $claim->ncf }})</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Montos</span>
                                    <span class="font-semibold text-slate-700 block mt-0.5">Sometido: DOP {{ number_format($claim->claimed_amount, 2) }}</span>
                                    <span class="font-semibold text-teal-600 block">Aprobado: DOP {{ number_format($claim->approved_amount, 2) }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Estado Reclamación</span>
                                    <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-blue-50 text-blue-800 border border-blue-150">
                                        {{ $claim->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach

                        @foreach($autorizacion->payables as $payable)
                            <div class="py-3 grid grid-cols-1 md:grid-cols-4 gap-4 bg-slate-50/30 p-3 rounded-xl border border-slate-100 mt-2">
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">No. Cuenta (CXP)</span>
                                    <span class="font-bold text-slate-800 block mt-0.5 font-mono">{{ $payable->payable_number }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Monto Neto</span>
                                    <span class="font-bold text-slate-800 block mt-0.5 font-mono">DOP {{ number_format($payable->net_amount, 2) }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Fecha Estimada</span>
                                    <span class="font-semibold text-slate-600 block mt-0.5">{{ $payable->due_date ? $payable->due_date->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-400 uppercase tracking-wider block text-[8px]">Estado Obligación</span>
                                    <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide bg-emerald-50 text-emerald-800 border border-emerald-150">
                                        {{ $payable->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Historial Clínico Reciente del Paciente -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Historial Clínico Reciente del Paciente</h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
                    @forelse($historialClinico as $hist)
                        <div class="p-6 flex items-center justify-between text-xs hover:bg-slate-50 transition">
                            <div>
                                <h4 class="font-bold text-slate-800">{{ optional($hist->servicio)->descripcion ?? optional($hist->servicioPdss)->coverage_description ?? $hist->procedimiento ?? '—' }}</h4>
                                <p class="text-slate-400 mt-0.5">Diagnóstico: {{ $hist->diagnostico }} • Solicitado por: {{ $hist->medico_solicitante }}</p>
                                <span class="text-[10px] text-slate-400 font-mono mt-1 block">Fecha: {{ $hist->fecha_solicitud->format('d/m/Y') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-slate-700 block">${{ number_format($hist->monto_solicitado, 2) }}</span>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ 
                                    $hist->estado === 'Aprobada' ? 'bg-emerald-50 text-emerald-700' : (
                                    $hist->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')
                                }}">{{ $hist->estado }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-sm">No existen solicitudes previas registradas para este afiliado.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Consola de Decisión y Adjuntos -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Consola de Decisión del Auditor -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-900 text-white flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Consola del Auditor Médico</h3>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ 
                        $autorizacion->estado === 'Aprobada' ? 'bg-emerald-500/20 text-emerald-200' : (
                        $autorizacion->estado === 'Rechazada' ? 'bg-rose-500/20 text-rose-200' : (
                        $autorizacion->estado === 'Auditoría' ? 'bg-purple-500/20 text-purple-200' : 'bg-amber-500/20 text-amber-200'))
                    }}">{{ $autorizacion->estado }}</span>
                </div>
                
                <div class="p-5 space-y-4">
                    <!-- Si la solicitud ya está evaluada (no en re-auditoría) -->
                    @if(in_array($autorizacion->estado, ['Aprobada', 'Rechazada']) && !request()->has('auditar_de_nuevo'))
                        <!-- Vista de resultado aprobado/rechazado -->
                        <div class="rounded-xl p-4 {{ $autorizacion->estado === 'Aprobada' ? 'bg-emerald-50 border border-emerald-100' : 'bg-rose-50 border border-rose-100' }} space-y-3 text-xs">
                            <div class="flex items-center gap-2">
                                @if($autorizacion->estado === 'Aprobada')
                                    <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <span class="font-bold text-emerald-800 text-sm">Solicitud Aprobada</span>
                                @else
                                    <div class="w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <span class="font-bold text-rose-800 text-sm">Solicitud Rechazada</span>
                                @endif
                            </div>
                            <div>
                                <span class="font-bold text-slate-500 uppercase tracking-wider block mb-1 text-[10px]">Justificación Clínica</span>
                                <p class="font-medium text-slate-700 leading-relaxed">{{ $autorizacion->motivo_estado }}</p>
                            </div>
                            <div class="flex justify-between pt-2 border-t {{ $autorizacion->estado === 'Aprobada' ? 'border-emerald-100' : 'border-rose-100' }}">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Monto adjudicado</span>
                                    <span class="font-bold text-slate-800 text-sm">${{ number_format($autorizacion->monto_contratado, 2) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">Auditor</span>
                                    <span class="font-semibold text-slate-600 text-[11px]">{{ $autorizacion->usuarioResponsable ? $autorizacion->usuarioResponsable->name : 'Motor de Reglas' }}</span>
                                </div>
                            </div>
                        </div>

                        @if(Auth::user()->role === 'Auditor Médico' || Auth::user()->role === 'Administrador ARS')
                            <a href="?auditar_de_nuevo=1" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border-2 border-dashed border-slate-300 rounded-xl text-xs font-bold text-slate-500 hover:border-slate-400 hover:text-slate-700 hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Re-auditar Solicitud
                            </a>
                        @endif

                    @else
                        {{-- ===== FORMULARIO DE DECISIÓN MÉDICA ===== --}}
                        
                        {{-- Banner indicador de estado actual --}}
                        @if($autorizacion->estado === 'Auditoría')
                        <div class="flex items-center gap-2 p-3 bg-purple-50 border border-purple-100 rounded-xl">
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse flex-shrink-0"></div>
                            <span class="text-[11px] font-semibold text-purple-700">Pendiente de revisión clínica por auditor médico</span>
                        </div>
                        @endif

                        <form action="{{ route('ars.autorizaciones.decision', $autorizacion->id) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            {{-- Sección de Botones Rápidos de Decisión --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Decisión Médica</label>
                                <div class="grid grid-cols-2 gap-2 mb-3" x-data="{ selected: 'Aprobada' }">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="decision" value="Aprobada" class="sr-only peer" @change="selected = 'Aprobada'" checked>
                                        <div class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl border-2 text-center text-[10px] font-bold transition-all
                                            border-slate-200 text-slate-400 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 hover:border-emerald-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Aprobar
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="decision" value="Rechazada" class="sr-only peer" @change="selected = 'Rechazada'">
                                        <div class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl border-2 text-center text-[10px] font-bold transition-all
                                            border-slate-200 text-slate-400 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 hover:border-rose-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Rechazar
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="decision" value="Pendiente Documento" class="sr-only peer" @change="selected = 'Pendiente Documento'">
                                        <div class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl border-2 text-center text-[10px] font-bold transition-all
                                            border-slate-200 text-slate-400 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 hover:border-amber-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Pedir Docs
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="decision" value="Auditoría" class="sr-only peer" @change="selected = 'Auditoría'">
                                        <div class="flex flex-col items-center justify-center gap-1 p-3 rounded-xl border-2 text-center text-[10px] font-bold transition-all
                                            border-slate-200 text-slate-400 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 hover:border-purple-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Mantener
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Monto Aprobado --}}
                            <div>
                                <label for="monto_contratado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Monto Aprobado <span class="normal-case font-normal text-slate-400">(DOP)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-bold">$</span>
                                    <input type="number" step="0.01" name="monto_contratado" id="monto_contratado"
                                        class="block w-full pl-7 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm font-semibold text-slate-800 bg-slate-50 transition"
                                        value="{{ $autorizacion->monto_contratado > 0 ? $autorizacion->monto_contratado : $autorizacion->monto_solicitado }}">
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">Solicitado: <span class="font-semibold">${{ number_format($autorizacion->monto_solicitado, 2) }}</span></p>
                            </div>

                            {{-- Justificación Clínica --}}
                            <div>
                                <label for="motivo_estado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Justificación Clínica <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="motivo_estado" id="motivo_estado" rows="4" required
                                    class="block w-full rounded-xl border border-slate-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none p-3 text-xs text-slate-800 bg-slate-50 transition resize-none"
                                    placeholder="Describe los motivos clínicos, normativa aplicable o criterios utilizados para tu decisión...">{{ $autorizacion->motivo_estado }}</textarea>
                            </div>

                            {{-- Botón Principal de Acción --}}
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl text-sm font-bold text-white shadow-lg transition-all duration-200 active:scale-95"
                                style="background: linear-gradient(135deg, #0b57d0 0%, #1a73e8 100%); box-shadow: 0 4px 16px rgba(11,87,208,0.35);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Guardar Decisión Médica
                            </button>

                            <p class="text-center text-[10px] text-slate-400">
                                Esta acción quedará registrada en la Línea de Tiempo y la Bitácora del sistema.
                            </p>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Documentos de Soporte Clínico -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Documentos Clínicos</h3>
                </div>
                <div class="p-6 divide-y divide-slate-100 text-xs">
                    @forelse($documentos as $doc)
                        <div class="py-3 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <svg class="w-8 h-8 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                <div>
                                    <h4 class="font-bold text-slate-800 truncate max-w-[150px]">{{ $doc->nombre_archivo }}</h4>
                                    <span class="text-[10px] text-slate-400 block font-mono">Cargado: {{ $doc->fecha_carga->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-semibold">Soporte Médico</span>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-4">No se han adjuntado documentos soporte para esta solicitud.</div>
                    @endforelse
                </div>
            </div>

            <!-- Línea de Tiempo del Trámite -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Línea de Tiempo Operativa</h3>
                </div>
                <div class="p-6 relative pl-8 border-l border-slate-200 space-y-6 ml-6 text-xs">
                    @forelse($autorizacion->timelineEvents as $event)
                        <div class="relative">
                            <span class="absolute -left-[30px] top-1 bg-white border-2 border-[#041e49] w-3.5 h-3.5 rounded-full flex items-center justify-center"></span>
                            <div class="text-[10px] text-slate-400 font-bold flex justify-between">
                                <span>{{ $event->title }}</span>
                                <span>{{ $event->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-slate-650 font-medium mt-0.5">{{ $event->description }}</p>
                            @if($event->new_status)
                                <span class="inline-block text-[9px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full mt-1 border border-teal-150">
                                    {{ $event->new_status }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-400 italic">No se registran eventos en la línea de tiempo.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
