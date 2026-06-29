@extends('layouts.pss')

@section('title', 'Portal de Autorizaciones')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="autorizacionPortal">

    <!-- Pantalla de carga animada -->
    <div x-show="isSubmitting" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak x-transition>
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center space-y-4 max-w-sm mx-auto text-center border border-slate-100">
            <div class="relative w-20 h-20">
                <!-- Circulo de progreso animado -->
                <div class="absolute inset-0 rounded-full border-4 border-teal-100 animate-pulse"></div>
                <div class="absolute inset-0 rounded-full border-4 border-teal-600 border-t-transparent animate-spin"></div>
                <!-- Icono de salud pulsando -->
                <div class="absolute inset-0 flex items-center justify-center text-teal-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <h3 class="font-title text-slate-800 font-extrabold text-base">Procesando Solicitud</h3>
                <p class="text-xs text-slate-400">Validando cobertura y registrando autorización en los sistemas de la ARS...</p>
            </div>
        </div>
    </div>

    <!-- Encabezado Unificado -->
    <div class="flex items-center space-x-4 pb-4">
        <!-- Logo Modelo ARS CMD -->
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-tr from-teal-600 to-teal-400 text-white p-2 rounded-xl shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="leading-none">
                <span class="text-slate-800 font-extrabold text-base tracking-wider block">ARS</span>
                <span class="text-teal-600 font-bold text-sm tracking-wide block -mt-1">CMD</span>
            </div>
        </div>
        <div class="border-l border-slate-200 pl-4">
            <h2 class="text-lg font-bold text-slate-800">Portal de Autorizaciones</h2>
            <p class="text-[11px] text-slate-400 font-medium">Búsqueda, validación e ingreso de solicitudes</p>
        </div>
    </div>

    <!-- Mensajes de Error de Validación -->
    <div x-show="errorMsg" x-transition class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start space-x-3 shadow-sm text-xs text-rose-800" x-cloak>
        <svg class="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <div>
            <p class="font-bold">Error de Validación</p>
            <p class="mt-0.5" x-text="errorMsg"></p>
        </div>
    </div>

    <!-- Formulario Unificado POST -->
    <form action="{{ route('pss.autorizaciones.guardar') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
        @csrf
        
        <!-- Inputs ocultos para enviar estado del Afiliado -->
        <input type="hidden" name="afiliado_id" :value="afiliado ? afiliado.id : ''">
        <input type="hidden" name="afiliado_type" :value="afiliado ? afiliado.type : ''">

        <!-- 1. Búsqueda de Afiliado -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 border-b border-slate-50 pb-2">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Búsqueda de Afiliado</span>
            </h3>
            
            <div class="space-y-2">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipo de Búsqueda</span>
                
                <div class="flex items-center space-x-3">
                    <!-- Botón Selección Póliza -->
                    <label class="flex items-center space-x-2 border rounded-full px-4 py-2 cursor-pointer transition select-none text-xs font-semibold"
                           :class="tipoBusqueda === 'poliza' ? 'border-teal-500 bg-teal-50/50 text-[#0f766e]' : 'border-slate-200 hover:bg-slate-50 text-slate-500'">
                        <input type="radio" value="poliza" x-model="tipoBusqueda" class="hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Póliza</span>
                    </label>

                    <!-- Botón Selección Cédula -->
                    <label class="flex items-center space-x-2 border rounded-full px-4 py-2 cursor-pointer transition select-none text-xs font-semibold"
                           :class="tipoBusqueda === 'cedula' ? 'border-teal-500 bg-teal-50/50 text-[#0f766e]' : 'border-slate-200 hover:bg-slate-50 text-slate-500'">
                        <input type="radio" value="cedula" x-model="tipoBusqueda" class="hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"/></svg>
                        <span>Cédula</span>
                    </label>
                </div>
            </div>

            <!-- Input y Botón Validar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="formatQuery"
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 pl-11 pr-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all"
                           :placeholder="tipoBusqueda === 'poliza' ? 'Ej: 00896-18979-01' : 'Ej: 225-0075615-4'">
                </div>
                <button type="button" @click="validarAfiliado" :disabled="loading"
                        class="px-6 py-2.5 rounded-full text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md disabled:bg-slate-300 disabled:text-slate-400 flex items-center justify-center space-x-2 transition shadow-xs">
                    <template x-if="!loading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </template>
                    <template x-if="loading">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="loading ? 'Validando...' : 'Validar'"></span>
                </button>
            </div>
            <p class="text-[10px] text-slate-400" x-text="tipoBusqueda === 'poliza' ? 'Ingrese el número de póliza en formato XXXXX-XXXXX-XX' : 'Ingrese el número de cédula en formato XXX-XXXXXXX-X'"></p>
        </div>

        <!-- 2. Información del Afiliado (Sección Dinámica) -->
        <div x-show="afiliado" x-transition class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm space-y-4" x-cloak>
            <h3 class="text-xs font-bold text-[#0f766e] flex items-center space-x-1.5 border-b border-slate-50 pb-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Información del Afiliado</span>
            </h3>

            <!-- Grid de Datos 3 Columnas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs border border-slate-100 rounded-xl p-4 bg-slate-50/30">
                <!-- Columna 1 -->
                <div class="space-y-4 border-r border-slate-100/80 pr-4 last:border-0">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Nombres</span>
                        <span class="font-bold text-slate-800 text-sm mt-0.5" x-text="afiliado ? afiliado.nombres : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Póliza</span>
                        <span class="font-bold text-slate-700 mt-0.5" x-text="afiliado ? afiliado.poliza : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Régimen</span>
                        <span class="font-bold text-teal-600 mt-0.5" x-text="afiliado ? afiliado.plan1 : ''"></span>
                    </div>
                </div>

                <!-- Columna 2 -->
                <div class="space-y-4 border-r border-slate-100/80 pr-4 last:border-0">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Apellidos</span>
                        <span class="font-bold text-slate-800 text-sm mt-0.5" x-text="afiliado ? afiliado.apellidos : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Género</span>
                        <span class="font-bold text-slate-700 mt-0.5" x-text="afiliado ? afiliado.sexo : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Plan Adicional</span>
                        <span class="font-bold text-slate-400 mt-0.5" x-text="afiliado ? afiliado.plan2 : ''"></span>
                    </div>
                </div>

                <!-- Columna 3 -->
                <div class="space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Cédula</span>
                            <span class="font-bold text-slate-700 mt-0.5" x-text="afiliado ? afiliado.cedula : ''"></span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Edad</span>
                            <span class="font-bold text-slate-700 mt-0.5 text-right block" x-text="afiliado ? afiliado.edad : ''"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Tipo de Afiliado</span>
                        <span class="font-bold text-slate-800 bg-slate-100 px-3 py-1 rounded-full text-[10px] mt-1 inline-block border border-slate-200" x-text="afiliado ? (afiliado.tipo || 'Titular Cotizante') : ''"></span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Estado de Afiliación</span>
                        <template x-if="afiliado">
                            <span class="font-extrabold px-3 py-1 rounded-full text-[10px] mt-1 inline-block border tracking-wide uppercase" 
                                  :class="afiliado.estado === 'Activo' ? 'bg-emerald-50 text-emerald-700 border-emerald-250' : 'bg-rose-50 text-rose-750 border-rose-250'"
                                  x-text="afiliado.estado || 'Activo'"></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Información del Paciente -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 border-b border-slate-50 pb-2">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Información del Paciente y Tipo de Solicitud</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Tipo de Autorización -->
                <div>
                    <label for="tipo_servicio" class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Tipo de Autorización <span class="text-rose-500">*</span></label>
                    <select name="tipo_servicio" id="tipo_servicio" x-model="tipoServicio" :disabled="!afiliado" required
                            class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 py-2.5 px-4 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="consulta">Consulta Médica Programada</option>
                        <option value="laboratorio">Apoyo Diagnóstico (Laboratorios)</option>
                        <option value="imagen">Apoyo Diagnóstico (Imágenes)</option>
                        <option value="cirugia">Cirugía / Procedimiento Quirúrgico</option>
                        <option value="internamiento">Internamiento / Hospitalización</option>
                        <option value="medicamento">Medicamentos Ambulatorios</option>
                        <option value="alto_costo">Tratamientos de Alto Costo</option>
                        <option value="emergencia">Atención de Emergencia</option>
                    </select>
                </div>
                <!-- Diagnóstico Autocomplete -->
                <div class="relative">
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Diagnóstico <span class="text-rose-500">*</span></label>
                    <input type="text" name="diagnostico_input" x-model="diagnostico" list="diagnosticos-list"
                           :disabled="!afiliado" required
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all disabled:bg-slate-50 disabled:text-slate-400"
                           placeholder="Buscar diagnóstico o código CIE-10">
                    <datalist id="diagnosticos-list">
                        <template x-for="diag in diagnosticosSugeridos" :key="diag">
                            <option :value="diag"></option>
                        </template>
                    </datalist>
                    <!-- Campo oculto real para el post -->
                    <input type="hidden" name="diagnostico" :value="diagnostico">
                </div>

                <!-- Teléfono con Máscara -->
                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Teléfono <span class="text-rose-500">*</span></label>
                    <input type="text" x-model="telefono" @input="formatPhone" maxlength="12" name="telefono"
                           :disabled="!afiliado" required
                           class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 px-4 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all disabled:bg-slate-50 disabled:text-slate-400"
                           placeholder="000-000-0000">
                </div>
            </div>
            
            <!-- Adjuntar receta (Opcional pero funcional para la simulación de soporte médico) -->
            <div class="border border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/30 flex flex-col sm:flex-row items-center justify-between space-y-2 sm:space-y-0 text-xs">
                <div>
                    <p class="font-semibold text-slate-600">Adjuntar Indicación Médica / Receta (Simulado)</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Requerido para exámenes especiales de laboratorio, imágenes o cirugías.</p>
                </div>
                <input type="file" name="documento_soporte" :disabled="!afiliado"
                       class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer disabled:opacity-50">
            </div>
        </div>

        <!-- 4. Agregar Servicios Médicos -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 border-b border-slate-50 pb-2">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Agregar Servicios Médicos</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs items-end">
                <!-- Dropdown Grupo Médico -->
                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Grupo Médico</label>
                    <select x-model="grupoMedico" @change="servicioId = ''; valorServicio = ''"
                            :disabled="!afiliado"
                            class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 py-2.5 px-4 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="">Seleccione un grupo...</option>
                        <option value="1">1 CONSULTAS</option>
                        <option value="2">2 LABORATORIO</option>
                        <option value="3">3 IMÁGENES</option>
                        <option value="4">4 PROCEDIMIENTOS</option>
                        <option value="5">5 CIRUGÍAS</option>
                        <option value="6">6 ALTO COSTO</option>
                        <option value="7">7 MEDICAMENTOS</option>
                        <option value="8">8 EMERGENCIAS</option>
                    </select>
                </div>

                <!-- Dropdown Servicio Médico -->
                <div>
                    <label class="block font-semibold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Servicio Médico</label>
                    <select x-model="servicioId" @change="updateValorDefault"
                            :disabled="!afiliado || !grupoMedico"
                            class="block w-full rounded-full border border-slate-200 bg-[#eaf1fb]/40 py-2.5 px-4 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 transition-all disabled:bg-slate-50 disabled:text-slate-400">
                        <option value="">Buscar servicio...</option>
                        <template x-for="srv in filteredServicios" :key="srv.id">
                            <option :value="srv.id" x-text="srv.codigo + ' - ' + srv.descripcion"></option>
                        </template>
                    </select>
                </div>

                <!-- Botón Agregar -->
                <div>
                    <button type="button" @click="agregarServicio" :disabled="!afiliado || !servicioId || incompatibilidadMsg !== ''"
                            class="w-full px-5 py-2.5 border border-transparent rounded-full shadow-xs text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md disabled:bg-slate-350 disabled:text-slate-400 flex items-center justify-center space-x-1.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Agregar Servicio</span>
                    </button>
                </div>
            </div>
            
            <!-- Mensaje de Incompatibilidad en Vivo -->
            <template x-if="incompatibilidadMsg">
                <div class="p-3.5 bg-rose-50 border border-rose-250 rounded-xl flex items-start space-x-2 text-xs text-rose-800 animate-pulse">
                    <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <div>
                        <span class="font-bold block">Incompatibilidad de Servicio</span>
                        <span class="mt-0.5 block leading-normal" x-text="incompatibilidadMsg"></span>
                    </div>
                </div>
            </template>

            <!-- Campo oculto / Editable para el monto del servicio seleccionado (se inyecta en la tabla) -->
            <template x-if="servicioId && !incompatibilidadMsg">
                <div class="p-3 bg-teal-50/50 border border-teal-150 rounded-xl flex items-center justify-between text-xs text-teal-850">
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tarifa Contratada: <strong x-text="'DOP ' + parseFloat(tarifas[servicioId] || 1500).toFixed(2)"></strong></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <label for="valor_servicio_input" class="font-bold text-[9px] uppercase tracking-wider text-slate-500">Monto a reclamar (DOP):</label>
                        <input type="number" step="0.01" id="valor_servicio_input" x-model="valorServicio" 
                               class="w-24 text-right py-1 px-3 border border-teal-200 rounded-full font-bold text-teal-900 bg-white focus:outline-none focus:ring-1 focus:ring-teal-400">
                    </div>
                </div>
            </template>
        </div>

        <!-- 5. Lista de Servicios (Tabla Interactiva) -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden space-y-4 p-5">
            <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Lista de Servicios</span>
                </h3>
                <!-- Badge Dinámico -->
                <span class="inline-flex items-center rounded-full bg-teal-600 px-3 py-1 text-[10px] font-bold text-white tracking-wider"
                      x-text="countServicios + ' servicios'"></span>
            </div>

            <!-- Tabla de Servicios -->
            <div class="border border-slate-100 rounded-xl overflow-hidden bg-slate-50/20">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50/50 font-bold text-slate-400">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left w-24 text-[9px] uppercase tracking-wider">Código</th>
                            <th scope="col" class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Nombre</th>
                            <th scope="col" class="px-4 py-3 text-right w-40 text-[9px] uppercase tracking-wider">Valor (Reclamado)</th>
                            <th scope="col" class="px-4 py-3 text-right w-36 text-[9px] uppercase tracking-wider">Cobertura</th>
                            <th scope="col" class="px-4 py-3 text-right w-36 text-[9px] uppercase tracking-wider">Diferencia</th>
                            <th scope="col" class="px-4 py-3 text-center w-16 text-[9px] uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <!-- Iteración de Servicios Agregados -->
                        <template x-for="(item, index) in serviciosAgregados" :key="item.id">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-2.5 font-mono font-bold text-slate-500" x-text="item.codigo"></td>
                                <td class="px-4 py-2.5 text-slate-700 font-semibold" x-text="item.descripcion"></td>
                                <td class="px-4 py-2.5 text-right font-bold text-slate-900">
                                    <!-- Inputs ocultos correspondientes para serializar en POST -->
                                    <input type="hidden" name="servicios[]" :value="item.id">
                                    <input type="hidden" name="valores[]" :value="item.valor">
                                    
                                    <!-- Input editable del valor del servicio en la tabla -->
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <span class="text-[9px] text-slate-400 font-normal">DOP</span>
                                        <input type="number" step="0.01" x-model="item.valor" 
                                               @input="
                                                    const base = Math.min(item.valor, item.tarifa);
                                                    const srvObj = servicios.find(s => s.id == item.id);
                                                    const pct = parseFloat(srvObj.cobertura_base) / 100;
                                                    item.cobertura = Math.round((base * pct) * 100) / 100;
                                                    item.diferencia = Math.round((item.valor - item.cobertura) * 100) / 100;
                                               "
                                               class="w-24 text-right py-1 px-3 border border-slate-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-100 font-bold text-slate-800 text-xs">
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-right text-slate-500 font-semibold font-mono" x-text="'DOP ' + parseFloat(item.cobertura).toFixed(2)"></td>
                                <td class="px-4 py-2.5 text-right text-slate-700 font-bold font-mono" x-text="'DOP ' + parseFloat(item.diferencia).toFixed(2)"></td>
                                <td class="px-4 py-2.5 text-center">
                                    <button type="button" @click="eliminarServicio(index)" class="text-rose-500 hover:text-rose-700 transition p-1.5 hover:bg-rose-50 rounded-full">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Mensaje Lista Vacía -->
                        <template x-if="countServicios === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400 font-semibold">
                                    No se han agregado servicios médicos a esta solicitud. Utilice el formulario superior para añadir procedimientos.
                                </td>
                            </tr>
                        </template>

                        <!-- Fila TOTAL GENERAL -->
                        <tr class="bg-slate-50/50 font-bold text-slate-800 border-t border-slate-100">
                            <td colspan="2" class="px-4 py-3 uppercase tracking-wider text-[10px] text-slate-500 font-bold">TOTAL GENERAL</td>
                            <td class="px-4 py-3 text-right text-slate-900 text-xs font-mono font-extrabold" x-text="'DOP ' + parseFloat(totalValor).toFixed(2)"></td>
                            <td class="px-4 py-3 text-right text-emerald-700 text-xs font-mono font-extrabold" x-text="'DOP ' + parseFloat(totalCobertura).toFixed(2)"></td>
                            <td class="px-4 py-3 text-right text-slate-900 text-xs font-mono font-extrabold" x-text="'DOP ' + parseFloat(totalDiferencia).toFixed(2)"></td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Botones de Acción Formulario -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-50">
                <a href="{{ route('pss.dashboard') }}"
                   class="px-5 py-2.5 border border-slate-200 rounded-full text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 hover:border-slate-350 transition flex items-center space-x-1.5 shadow-xs">
                    <svg class="w-4 h-4 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>Cancelar</span>
                </a>
                
                <button type="submit" :disabled="!afiliado || countServicios === 0 || isSubmitting"
                        class="px-6 py-2.5 border border-transparent rounded-full shadow-sm text-xs font-extrabold text-white bg-teal-600 hover:bg-teal-700 hover:shadow-md disabled:bg-slate-350 disabled:text-slate-400 flex items-center space-x-2 transition active:scale-95">
                    <template x-if="!isSubmitting">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </template>
                    <template x-if="isSubmitting">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="isSubmitting ? 'Enviando...' : 'Enviar Solicitud ARS'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('autorizacionPortal', () => ({
        tipoBusqueda: 'poliza',
        searchQuery: '',
        loading: false,
        isSubmitting: false,
        errorMsg: '',
        afiliado: null,
        diagnostico: '',
        telefono: '',
        tipoServicio: 'consulta',
        incompatibilidadMsg: '',
        grupoMedico: '',
        servicioId: '',
        valorServicio: '',
        serviciosAgregados: [],
        
        servicios: @json($servicios),
        tarifas: @json($tarifas),
        
        diagnosticosSugeridos: [
            'I10 - HTA Esencial (Primaria)',
            'E11 - Diabetes Mellitus No Insulinodependiente',
            'K80 - Colelitiasis',
            'C50 - Tumor Maligno de la Mama',
            'N18 - Insuficiencia Renal Crónica',
            'M54 - Dorsalgia',
            'Z00 - Examen Médico General',
            'R51 - Cefalea',
            'K35 - Apendicitis Aguda',
            'O82 - Parto por Cesárea',
            '0034 - ABORTO ESPONTANEO, INCOMPLETO, SIN COMPLICACION'
        ],

        init() {
            @if($afiliado)
                this.afiliado = {
                    id: {{ $afiliado->id }},
                    type: '{{ $afiliadoType }}',
                    tipo: '{{ $afiliadoType === "titular" ? "Titular Cotizante" : "Dependiente Beneficiario" }}',
                    nombres: '{{ strtoupper($afiliado->nombres) }}',
                    apellidos: '{{ strtoupper($afiliadoType === "titular" ? ($afiliado->primer_apellido . " " . $afiliado->segundo_apellido) : $afiliado->apellidos) }}',
                    cedula: '{{ $afiliado->cedula }}',
                    poliza: '{{ $afiliadoType === "titular" ? $afiliado->numero_contrato : ($afiliado->titular ? $afiliado->titular->numero_contrato : "N/A") }}',
                    sexo: '{{ $afiliado->sexo }}',
                    edad: '{{ $afiliado->edad }} años',
                    plan1: '{{ $afiliadoType === "titular" ? $afiliado->regimen_actual : ($afiliado->titular ? $afiliado->titular->regimen_actual : "Contributivo") }}',
                    plan2: 'PDSS 11.0',
                    estado: '{{ $afiliado->estado_afiliacion === "OK" ? "Activo" : "Inactivo" }}',
                    telefono: '{{ $afiliadoType === "titular" ? $afiliado->telefono : ($afiliado->titular ? $afiliado->titular->telefono : "") }}'
                };
                this.telefono = this.afiliado.telefono;
                this.searchQuery = this.afiliado.poliza !== 'N/A' ? this.afiliado.poliza : this.afiliado.cedula;
                this.tipoBusqueda = this.afiliado.poliza !== 'N/A' ? 'poliza' : 'cedula';
            @endif

            this.$watch('tipoBusqueda', value => {
                this.searchQuery = '';
                this.errorMsg = '';
            });

            this.$watch('servicioId', value => {
                this.verificarCompatibilidad();
            });

            this.$watch('tipoServicio', value => {
                this.verificarCompatibilidad();
            });
        },

        verificarCompatibilidad() {
            this.incompatibilidadMsg = '';
            if (!this.servicioId) return;

            const service = this.servicios.find(s => s.id == this.servicioId);
            if (!service) return;

            const codigo = (service.codigo || '').toLowerCase();
            const tipo = this.tipoServicio.toLowerCase();

            if (tipo === 'consulta') {
                if (!codigo.startsWith('con')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Consulta Médica", pero el servicio elegido no es una consulta. Los laboratorios, imágenes o cirugías no son compatibles en esta categoría.';
                }
            } else if (tipo === 'laboratorio') {
                if (!codigo.startsWith('lab')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Apoyo Diagnóstico (Laboratorios)", pero el servicio elegido no corresponde a un examen de laboratorio.';
                }
            } else if (tipo === 'imagen') {
                if (!codigo.startsWith('ima')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Apoyo Diagnóstico (Imágenes)", pero el servicio elegido no corresponde a un estudio de imágenes.';
                }
            } else if (tipo === 'cirugia') {
                if (!codigo.startsWith('cir') && !codigo.startsWith('pro') && !codigo.startsWith('hos')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Cirugía / Procedimiento", pero el servicio elegido no corresponde a un acto quirúrgico o procedimiento clínico.';
                }
            } else if (tipo === 'medicamento') {
                if (!codigo.startsWith('med')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Medicamentos", pero el servicio elegido no es un fármaco ambulatorio.';
                }
            } else if (tipo === 'alto_costo') {
                if (!codigo.startsWith('atc')) {
                    this.incompatibilidadMsg = 'El tipo de autorización seleccionado es "Alto Costo", pero el servicio elegido no pertenece al catálogo especial de medicamentos o de alto costo.';
                }
            }
        },

        get filteredServicios() {
            if (!this.grupoMedico) return [];
            const mapping = {
                '1': 'CON',
                '2': 'LAB',
                '3': 'IMA',
                '4': 'PRO',
                '5': 'CIR',
                '6': 'ATC',
                '7': 'MED',
                '8': 'EME'
            };
            const prefix = mapping[this.grupoMedico];
            if (!prefix) return [];
            return this.servicios.filter(s => s.grupo === prefix || s.codigo.startsWith(prefix));
        },

        formatQuery(e) {
            let value = this.searchQuery.replace(/[^a-zA-Z0-9]/g, '');
            if (this.tipoBusqueda === 'cedula') {
                if (value.length > 3 && value.length <= 10) {
                    value = value.slice(0, 3) + '-' + value.slice(3);
                } else if (value.length > 10) {
                    value = value.slice(0, 3) + '-' + value.slice(3, 10) + '-' + value.slice(10, 11);
                }
            } else if (this.tipoBusqueda === 'poliza') {
                if (value.length > 5 && value.length <= 10) {
                    value = value.slice(0, 5) + '-' + value.slice(5);
                } else if (value.length > 10) {
                    value = value.slice(0, 5) + '-' + value.slice(5, 10) + '-' + value.slice(10, 12);
                }
            }
            this.searchQuery = value;
        },

        formatPhone(e) {
            let value = this.telefono.replace(/[^0-9]/g, '');
            if (value.length > 3 && value.length <= 6) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else if (value.length > 6) {
                value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
            }
            this.telefono = value;
        },

        async validarAfiliado() {
            if (!this.searchQuery) return;
            this.loading = true;
            this.errorMsg = '';
            this.afiliado = null;

            try {
                const response = await fetch(`/portal-autorizaciones/afiliados/validar-json?identificacion=${encodeURIComponent(this.searchQuery)}&tipo_busqueda=${this.tipoBusqueda}`);
                const data = await response.json();
                
                if (data.success) {
                    this.afiliado = data.afiliado;
                    this.telefono = data.afiliado.telefono || '';
                    if (this.telefono) {
                        this.telefono = this.telefono.replace(/[^0-9]/g, '');
                        if (this.telefono.length === 10) {
                            this.telefono = this.telefono.slice(0, 3) + '-' + this.telefono.slice(3, 6) + '-' + this.telefono.slice(6, 10);
                        }
                    }
                } else {
                    this.errorMsg = data.message;
                }
            } catch (err) {
                this.errorMsg = 'Error de red al intentar validar el afiliado. Por favor intente de nuevo.';
            } finally {
                this.loading = false;
            }
        },

        updateValorDefault() {
            if (!this.servicioId) {
                this.valorServicio = '';
                return;
            }
            const tarifa = this.tarifas[this.servicioId] || 1500;
            this.valorServicio = tarifa;
        },

        agregarServicio() {
            if (!this.servicioId || !this.valorServicio) return;
            
            const service = this.servicios.find(s => s.id == this.servicioId);
            if (!service) return;

            const tarifa = this.tarifas[this.servicioId] || 1500;
            const valorReq = parseFloat(this.valorServicio);
            
            const coberturaBase = parseFloat(service.cobertura_base) / 100;
            const baseCalculo = Math.min(valorReq, tarifa);
            const cobertura = Math.round((baseCalculo * coberturaBase) * 100) / 100;
            const diferencia = Math.round((valorReq - cobertura) * 100) / 100;

            if (this.serviciosAgregados.some(s => s.id === service.id)) {
                alert('Este servicio ya ha sido agregado a la solicitud.');
                return;
            }

            this.serviciosAgregados.push({
                id: service.id,
                codigo: service.codigo,
                descripcion: service.descripcion,
                valor: valorReq,
                cobertura: cobertura,
                diferencia: diferencia,
                tarifa: tarifa
            });

            this.servicioId = '';
            this.valorServicio = '';
        },

        eliminarServicio(index) {
            this.serviciosAgregados.splice(index, 1);
        },

        get totalValor() {
            return this.serviciosAgregados.reduce((sum, item) => sum + parseFloat(item.valor), 0);
        },

        get totalCobertura() {
            return this.serviciosAgregados.reduce((sum, item) => sum + parseFloat(item.cobertura), 0);
        },

        get totalDiferencia() {
            return this.serviciosAgregados.reduce((sum, item) => sum + parseFloat(item.diferencia), 0);
        },

        get countServicios() {
            return this.serviciosAgregados.length;
        }
    }));
});
</script>
@endsection
