@extends('layouts.ars')

@section('title', 'Detalle Envío SIMON')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Detalle de Envío SIMON: {{ $submission->submission_number }}</h2>
            <p class="text-xs text-slate-500 font-medium">Buzón de trazabilidad técnica del archivo regulatorio ante SIMON.</p>
        </div>
        <div>
            <a href="{{ route('sisalril.simulador') }}" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-full font-bold hover:bg-slate-200 transition text-xs">
                Volver a Bandeja SIMON
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ficha Técnica y Aprobación del Supervisor -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Información del Envío</h3>
            <div class="space-y-3 font-medium">
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Esquema Regulatorio</span>
                    <span class="text-slate-800 text-xs font-bold">Esquema {{ $submission->schema?->schema_code }} - {{ $submission->schema?->name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Periodo de Reporte</span>
                    <span class="text-slate-800 font-semibold font-mono">{{ $submission->period?->period_code }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Nombre del Archivo Plano</span>
                    <span class="text-slate-800 font-mono">{{ $submission->run?->file_name }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Estatus Actual</span>
                    <span class="text-slate-800 font-bold font-mono uppercase">{{ $submission->status }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Resumen de Validación</span>
                    <span class="text-slate-650 block leading-relaxed">{{ $submission->response_summary }}</span>
                </div>
            </div>

            <!-- Formulario de Aprobación/Rechazo (Solo si no está aprobado/rechazado definitivo) -->
            @if($submission->status !== 'aprobado' && $submission->status !== 'rechazado')
                <div class="border-t border-slate-100 pt-4 space-y-4">
                    <h4 class="font-bold text-slate-800">Controles de Supervisor SIMON</h4>
                    <form action="{{ route('sisalril.submission_action', $submission->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Acción Regulatoria <span class="text-rose-500">*</span></label>
                            <select name="action" id="action-select" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required onchange="toggleRejectionReason()">
                                <option value="aprobar">Aprobar Presentación</option>
                                <option value="rechazar">Rechazar Presentación</option>
                            </select>
                        </div>
                        <div id="rejection-reason-div" class="hidden">
                            <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Rechazo <span class="text-rose-500">*</span></label>
                            <textarea name="rejection_reason" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Especifique el error detectado..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">
                            Confirmar Resolución
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Muestra de Líneas del Archivo Plano y Log de Auditoría -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Muestra del Archivo Plano de Ancho Fijo -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Vista Previa de Ancho Fijo (Muestra)</h3>
                <div class="bg-slate-900 text-slate-300 font-mono text-[10px] p-4 rounded-2xl overflow-x-auto whitespace-pre leading-relaxed select-all">
                    @foreach($submission->run?->details->take(10) as $line)
[{{ $line->record_type }}] {{ $line->raw_line }}
                    @endforeach
                </div>
                @if($submission->run?->details->count() > 10)
                    <p class="text-[10px] text-slate-400 italic">Muestra limitada a las primeras 10 líneas de la presentación.</p>
                @endif
            </div>

            <!-- Log de Trazabilidad SIMON -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Log de Trazabilidad SIMON</h3>
                <div class="space-y-3">
                    @forelse($submission->logs as $log)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-150 flex items-start space-x-3">
                            <span class="material-symbols-outlined text-base text-slate-400 mt-0.5">history</span>
                            <div>
                                <span class="block font-bold text-slate-800 text-[10px]">{{ $log->message }}</span>
                                <span class="block text-[9px] text-slate-400 font-mono">{{ $log->created_at }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-4 text-slate-400 italic">No hay logs registrados para este envío.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRejectionReason() {
        const select = document.getElementById('action-select');
        const reasonDiv = document.getElementById('rejection-reason-div');
        if (select.value === 'rechazar') {
            reasonDiv.classList.remove('hidden');
        } else {
            reasonDiv.classList.add('hidden');
        }
    }
</script>
@endsection
