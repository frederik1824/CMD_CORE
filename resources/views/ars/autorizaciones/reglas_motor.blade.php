@extends('layouts.ars')

@section('title', 'Motor de Reglas de Autorización')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in" x-data="{
    showCreateModal: false,
    showTestModal: false,
    selectedRule: null,
    testAfiliado: '',
    testPss: '',
    testServicio: '',
    testMonto: 0,
    testResult: null,
    testLoading: false,
    openTest(rule) {
        this.selectedRule = rule;
        this.showTestModal = true;
        this.testResult = null;
    },
    runTest() {
        this.testLoading = true;
        fetch(`/core/autorizaciones/reglas-motor/${this.selectedRule.id}/test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                afiliado_id: this.testAfiliado,
                pss_id: this.testPss,
                pdss_service_id: this.testServicio,
                monto: this.testMonto
            })
        })
        .then(r => r.json())
        .then(data => {
            this.testResult = data;
            this.testLoading = false;
        })
        .catch(e => {
            this.testLoading = false;
            alert('Error al ejecutar la prueba de regla');
        });
    }
}">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Motor de Reglas de Autorización</h2>
            <p class="text-xs text-slate-500 font-medium">Consola de administración y simulación en tiempo real de reglas de validación médica y financiera.</p>
        </div>
        <div>
            <button @click="showCreateModal = true" class="bg-[#041e49] hover:bg-slate-800 text-white rounded-full px-4 py-2 font-bold shadow-xs transition inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Crear Regla
            </button>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Listado de Reglas del Motor -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Reglas Configuradas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código / Regla</th>
                            <th class="px-4 py-3 text-left">Proceso</th>
                            <th class="px-4 py-3 text-center">Severidad</th>
                            <th class="px-4 py-3 text-center">Prioridad</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($reglas as $r)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-[#041e49] font-mono" x-text="'{{ $r->rule_code }}'"></span>
                                        <span class="block font-bold text-slate-700" x-text="'{{ $r->name }}'"></span>
                                        <span class="block text-[10px] text-slate-450 leading-relaxed font-normal" x-text="'{{ $r->description }}'"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 capitalize" x-text="'{{ $r->process }}'"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                        {{ $r->severity === 'blocking' ? 'bg-rose-50 text-rose-700 border border-rose-220' : 
                                           ($r->severity === 'audit_required' ? 'bg-amber-50 text-amber-700 border border-amber-220' : 'bg-blue-50 text-blue-700 border border-blue-220') }}">
                                        {{ $r->severity === 'blocking' ? 'Bloqueo' : ($r->severity === 'audit_required' ? 'Auditoría' : 'Alerta') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono" x-text="'{{ $r->priority }}'"></td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('ars.autorizaciones.toggle_regla_motor', $r->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold border 
                                            {{ $r->status === 'Activa' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                            {{ $r->status }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap space-x-1">
                                    <button @click="openTest({{ json_encode($r) }})" class="bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-2.5 py-1 text-[9px] hover:bg-blue-100 font-bold inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[10px]">science</span> Probar
                                    </button>
                                    <form action="{{ route('ars.autorizaciones.eliminar_regla_motor', $r->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar esta regla?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-50 text-rose-700 border border-rose-200 rounded-full px-2.5 py-1 text-[9px] hover:bg-rose-100 font-bold inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado reglas del motor dinámico.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Información del Motor</h3>
            <div class="space-y-3 leading-relaxed text-slate-600">
                <p>Las reglas del motor de autorización automática se ejecutan en cascada evaluando el origen, proceso y la severidad de las atenciones.</p>
                <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl space-y-2">
                    <span class="font-bold text-[#041e49] block">Severidades Disponibles:</span>
                    <ul class="list-disc pl-4 space-y-1">
                        <li><strong>Bloqueo:</strong> Rechaza inmediatamente la solicitud sin pasar a auditoría.</li>
                        <li><strong>Auditoría:</strong> Envía la solicitud al buzón de auditoría médica para revisión manual.</li>
                        <li><strong>Alerta:</strong> Aprueba la solicitud pero genera una bitácora o advertencia en el reporte.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Creación de Regla -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl p-6 w-full max-w-xl shadow-2xl border border-slate-100 space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Nueva Regla de Autorización</h3>
            <form action="{{ route('ars.autorizaciones.guardar_regla_motor') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-500 mb-1">Código Regla <span class="text-rose-500">*</span></label>
                        <input type="text" name="rule_code" placeholder="Ej. R-MONTO-MAX" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-500 mb-1">Nombre <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" placeholder="Nombre descriptivo" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-500 mb-1">Descripción</label>
                    <textarea name="description" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" placeholder="Indique la justificación o norma aplicable..."></textarea>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-500 mb-1">Proceso <span class="text-rose-500">*</span></label>
                        <select name="process" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" required>
                            <option value="Core">Core ARS</option>
                            <option value="Portal">Portal PSS</option>
                            <option value="Farmacia">Farmacia</option>
                            <option value="Laboratorio">Laboratorio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-500 mb-1">Severidad <span class="text-rose-500">*</span></label>
                        <select name="severity" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" required>
                            <option value="blocking">Bloqueo (Rechazo)</option>
                            <option value="audit_required">Requiere Auditoría</option>
                            <option value="warning">Alerta (Advertencia)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-500 mb-1">Prioridad <span class="text-rose-500">*</span></label>
                        <input type="number" name="priority" value="1" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none font-mono" required>
                    </div>
                </div>

                <!-- Constructor de Condiciones Visuales -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-150 space-y-3">
                    <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[9px]">Condición Lógica de Activación</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-400 mb-1 text-[8px] uppercase tracking-wider">Campo a Evaluar</label>
                            <select name="condition_field" class="w-full rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none">
                                <option value="monto_solicitado">Monto Solicitado (DOP)</option>
                                <option value="afiliado_estado">Estado Afiliado</option>
                                <option value="servicio_alto_costo">Es Servicio Alto Costo (PDSS)</option>
                                <option value="tipo_pss">Tipo Prestadora (PSS)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-400 mb-1 text-[8px] uppercase tracking-wider">Operador</label>
                            <select name="condition_operator" class="w-full rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none font-mono">
                                <option value=">">&gt; (Mayor que)</option>
                                <option value="<">&lt; (Menor que)</option>
                                <option value="==">== (Igual a)</option>
                                <option value="!=">!= (Diferente a)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-400 mb-1 text-[8px] uppercase tracking-wider">Valor de Referencia</label>
                            <input type="text" name="condition_value" placeholder="Ej. 15000 o OK" class="w-full rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-xs focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Acción y Mensaje -->
                <div class="space-y-2">
                    <input type="hidden" name="action_type" value="set_status">
                    <label class="block font-bold text-slate-500 mb-1">Mensaje de Rechazo / Auditoría <span class="text-rose-500">*</span></label>
                    <input type="text" name="action_message" placeholder="Ej. Límite anual excedido para medicamentos o requiere validación de diagnóstico" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs focus:bg-white focus:outline-none" required>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="showCreateModal = false" class="bg-slate-100 text-slate-700 rounded-full px-4 py-2 font-bold hover:bg-slate-200 transition">Cancelar</button>
                    <button type="submit" class="bg-[#041e49] text-white rounded-full px-4 py-2 font-bold hover:bg-slate-800 transition">Crear Regla</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Simulación / Prueba de Regla -->
    <div x-show="showTestModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in" style="display: none;" x-cloak>
        <div class="bg-white rounded-3xl p-6 w-full max-w-lg shadow-2xl border border-slate-100 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Simular Prueba de Regla</h3>
            <div class="space-y-1">
                <span class="block font-bold text-slate-600" x-text="selectedRule?.name"></span>
                <p class="text-slate-400 font-normal leading-relaxed" x-text="selectedRule?.description || 'Sin descripción'"></p>
            </div>

            <!-- Parámetros de Prueba -->
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1 text-[8px] uppercase tracking-wider">Afiliado de Prueba</label>
                        <select x-model="testAfiliado" class="w-full rounded-xl border border-slate-250 bg-slate-50 px-3 py-2 text-xs focus:bg-white">
                            <option value="">Seleccione un afiliado...</option>
                            @foreach($afiliados as $a)
                                <option value="{{ $a->id }}">{{ $a->nombres }} {{ $a->primer_apellido }} (Estado: {{ $a->estado_afiliacion }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1 text-[8px] uppercase tracking-wider">Prestador (PSS) de Prueba</label>
                        <select x-model="testPss" class="w-full rounded-xl border border-slate-250 bg-slate-50 px-3 py-2 text-xs focus:bg-white">
                            <option value="">Seleccione una PSS...</option>
                            @foreach($pssList as $pss)
                                <option value="{{ $pss->id }}">{{ $pss->nombre }} (Tipo: {{ $pss->tipo_entidad }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1 text-[8px] uppercase tracking-wider">Servicio del Catálogo</label>
                        <select x-model="testServicio" class="w-full rounded-xl border border-slate-250 bg-slate-50 px-3 py-2 text-xs focus:bg-white">
                            <option value="">Seleccione un servicio...</option>
                            @foreach($servicios as $s)
                                <option value="{{ $s->id }}">{{ $s->simon_code }} - {{ $s->coverage_description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1 text-[8px] uppercase tracking-wider">Monto Solicitado (DOP)</label>
                        <input type="number" x-model="testMonto" placeholder="Ej. 25000" class="w-full rounded-xl border border-slate-250 bg-slate-50 px-3 py-2 text-xs focus:bg-white font-mono">
                    </div>
                </div>
            </div>

            <!-- Botón Ejecutar Simulación -->
            <button @click="runTest()" :disabled="testLoading || !testAfiliado || !testPss || !testServicio" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 disabled:opacity-50 transition shadow-xs flex items-center justify-center gap-1.5 text-xs">
                <span x-show="testLoading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                Ejecutar Simulación en Vivo
            </button>

            <!-- Panel de Resultados de Prueba -->
            <div x-show="testResult" class="p-4 rounded-2xl border transition animate-fade-in"
                 :class="testResult?.is_match ? 'bg-amber-50/50 border-amber-200 text-amber-900' : 'bg-emerald-50/50 border-emerald-250 text-emerald-900'">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-lg" :class="testResult?.is_match ? 'text-amber-600' : 'text-emerald-600'">
                        <span x-text="testResult?.is_match ? 'warning' : 'check_circle'"></span>
                    </span>
                    <span class="font-bold text-xs uppercase tracking-wider">Resultado de la Regla:</span>
                </div>
                <div class="space-y-1 text-slate-700">
                    <p>Condición Coincide (Match): <strong x-text="testResult?.is_match ? 'Sí' : 'No'"></strong></p>
                    <p>Decisión Sugerida por Motor: <strong x-text="testResult?.veredicto"></strong></p>
                    <p>Mensaje Retornado: <span class="italic font-medium" x-text="testResult?.observacion"></span></p>
                </div>
            </div>

            <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                <button type="button" @click="showTestModal = false" class="bg-slate-100 text-slate-700 rounded-full px-4 py-2 font-bold hover:bg-slate-200 transition">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection
