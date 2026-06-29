@extends('layouts.core')

@section('title', 'Detalle de Reclamación')

@section('content')
<div class="space-y-6" x-data="{ decision: 'Aprobada', approvedAmount: {{ $reclamacion->claimed_amount }} }">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reclamación: {{ $reclamacion->claim_number }}</h2>
            <p class="text-xs text-slate-500 font-medium">Asociada a la autorización médica <a class="text-[#041e49] font-bold underline" href="{{ route('ars.autorizaciones.show', $reclamacion->authorization_id) }}">{{ $reclamacion->authorization->numero_autorizacion }}</a></p>
        </div>
        <a href="{{ route('ars.reclamaciones.index') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
            Volver a la bandeja
        </a>
    </div>

    <!-- Grid Detalle y Auditoría -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel de Datos (Izquierda 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tarjeta Datos Generales -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Información de la Reclamación</span>
                </h3>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 text-xs">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Prestadora (PSS)</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $reclamacion->pss->nombre }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Afiliado Paciente</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $afiliado->nombres }} {{ $afiliado->primer_apellido ?? $afiliado->apellidos }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">No. Contrato / Póliza</span>
                        <span class="font-bold text-slate-700 block mt-0.5">{{ $reclamacion->authorization->numero_contrato ?? 'POL-2026-000001' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fecha Autorización</span>
                        <span class="font-semibold text-slate-600 block mt-0.5">{{ $reclamacion->authorization->created_at->format('d/m/Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fecha Prestación</span>
                        <span class="font-semibold text-slate-600 block mt-0.5">{{ $reclamacion->service_date->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Fecha Recepción</span>
                        <span class="font-semibold text-slate-600 block mt-0.5">{{ $reclamacion->received_at->format('d/m/Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Número de Factura</span>
                        <span class="font-bold font-mono text-slate-700 block mt-0.5">{{ $reclamacion->invoice_number }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">NCF</span>
                        <span class="font-bold font-mono text-slate-700 block mt-0.5">{{ $reclamacion->ncf }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Estado Actual</span>
                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-[10px] font-bold text-slate-700 mt-1 border border-slate-200">
                            {{ $reclamacion->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detalle de Prestaciones Médicas Reclamadas -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Procedimientos y Tarifas Sometidas</span>
                </h3>

                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 font-bold text-slate-400">
                            <tr>
                                <th class="px-4 py-3 text-left w-24 text-[9px] uppercase tracking-wider">Código</th>
                                <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Procedimiento / Cobertura</th>
                                <th class="px-4 py-3 text-right w-36 text-[9px] uppercase tracking-wider">Monto Autorizado</th>
                                <th class="px-4 py-3 text-right w-36 text-[9px] uppercase tracking-wider">Monto Reclamado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-[#041e49]">
                                    {{ $reclamacion->authorization->simon_code_snapshot ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $reclamacion->authorization->procedimiento }}
                                    <span class="block text-[10px] text-slate-400 font-normal">Tipo: {{ $reclamacion->authorization->coverage_type_snapshot ?? 'General' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-500 font-mono">
                                    DOP {{ number_format($reclamacion->authorized_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900 font-mono">
                                    DOP {{ number_format($reclamacion->claimed_amount, 2) }}
                                </td>
                            </tr>
                            <tr class="bg-slate-50/50 font-bold text-slate-800">
                                <td colspan="2" class="px-4 py-3 text-[10px] uppercase">Total General</td>
                                <td class="px-4 py-3 text-right font-mono">DOP {{ number_format($reclamacion->authorized_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono text-[#041e49]">DOP {{ number_format($reclamacion->claimed_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Documentos de Soporte -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Archivos Adjuntos / NCF</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($reclamacion->documents as $doc)
                        <div class="border border-slate-150 p-4 rounded-2xl flex items-center justify-between text-xs bg-slate-50/50">
                            <div>
                                <p class="font-bold text-slate-700">{{ $doc->document_type }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Subido: {{ $doc->uploaded_at->format('d/m/Y h:i A') }}</p>
                            </div>
                            <a href="/storage/{{ $doc->file_path }}" target="_blank" class="bg-white border border-slate-200 text-slate-700 px-3 py-1.5 rounded-full font-bold hover:bg-slate-50 transition shadow-2xs">
                                Ver Documento
                            </a>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs italic">No se adjuntaron documentos de soporte digitales en esta reclamación.</p>
                    @endforelse
                </div>
            </div>

            <!-- Historial de Auditorías Médicas / Objeciones -->
            @if($reclamacion->audits->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Historial de Auditoría Interna</span>
                    </h3>

                    <div class="space-y-4">
                        @foreach($reclamacion->audits as $audit)
                            <div class="border border-slate-100 rounded-2xl p-4 space-y-3 text-xs bg-slate-50/30">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-[#041e49]">Tipo: {{ $audit->audit_type }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $audit->created_at->format('d/m/Y h:i A') }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-4 font-mono font-bold text-slate-700 bg-white p-2 rounded-xl border border-slate-100">
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">Sometido</span>
                                        <span>DOP {{ number_format($audit->claimed_amount, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block text-teal-600">Aprobado</span>
                                        <span class="text-teal-600">DOP {{ number_format($audit->approved_amount, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block text-rose-600">Objetado</span>
                                        <span class="text-rose-600">DOP {{ number_format($audit->objected_amount, 2) }}</span>
                                    </div>
                                </div>
                                @if($audit->objection_reason)
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Motivo Objeción</span>
                                        <p class="font-semibold text-rose-800 mt-0.5">{{ $audit->objection_reason }}</p>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Observación Interna</span>
                                    <p class="text-slate-600 mt-0.5">{{ $audit->internal_observation ?: 'Sin comentarios internos.' }}</p>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase block">Observación Visible PSS</span>
                                    <p class="text-slate-600 mt-0.5">{{ $audit->pss_observation ?: 'Sin observaciones para el prestador.' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Glosas Médicas & Conciliaciones -->
            @if($reclamacion->glosses->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                        <span class="material-symbols-outlined text-lg text-rose-600">gavel</span>
                        <span>Glosas de Facturación & Conciliaciones</span>
                    </h3>

                    <div class="space-y-4">
                        @foreach($reclamacion->glosses as $glosa)
                            <div class="border border-slate-150 rounded-2xl p-4 space-y-3 bg-slate-50/20 text-xs">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-black text-[#041e49] text-[13px]">{{ $glosa->glosa_code }}</span>
                                        <span class="ml-2 text-[9px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-bold border border-amber-100 uppercase tracking-wider">
                                            {{ $glosa->status }}
                                        </span>
                                    </div>
                                    <span class="text-slate-400 font-mono text-[10px]">{{ $glosa->created_at->format('d/m/Y') }}</span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-mono font-bold bg-white p-3 rounded-2xl border border-slate-100">
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">Monto Reclamado</span>
                                        <span>DOP {{ number_format($glosa->original_amount, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block text-rose-600">Monto Glosado</span>
                                        <span class="text-rose-600">DOP {{ number_format($glosa->objected_amount, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block text-teal-600">Monto Aprobado</span>
                                        <span class="text-teal-600">DOP {{ number_format($glosa->recognized_amount, 2) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-wider block">Diferencia Neta</span>
                                        <span>DOP {{ number_format($glosa->objected_amount - $glosa->recognized_amount, 2) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Motivo Objeción & Sustento</span>
                                    <p class="font-medium text-slate-650 leading-relaxed">{{ $glosa->objection_reason }}</p>
                                </div>

                                <!-- Historial de Conciliaciones de esta Glosa -->
                                @if($glosa->conciliations->isNotEmpty())
                                    <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Resoluciones de Conciliación</span>
                                        @foreach($glosa->conciliations as $con)
                                            <div class="p-3 bg-white border border-slate-100 rounded-xl space-y-1">
                                                <div class="flex justify-between items-center text-[10px] font-bold text-[#041e49]">
                                                    <span>Instancia: {{ ucfirst($con->instance) }}</span>
                                                    <span class="text-emerald-700">Acuerdo: DOP {{ number_format($con->agreement_amount, 2) }}</span>
                                                </div>
                                                <p class="text-slate-600 text-[11px] leading-relaxed"><b>Decisión:</b> {{ $con->final_decision }} - {{ $con->ars_observation }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Formulario de Conciliación (si está pendiente) -->
                                @if(in_array($glosa->status, ['Notificada a PSS', 'En conciliación']))
                                    <div class="mt-4 pt-3 border-t border-slate-100" x-data="{ finalDecision: 'Ratificada', agreementAmount: 0 }">
                                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Conciliar Objeción (Módulo Financiero)</h4>
                                        <form action="{{ route('ars.reclamaciones.conciliar', $reclamacion->id) }}" method="POST" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="glosa_id" value="{{ $glosa->id }}">
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider mb-1">Instancia</label>
                                                    <select name="instance" class="w-full rounded-xl border border-slate-200 bg-white text-[10px] text-slate-600 focus:outline-none transition px-3 py-1.5">
                                                        <option value="primera_instancia">Primera Instancia (ARS)</option>
                                                        <option value="segunda_instancia">Segunda Instancia (ARS)</option>
                                                        <option value="arbitraje">Arbitraje Externo (SISALRIL)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider mb-1">Decisión Final</label>
                                                    <select name="final_decision" x-model="finalDecision" @change="agreementAmount = (finalDecision === 'Levantada' ? {{ $glosa->objected_amount }} : 0)" class="w-full rounded-xl border border-slate-200 bg-white text-[10px] text-slate-600 focus:outline-none transition px-3 py-1.5">
                                                        <option value="Ratificada">Ratificada (Mantener Glosa)</option>
                                                        <option value="Levantada">Levantada (Pagar Todo)</option>
                                                        <option value="Parcialmente Aceptada">Parcialmente Aceptada (Pagar Parcial)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider mb-1">Monto de Acuerdo (DOP)</label>
                                                    <input type="number" step="0.01" name="agreement_amount" x-model="agreementAmount" max="{{ $glosa->objected_amount }}" required
                                                           class="w-full rounded-xl border border-slate-200 bg-white text-[10px] text-slate-600 focus:outline-none transition px-3 py-1.5">
                                                </div>
                                            </div>

                                            <input type="hidden" name="result_status" :value="finalDecision === 'Ratificada' ? 'Rechazada' : (finalDecision === 'Levantada' ? 'Aprobada' : 'Aprobada Parcial')">

                                            <div>
                                                <label class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider mb-1">Acta / Justificación del Acuerdo</label>
                                                <textarea name="ars_observation" rows="2" required class="w-full rounded-xl border border-slate-200 bg-white text-[10px] text-slate-600 focus:outline-none transition px-3 py-2" placeholder="Detalle los fundamentos contables o técnicos acordados en la conciliación..."></textarea>
                                            </div>

                                            <button type="submit" class="w-full bg-[#0b57d0] hover:bg-[#083d91] text-white text-[10px] font-bold py-2 rounded-xl transition shadow-2xs">
                                                Aplicar Acuerdo Contable y Generar CXP Complementaria
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel de Decisiones y Timeline (Derecha 1/3) -->
        <div class="space-y-6">
            <!-- Formulario Auditoría Reclamación -->
            @if($reclamacion->status === 'Reclamación recibida' || $reclamacion->status === 'En auditoría de reclamación')
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-5 8a2 2 0 012-2m0 0a5 5 0 11-5.93-9.14M9 12h.01M9 16h.01"/></svg>
                        <span>Decisión de Auditoría</span>
                    </h3>

                    <form action="{{ route('ars.reclamaciones.auditar', $reclamacion->id) }}" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Auditoría</label>
                            <select name="audit_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                                <option value="Administrativa">Administrativa</option>
                                <option value="Médica">Médica / Clínica</option>
                                <option value="Tarifa">Tarifa Contratada</option>
                                <option value="Documental">Auditoría Documentaria</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Decisión Operativa</label>
                            <select name="decision" x-model="decision" 
                                    @change="approvedAmount = (decision === 'Aprobada' ? {{ $reclamacion->claimed_amount }} : 0)"
                                    class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                                <option value="Aprobada">Aprobar Completo</option>
                                <option value="Objetada parcial">Aprobación Parcial / Objetar Diferencia</option>
                                <option value="Objetada total">Objeción Total (No Pagar)</option>
                                <option value="Pendiente documento">Solicitar Documentos Faltantes</option>
                                <option value="Rechazada">Rechazar Reclamación</option>
                            </select>
                        </div>

                        <!-- Input del monto aprobado (para objeción parcial) -->
                        <div x-show="decision === 'Objetada parcial'">
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Monto Aprobado (DOP)</label>
                            <input type="number" step="0.01" name="approved_amount" x-model="approvedAmount" max="{{ $reclamacion->claimed_amount }}"
                                   class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                            <span class="block text-[10px] text-rose-500 mt-1 font-semibold" x-text="'Monto Objetado: DOP ' + parseFloat({{ $reclamacion->claimed_amount }} - approvedAmount).toFixed(2)"></span>
                        </div>

                        <div x-show="decision.includes('Objetada') || decision === 'Rechazada'">
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Causal / Motivo de Objeción</label>
                            <input type="text" name="objection_reason" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all" placeholder="Ej: Tarifa excede el catálogo PDSS contratado...">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Observación Interna (Solo ARS)</label>
                            <textarea name="internal_observation" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all" placeholder="Escriba comentarios internos de auditoría..."></textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Mensaje Visible para PSS</label>
                            <textarea name="pss_observation" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all" placeholder="Explicación de la decisión que verá la PSS en su portal..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-teal-600 text-white rounded-full py-2.5 font-bold hover:bg-teal-700 transition shadow-xs flex items-center justify-center space-x-2">
                            <span>Registrar Auditoría & Liquidar</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- Línea de Tiempo Unificada (Timeline) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-[#041e49]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Línea de Tiempo del Trámite</span>
                </h3>

                <!-- Contenedor Timeline -->
                <div class="flow-root relative pl-4 border-l border-slate-150 space-y-6">
                    @forelse($timeline as $event)
                        <div class="relative">
                            <!-- Bullet -->
                            <span class="absolute -left-[21.5px] top-1 bg-white border-2 border-[#041e49] w-3 h-3 rounded-full flex items-center justify-center"></span>
                            
                            <div class="text-[11px] text-slate-400 font-semibold flex items-center justify-between">
                                <span>{{ $event->title }}</span>
                                <span>{{ $event->created_at->format('d/m H:i') }}</span>
                            </div>
                            <p class="text-xs font-medium text-slate-700 mt-0.5">{{ $event->description }}</p>
                            @if($event->new_status)
                                <span class="inline-block text-[9px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full mt-1 border border-teal-150">
                                    {{ $event->new_status }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs italic">No se registran eventos en el timeline de esta autorización.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
