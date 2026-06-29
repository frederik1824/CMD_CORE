@extends('layouts.ars')

@section('title', 'Detalle de Autorización Médica')

@section('content')
<div class="space-y-6" x-data="{ 
    openOverrideModal: false, 
    openAuditModal: false,
    auditDecision: 'Aprobada'
}">
    <!-- Encabezado de la Autorización -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-2.5 bg-white rounded-2xl border border-slate-150 shadow-2xs">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-7 w-auto object-contain">
            </div>
            <div>
                <div class="flex items-center space-x-3 flex-wrap gap-2">
                    <h2 class="text-base font-extrabold text-slate-800 font-mono">{{ $autorizacion->numero_autorizacion }}</h2>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-black border uppercase tracking-wider
                        {{ $autorizacion->estado === 'Aprobada' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                           ($autorizacion->estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-250' : 
                           ($autorizacion->estado === 'Auditoría' ? 'bg-purple-50 text-purple-700 border-purple-250' : 'bg-slate-50 text-slate-655 border-slate-200')) }}">
                        {{ $autorizacion->estado }}
                    </span>
                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-bold uppercase tracking-wider">{{ $autorizacion->origin }}</span>
                </div>
                <p class="text-xs text-slate-400 font-semibold mt-1">Fecha Solicitud: {{ $autorizacion->fecha_solicitud->format('d/m/Y h:i A') }}</p>
            </div>
        </div>

        <div class="flex space-x-2 flex-wrap gap-2">
            <a href="{{ route('ars.autorizaciones_medicas.index') }}" class="text-slate-500 hover:text-slate-700 border border-slate-200 rounded-full px-4.5 py-2 text-xs font-bold bg-white hover:bg-slate-50 transition shadow-2xs">
                Bandeja General
            </a>

            @if($autorizacion->estado === 'Auditoría')
                <button @click="openAuditModal = true" class="bg-[#0056c5] hover:bg-blue-700 text-white font-bold px-4.5 py-2 rounded-full transition text-xs shadow-md">
                    Dictaminar Auditoría
                </button>
            @endif

            @if($autorizacion->estado !== 'Aprobada')
                <button @click="openOverrideModal = true" class="bg-[#0056c5] hover:bg-blue-700 text-white font-bold px-4.5 py-2 rounded-full transition text-xs shadow-md">
                    Override Manual
                </button>
            @endif
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <!-- Panel Izquierdo: Información del Afiliado y PSS (2 cols) -->
        <div class="space-y-6 md:col-span-2">
            <!-- Bloque Afiliado -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2 uppercase tracking-wider text-[10px]">Información del Afiliado</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">Nombre Completo</span>
                        <span class="font-extrabold text-slate-850 text-xs mt-0.5 block">{{ $autorizacion->afiliado->nombres }} {{ $autorizacion->afiliado->primer_apellido }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">Cédula / NSS</span>
                        <span class="font-bold text-slate-700 text-xs mt-0.5 block font-mono">{{ $autorizacion->afiliado->cedula }} / {{ $autorizacion->afiliado->nss }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">Estatus Afiliación</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-black bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wide mt-1">
                            {{ $autorizacion->afiliado->estado_afiliacion }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bloque Prestador y Contrato -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2 uppercase tracking-wider text-[10px]">Prestador (PSS) y Contrato</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">Nombre PSS</span>
                        <span class="font-extrabold text-slate-850 text-xs mt-0.5 block">{{ $autorizacion->pss->nombre }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">RNC / Tipo</span>
                        <span class="font-bold text-slate-700 text-xs mt-0.5 block font-mono">{{ $autorizacion->pss->rnc }} ({{ $autorizacion->pss->tipo_entidad }})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block uppercase text-[9px] tracking-wider">Contrato / Tarifario</span>
                        @if($autorizacion->contract)
                            <a href="{{ route('ars.pss.contratos_tarifarios.show', $autorizacion->contract->id) }}" class="font-extrabold text-[#0056c5] hover:underline font-mono text-xs mt-0.5 block">
                                {{ $autorizacion->contract->contract_number }} (V{{ $autorizacion->pss_contract_version_id }})
                            </a>
                        @else
                            <span class="text-rose-600 font-black block mt-0.5">Sin contrato vigente</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Liquidación Financiera y Snapshots (Múltiples Servicios) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2 uppercase tracking-wider text-[10px]">Servicios Clínicos y Desglose Financiero</h3>
                
                @if($autorizacion->detalles && $autorizacion->detalles->count() > 0)
                    <!-- Tabla de Servicios Individuales -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 mb-4 text-[11px]">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                    <th class="p-3">Servicio / Procedimiento</th>
                                    <th class="p-3 text-right">Monto Solicitado</th>
                                    <th class="p-3 text-center">Estado Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autorizacion->detalles as $det)
                                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/40">
                                        <td class="p-3 font-semibold text-slate-800 leading-snug">
                                            <span class="block text-[9px] text-[#0056c5] font-mono font-bold">[{{ $det->codigo }}]</span>
                                            {{ $det->descripcion }}
                                        </td>
                                        <td class="p-3 text-right font-mono font-bold text-slate-900">
                                            DOP {{ number_format($det->monto, 2) }}
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[8px] font-black border uppercase tracking-wider
                                                {{ $det->estado === 'Aprobada' || $det->estado === 'Aprobado' ? 'bg-blue-50 text-blue-700 border-blue-100' : 
                                                   ($det->estado === 'Rechazada' || $det->estado === 'Rechazado' ? 'bg-rose-50 text-rose-700 border-rose-100' : 'bg-purple-50 text-purple-750 border-purple-100') }}">
                                                {{ $det->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Fallback de Procedimiento Legacy Único -->
                    <div class="border border-slate-150 rounded-2xl p-4 bg-slate-50/50 flex justify-between items-center text-xs font-semibold mb-4 leading-normal">
                        <span class="text-slate-500 font-bold uppercase tracking-wider text-[9px]">Servicio Solicitado:</span>
                        <span class="font-extrabold text-slate-850 text-right">{{ $autorizacion->procedimiento ?? 'Consulta general o estudio' }}</span>
                    </div>
                @endif

                <!-- Totalización Consolidada de la Orden de Autorización -->
                <div class="border border-slate-150 rounded-2xl p-4 bg-slate-50/30 space-y-3 font-mono text-[11px]">
                    <div class="flex justify-between items-center font-bold text-slate-750 uppercase tracking-wider text-[9px] font-sans pb-1.5 border-b border-slate-200/60">
                        <span>Resumen Financiero</span>
                        <span class="font-mono text-slate-400">Total Orden</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold font-sans">Suma Total Solicitada:</span>
                        <span class="font-bold text-slate-800">DOP {{ number_format($autorizacion->monto_solicitado, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold font-sans">Tarifa Contratada (Snapshot):</span>
                        <span class="font-bold text-slate-800">DOP {{ number_format($autorizacion->contracted_amount_snapshot ?? $autorizacion->monto_contratado, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-slate-200/60 pt-2 text-[#0056c5]">
                        <span class="font-bold font-sans">Monto Aprobado / Cubierto ARS:</span>
                        <span class="font-extrabold text-xs">DOP {{ number_format($autorizacion->ars_amount ?? $autorizacion->monto_contratado, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-amber-700">
                        <span class="font-semibold font-sans">Copago del Afiliado:</span>
                        <span class="font-bold">DOP {{ number_format($autorizacion->affiliate_copay_amount ?? $autorizacion->copago, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-rose-700">
                        <span class="font-semibold font-sans">Diferencia Excedente (No Cubierta):</span>
                        <span class="font-bold">DOP {{ number_format($autorizacion->non_covered_amount ?? $autorizacion->exceso, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Timeline y Documentos (1 col) -->
        <div class="space-y-6 self-start">
            <!-- Timeline de la Autorización -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2 uppercase tracking-wider text-[10px]">Timeline & Auditoría</h3>
                
                <div class="relative pl-4 border-l border-slate-100 space-y-4">
                    @foreach($timeline as $t)
                        <div class="relative">
                            <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full border bg-white border-[#0056c5]"></span>
                            <div class="flex justify-between items-center font-bold text-slate-850">
                                <span>{{ $t->title }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ $t->created_at->format('d/m/Y h:i A') }}</span>
                            </div>
                            <p class="text-[10px] text-slate-450 mt-0.5 leading-relaxed font-semibold">{{ $t->description }}</p>
                            @if($t->user)
                                <span class="text-[9px] text-[#0056c5] font-bold mt-1 block">Por: {{ $t->user->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Soporte Documental -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 border-b border-slate-50 pb-2 uppercase tracking-wider text-[10px]">Soportes Adjuntos</h3>
                <div class="space-y-2">
                    @forelse($docs as $d)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-slate-100/50 transition">
                            <div class="min-w-0 flex-1 mr-2">
                                <span class="font-bold text-slate-800 block truncate text-[11px]">{{ $d->nombre_archivo }}</span>
                                <span class="text-[9px] text-slate-400 block mt-0.5 font-bold uppercase">{{ $d->tipo_documento }}</span>
                            </div>
                            <a href="/storage/{{ $d->ruta_archivo }}" target="_blank" 
                               class="text-[#0056c5] hover:text-blue-800 font-bold text-[10px] bg-blue-50 border border-blue-100 px-3 py-1 rounded-full transition flex-shrink-0">
                                Descargar
                            </a>
                        </div>
                    @empty
                        <p class="text-slate-400 italic text-center py-4">No se registran adjuntos para este caso.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dictaminar Auditoría -->
    <div x-show="openAuditModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
         <div class="bg-white rounded-3xl shadow-xl max-w-md w-full border border-slate-150 p-6 space-y-4"
              @click.away="openAuditModal = false">
             <div>
                 <h3 class="font-bold text-slate-800 text-sm">Dictamen de Auditoría Clínica</h3>
                 <p class="text-[10px] text-slate-400 mt-0.5">Emita la resolución médica y justificación oficial.</p>
             </div>

             <form action="{{ route('ars.autorizaciones_medicas.auditar', $autorizacion->id) }}" method="POST" class="space-y-4 text-xs">
                 @csrf
                 <div>
                     <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Dictamen Médico <span class="text-rose-500">*</span></label>
                     <select name="decision" x-model="auditDecision" required class="w-full rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                         <option value="Aprobada">Aprobar Solicitud</option>
                         <option value="Rechazada">Rechazar Solicitud</option>
                     </select>
                 </div>

                 <div>
                     <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Justificación Clínica <span class="text-rose-500">*</span></label>
                     <textarea name="motivo" rows="4" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-slate-850 focus:bg-white focus:outline-none" placeholder="Indique el criterio médico de la resolución..."></textarea>
                 </div>

                 <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                     <button type="button" @click="openAuditModal = false" class="px-4.5 py-2 border border-slate-250 rounded-full text-slate-500 hover:bg-slate-50 transition font-bold">
                         Cancelar
                     </button>
                     <button type="submit" class="px-5 py-2 bg-[#0056c5] hover:bg-blue-700 text-white font-bold rounded-full transition shadow-md">
                         Dictaminar
                     </button>
                 </div>
             </form>
         </div>
    </div>

    <!-- Modal Aplicar Override Manual -->
    <div x-show="openOverrideModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
         <div class="bg-white rounded-3xl shadow-xl max-w-md w-full border border-slate-150 p-6 space-y-4"
              @click.away="openOverrideModal = false">
             <div>
                 <h3 class="font-bold text-slate-800 text-sm">Override de Aprobación Manual</h3>
                 <p class="text-[10px] text-slate-400 mt-0.5">Esta acción forzará la aprobación del caso en el sistema omitiendo el motor de reglas.</p>
             </div>

             <form action="{{ route('ars.autorizaciones_medicas.override', $autorizacion->id) }}" method="POST" class="space-y-4 text-xs">
                 @csrf
                 <div>
                     <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Motivo de Override <span class="text-rose-500">*</span></label>
                     <select name="override_type" required class="w-full rounded-full border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                         <option value="monto_excedido">Monto Excede Tarifa Contratada</option>
                         <option value="pss_sin_contrato">PSS Fuera de Red / Sin Contrato</option>
                         <option value="afiliado_inactivo">Caso de Afiliado Inactivo (Supervisor)</option>
                         <option value="urgencia_medica">Urgencia / Emergencia Vital</option>
                     </select>
                 </div>

                 <div>
                     <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Justificación Obligatoria <span class="text-rose-500">*</span></label>
                     <textarea name="reason" rows="4" required minlength="8"
                               class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-slate-850 focus:bg-white focus:outline-none"
                               placeholder="Explique el motivo y autorización superior para este override..."></textarea>
                 </div>

                 <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                     <button type="button" @click="openOverrideModal = false" class="px-4.5 py-2 border border-slate-250 rounded-full text-slate-500 hover:bg-slate-50 transition font-bold">
                         Cancelar
                     </button>
                     <button type="submit" class="px-5 py-2 bg-[#0056c5] hover:bg-blue-700 text-white font-bold rounded-full transition shadow-md">
                         Liberar Autorización
                     </button>
                 </div>
             </form>
         </div>
    </div>
</div>
@endsection
