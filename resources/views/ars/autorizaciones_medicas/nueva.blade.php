@extends('layouts.ars')

@section('title', 'Crear Autorización Médica Core')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    step: 1,
    selectedAfiliado: null,
    selectedPss: null,
    selectedService: null,
    montoSolicitado: '',
    canalEntrada: 'llamada',
    medico: '',
    diagnostico: '',
    prioridad: 'Media',
    addedServices: [],

    agregarServicio() {
        if (!this.selectedService) return;
        if (this.addedServices.some(s => s.id === this.selectedService.id)) {
            alert('Este servicio ya está agregado a la solicitud.');
            return;
        }
        this.addedServices.push({
            id: this.selectedService.id,
            simon_code: this.selectedService.simon_code,
            description: this.selectedService.description,
            coverage_type: this.selectedService.coverage_type,
            contracted_amount: this.tariffAmount || 0,
            monto_solicitado: this.montoSolicitado || this.tariffAmount || 0,
            requires_medical_audit: this.tariffMessage.includes('⚠️'),
            is_high_cost: this.tariffMessage.includes('💎'),
            message: this.tariffMessage
        });
        this.selectedService = null;
        this.searchServiceQuery = '';
        this.tariffAmount = '';
        this.tariffMessage = '';
        this.montoSolicitado = '';
    },
    eliminarServicio(index) {
        this.addedServices.splice(index, 1);
    },
    get totalSolicitado() {
        return this.addedServices.reduce((sum, s) => sum + parseFloat(s.monto_solicitado || 0), 0);
    },

    // Estados de búsqueda
    searchAfiliadoQuery: '',
    searchPssQuery: '',
    searchServiceQuery: '',

    // Resultados AJAX
    afiliadosResults: [],
    pssResults: [],
    servicesResults: [],

    // Cargas
    loadingAfiliados: false,
    loadingPss: false,
    loadingServices: false,

    // Tarifas y Mensajes dinámicos
    tariffAmount: '',
    tariffMessage: '',
    loadingTariff: false,

    // Historial del afiliado (Sidebar)
    afiliadoHistorial: [],
    loadingHistorial: false,

    async cargarHistorial() {
        if (!this.selectedAfiliado) {
            this.afiliadoHistorial = [];
            return;
        }
        this.loadingHistorial = true;
        try {
            let response = await fetch(`/core/autorizaciones-medicas/historial-afiliado-ajax?afiliado_id=${this.selectedAfiliado.id}`);
            this.afiliadoHistorial = await response.json();
        } catch (e) {
            console.error('Error al cargar historial', e);
        } finally {
            this.loadingHistorial = false;
        }
    },

    // Buscador predictivo de Afiliados
    async buscarAfiliados() {
        if (this.searchAfiliadoQuery.length < 2) {
            this.afiliadosResults = [];
            return;
        }
        this.loadingAfiliados = true;
        try {
            let response = await fetch(`/core/autorizaciones-medicas/buscar-afiliado-ajax?q=${encodeURIComponent(this.searchAfiliadoQuery)}`);
            this.afiliadosResults = await response.json();
        } catch (e) {
            console.error('Error al buscar afiliados', e);
        } finally {
            this.loadingAfiliados = false;
        }
    },

    // Buscador predictivo de PSS
    async buscarPss() {
        if (this.searchPssQuery.length < 2) {
            this.pssResults = [];
            return;
        }
        this.loadingPss = true;
        try {
            let response = await fetch(`/core/autorizaciones-medicas/buscar-pss-ajax?q=${encodeURIComponent(this.searchPssQuery)}`);
            this.pssResults = await response.json();
        } catch (e) {
            console.error('Error al buscar PSS', e);
        } finally {
            this.loadingPss = false;
        }
    },

    // Buscador predictivo de Servicios del Catálogo
    async buscarServicios() {
        if (this.searchServiceQuery.length < 2) {
            this.servicesResults = [];
            return;
        }
        this.loadingServices = true;
        try {
            let pssParam = this.selectedPss ? `&pss_id=${this.selectedPss.id}` : '';
            let response = await fetch(`/core/pdss/buscar-servicio?q=${encodeURIComponent(this.searchServiceQuery)}${pssParam}`);
            this.servicesResults = await response.json();
        } catch (e) {
            console.error('Error al buscar servicios', e);
        } finally {
            this.loadingServices = false;
        }
    },

    // Obtiene la tarifa contratada dinámicamente
    async obtenerTarifa() {
        if (!this.selectedPss || !this.selectedService) return;
        this.loadingTariff = true;
        this.tariffMessage = '';
        try {
            let response = await fetch(`/core/autorizaciones-medicas/obtener-tarifa-ajax?pss_id=${this.selectedPss.id}&pdss_service_id=${this.selectedService.id}`);
            let data = await response.json();
            if (data.success) {
                this.montoSolicitado = data.contracted_amount;
                this.tariffAmount = data.contracted_amount;
                if (data.requires_medical_audit) {
                    this.tariffMessage = '⚠️ Requiere Auditoría Médica obligatoria.';
                } else if (data.is_high_cost) {
                    this.tariffMessage = '💎 Servicio de Alto Costo. Requiere aprobación especial.';
                } else {
                    this.tariffMessage = '✅ Tarifa pactada cargada exitosamente.';
                }
            } else {
                this.montoSolicitado = '';
                this.tariffAmount = '';
                this.tariffMessage = '❌ ' + data.message;
            }
        } catch (e) {
            console.error('Error al cargar tarifa', e);
            this.tariffMessage = '❌ Error de comunicación con el motor de convenios.';
        } finally {
            this.loadingTariff = false;
        }
    }
}">
    <!-- Header Premium -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-3xl p-6 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="p-2.5 bg-white rounded-2xl shadow-sm flex-shrink-0">
                <img src="{{ asset('assets/images/arscmd2.png') }}" alt="ARS CMD Logo" class="h-8 w-auto object-contain">
            </div>
            <div>
                <h2 class="text-xl font-bold tracking-tight">Nueva Solicitud de Autorización Médica</h2>
                <p class="text-xs text-blue-100 mt-1 font-medium">Consola de evaluación automática de reglas clínicas, convenios y tarifarios activos.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-2xl border border-white/10 text-xs">
            <span class="font-extrabold uppercase font-mono tracking-wider">Canal:</span>
            <span class="font-bold capitalize bg-white/20 px-2 py-0.5 rounded" x-text="canalEntrada"></span>
        </div>
    </div>

    <!-- Diseño en Rejilla de Dos Columnas (Formulario + Sidebar) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- COLUMNA IZQUIERDA: FORMULARIO PRINCIPAL (col-span-2) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Indicador de Progreso Visual Avanzado -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm grid grid-cols-3 gap-2">
                <!-- Paso 1 -->
                <button type="button" @click="if(selectedAfiliado) step = 1" 
                        class="flex flex-col items-center md:items-start text-center md:text-left p-3 rounded-2xl transition-all"
                        :class="step === 1 ? 'bg-blue-50/50 border-l-4 border-blue-600' : 'hover:bg-slate-50'">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paso 1</span>
                    <span class="text-xs font-black mt-1" :class="step === 1 ? 'text-blue-700' : 'text-slate-700'">Canal y Afiliado</span>
                </button>
                <!-- Paso 2 -->
                <button type="button" @click="if(selectedAfiliado && selectedPss && addedServices.length > 0) step = 2" 
                        class="flex flex-col items-center md:items-start text-center md:text-left p-3 rounded-2xl transition-all"
                        :class="step === 2 ? 'bg-blue-50/50 border-l-4 border-blue-600' : 'hover:bg-slate-50'"
                        :disabled="!selectedAfiliado">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paso 2</span>
                    <span class="text-xs font-black mt-1" :class="step === 2 ? 'text-blue-700' : 'text-slate-750'">Prestador y Tarifa</span>
                </button>
                <!-- Paso 3 -->
                <button type="button" @click="if(selectedAfiliado && selectedPss && addedServices.length > 0 && medico && diagnostico) step = 3" 
                        class="flex flex-col items-center md:items-start text-center md:text-left p-3 rounded-2xl transition-all"
                        :class="step === 3 ? 'bg-blue-50/50 border-l-4 border-blue-600' : 'hover:bg-slate-50'"
                        :disabled="!selectedAfiliado || !selectedPss || addedServices.length === 0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paso 3</span>
                    <span class="text-xs font-black mt-1" :class="step === 3 ? 'text-blue-700' : 'text-slate-750'">Datos y Envío</span>
                </button>
            </div>

            <!-- Formulario Principal -->
            <form action="{{ route('ars.autorizaciones_medicas.store') }}" method="POST" enctype="multipart/form-data" 
                  class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6 text-xs">
                @csrf
                <input type="hidden" name="afiliado_id" :value="selectedAfiliado ? selectedAfiliado.id : ''">
                <input type="hidden" name="pss_id" :value="selectedPss ? selectedPss.id : ''">
                <input type="hidden" name="pdss_service_id" :value="addedServices.length > 0 ? addedServices[0].id : ''">
                <input type="hidden" name="servicios_json" :value="JSON.stringify(addedServices)">

                <!-- PASO 1: CANAL Y AFILIADO -->
                <div x-show="step === 1" class="space-y-6" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Canal de entrada -->
                        <div class="md:col-span-1 space-y-2">
                            <label class="block font-bold text-slate-750 uppercase tracking-wider text-[9px]">Canal de Entrada</label>
                            <div class="grid grid-cols-1 gap-2">
                                <template x-for="c in ['llamada', 'correo', 'whatsapp', 'presencial', 'interno']">
                                    <label class="flex items-center space-x-3 p-3 border border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition"
                                           :class="canalEntrada === c ? 'bg-blue-50/30 border-blue-200' : ''">
                                        <input type="radio" name="channel" :value="c" x-model="canalEntrada" class="text-blue-600 focus:ring-blue-500">
                                        <span class="capitalize font-bold text-slate-750 text-xs" x-text="c"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Búsqueda de Afiliado -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="space-y-2">
                                <label class="block font-bold text-slate-750 uppercase tracking-wider text-[9px]">Buscar Afiliado (Cédula o Nombres)</label>
                                <div class="relative">
                                    <input type="text" x-model="searchAfiliadoQuery" @input.debounce.300ms="buscarAfiliados"
                                           placeholder="Ingrese cédula o nombres a buscar..." 
                                           class="w-full rounded-full border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all font-semibold">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </span>
                                    <!-- Spinner de carga -->
                                    <span x-show="loadingAfiliados" class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                        <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </span>
                                </div>
                            </div>

                            <!-- Resultados de Búsqueda -->
                            <div x-show="afiliadosResults.length > 0" class="bg-white border border-slate-100 rounded-2xl shadow-md max-h-60 overflow-y-auto p-2 space-y-1">
                                <template x-for="a in afiliadosResults" :key="a.id">
                                    <button type="button" @click="selectedAfiliado = a; afiliadosResults = []; searchAfiliadoQuery = ''; cargarHistorial();" 
                                            class="w-full text-left p-3 hover:bg-slate-50 rounded-xl flex justify-between items-center transition border border-transparent hover:border-slate-100">
                                        <div>
                                            <span class="block font-extrabold text-slate-800" x-text="a.nombres + ' ' + a.primer_apellido"></span>
                                            <span class="text-[10px] text-slate-400 font-mono mt-0.5 block" x-text="'Cédula: ' + a.cedula + ' | NSS: ' + a.nss"></span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-[9px] bg-slate-100 text-slate-650 px-2 py-0.5 rounded font-bold uppercase" x-text="a.tipo_afiliado"></span>
                                            <span class="text-[9px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-bold uppercase border border-blue-200" x-text="a.estado_afiliacion"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Afiliado Seleccionado -->
                            <div x-show="selectedAfiliado" class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center space-x-3.5">
                                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Afiliado Seleccionado</span>
                                        <h4 class="font-extrabold text-slate-855 text-xs mt-0.5" x-text="selectedAfiliado ? selectedAfiliado.nombres + ' ' + selectedAfiliado.primer_apellido + ' ' + (selectedAfiliado.segundo_apellido || '') : ''"></h4>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-[10px] text-slate-450 font-mono" x-text="'Cédula: ' + (selectedAfiliado ? selectedAfiliado.cedula : '')"></span>
                                            <span class="text-slate-300">|</span>
                                            <span class="text-[10px] text-slate-450 font-bold" x-text="selectedAfiliado ? selectedAfiliado.tipo_afiliado : ''"></span>
                                            <span class="text-slate-300">|</span>
                                            <span class="text-[9px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-extrabold border border-blue-200" x-text="selectedAfiliado ? selectedAfiliado.estado_afiliacion : ''"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="selectedAfiliado = null; afiliadoHistorial = [];" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 border border-rose-200 px-3 py-1 rounded-full hover:bg-rose-100 transition">Quitar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 2" :disabled="!selectedAfiliado" 
                                class="bg-[#0056c5] disabled:opacity-50 text-white rounded-full px-6 py-2.5 font-bold hover:bg-blue-700 transition shadow-xs">
                            Siguiente Paso
                        </button>
                    </div>
                </div>

                <!-- PASO 2: PRESTADOR Y SERVICIO -->
                <div x-show="step === 2" class="space-y-6" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Buscador de PSS -->
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-750 uppercase tracking-wider text-[9px]">Buscar Prestadora (PSS)</label>
                            <div class="relative">
                                <input type="text" x-model="searchPssQuery" @input.debounce.300ms="buscarPss"
                                       placeholder="Ingrese nombre del hospital, clínica o RNC..." 
                                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all font-semibold">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </span>
                                <span x-show="loadingPss" class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </div>

                            <!-- Resultados PSS -->
                            <div x-show="pssResults.length > 0" class="bg-white border border-slate-100 rounded-2xl shadow-md max-h-48 overflow-y-auto p-2 space-y-1">
                                <template x-for="p in pssResults" :key="p.id">
                                    <button type="button" @click="selectedPss = p; pssResults = []; searchPssQuery = ''; obtenerTarifa();" 
                                            class="w-full text-left p-3 hover:bg-slate-50 rounded-xl flex justify-between items-center transition border border-transparent hover:border-slate-100">
                                        <div>
                                            <span class="block font-bold text-slate-800" x-text="p.nombre"></span>
                                            <span class="text-[10px] text-slate-400 font-mono mt-0.5 block" x-text="'RNC: ' + p.rnc"></span>
                                        </div>
                                        <span class="text-[9px] bg-slate-100 text-slate-650 px-2 py-0.5 rounded font-bold uppercase" x-text="p.tipo_entidad"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Buscador de Servicio -->
                        <div class="space-y-2">
                            <label class="block font-bold text-slate-750 uppercase tracking-wider text-[9px]">Buscar Servicio o Procedimiento (Catálogo PDSS)</label>
                            <div class="relative">
                                <input type="text" x-model="searchServiceQuery" @input.debounce.300ms="buscarServicios"
                                       placeholder="Ingrese código Simon, CUPS o descripción..." 
                                       class="w-full rounded-full border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-150 transition-all font-semibold">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <span x-show="loadingServices" class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </div>

                            <!-- Resultados Servicios -->
                            <div x-show="servicesResults.length > 0" class="bg-white border border-slate-100 rounded-2xl shadow-md max-h-48 overflow-y-auto p-2 space-y-1">
                                <template x-for="s in servicesResults" :key="s.id">
                                    <button type="button" @click="selectedService = s; servicesResults = []; searchServiceQuery = ''; obtenerTarifa();" 
                                            class="w-full text-left p-3 hover:bg-slate-50 rounded-xl flex flex-col justify-center transition border border-transparent hover:border-slate-100">
                                        <span class="font-bold text-slate-800 text-[11px] leading-snug" x-text="'[' + s.simon_code + '] ' + s.description"></span>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-[9px] text-slate-400 font-bold uppercase" x-text="'Grupo: ' + s.group_name"></span>
                                            <span class="text-slate-300">|</span>
                                            <span class="text-[9px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-extrabold uppercase border border-blue-100" x-text="s.coverage_type"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Selecciones -->
                    <div class="grid grid-cols-1 gap-4">
                        <!-- PSS Seleccionada -->
                        <div x-show="selectedPss" class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between animate-fade-in">
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Prestadora Seleccionada</span>
                                <h4 class="font-extrabold text-slate-850 text-xs mt-0.5" x-text="selectedPss ? selectedPss.nombre : ''"></h4>
                                <p class="text-[10px] text-slate-450 font-mono mt-0.5" x-text="'RNC: ' + (selectedPss ? selectedPss.rnc : '') + ' | Tipo: ' + (selectedPss ? selectedPss.tipo_entidad : '')"></p>
                            </div>
                            <button type="button" @click="selectedPss = null; tariffMessage = ''; montoSolicitado = '';" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 border border-rose-200 px-3 py-1 rounded-full hover:bg-rose-100 transition">Quitar</button>
                        </div>

                        <!-- Servicio Seleccionado -->
                        <div x-show="selectedService" class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between animate-fade-in">
                            <div class="flex-1 mr-4">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Servicio PDSS Seleccionado</span>
                                <h4 class="font-extrabold text-[#0056c5] text-xs mt-0.5" x-text="selectedService ? '[' + selectedService.simon_code + '] ' + selectedService.description : ''"></h4>
                                <p class="text-[10px] text-slate-450 mt-0.5" x-text="'Tipo: ' + (selectedService ? selectedService.coverage_type : '') + ' | Cobertura base: ' + (selectedService ? (selectedService.copay_type === 'variable' ? 'Variable' : 'DOP ' + selectedService.amount_coverage) : '')"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" @click="agregarServicio()" class="bg-[#0056c5] hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-full transition shadow-xs">
                                    + Agregar a la Solicitud
                                </button>
                                <button type="button" @click="selectedService = null; tariffMessage = ''; montoSolicitado = '';" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 border border-rose-200 px-3 py-1 rounded-full hover:bg-rose-100 transition">Quitar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Loader de Tarifas & Mensajes de Negocio Clínico -->
                    <div x-show="loadingTariff || tariffMessage" class="p-4 rounded-2xl border transition-all animate-fade-in font-bold text-xs"
                         :class="tariffMessage.includes('❌') ? 'bg-rose-50 border-rose-150 text-rose-700' : (tariffMessage.includes('⚠️') || tariffMessage.includes('💎') ? 'bg-amber-50 border-amber-150 text-amber-700' : 'bg-blue-50/50 border-blue-150 text-blue-700')">
                        <div class="flex items-center space-x-2">
                            <span x-show="loadingTariff">
                                <svg class="animate-spin h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </span>
                            <span x-text="loadingTariff ? 'Consultando tarifario contractual...' : tariffMessage"></span>
                        </div>
                    </div>

                    <!-- Tabla de Servicios Agregados -->
                    <div x-show="addedServices.length > 0" class="space-y-3 pt-4 border-t border-slate-100/50">
                        <h3 class="font-extrabold text-slate-800 text-[10px] uppercase tracking-wider">Servicios Agregados a la Solicitud</h3>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left border-collapse text-[11px]">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                        <th class="p-3">Servicio</th>
                                        <th class="p-3">Cobertura</th>
                                        <th class="p-3 text-right">Tarifa Pactada</th>
                                        <th class="p-3 text-right" style="width: 160px;">Monto Solicitado (DOP)</th>
                                        <th class="p-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(s, index) in addedServices" :key="index">
                                        <tr class="border-b border-slate-150 last:border-0 hover:bg-slate-50/50">
                                            <td class="p-3 font-bold text-slate-800" x-text="'[' + s.simon_code + '] ' + s.description"></td>
                                            <td class="p-3">
                                                <span class="inline-block text-[9px] bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded font-bold uppercase" x-text="s.coverage_type"></span>
                                            </td>
                                            <td class="p-3 text-right font-mono font-semibold" x-text="'DOP ' + Number(s.contracted_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></td>
                                            <td class="p-3 text-right">
                                                <input type="number" step="0.01" x-model="s.monto_solicitado" class="w-full rounded-xl border border-slate-300 px-3 py-1.5 text-right font-mono font-bold focus:border-blue-500 focus:outline-none">
                                            </td>
                                            <td class="p-3 text-center">
                                                <button type="button" @click="eliminarServicio(index)" class="text-rose-600 hover:text-rose-800 p-1.5 bg-rose-50 border border-rose-200 rounded-xl transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-end p-3 bg-slate-50 rounded-2xl border border-slate-150 font-mono font-bold text-xs text-slate-750">
                            Total Solicitado: DOP <span x-text="Number(totalSolicitado).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})"></span>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 1" class="px-5 py-2.5 border border-slate-200 rounded-full text-slate-500 hover:bg-slate-100 transition font-bold">
                            Atrás
                        </button>
                        <button type="button" @click="step = 3" :disabled="!selectedPss || addedServices.length === 0" 
                                class="bg-[#0056c5] disabled:opacity-50 text-white rounded-full px-6 py-2.5 font-bold hover:bg-blue-700 transition shadow-xs">
                            Siguiente Paso
                        </button>
                    </div>
                </div>

                <!-- PASO 3: DATOS CLÍNICOS Y ENVÍO -->
                <div x-show="step === 3" class="space-y-6" x-transition>
                    <!-- Resumen de Servicios en la Orden -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex justify-between items-center font-mono">
                        <div>
                            <span class="text-[9px] text-slate-400 font-sans font-bold uppercase block tracking-wider">Resumen de Servicios a Autorizar</span>
                            <span class="text-xs font-black text-slate-800 mt-1 block">
                                Total Solicitado: DOP <span x-text="Number(totalSolicitado).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-slate-450 font-bold bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-200" x-text="addedServices.length + ' servicio(s) en la orden'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Médico Solicitante <span class="text-rose-500">*</span></label>
                            <input type="text" name="medico_solicitante" x-model="medico" required placeholder="Ej: Dr. Ramón Almonte" 
                                   class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Diagnóstico Clínico (Glosa o Código CIE-10) <span class="text-rose-500">*</span></label>
                            <input type="text" name="diagnostico" x-model="diagnostico" required placeholder="Ej: I10 - HTA Esencial" 
                                   class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Monto Total Solicitado (DOP)</label>
                            <input type="hidden" name="monto_solicitado" :value="totalSolicitado">
                            <div class="w-full rounded-full border border-slate-200 bg-slate-100 px-4 py-2.5 text-slate-800 font-mono font-bold text-xs" x-text="'DOP ' + Number(totalSolicitado).toLocaleString('en-US', {minimumFractionDigits: 2})"></div>
                            <p class="text-[10px] text-slate-400 mt-1 font-semibold">El monto total de la solicitud se calcula a partir de los montos individuales agregados en el Paso 2.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Prioridad Operativa</label>
                            <select name="prioridad" x-model="prioridad" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-800 focus:bg-white focus:outline-none">
                                <option value="Baja">Baja</option>
                                <option value="Media" selected>Media</option>
                                <option value="Alta">Alta / Urgencia</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Documento de Soporte (Orden Médica/CIE-10)</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 bg-slate-50 text-center hover:bg-slate-50/70 transition-all cursor-pointer">
                            <input type="file" name="documento_soporte" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-full">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Notas Internas (Visible solo para personal ARS)</label>
                        <textarea name="internal_notes" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-3.5 text-slate-800 focus:bg-white focus:outline-none" placeholder="Ingrese aclaraciones u observaciones para auditoría médica..."></textarea>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="step = 2" class="px-5 py-2.5 border border-slate-200 rounded-full text-slate-500 hover:bg-slate-100 transition font-bold">
                            Atrás
                        </button>
                        <button type="submit" :disabled="!medico || !diagnostico || addedServices.length === 0"
                                class="bg-[#0056c5] disabled:opacity-50 text-white rounded-full px-6 py-2.5 font-bold hover:bg-blue-700 transition shadow-xs">
                            Someter y Ejecutar Motor de Reglas
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- COLUMNA DERECHA: SIDEBAR CON HISTORIAL CLÍNICO (col-span-1) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Tarjeta de Historial -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-xs">
                <div class="flex items-center space-x-2 border-b border-slate-50 pb-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-xs">Historial del Afiliado</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Pre-autorizaciones y alertas clínicas</p>
                    </div>
                </div>

                <!-- Sin Afiliado Seleccionado -->
                <div x-show="!selectedAfiliado" class="text-center py-12 px-4 space-y-2">
                    <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <p class="text-slate-450 font-bold">Sin afiliado seleccionado</p>
                    <p class="text-[10px] text-slate-400 leading-relaxed">Busca y selecciona un afiliado en el Paso 1 para visualizar su historial clínico e información de cobertura en tiempo real.</p>
                </div>

                <!-- Con Afiliado Seleccionado -->
                <div x-show="selectedAfiliado" class="space-y-4" x-cloak>
                    <!-- Información del Plan -->
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-2 animate-fade-in">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Detalles de Afiliación</span>
                        <div class="grid grid-cols-2 gap-2 text-[10px] font-medium text-slate-650">
                            <div>
                                <span class="text-slate-400 block text-[9px] uppercase">Régimen:</span>
                                <span class="font-bold text-slate-800" x-text="selectedAfiliado ? selectedAfiliado.tipo_afiliado || 'Contributivo' : ''"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px] uppercase">Plan ARS:</span>
                                <span class="font-bold text-slate-800">Plan PDSS 11.0</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px] uppercase">NSS:</span>
                                <span class="font-bold font-mono text-slate-800" x-text="selectedAfiliado ? selectedAfiliado.nss : ''"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[9px] uppercase">Carnetizado:</span>
                                <span class="font-bold text-slate-800" x-text="selectedAfiliado && selectedAfiliado.esta_carnetizado ? 'Sí' : 'Sí'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Alertas Clínicas Predictivas -->
                    <div class="p-3 bg-amber-50/50 border border-amber-200 rounded-2xl text-[10px] text-amber-850 space-y-1 animate-fade-in">
                        <span class="font-bold flex items-center space-x-1 uppercase tracking-wider text-[9px] text-amber-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Alertas Clínicas</span>
                        </span>
                        <p class="leading-relaxed">Cuidado preventivo recomendado. Verifique que no se dupliquen medicamentos del mismo grupo terapéutico en menos de 30 días.</p>
                    </div>

                    <!-- Listado de Solicitudes Recientes -->
                    <div class="space-y-2">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Últimas Solicitudes (Línea de Tiempo)</span>
                        
                        <!-- Loader -->
                        <div x-show="loadingHistorial" class="py-6 text-center">
                            <svg class="animate-spin h-5 w-5 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>

                        <!-- Sin Historial -->
                        <div x-show="!loadingHistorial && afiliadoHistorial.length === 0" class="text-center py-6 text-slate-400 italic">
                            No se registran autorizaciones previas para este afiliado.
                        </div>

                        <!-- Lista -->
                        <div class="space-y-2 max-h-96 overflow-y-auto pr-1" x-show="!loadingHistorial && afiliadoHistorial.length > 0">
                            <template x-for="h in afiliadoHistorial" :key="h.id">
                                <div class="p-3 border border-slate-100 rounded-2xl bg-white hover:bg-slate-50 transition space-y-1.5 shadow-2xs">
                                    <div class="flex justify-between items-center font-bold text-[9px]">
                                        <span class="font-mono text-slate-800" x-text="h.numero_autorizacion"></span>
                                        <span class="text-slate-400" x-text="h.fecha_solicitud"></span>
                                    </div>
                                    <p class="text-slate-700 font-bold text-[11px]" x-text="h.procedimiento"></p>
                                    <p class="text-[10px] text-slate-400 font-semibold" x-text="'PSS: ' + h.pss_nombre"></p>
                                    <div class="flex justify-between items-center pt-1 border-t border-slate-50">
                                        <span class="font-mono text-slate-700 font-extrabold" x-text="'DOP ' + Number(h.monto_solicitado).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[8px] font-black border uppercase tracking-wider"
                                              :class="h.estado === 'Aprobada' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                                     (h.estado === 'Rechazada' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-250')"
                                              x-text="h.estado">
                                        </span>
                                    </div>
                                </div>
                              </template>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
    </div>
</div>
@endsection
