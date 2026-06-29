@extends('layouts.authorization-portal')

@section('title', 'Nueva Dispensación de Medicamentos')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="dispensacionFarmacia">
    
    <!-- Pantalla de Carga de Simulación de Envío a ARS -->
    <div x-show="submitting" 
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-900/80 backdrop-blur-md text-white p-6"
         x-transition x-cloak>
        <div class="bg-white text-slate-800 p-8 rounded-3xl border border-slate-100 shadow-2xl max-w-sm w-full text-center space-y-6 animate-fade-in-up">
            <!-- Spinner Animado -->
            <div class="relative w-20 h-20 mx-auto">
                <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-[#49bcf7] animate-spin"></div>
                <div class="absolute inset-2 bg-slate-50 rounded-full flex items-center justify-center">
                    <img src="{{ asset('assets/images/arscmd2.png') }}" alt="CMD" class="h-8 w-auto object-contain animate-pulse">
                </div>
            </div>
            
            <div class="space-y-2">
                <h4 class="font-extrabold text-slate-800 text-sm tracking-wide">Procesando Dispensación ARS</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Validando receta médica y saldos con la base de datos de **ARS CMD**...
                </p>
            </div>

            <!-- Pasos de simulación en vivo -->
            <div class="text-[10px] bg-slate-50 border border-slate-100 rounded-2xl p-4 text-left font-mono space-y-1.5 text-slate-500">
                <div class="flex items-center space-x-2">
                    <template x-if="submitStep >= 1">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="submitStep < 1">
                        <svg class="animate-spin w-3 h-3 text-[#49bcf7]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span :class="submitStep >= 1 ? 'text-emerald-700 font-bold' : 'text-slate-700 font-bold animate-pulse'">1. Transmitiendo datos de receta...</span>
                </div>
                <div class="flex items-center space-x-2">
                    <template x-if="submitStep >= 2">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="submitStep < 2">
                        <template x-if="submitStep >= 1">
                            <svg class="animate-spin w-3 h-3 text-[#49bcf7]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="submitStep < 1">
                            <span class="w-3"></span>
                        </template>
                    </template>
                    <span :class="submitStep >= 2 ? 'text-emerald-700 font-bold' : (submitStep === 1 ? 'text-slate-700 font-bold animate-pulse' : '')">2. Verificando tope anual de medicamentos...</span>
                </div>
                <div class="flex items-center space-x-2">
                    <template x-if="submitStep >= 3">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="submitStep < 3">
                        <template x-if="submitStep >= 2">
                            <svg class="animate-spin w-3 h-3 text-[#49bcf7]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="submitStep < 2">
                            <span class="w-3"></span>
                        </template>
                    </template>
                    <span :class="submitStep >= 3 ? 'text-emerald-700 font-bold' : (submitStep === 2 ? 'text-slate-700 font-bold animate-pulse' : '')">3. Registrando copagos y autorización...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Título y Encabezado -->
    <div class="border-b border-[#ecf0f3] pb-4">
        <h1 class="text-xl font-black font-rubik text-[#403663] uppercase tracking-wide flex items-center gap-2">
            <i class="fas fa-prescription-bottle-medical text-[#49bcf7]"></i> Nueva Dispensación Farmacéutica
        </h1>
        <p class="text-xs text-slate-400 mt-0.5">Ingresa los datos de la receta médica del afiliado para validar la cobertura y registrar la dispensación.</p>
    </div>

    <form action="{{ route('pss.farmacia.guardar_dispensacion') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit.prevent="onSubmitForm($event)">
        @csrf
        
        <!-- SECCIÓN A: BÚSQUEDA Y VALIDACIÓN DE AFILIADO -->
        <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-black font-rubik text-[#403663] uppercase tracking-wider border-b border-[#ecf0f3] pb-2 flex items-center gap-2">
                <span class="h-5 w-5 bg-blue-50 text-[#49bcf7] rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
                Validación de Cobertura del Afiliado
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1 font-rubik">Tipo de Identificación</label>
                    <select x-model="tipoBusqueda" class="block w-full rounded-full border-slate-200 py-2.5 text-xs text-slate-700 font-bold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                        <option value="cedula">Cédula / NSS</option>
                        <option value="poliza">Número de Contrato (Póliza)</option>
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1 font-rubik">Identificación</label>
                    <input type="text" x-model="searchQuery" @input="formatQuery" 
                           placeholder="Ingresa la identificación..."
                           class="block w-full rounded-full border-slate-200 py-2.5 px-4 text-xs font-bold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                </div>

                <div>
                    <button type="button" @click="buscarAfiliado" :disabled="searching || !searchQuery"
                            class="w-full bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-xs py-3 px-6 rounded-full transition disabled:opacity-50 flex items-center justify-center gap-2 shadow-sm">
                        <template x-if="searching">
                            <i class="fas fa-spinner animate-spin"></i>
                        </template>
                        <template x-if="!searching">
                            <i class="fas fa-search"></i>
                        </template>
                        <span>Consultar Elegibilidad</span>
                    </button>
                </div>
            </div>

            <!-- Error de busqueda -->
            <div x-show="errorMessage" x-transition class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3" x-cloak>
                <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 text-sm"></i>
                <span class="text-xs font-semibold text-rose-900" x-text="errorMessage"></span>
            </div>

            <!-- Datos del Afiliado Consultados -->
            <div x-show="afiliado" x-transition class="p-6 bg-slate-50/50 border border-[#ecf0f3] rounded-3xl grid grid-cols-1 md:grid-cols-4 gap-6" x-cloak>
                <input type="hidden" name="afiliado_id" :value="afiliado ? afiliado.id : ''">
                <input type="hidden" name="afiliado_type" :value="afiliado ? afiliado.type : ''">
                
                <div class="md:col-span-2">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Nombre del Afiliado</span>
                    <span class="text-sm font-black font-rubik text-[#403663] block mt-0.5" x-text="afiliado ? (afiliado.nombres + ' ' + afiliado.apellidos) : ''"></span>
                    
                    <div class="flex gap-4 mt-2">
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Cédula</span>
                            <span class="text-xs font-mono font-bold text-slate-700" x-text="afiliado ? afiliado.cedula : ''"></span>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">No. Contrato</span>
                            <span class="text-xs font-mono font-bold text-slate-700" x-text="afiliado ? afiliado.poliza : ''"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Plan de Salud</span>
                    <span class="text-xs font-bold text-slate-700 block mt-0.5" x-text="afiliado ? (afiliado.plan1 + ' · ' + afiliado.plan2) : ''"></span>
                    
                    <div class="mt-2">
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Estatus de Afiliación</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 mt-0.5">Activo</span>
                    </div>
                </div>

                <div class="border-l border-[#ecf0f3] pl-6 flex flex-col justify-center bg-blue-50/20 p-4 rounded-2xl">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Disponible Anual Medicamentos</span>
                    <span class="text-lg font-black text-[#49bcf7] font-rubik mt-1">DOP <span x-text="formatCurrency(disponibleAnual)"></span></span>
                    <span class="text-[9px] text-slate-400 mt-0.5">Tope total: DOP <span x-text="formatCurrency(limiteAnual)"></span></span>
                </div>
            </div>
        </div>

        <!-- SECCIÓN B: DATOS DE LA PRESCRIPCIÓN / RECETA -->
        <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6 shadow-sm space-y-4" x-show="afiliado" x-transition x-cloak>
            <h3 class="text-xs font-black font-rubik text-[#403663] uppercase tracking-wider border-b border-[#ecf0f3] pb-2 flex items-center gap-2">
                <span class="h-5 w-5 bg-blue-50 text-[#49bcf7] rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
                Información del Médico & Prescripción
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Médico Prescriptor <span class="text-rose-500">*</span></label>
                    <input type="text" name="doctor_name" required placeholder="Ej. Dr. Juan Pérez"
                           class="block w-full rounded-full border-slate-200 py-2.5 px-4 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                </div>

                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Exequátur <span class="text-rose-500">*</span></label>
                    <input type="text" name="doctor_exequatur" required placeholder="Ej. 12345-67"
                           class="block w-full rounded-full border-slate-200 py-2.5 px-4 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                </div>

                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Especialidad</label>
                    <input type="text" name="specialty" placeholder="Ej. Pediatría / General"
                           class="block w-full rounded-full border-slate-200 py-2.5 px-4 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                </div>

                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Fecha de Receta <span class="text-rose-500">*</span></label>
                    <input type="date" name="prescription_date" required max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                           class="block w-full rounded-full border-slate-200 py-2.5 px-4 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                </div>

                <!-- Diagnóstico CIE-10 (Buscador Predictivo con Dropdown) -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Diagnóstico CIE-10 <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="text" 
                               x-model="diagnostico" 
                               @focus="open = true"
                               @input="open = true" required
                               class="block w-full rounded-full border-slate-200 py-2.5 pl-4 pr-10 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]"
                               placeholder="Escribe diagnóstico o CIE-10...">
                        
                        <button type="button" @click="open = !open" 
                                class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-650">
                            <i class="fas fa-chevron-down text-xs transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                    </div>

                    <!-- Dropdown Results -->
                    <div x-show="open && searchedDiagnosticos.length > 0" 
                         class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-2xl bg-white border border-slate-200 shadow-lg py-1 text-xs" x-cloak>
                        <template x-for="diag in searchedDiagnosticos" :key="diag">
                            <button type="button" 
                                    @click="
                                        diagnostico = diag;
                                        open = false;
                                    " 
                                    class="w-full text-left px-4 py-2 hover:bg-slate-50 focus:bg-slate-50 text-slate-700 font-semibold transition border-b border-slate-50 last:border-b-0"
                                    x-text="diag">
                            </button>
                        </template>
                    </div>

                    <input type="hidden" name="diagnostico" :value="diagnostico">
                </div>

                <div>
                    <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Imagen / Adjunto Receta</label>
                    <input type="file" name="documento_receta"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#49bcf7] hover:file:bg-blue-100 transition cursor-pointer">
                </div>
            </div>
            
            <div>
                <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-wider mb-1 font-rubik">Observaciones de Receta</label>
                <textarea name="observations" rows="2" placeholder="Notas adicionales..."
                          class="block w-full rounded-2xl border-slate-200 py-3 px-4 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]"></textarea>
            </div>
        </div>

        <!-- SECCIÓN C: MEDICAMENTOS Y FACTURACIÓN -->
        <div class="bg-white border border-[#ecf0f3] rounded-3xl p-6 shadow-sm space-y-4" x-show="afiliado" x-transition x-cloak>
            <h3 class="text-xs font-black font-rubik text-[#403663] uppercase tracking-wider border-b border-[#ecf0f3] pb-2 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="h-5 w-5 bg-blue-50 text-[#49bcf7] rounded-full flex items-center justify-center text-[10px] font-bold">3</span>
                    Detalle de Medicamentos
                </span>
                
                <button type="button" @click="agregarMedicamento"
                        class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-bold text-[10px] px-3 py-1.5 rounded-full transition shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-plus"></i> Agregar Fila
                </button>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-slate-400 font-bold border-b border-[#ecf0f3]">
                            <th class="py-2.5 px-3 w-1/3">Medicamento (Buscador)</th>
                            <th class="py-2.5 px-3 text-center w-20">Cantidad</th>
                            <th class="py-2.5 px-3 text-right w-28">P. Unitario</th>
                            <th class="py-2.5 px-3 text-right w-28">P. Total</th>
                            <th class="py-2.5 px-3 text-right w-28 text-emerald-600">Cobert. ARS (70%)</th>
                            <th class="py-2.5 px-3 text-right w-28 text-slate-600">Copago</th>
                            <th class="py-2.5 px-3 text-center w-12">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecf0f3] text-slate-700">
                        <template x-for="(med, index) in medicamentosAgregados" :key="index">
                            <tr class="hover:bg-slate-50/50 transition">
                                <!-- Medicamento Autocomplete -->
                                <td class="py-3 px-3">
                                    <input type="text" x-model="med.search" @input="onMedicamentoInput(med)" @change="onMedicamentoInput(med)"
                                           list="medicamentos-list"
                                           placeholder="Escribe código o nombre..." required
                                           class="block w-full rounded-full border-slate-200 py-1.5 px-3 text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                                    
                                    <input type="hidden" :name="'medicamentos['+index+']'" :value="med.id">
                                </td>

                                <!-- Cantidad -->
                                <td class="py-3 px-3 text-center">
                                    <input type="number" :name="'cantidades['+index+']'" x-model="med.cantidad" @input="recalcular" min="1" required
                                           class="block w-16 mx-auto rounded-full border-slate-200 py-1.5 text-center text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                                </td>

                                <!-- Precio unitario -->
                                <td class="py-3 px-3">
                                    <input type="number" :name="'precios['+index+']'" x-model="med.precio" @input="recalcular" step="0.01" min="0.01" required
                                           class="block w-24 ml-auto rounded-full border-slate-200 py-1.5 text-right text-xs font-semibold focus:border-[#49bcf7] focus:ring-[#49bcf7]">
                                </td>

                                <!-- Total solicitado -->
                                <td class="py-3 px-3 text-right font-bold text-slate-800" x-text="'DOP ' + formatCurrency(med.precio * med.cantidad)"></td>

                                <!-- Cobertura ARS (70% o límite) -->
                                <td class="py-3 px-3 text-right font-black text-emerald-600" x-text="'DOP ' + formatCurrency(med.cobertura)"></td>

                                <!-- Copago (30% o excedente) -->
                                <td class="py-3 px-3 text-right font-bold text-slate-600" x-text="'DOP ' + formatCurrency(med.copago)"></td>

                                <!-- Borrar fila -->
                                <td class="py-3 px-3 text-center">
                                    <button type="button" @click="eliminarMedicamento(index)"
                                            class="text-rose-500 hover:text-rose-700 p-2 transition">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Resumen y Totales -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end border-t border-[#ecf0f3] pt-6 gap-6">
                <!-- Alertas de Tope / Límite -->
                <div class="max-w-md w-full space-y-2">
                    <div x-show="totalReclamadoExcede" class="p-3 bg-amber-50 border border-amber-100 rounded-2xl flex items-start gap-2.5" x-cloak>
                        <i class="fas fa-circle-exclamation text-amber-500 mt-0.5"></i>
                        <span class="text-[10px] font-bold text-amber-900 leading-tight">El monto solicitado de cobertura excede tu tope anual disponible para medicamentos ambulatorios. El excedente se sumará al copago.</span>
                    </div>
                </div>

                <!-- Cuadro de Totales -->
                <div class="bg-slate-50/50 border border-[#ecf0f3] rounded-3xl p-6 w-full md:w-80 space-y-3 font-semibold text-xs">
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Total Facturado</span>
                        <span class="font-bold text-slate-800" x-text="'DOP ' + formatCurrency(totalFacturado)"></span>
                    </div>
                    <div class="flex justify-between items-center text-emerald-600 font-bold border-b border-[#ecf0f3] pb-2">
                        <span>Cobertura ARS (70%)</span>
                        <span class="font-black text-emerald-600" x-text="'DOP ' + formatCurrency(totalCobertArs)"></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-700 font-black text-sm pt-1">
                        <span>Copago Afiliado</span>
                        <span class="text-[#403663]" x-text="'DOP ' + formatCurrency(totalCopago)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end gap-2" x-show="afiliado" x-transition x-cloak>
            <a href="{{ route('pss.dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-550 font-bold text-xs px-6 py-3.5 rounded-2xl transition">
                Cancelar
            </a>
            <button type="submit" :disabled="medicamentosAgregados.length === 0"
                    class="bg-[#49bcf7] hover:bg-[#31a3e6] text-white font-black uppercase tracking-wider text-xs px-8 py-3.5 rounded-2xl transition disabled:opacity-50 shadow-md shadow-blue-500/10">
                Dispensar y Reclamar ARS
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dispensacionFarmacia', () => ({
        submitting: false,
        submitStep: 0,
        tipoBusqueda: 'cedula',
        searchQuery: '',
        searching: false,
        afiliado: null,
        disponibleAnual: 0.00,
        limiteAnual: 8000.00,
        errorMessage: '',
        
        doctorName: '',
        doctorExequatur: '',
        diagnostico: '',
        diagnosticosSugeridos: @json($diagnosticos),

        medicamentos: @json($medicamentos),
        medicamentosAgregados: [],

        totalFacturado: 0.00,
        totalCobertArs: 0.00,
        totalCopago: 0.00,
        totalReclamadoExcede: false,

        init() {
            // Fila inicial de medicamento
            this.agregarMedicamento();
        },

        get searchedDiagnosticos() {
            const query = (this.diagnostico || '').toLowerCase().trim();
            const list = this.diagnosticosSugeridos || [];
            if (this.diagnostico && list.includes(this.diagnostico)) {
                return list;
            }
            if (!query) return list;
            return list.filter(d => d.toLowerCase().includes(query));
        },

        formatQuery(e) {
            let value = this.searchQuery.replace(/[^a-zA-Z0-9]/g, '');
            if (this.tipoBusqueda === 'cedula') {
                if (value.length > 11) value = value.slice(0, 11);
            }
            this.searchQuery = value;
        },

        async buscarAfiliado() {
            this.searching = true;
            this.errorMessage = '';
            this.afiliado = null;
            
            try {
                const response = await fetch(`/portal-autorizaciones/afiliados/validar-json?identificacion=${this.searchQuery}&tipo_busqueda=${this.tipoBusqueda}`);
                const data = await response.json();
                
                if (data.success) {
                    this.afiliado = data.afiliado;
                    // Mock balance disponible
                    this.disponibleAnual = 8000.00 - (Math.random() * 3200); // balance disponible simulado
                    this.recalcular();
                } else {
                    this.errorMessage = data.message;
                }
            } catch (err) {
                this.errorMessage = 'Ocurrió un error al consultar elegibilidad del afiliado.';
            } finally {
                this.searching = false;
            }
        },

        agregarMedicamento() {
            this.medicamentosAgregados.push({
                id: '',
                search: '',
                cantidad: 1,
                precio: 0.00,
                cobertura: 0.00,
                copago: 0.00
            });
            this.recalcular();
        },

        eliminarMedicamento(index) {
            this.medicamentosAgregados.splice(index, 1);
            this.recalcular();
        },

        onMedicamentoInput(med) {
            const query = (med.search || '').trim();
            const selected = this.medicamentos.find(m => (m.codigo + ' - ' + m.descripcion) === query);
            if (selected) {
                med.id = selected.id;
                med.precio = parseFloat(selected.precio || 500.00);
            } else {
                med.id = '';
            }
            this.recalcular();
        },

        recalcular() {
            let facturado = 0;
            let cobertArs = 0;
            let copago = 0;
            let disponibleTemp = this.disponibleAnual;

            this.medicamentosAgregados.forEach(med => {
                const itemTotal = med.precio * med.cantidad;
                facturado += itemTotal;

                // Cobertura del 70% para medicamentos ambulatorios
                let coverPossible = itemTotal * 0.70;
                
                if (coverPossible > disponibleTemp) {
                    const covered = disponibleTemp;
                    disponibleTemp = 0;
                    med.cobertura = covered;
                    med.copago = itemTotal - covered;
                } else {
                    disponibleTemp -= coverPossible;
                    med.cobertura = coverPossible;
                    med.copago = itemTotal - coverPossible;
                }

                cobertArs += med.cobertura;
                copago += med.copago;
            });

            this.totalFacturado = facturado;
            this.totalCobertArs = cobertArs;
            this.totalCopago = copago;
            
            // Si el saldo disponible es 0 y el facturado es mayor a 0, alerta de tope excedido
            this.totalReclamadoExcede = disponibleTemp <= 0 && facturado > 0;
        },

        formatCurrency(value) {
            return parseFloat(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        onSubmitForm(e) {
            if (this.medicamentosAgregados.length === 0) {
                alert('Debes agregar al menos un medicamento.');
                return;
            }
            this.submitting = true;
            this.submitStep = 0;

            setTimeout(() => { this.submitStep = 1; }, 1000);
            setTimeout(() => { this.submitStep = 2; }, 2000);
            setTimeout(() => { this.submitStep = 3; }, 3000);

            setTimeout(() => {
                e.target.submit();
            }, 4000);
        }
    }));
});
</script>

<datalist id="medicamentos-list">
    @foreach($medicamentos as $item)
        <option value="{{ $item['codigo'] }} - {{ $item['descripcion'] }}"></option>
    @endforeach
</datalist>
@endsection
