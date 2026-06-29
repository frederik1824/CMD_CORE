@extends('layouts.core')

@section('title', 'Gestionar Solicitud de Reembolso')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#0b57d0]/10 text-[#0b57d0] border border-[#0b57d0]/20 uppercase tracking-wider">
                    📄 Expediente # {{ $case->case_number }}
                </span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Detalle del Reembolso</h2>
            <p class="text-xs text-slate-500 font-medium">Gestione la documentación, apruebe o rechace y consulte la afectación contable de esta solicitud.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.reembolsos.index') }}" class="bg-slate-50 text-slate-600 rounded-full border border-slate-200 px-4 py-2 text-xs font-bold hover:bg-slate-100 transition shadow-sm">
                ← Regresar
            </a>
        </div>
    </div>

    <!-- Grid de contenido de dos columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Información de la Solicitud y Acciones (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Info del caso -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-50 pb-3">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Datos de la Solicitud</h3>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold border bg-blue-50 text-blue-700 border-blue-150">
                        {{ $case->status }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-5 text-xs">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Afiliado</span>
                        <p class="font-bold text-slate-800">{{ $case->afiliado->nombre_completo }}</p>
                        <p class="text-[9px] text-slate-400 font-mono">Ced: {{ $case->afiliado->cedula }}</p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">PSS involucrada</span>
                        <p class="font-bold text-slate-800">{{ $case->pss->nombre }}</p>
                        <p class="text-[9px] text-slate-400 font-mono">Código PSS: {{ $case->pss->codigo_pss }}</p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tipo de Reclamación</span>
                        <p class="font-bold text-slate-800">
                            @if($case->request_type === 'cobro_indebido')
                                💸 Cobro Indebido
                            @elseif($case->request_type === 'negacion_cobertura')
                                🛡️ Negación de Cobertura
                            @else
                                🔄 Ambos
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Monto Solicitado</span>
                        <p class="font-bold text-slate-800 font-mono text-sm">DOP {{ number_format($case->requested_amount, 2) }}</p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Monto Autorizado</span>
                        <p class="font-bold text-emerald-700 font-mono text-sm">DOP {{ number_format($case->approved_amount, 2) }}</p>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Plazo Límite de Respuesta</span>
                        <p class="font-bold text-slate-800 font-mono">{{ $case->response_due_date ? $case->response_due_date->format('d/m/Y') : 'Pendiente' }}</p>
                        @if($case->completed_documents_at)
                            <span class="text-[9px] text-emerald-600 block">Expediente completo: {{ $case->completed_documents_at->format('d/m/Y') }}</span>
                        @else
                            <span class="text-[9px] text-amber-500 block">Requiere completar expediente</span>
                        @endif
                    </div>
                </div>

                @if($case->final_decision)
                    <div class="pt-4 border-t border-slate-50 text-xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Decisión / Resolución Final</span>
                        <p class="p-3 bg-slate-50 rounded-2xl border border-slate-100 leading-relaxed text-slate-700 font-medium">{{ $case->final_decision }}</p>
                    </div>
                @endif
            </div>

            <!-- Tramitación y Resolución (Formulario para Aprobación / Rechazo) -->
            @if(in_array($case->status, ['Recibido', 'Expediente completo', 'En revisión', 'Pendiente de documentos']))
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-3">
                        Auditar y Resolver Reembolso
                    </h3>

                    <form action="{{ route('ars.reembolsos.estado', $case->id) }}" method="POST" class="space-y-4 text-xs font-medium text-slate-600">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Monto Aprobado</label>
                                <input type="number" step="0.01" name="approved_amount" max="{{ $case->requested_amount }}" required value="{{ $case->requested_amount }}"
                                       class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Resolución / Decisión</label>
                                <select name="status" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-2.5">
                                    <option value="Aprobado">Aprobado Total / Parcial</option>
                                    <option value="Rechazado">Rechazado</option>
                                    <option value="En revisión">Enviar a Revisión Administrativa</option>
                                    <option value="Escalado a DIDA">Escalar a la DIDA</option>
                                    <option value="Escalado a SISALRIL">Escalar a la SISALRIL</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Comentarios de Resolución / Razón de Rechazo</label>
                            <textarea name="description" rows="3" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-xs text-slate-600 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all px-4 py-3" placeholder="Detalle la fundamentación clínica o de auditoría para la resolución..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#0b57d0] text-white rounded-full py-2.5 font-bold hover:bg-[#083d91] transition shadow-xs text-center block">
                            Aplicar Resolución y Contabilizar
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Columna Derecha: Documentos cargados e Historial de Acciones (1/3) -->
        <div class="space-y-6">
            <!-- Carga de Documentos -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                    Documentación Soporte
                </h3>

                <div class="space-y-3 divide-y divide-slate-50">
                    @forelse($case->documents as $doc)
                        <div class="flex items-center justify-between py-2.5 text-[11px] font-medium">
                            <div>
                                <p class="text-slate-700 font-semibold">{{ $doc->document_type }}</p>
                                <span class="text-[9px] text-slate-400 block font-mono">Por: {{ $doc->uploader->name ?? 'Sistema' }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 font-bold hover:underline">Ver ↗</a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2 italic text-center">No se registran archivos soporte.</p>
                    @endforelse
                </div>

                @if(!in_array($case->status, ['Aprobado', 'Rechazado', 'Cerrado']))
                    <form action="{{ route('ars.reembolsos.documentos', $case->id) }}" method="POST" enctype="multipart/form-data" class="pt-4 border-t border-slate-50 space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tipo de Documento</label>
                            <select name="document_type" class="w-full rounded-xl border-slate-200 bg-slate-50 text-[11px] text-slate-600 focus:bg-white focus:outline-none transition-all px-3 py-1.5">
                                <option value="Factura Original">Factura Original</option>
                                <option value="Recibo de Pago">Recibo de Pago</option>
                                <option value="Indicación Médica">Indicación Médica</option>
                                <option value="Cédula Afiliado">Cédula del Afiliado</option>
                            </select>
                        </div>
                        <div>
                            <input type="file" name="file" required class="block w-full text-[11px] text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 text-[10px] font-bold py-2 rounded-xl transition">
                            Cargar Documento
                        </button>
                    </form>
                @endif
            </div>

            <!-- Trazabilidad del Expediente -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
                    Trazabilidad del Expediente
                </h3>

                <div class="space-y-4 max-h-72 overflow-y-auto">
                    @foreach($case->actions as $action)
                        <div class="relative pl-6 pb-2 text-[11px]">
                            <!-- Línea -->
                            <div class="absolute left-1.5 top-1.5 bottom-0 w-0.5 bg-slate-100"></div>
                            <!-- Icono hito -->
                            <div class="absolute left-0 top-1 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white"></div>
                            
                            <div>
                                <span class="font-bold text-slate-700 block">{{ $action->action_type }}</span>
                                <p class="text-slate-500 mt-0.5">{{ $action->description }}</p>
                                <span class="text-[9px] text-slate-400 block mt-1 font-mono">
                                    {{ $action->created_at->format('d/m/Y H:i') }} - por: {{ $action->user->name ?? 'Sistema' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
