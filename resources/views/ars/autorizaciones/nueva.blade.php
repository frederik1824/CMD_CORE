@extends('layouts.ars')
@section('title', 'Nueva Autorización — ARS Core')
@section('content')
<div class="max-w-7xl mx-auto space-y-8 animate-fade-in font-sans" x-data="{
    afiliadoSeleccionado: null,
    afiliadoBusqueda: '',
    resultados: [],
    buscando: false,
    catalogoTipo: 'pdss',
    servicioBusqueda: '',
    serviciosResultados: [],
    buscandoServicio: false,
    servicioSeleccionado: null,
    servicioFocused: false,
    servicioGrupoFiltro: '',
    grupos: [],
    selectedIndex: -1,
    pssSeleccionada: '',
    medicoSolicitante: '',
    especialidad: '',
    diagnosticoCodigo: '',
    diagnosticoDesc: '',
    montoSolicitado: '',
    prioridad: 'Media',
    procedimiento: '',
    hasDocument: false,
    async init() {
        try {
            const r = await fetch('/core/pdss/grupos');
            this.grupos = await r.json();
        } catch(e) { this.grupos = []; }
    },
    async buscarAfiliado() {
        if (this.afiliadoBusqueda.length < 2) { this.resultados = []; return; }
        this.buscando = true;
        try {
            const r = await fetch('/core/autorizaciones/buscar-afiliado?q=' + encodeURIComponent(this.afiliadoBusqueda));
            this.resultados = await r.json();
        } catch(e) { this.resultados = []; }
        this.buscando = false;
    },
    seleccionarAfiliado(a) {
        this.afiliadoSeleccionado = a;
        this.afiliadoBusqueda = a.label;
        this.resultados = [];
        document.getElementById('afiliado_type_input').value = a.type;
        document.getElementById('afiliado_id_input').value = a.id;
    },
    async buscarServicio() {
        this.selectedIndex = -1;
        if (this.servicioBusqueda.length < 2) { this.serviciosResultados = []; return; }
        this.buscandoServicio = true;
        const pssId = document.getElementsByName('pss_id')[0]?.value || '';
        let url = `/core/pdss/buscar-servicio?q=${encodeURIComponent(this.servicioBusqueda)}&pss_id=${pssId}`;
        if (this.servicioGrupoFiltro) url += `&group_id=${this.servicioGrupoFiltro}`;
        try {
            const r = await fetch(url);
            this.serviciosResultados = await r.json();
        } catch(e) { this.serviciosResultados = []; }
        this.buscandoServicio = false;
    },
    seleccionarServicio(s) {
        this.servicioSeleccionado = s;
        this.servicioBusqueda = s.label;
        this.serviciosResultados = [];
        this.servicioFocused = false;
        document.getElementById('pdss_service_id_input').value = s.id;
        
        const tipoMap = {
            'Consultas': 'consulta',
            'Laboratorio': 'laboratorio',
            'Ecografías': 'imagen',
            'T.A.C.': 'imagen',
            'R.M.': 'imagen',
            'Radiología convencional': 'imagen',
            'Estudios radiológicos': 'imagen',
            'Actos Quirúrgicos/anestésicos': 'cirugia',
            'Cirugía': 'cirugia',
            'Hospitalización': 'internamiento',
            'Hotelería': 'internamiento',
            'Emergencia': 'emergencia',
            'Fármacos': 'medicamento',
            'Fármacos (Principio Activo)': 'medicamento',
            'Material Sanitario': 'medicamento',
            'Apoyo Diagnóstico': 'laboratorio',
            'Anatomía patológica': 'laboratorio',
            'Uso de aparatología': 'imagen',
            'Otras técnicas de tratamiento': 'otro',
            'Hemoterapia': 'medicamento',
            'Otros honorarios médicos': 'otro',
            'Actos de enfermería': 'internamiento',
            'Pruebas cardiológicas': 'imagen',
            'Pruebas neurológicas': 'imagen',
            'Endoscopias': 'imagen',
            'Otros Medios Diagnósticos': 'laboratorio',
            'Medicina Nuclear': 'imagen',
            'Densitometría ósea': 'imagen',
            'Mamografías': 'imagen',
            'Neumología': 'imagen',
            'Rehabilitación': 'consulta',
            'Odontología': 'consulta',
            'Dialisis': 'internamiento',
            'Protesis': 'medicamento',
            'Vacunas': 'medicamento',
            'Capitación': 'otro',
            'Sistemas de movilización/inmovilización/ortopédicos/ortesis': 'medicamento',
            'Otras Coberturas': 'otro',
            'No Clasificada': 'otro',
        };
        const mappedType = tipoMap[s.coverage_type] || 'otro';
        document.getElementById('tipo_servicio_select').value = mappedType;
    },
    limpiarServicio() {
        this.servicioSeleccionado = null;
        this.servicioBusqueda = '';
        document.getElementById('pdss_service_id_input').value = '';
    },
    navigateResults(e) {
        if (!this.serviciosResultados.length) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.selectedIndex = Math.min(this.selectedIndex + 1, this.serviciosResultados.length - 1);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
        } else if (e.key === 'Enter' && this.selectedIndex >= 0) {
            e.preventDefault();
            this.seleccionarServicio(this.serviciosResultados[this.selectedIndex]);
        } else if (e.key === 'Escape') {
            this.serviciosResultados = [];
            this.servicioFocused = false;
        }
    },
    getBadgeColor(s) {
        if (s.is_surgery) return 'bg-red-100 text-red-700';
        if (s.is_high_cost) return 'bg-purple-100 text-purple-700';
        if (s.is_emergency) return 'bg-orange-100 text-orange-700';
        if (s.is_medicine) return 'bg-green-100 text-green-700';
        if (s.is_hospitalization) return 'bg-blue-100 text-blue-700';
        if (s.is_diagnostic_support) return 'bg-teal-100 text-teal-700';
        return 'bg-gray-100 text-gray-600';
    },
}">

    <!-- Top Banner (Matching Screenshot exactly) -->
    <div class="bg-gradient-to-r from-primary to-secondary rounded-3xl p-7 text-white flex flex-col sm:flex-row justify-between items-start sm:items-center shadow-lg gap-4">
        <div class="space-y-1.5">
            <h1 class="text-2xl font-bold font-title">Nueva Solicitud de Autorización Médica</h1>
            <p class="text-xs text-blue-100 font-medium opacity-90">Consola de evaluación automática de reglas clínicas, convenios y tarifarios activos.</p>
        </div>
        <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full text-xs font-bold border border-white/20 self-end sm:self-auto shadow-sm">
            <span>CANAL:</span>
            <span class="bg-white text-secondary px-3 py-0.5 rounded-full font-extrabold uppercase">Llamada</span>
        </div>
    </div>

    <!-- Step Indicator Tracker (Matching Screenshot) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-5 bg-white/70 border border-slate-100 rounded-2xl shadow-xs text-left transition duration-200">
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Paso 1</span>
            <span class="text-sm font-bold text-slate-700">Canal y Afiliado</span>
        </div>
        <div class="p-5 bg-white border-2 border-secondary rounded-2xl shadow-sm text-left relative overflow-hidden transition duration-200">
            <span class="block text-[10px] text-secondary font-bold uppercase tracking-wider mb-1">Paso 2</span>
            <span class="text-sm font-bold text-slate-900">Prestador y Tarifa</span>
        </div>
        <div class="p-5 bg-white/70 border border-slate-100 rounded-2xl shadow-xs text-left transition duration-200">
            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Paso 3</span>
            <span class="text-sm font-bold text-slate-700">Datos y Envío</span>
        </div>
    </div>

    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-250 rounded-2xl text-rose-800 text-xs">
        <ul class="list-disc pl-5 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Formulario Unificado en Grid de 12 Columnas -->
    <form action="{{ route('ars.autorizaciones.crear') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8" @submit="if(servicioBusqueda && !servicioSeleccionado) { servicioBusqueda = ''; serviciosResultados = []; }">
        @csrf

        {{-- Campos ocultos para el Afiliado --}}
        <input type="hidden" name="afiliado_type" id="afiliado_type_input" required>
        <input type="hidden" name="afiliado_id" id="afiliado_id_input" required>
        <input type="hidden" name="pdss_service_id" id="pdss_service_id_input">

        <!-- Left Block: Form & Selection (8 Columns) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Step 1: Búsqueda del Afiliado -->
            <div class="glass-card p-6 rounded-3xl space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5 border-b border-slate-100 pb-2">
                    <span class="material-symbols-outlined text-base text-secondary">person_search</span>
                    <span>Búsqueda de Afiliado</span>
                </h3>
                
                <div class="relative">
                    <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Cédula, NSS o Nombre del Afiliado</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input type="text" 
                               x-model="afiliadoBusqueda" 
                               @input.debounce.300ms="buscarAfiliado()" 
                               placeholder="Escriba para buscar cotizante..." 
                               class="w-full pl-11 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition"
                        >
                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <template x-if="buscando">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                        </div>
                    </div>

                    {{-- Resultados dropdown --}}
                    <div x-show="resultados.length > 0" class="absolute left-0 right-0 mt-1.5 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100">
                        <template x-for="a in resultados" :key="a.type + '-' + a.id">
                            <button type="button" @click="seleccionarAfiliado(a)" class="w-full text-left px-4 py-3 hover:bg-slate-50 transition text-xs flex flex-col gap-0.5">
                                <span class="font-bold text-slate-800" x-text="a.label"></span>
                                <span class="text-slate-500 font-mono">Cédula: <span x-text="a.cedula || 'N/D'"></span> | NSS: <span x-text="a.nss || 'N/D'"></span></span>
                                <span class="text-[9px] font-bold text-secondary tracking-wide uppercase mt-0.5" x-text="a.tipo"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Step 2: Buscadores (Prestadora y Servicio) -->
            <div class="glass-card p-6 rounded-3xl space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Buscar Prestadora (PSS) -->
                    <div>
                        <label for="pss_id" class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Buscar Prestadora (PSS)</label>
                        <select name="pss_id" x-model="pssSeleccionada" required 
                                class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                            <option value="">Seleccione PSS contratante...</option>
                            @foreach($pssList as $pss)
                                <option value="{{ $pss->id }}" {{ old('pss_id') == $pss->id ? 'selected' : '' }}>{{ $pss->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Buscar Servicio o Procedimiento (Catálogo PDSS) -->
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Buscar Servicio o Procedimiento (Catálogo PDSS)</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">clinical_notes</span>
                            <input type="text" 
                                   x-model="servicioBusqueda" 
                                   @input.debounce.300ms="buscarServicio()"
                                   @focus="servicioFocused = true"
                                   @keydown="navigateResults($event)"
                                   placeholder="Ingrese código Simon, CUPS o descripción..." 
                                   class="w-full pl-11 pr-10 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition"
                            >
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <template x-if="buscandoServicio">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                            </div>
                        </div>

                        {{-- Dropdown de resultados del servicio --}}
                        <div x-show="serviciosResultados.length > 0 && servicioFocused" 
                             class="absolute left-0 right-0 mt-1.5 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 max-h-60 overflow-y-auto divide-y divide-slate-100"
                             @click.outside="servicioFocused = false">
                            @verbatim
                            <template x-for="(s, idx) in serviciosResultados" :key="s.id">
                                <button type="button" 
                                        @click="seleccionarServicio(s)" 
                                        @mouseenter="selectedIndex = idx"
                                        :class="selectedIndex === idx ? 'bg-slate-50' : ''"
                                        class="w-full text-left px-4 py-3 transition text-xs flex gap-2.5 items-start">
                                    <span class="inline-block px-2 py-0.5 rounded bg-primary text-white text-[9px] font-mono font-bold" x-text="s.simon_code"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-bold text-slate-800 leading-tight" x-text="s.description"></div>
                                        <div class="text-[10px] text-slate-400 mt-0.5" x-text="s.group_name + ' | ' + s.coverage_type"></div>
                                    </div>
                                </button>
                            </template>
                            @endverbatim
                        </div>
                    </div>
                </div>

                <!-- Info Box de la Prestadora Seleccionada (CEDIMAT en Screenshot) -->
                <div x-show="pssSeleccionada" class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between transition-all">
                    <div>
                        <span class="block text-[9px] font-bold text-slate-450 uppercase tracking-wider">Prestadora Seleccionada</span>
                        <span class="font-bold text-slate-800 text-xs">Clínica Contratada</span>
                        <span class="text-[10px] text-slate-400 block mt-0.5">RNC/Cédula Activo • Acreditación Vigente</span>
                    </div>
                    <button type="button" @click="pssSeleccionada = ''" class="px-3.5 py-1.5 border border-rose-200 text-rose-600 rounded-full text-xs font-bold hover:bg-rose-50 transition">Quitar</button>
                </div>

                <!-- Tabla de Servicios Agregados a la Solicitud (Bento form layout) -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-primary uppercase tracking-wider">Servicios Agregados a la Solicitud</h4>
                    
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <table class="min-w-full divide-y divide-slate-100 text-xs">
                            <thead class="bg-slate-50 font-bold text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 text-left">Servicio</th>
                                    <th class="px-5 py-3 text-left">Cobertura</th>
                                    <th class="px-5 py-3 text-right">Tarifa Pactada</th>
                                    <th class="px-5 py-3 text-right w-44">Monto Solicitado (DOP)</th>
                                    <th class="px-5 py-3 text-center w-16">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <!-- Fila Dinámica de Servicio Seleccionado -->
                                <template x-if="servicioSeleccionado">
                                    <tr class="hover:bg-slate-50/20 transition-colors">
                                        <td class="px-5 py-3">
                                            <span class="font-bold text-slate-800 block" x-text="servicioSeleccionado.description"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="'[' + servicioSeleccionado.simon_code + ']'"></span>
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="px-2.5 py-0.5 rounded-full bg-secondary/15 text-secondary text-[10px] font-bold uppercase tracking-wider" x-text="servicioSeleccionado.coverage_type"></span>
                                        </td>
                                        <td class="px-5 py-3 text-right font-mono text-slate-700" x-text="'DOP ' + parseFloat(servicioSeleccionado.is_high_cost ? 129600 : 1500).toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                                        <td class="px-5 py-3 text-right">
                                            <input type="number" step="0.01" name="monto_solicitado" x-model="montoSolicitado" required 
                                                   class="w-36 text-right py-1.5 px-3 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-secondary/20">
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <button type="button" @click="limpiarServicio()" class="text-rose-500 hover:text-rose-700 transition p-1 bg-rose-50 rounded-full">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!servicioSeleccionado">
                                    <tr>
                                        <td colspan="5" class="px-5 py-6 text-center text-slate-400 font-medium">Ningún servicio seleccionado todavía. Utilice el buscador superior.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-end text-xs font-bold text-primary px-3" x-show="servicioSeleccionado">
                        <span x-text="'Total Solicitado: DOP ' + parseFloat(montoSolicitado || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                    </div>
                </div>

                <!-- Datos Médicos y de Control (Mismo formulario anterior pero mejor agrupado) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-6">
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Médico Solicitante</label>
                        <input type="text" name="medico_solicitante" required value="{{ old('medico_solicitante') }}" placeholder="Nombre del médico" class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Especialidad</label>
                        <select name="especialidad" class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                            <option value="">Seleccione...</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp }}" {{ old('especialidad') == $esp ? 'selected' : '' }}>{{ $esp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Diagnóstico (CIE-10)</label>
                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" name="codigo_diagnostico" placeholder="Código" value="{{ old('codigo_diagnostico') }}" class="col-span-1 px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                            <input type="text" name="diagnostico" required placeholder="Descripción del diagnóstico" value="{{ old('diagnostico') }}" class="col-span-2 px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Prioridad</label>
                        <select name="prioridad" required class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                            <option value="Baja">Baja</option>
                            <option value="Media" selected>Media</option>
                            <option value="Alta">Alta</option>
                            <option value="Emergencia">Emergencia</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-2 uppercase tracking-wider">Canal de Recepción</label>
                        <select name="canal_recepcion" required class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary focus:bg-white transition">
                            <option value="llamada" selected>Llamada</option>
                            <option value="portal">Portal PSS</option>
                            <option value="correo">Correo</option>
                            <option value="presencial">Presencial</option>
                        </select>
                    </div>

                    <input type="hidden" name="tipo_servicio" id="tipo_servicio_select" value="consulta">

                    <div class="sm:col-span-2 flex items-center gap-2.5 py-1">
                        <input type="checkbox" name="hasDocument" id="hasDocument" value="1" class="h-4 w-4 text-secondary border-slate-200 rounded focus:ring-secondary">
                        <label for="hasDocument" class="text-xs text-slate-650 font-bold cursor-pointer">
                            ¿Se adjunta receta médica, indicación o soporte clínico? (Afecta evaluación de reglas)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Botones inferiores de navegación -->
            <div class="flex justify-between items-center">
                <a href="{{ route('ars.autorizaciones.dashboard') }}" class="px-6 py-2.5 border border-slate-200 rounded-full text-xs font-bold text-on-surface-variant bg-white hover:bg-slate-50 transition active:scale-95 shadow-sm">Atrás</a>
                
                <button type="submit" :disabled="!afiliadoSeleccionado || !servicioSeleccionado" 
                        class="px-6 py-2.5 border border-transparent rounded-full shadow-lg shadow-secondary/15 text-xs font-bold text-white bg-secondary hover:bg-primary transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
                    Siguiente Paso
                </button>
            </div>
        </div>

        <!-- Right Block: Sidebar (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Historial del Afiliado -->
            <div class="glass-card p-6 rounded-3xl space-y-5">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">analytics</span>
                    <div>
                        <h3 class="font-title text-primary font-bold text-sm">Historial del Afiliado</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Pre-autorizaciones y alertas clínicas</p>
                    </div>
                </div>

                <!-- Detalles de Afiliación -->
                <div class="space-y-3">
                    <h4 class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Detalles de Afiliación</h4>
                    
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs border border-slate-100 rounded-2xl p-4 bg-slate-50/40">
                        <div>
                            <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Régimen</span>
                            <span class="font-bold text-slate-700" x-text="afiliadoSeleccionado ? afiliadoSeleccionado.regimen : 'Titular'"></span>
                        </div>
                        <div>
                            <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Plan ARS</span>
                            <span class="font-bold text-slate-700">Plan PDSS 11.0</span>
                        </div>
                        <div class="mt-1">
                            <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">NSS</span>
                            <span class="font-bold text-slate-700" x-text="afiliadoSeleccionado ? (afiliadoSeleccionado.nss || '10790017590') : '10790017590'"></span>
                        </div>
                        <div class="mt-1">
                            <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-wider">Carnetizado</span>
                            <span class="font-bold text-emerald-600">Sí</span>
                        </div>
                    </div>
                </div>

                <!-- Alertas Clínicas -->
                <div class="space-y-2">
                    <h4 class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Alertas Clínicas</h4>
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-[11px] text-amber-800 leading-normal flex gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-sm mt-0.5 shrink-0">warning</span>
                        <p class="font-medium">Cuidado preventivo recomendado. Verifique que no se dupliquen medicamentos del mismo grupo terapéutico en menos de 30 días.</p>
                    </div>
                </div>

                <!-- Últimas Solicitudes (Línea de Tiempo) -->
                <div class="space-y-3">
                    <h4 class="text-[10px] font-bold text-slate-450 uppercase tracking-wider">Últimas Solicitudes (Línea de Tiempo)</h4>
                    
                    <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto pr-1 space-y-3 scrollbar-thin">
                        
                        <!-- Timeline Item 1 -->
                        <div class="pt-3 first:pt-0 space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-700 font-mono">AUT-20260629-00001</span>
                                <span class="text-[10px] text-slate-400 font-semibold">29/06/2026</span>
                            </div>
                            <div class="font-bold text-slate-800">3 Servicios Médicos</div>
                            <div class="text-[11px] text-slate-400">PSS: Clínica Abreu</div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="font-bold text-slate-900">DOP 7,875.00</span>
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-bold text-[9px] uppercase tracking-wider">Pendiente Documento</span>
                            </div>
                        </div>

                        <!-- Timeline Item 2 -->
                        <div class="pt-3 space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-700 font-mono">AUT-20260628-00002</span>
                                <span class="text-[10px] text-slate-400 font-semibold">28/06/2026</span>
                            </div>
                            <div class="font-bold text-slate-800">MAGNESIO</div>
                            <div class="text-[11px] text-slate-400">PSS: CEDIMAT</div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="font-bold text-slate-900">DOP 307,800.00</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold text-[9px] uppercase tracking-wider">Aprobada</span>
                            </div>
                        </div>

                        <!-- Timeline Item 3 -->
                        <div class="pt-3 space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-slate-700 font-mono">AUT-20260628-00001</span>
                                <span class="text-[10px] text-slate-400 font-semibold">28/06/2026</span>
                            </div>
                            <div class="font-bold text-slate-800">3 Servicios Médicos</div>
                            <div class="text-[11px] text-slate-400">PSS: Clínica Abreu</div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="font-bold text-slate-900">DOP 1,417.50</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold text-[9px] uppercase tracking-wider">Aprobada</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>
</div>
@endsection
