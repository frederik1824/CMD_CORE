@extends('layouts.ars')

@section('title', 'Detalle de Contrato & Tarifario')

@section('content')
<div class="space-y-6" x-data="{ 
    activeTab: 'tarifas', 
    openTarifaModal: false, 
    openVersionModal: false,
    selectedServiceId: '',
    selectedAmount: '',
    selectedCopay: 20,
    selectedReqAuth: true,
    selectedReqAudit: false,
    selectedFreq: '',
    selectedPeriod: 'año'
}">
    <!-- Encabezado de Contrato -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-teal-50 text-teal-600 rounded-2xl shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h2 class="text-base font-extrabold text-slate-800 font-mono">{{ $contrato->contract_number }}</h2>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider border
                        {{ $contrato->status === 'vigente' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                        {{ $contrato->status }}
                    </span>
                    <span class="text-xs text-slate-400 font-semibold">Versión Vigente: {{ $activeVersion->version_number ?? '1.0.0' }}</span>
                </div>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Prestadora: <span class="text-slate-650 font-bold">{{ $contrato->pss->nombre }}</span> (Nivel de Atención: {{ $contrato->pss->level_of_care ?? '2' }})</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('ars.pss.contratos_tarifarios') }}" class="text-slate-500 hover:text-slate-700 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-2xs">
                Volver a contratos
            </a>
            <button @click="openVersionModal = true" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-4 py-2 rounded-full transition text-xs shadow-2xs">
                + Crear Nueva Versión
            </button>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Navegación por Tabs -->
    <div class="flex border-b border-slate-200 text-xs font-bold text-slate-450 space-x-6">
        <button @click="activeTab = 'tarifas'" :class="activeTab === 'tarifas' ? 'text-teal-600 border-b-2 border-teal-600 pb-3' : 'pb-3 hover:text-slate-650'">
            Tarifario Vigente
        </button>
        <button @click="activeTab = 'importar'" :class="activeTab === 'importar' ? 'text-teal-600 border-b-2 border-teal-600 pb-3' : 'pb-3 hover:text-slate-650'">
            Importación CSV
        </button>
        <button @click="activeTab = 'historial'" :class="activeTab === 'historial' ? 'text-teal-600 border-b-2 border-teal-600 pb-3' : 'pb-3 hover:text-slate-650'">
            Historial de Cambios
        </button>
        <button @click="activeTab = 'versiones'" :class="activeTab === 'versiones' ? 'text-teal-600 border-b-2 border-teal-600 pb-3' : 'pb-3 hover:text-slate-650'">
            Versiones de Contrato
        </button>
    </div>

    <!-- Contenido de los Tabs -->
    <div>
        <!-- TAB 1: TARIFARIO VIGENTE -->
        <div x-show="activeTab === 'tarifas'" class="space-y-4 animate-fade-in">
            <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100 shadow-2xs">
                <span class="text-xs text-slate-500 font-semibold">Agregar o modificar tarifas individuales en esta versión.</span>
                <button @click="openTarifaModal = true; selectedServiceId = ''; selectedAmount = ''; selectedCopay = 20;" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-3 py-1.5 rounded-full transition text-[10px] shadow-3xs uppercase tracking-wide">
                    + Configurar Tarifa Individual
                </button>
            </div>

            <!-- Tabla de Tarifas -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden text-xs">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50 font-bold text-slate-450 text-[10px] uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-6 py-3.5 text-left">Código Simon</th>
                                <th scope="col" class="px-6 py-3.5 text-left">Procedimiento / Cobertura</th>
                                <th scope="col" class="px-6 py-3.5 text-right">Monto Contratado</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Copago</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Auditoría / Autorización</th>
                                <th scope="col" class="px-6 py-3.5 text-center w-24">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                            @forelse($tariffItems as $item)
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="px-6 py-3.5 font-bold font-mono text-[#0056c5] text-xs">
                                        {{ $item->simon_code_snapshot }}
                                        @if($item->cups_code_snapshot)
                                            <span class="block text-[9px] text-slate-400 font-normal mt-0.5">CUPS: {{ $item->cups_code_snapshot }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="font-bold text-slate-800 block leading-tight">{{ $item->service_description_snapshot }}</span>
                                        <span class="text-[10px] text-slate-400 block mt-1">Grupo: {{ $item->service_group_snapshot ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-bold text-slate-900 font-mono text-xs">
                                        DOP {{ number_format($item->contracted_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="bg-slate-50 text-slate-600 px-2 py-0.5 rounded border border-slate-150 font-bold font-mono">{{ $item->copay_percent }}%</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <div class="flex flex-col items-center space-y-1 text-[9px] font-bold">
                                            @if($item->requires_authorization)
                                                <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-150 uppercase">Requiere Aut.</span>
                                            @endif
                                            @if($item->requires_medical_audit)
                                                <span class="bg-purple-50 text-purple-700 px-2 py-0.5 rounded border border-purple-150 uppercase">Requiere Aud. Médica</span>
                                            @endif
                                            @if($item->is_high_cost)
                                                <span class="bg-rose-50 text-rose-700 px-2 py-0.5 rounded border border-rose-150 uppercase">Alto Costo</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <button type="button" 
                                                @click="
                                                    openTarifaModal = true; 
                                                    selectedServiceId = '{{ $item->pdss_service_id }}';
                                                    selectedAmount = '{{ $item->contracted_amount }}';
                                                    selectedCopay = '{{ $item->copay_percent }}';
                                                    selectedReqAuth = {{ $item->requires_authorization ? 'true' : 'false' }};
                                                    selectedReqAudit = {{ $item->requires_medical_audit ? 'true' : 'false' }};
                                                    selectedFreq = '{{ $item->frequency_limit }}';
                                                    selectedPeriod = '{{ $item->frequency_period ?? 'año' }}';
                                                "
                                                class="text-teal-600 hover:text-teal-800 font-bold border border-teal-200 px-2.5 py-1 rounded-full transition text-[10px] bg-white">
                                            Editar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-450 font-semibold">
                                        No hay servicios configurados en esta versión de tarifario.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($tariffItems, 'links') && $tariffItems->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/20">
                        {{ $tariffItems->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 2: IMPORTACIÓN CSV -->
        <div x-show="activeTab === 'importar'" class="space-y-6 animate-fade-in text-xs">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4 md:col-span-2">
                    <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Cargar Tarifario por CSV</h3>
                    
                    <form action="{{ route('ars.pss.contratos_tarifarios.importar', $contrato->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="border border-dashed border-slate-200 rounded-xl p-6 bg-slate-50/50 flex flex-col items-center justify-center text-center space-y-2">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div>
                                <p class="font-bold text-slate-700">Subir archivo CSV de tarifas</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">El archivo debe contener el formato oficial del tarifario.</p>
                            </div>
                            <input type="file" name="csv_file" required accept=".csv"
                                   class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-full px-5 py-2 transition shadow-xs text-xs">
                                Iniciar Importación de Tarifas
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Plantilla y Consejos -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4 self-start">
                    <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Plantilla de Tarifario</h3>
                    <p class="text-slate-500 leading-relaxed">Descargue la plantilla oficial en formato CSV para estructurar los códigos de Simon y tarifas antes de subirlos.</p>
                    
                    <a href="{{ route('ars.pss.contratos_tarifarios.plantilla') }}" class="w-full flex items-center justify-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-full text-xs transition border border-slate-200 shadow-sm text-center">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Descargar Plantilla CSV</span>
                    </a>
                </div>
            </div>

            <!-- Historial de Importaciones -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Importaciones Recientes</h3>
                
                <div class="space-y-4">
                    @forelse($imports as $log)
                        <div class="border border-slate-150 rounded-xl p-4 bg-slate-50/20 text-xs relative">
                            <div class="flex justify-between items-center font-bold text-slate-750">
                                <span class="capitalize">Archivo: <span class="font-mono text-slate-600">{{ basename($log->file_path) }}</span></span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d/m/Y h:i A') }}</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-2 font-semibold text-slate-600">
                                <div>
                                    <span class="text-[10px] text-slate-450 uppercase block">Total Filas:</span>
                                    <span class="font-extrabold text-slate-850" x-text="Number({{ $log->total_rows }}).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-450 uppercase block">Procesadas OK:</span>
                                    <span class="font-extrabold text-emerald-600" x-text="Number({{ $log->imported_rows }}).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-450 uppercase block">Rechazadas:</span>
                                    <span class="font-extrabold text-rose-600" x-text="Number({{ $log->rejected_rows }}).toLocaleString()"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-450 uppercase block">Estatus:</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold border uppercase tracking-wider
                                        {{ $log->status === 'completado' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-rose-50 text-rose-700 border-rose-250' }}">
                                        {{ $log->status }}
                                    </span>
                                </div>
                            </div>

                            @if($log->errors && count($log->errors) > 0)
                                <div class="mt-3 bg-rose-50/50 border border-rose-150 rounded-lg p-3 space-y-1">
                                    <span class="font-bold text-rose-700 block text-[9px] uppercase tracking-wider">Errores Detectados:</span>
                                    <div class="max-h-28 overflow-y-auto space-y-1 font-mono text-[10px] text-rose-600">
                                        @foreach($log->errors as $err)
                                            <p class="leading-tight">&#8226; {{ $err }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-400 italic text-center py-4">No se registran importaciones previas de tarifario para este contrato.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 3: HISTORIAL DE CAMBIOS -->
        <div x-show="activeTab === 'historial'" class="space-y-4 animate-fade-in text-xs">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2 mb-4">Bitácora de Modificaciones a Tarifas</h3>
                
                <div class="space-y-4">
                    @forelse($logs as $log)
                        <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/30 text-xs relative">
                            <div class="flex justify-between items-center font-bold text-slate-750">
                                <span class="capitalize">Acción: {{ str_replace('_', ' ', $log->action) }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('d/m/Y h:i A') }}</span>
                            </div>
                            <p class="text-slate-500 mt-1 font-semibold">Modificado por: {{ $log->user->name ?? 'Usuario Sistema' }}</p>
                            <p class="text-slate-600 mt-1"><b>Detalle:</b> {{ $log->observation }}</p>
                        </div>
                    @empty
                        <p class="text-slate-400 italic text-center py-6">No se registran cambios tarifarios en la bitácora de auditoría.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- TAB 4: VERSIONES DE CONTRATO -->
        <div x-show="activeTab === 'versiones'" class="space-y-4 animate-fade-in text-xs">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/50 font-bold text-slate-450 text-[10px] uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">No. Versión</th>
                            <th scope="col" class="px-6 py-4 text-left">Motivo del Cambio</th>
                            <th scope="col" class="px-6 py-4 text-center">F. Vigencia</th>
                            <th scope="col" class="px-6 py-4 class-center">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-650">
                        @foreach($contrato->versions as $ver)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $ver->version_number }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $ver->change_reason }}
                                    <span class="block text-[10px] text-slate-450 mt-1">Aprobado por: {{ $ver->approved_by ? 'Administrador ARS' : 'Sistema' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span>{{ $ver->effective_from->format('d/m/Y') }}</span>
                                    @if($ver->effective_to)
                                        <span class="text-slate-400 block mt-0.5">al {{ $ver->effective_to->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-emerald-600 block mt-0.5 font-bold">Activo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold border uppercase tracking-wider
                                        {{ $ver->status === 'vigente' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                        {{ $ver->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Editar Tarifa Individual -->
    <div x-show="openTarifaModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-100 p-5 space-y-4"
             @click.away="openTarifaModal = false">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Configuración de Tarifa y Reglas</h3>
                <p class="text-[10px] text-slate-450 mt-0.5">Establezca los montos pactados y límites clínicos para la autorización del servicio.</p>
            </div>

            <form action="{{ route('ars.pss.contratos_tarifarios.tarifa', $contrato->id) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Servicio del Catálogo <span class="text-rose-500">*</span></label>
                    <select name="pdss_service_id" x-model="selectedServiceId" required class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                        <option value="">Seleccione servicio...</option>
                        @foreach($pdssServices as $s)
                            <option value="{{ $s->id }}">{{ $s->simon_code }} - {{ $s->coverage_description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Monto Pactado (DOP) <span class="text-rose-500">*</span></label>
                        <input type="number" name="contracted_amount" x-model="selectedAmount" required step="0.01" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Copago del Afiliado (%) <span class="text-rose-500">*</span></label>
                        <input type="number" name="copay_percent" x-model="selectedCopay" required min="0" max="100" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Límite Frecuencia (Cantidad)</label>
                        <input type="number" name="frequency_limit" x-model="selectedFreq" placeholder="Opcional" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Período Frecuencia</label>
                        <select name="frequency_period" x-model="selectedPeriod" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                            <option value="dia">Día</option>
                            <option value="mes">Mes</option>
                            <option value="año">Año</option>
                            <option value="evento">Evento</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2 border-t border-slate-50 pt-3">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="requires_authorization" x-model="selectedReqAuth" value="1" class="rounded border-slate-200 text-teal-600 focus:ring-teal-200">
                        <span class="text-slate-650 font-bold">Requiere Autorización Previa</span>
                    </label>

                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="requires_medical_audit" x-model="selectedReqAudit" value="1" class="rounded border-slate-200 text-teal-600 focus:ring-teal-200">
                        <span class="text-slate-650 font-bold">Requiere Auditoría Médica</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-50">
                    <button type="button" @click="openTarifaModal = false" class="px-4 py-2 border border-slate-200 rounded-full text-slate-500 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-full transition shadow-xs">
                        Guardar Tarifa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Crear Nueva Versión del Contrato -->
    <div x-show="openVersionModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fade-in"
         x-cloak>
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-100 p-5 space-y-4"
             @click.away="openVersionModal = false">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Crear Nueva Versión del Tarifario</h3>
                <p class="text-[10px] text-slate-450 mt-0.5">El sistema duplicará las tarifas vigentes a la nueva versión para modificaciones masivas futuras.</p>
            </div>

            <form action="{{ route('ars.pss.contratos_tarifarios.version', $contrato->id) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Código Versión <span class="text-rose-500">*</span></label>
                    <input type="text" name="version_number" required placeholder="Ej: 2.0.0 o 1.1.0" class="w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Cambio o Revisión <span class="text-rose-500">*</span></label>
                    <textarea name="change_reason" rows="4" required minlength="5"
                              class="w-full rounded-xl border border-slate-200 bg-[#eaf1fb]/40 p-3 text-slate-800 focus:bg-white focus:outline-none placeholder:text-slate-400"
                              placeholder="Indique detalladamente las razones de esta nueva versión del contrato..."></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-50">
                    <button type="button" @click="openVersionModal = false" class="px-4 py-2 border border-slate-200 rounded-full text-slate-500 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-full transition shadow-xs">
                        Crear Versión
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
