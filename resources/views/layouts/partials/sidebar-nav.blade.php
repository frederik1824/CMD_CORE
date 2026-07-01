<nav class="space-y-6 flex-1 flex flex-col font-sans select-none" x-data="{ 
    openMenu: localStorage.getItem('sidebar_menu') || '' 
}">
    @php
        $role = Auth::user()->role;
        $isAdmin = $role === 'Administrador ARS';
        $isSupervisor = $role === 'Supervisor Afiliación';
        $isAnalista = $role === 'Analista Afiliación';
        $isAuditor = $role === 'Auditor Médico';
        $isAutorizaciones = $role === 'Autorizaciones Médicas';
        $isConsulta = $role === 'Consulta';
    @endphp

    <!-- GRUPO: MÓDULOS DE NEGOCIO -->
    <div class="space-y-1">
        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
            Módulos de Negocio
        </div>

        <!-- Dashboard Ejecutivo -->
        <a href="{{ route('ars.dashboard') }}" 
           class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('ars.dashboard') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
            <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('ars.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">analytics</span>
            <span>Dashboard Ejecutivo</span>
        </a>

        <!-- Sección Afiliaciones -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'afiliados' ? '' : 'afiliados'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.titulares.index', 'ars.carga.masiva', 'ars.lotes.index', 'ars.contract_control.dashboard', 'ars.afiliaciones.mantenimiento', 'ars.afiliaciones.tipos_contratos', 'ars.afiliaciones.traspasos', 'ars.afiliaciones.grupos', 'ars.afiliaciones.unidades_negocio', 'ars.afiliaciones.transacciones', 'ars.afiliaciones.codificacion_geografica']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.titulares.index', 'ars.carga.masiva', 'ars.lotes.index', 'ars.contract_control.dashboard', 'ars.afiliaciones.mantenimiento', 'ars.afiliaciones.tipos_contratos', 'ars.afiliaciones.traspasos', 'ars.afiliaciones.grupos', 'ars.afiliaciones.unidades_negocio', 'ars.afiliaciones.transacciones', 'ars.afiliaciones.codificacion_geografica']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">group</span>
                    <span>Afiliaciones</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'afiliados' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'afiliados'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.afiliaciones.mantenimiento') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.mantenimiento') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Mantenimiento Afiliados</a>
                <a href="{{ route('ars.solicitudes.titulares.nueva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.solicitudes.titulares.nueva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Carga Individual Titular</a>
                <a href="{{ route('ars.solicitudes.dependientes.nueva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.solicitudes.dependientes.nueva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Afiliación Dependiente</a>
                <a href="{{ route('ars.afiliaciones.traspasos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.traspasos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Gestión de Traspasos</a>
                <a href="{{ route('ars.afiliaciones.grupos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.grupos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Grupos Afiliados</a>
                <a href="{{ route('ars.afiliaciones.tipos_contratos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.tipos_contratos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Tipos de Contratos</a>
                <a href="{{ route('ars.afiliaciones.unidades_negocio') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.unidades_negocio') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Unidades de Negocio</a>
                <a href="{{ route('ars.afiliaciones.transacciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.transacciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Log Transacciones</a>
                <a href="{{ route('ars.afiliaciones.codificacion_geografica') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.codificacion_geografica') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Codificación Geográfica</a>
                <a href="{{ route('ars.contract_control.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contract_control.*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Control de Formularios</a>
                <a href="{{ route('ars.carga.masiva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carga.masiva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Carga Masiva</a>
                <a href="{{ route('ars.lotes.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.lotes.index') && !request()->is('*novedades*') && !request()->is('*reclamaciones*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes Afiliación</a>
            </div>
        </div>
        @endif

        <!-- Sección Novedades -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'novedades' ? '' : 'novedades'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.novedades.create', 'ars.novedades.index', 'ars.novedades.lotes', 'ars.afiliaciones.archivos']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.novedades.create', 'ars.novedades.index', 'ars.novedades.lotes', 'ars.afiliaciones.archivos']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">notifications</span>
                    <span>Novedades</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'novedades' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'novedades'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.novedades.create') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.novedades.create') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Registrar Novedad</a>
                <a href="{{ route('ars.novedades.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.novedades.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja Novedades</a>
                <a href="{{ route('ars.novedades.lotes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.novedades.lotes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes Novedades</a>
                <a href="{{ route('ars.afiliaciones.archivos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.afiliaciones.archivos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Archivos de Afiliados</a>
            </div>
        </div>
        @endif

        <!-- Sección Autorizaciones -->
        @if($isAdmin || $isAuditor || $isAutorizaciones)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'autorizaciones' ? '' : 'autorizaciones'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.autorizaciones.index', 'ars.autorizaciones.pendientes', 'ars.autorizaciones.auditoria', 'ars.autorizaciones.aprobadas', 'ars.autorizaciones.rechazadas', 'ars.autorizaciones.reglas', 'ars.autorizaciones_medicas.dashboard', 'ars.autorizaciones_medicas.index', 'ars.pdss.catalogo', 'ars.autorizaciones.reglas_pdss']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.autorizaciones.index', 'ars.autorizaciones.pendientes', 'ars.autorizaciones.auditoria', 'ars.autorizaciones.aprobadas', 'ars.autorizaciones.rechazadas', 'ars.autorizaciones.reglas', 'ars.autorizaciones_medicas.dashboard', 'ars.autorizaciones_medicas.index', 'ars.pdss.catalogo', 'ars.autorizaciones.reglas_pdss']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">clinical_notes</span>
                    <span>Autorizaciones</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'autorizaciones' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'autorizaciones'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.autorizaciones_medicas.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones_medicas.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard Core ARS</a>
                <a href="{{ route('ars.autorizaciones_medicas.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones_medicas.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja General</a>
                <a href="{{ route('ars.autorizaciones.pendientes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.pendientes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Pendientes & Docs</a>
                <a href="{{ route('ars.autorizaciones.auditoria') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.auditoria') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Auditoría Médica</a>
                <a href="{{ route('ars.pdss.catalogo') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pdss.catalogo') || Route::is('ars.pdss.show') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogo Oficial PDSS</a>
                <a href="{{ route('ars.autorizaciones.reglas_pdss') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.reglas_pdss') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reglas Catálogo PDSS</a>
                <a href="{{ route('ars.autorizaciones.aprobadas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.aprobadas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Aprobadas</a>
                <a href="{{ route('ars.autorizaciones.rechazadas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.rechazadas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Rechazadas</a>
                <a href="{{ route('ars.autorizaciones.reglas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.autorizaciones.reglas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reglas del Motor</a>
            </div>
        </div>
        @endif

        <!-- Sección Prestadoras (PSS) -->
        @if($isAdmin || $isAuditor || $isAutorizaciones)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'pss' ? '' : 'pss'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.pss.index', 'ars.pss.contratos', 'ars.pss.servicios', 'ars.pss.contratos_tarifarios', 'ars.prestadores.personas_fisicas', 'ars.prestadores.personas_juridicas', 'ars.prestadores.auditores_medicos', 'ars.prestadores.servicios_contratados', 'ars.prestadores.convenios_precios', 'ars.prestadores.precios_convenidos', 'ars.prestadores.grupos', 'ars.prestadores.red_por_plan', 'ars.prestadores.habilitacion_servicios', 'ars.prestadores.georreferencial', 'ars.prestadores.capitados_contratos', 'ars.prestadores.capitados_pagos']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.pss.index', 'ars.pss.contratos', 'ars.pss.servicios', 'ars.pss.contratos_tarifarios', 'ars.prestadores.personas_fisicas', 'ars.prestadores.personas_juridicas', 'ars.prestadores.auditores_medicos', 'ars.prestadores.servicios_contratados', 'ars.prestadores.convenios_precios', 'ars.prestadores.precios_convenidos', 'ars.prestadores.grupos', 'ars.prestadores.red_por_plan', 'ars.prestadores.habilitacion_servicios', 'ars.prestadores.georreferencial', 'ars.prestadores.capitados_contratos', 'ars.prestadores.capitados_pagos']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">domain</span>
                    <span>Prestadoras (PSS)</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'pss' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'pss'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.prestadores.personas_fisicas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.personas_fisicas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Personas Físicas</a>
                <a href="{{ route('ars.prestadores.personas_juridicas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.personas_juridicas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Personas Jurídicas</a>
                <a href="{{ route('ars.prestadores.auditores_medicos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.auditores_medicos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Auditores Médicos</a>
                <a href="{{ route('ars.prestadores.servicios_contratados') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.servicios_contratados') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Servicios Contratados</a>
                <a href="{{ route('ars.pss.contratos_tarifarios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pss.contratos_tarifarios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Convenios de Precios (Tarifas V2)</a>
                <a href="{{ route('ars.prestadores.precios_convenidos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.precios_convenidos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Precios Convenidos</a>
                <a href="{{ route('ars.prestadores.grupos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.grupos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Grupos de Prestadores</a>
                <a href="{{ route('ars.prestadores.red_por_plan') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.red_por_plan') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Red de Prestadores por Plan</a>
                <a href="{{ route('ars.prestadores.habilitacion_servicios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.habilitacion_servicios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Habilitación de Servicios</a>
                <a href="{{ route('ars.prestadores.georreferencial') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.georreferencial') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Consulta Georreferencial</a>
                <a href="{{ route('ars.prestadores.capitados_contratos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.capitados_contratos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Servicios Capitados Contratos</a>
                <a href="{{ route('ars.prestadores.capitados_pagos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.prestadores.capitados_pagos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Servicios Capitados Pagos</a>
            </div>
        </div>
        @endif

        <!-- Módulo PyP (Promoción y Prevención) -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'pyp' ? '' : 'pyp'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.pyp.dashboard', 'ars.pyp.actividades', 'ars.pyp.grupos_riesgo', 'ars.pyp.factores_riesgo', 'ars.pyp.tipos_programas', 'ars.pyp.programas', 'ars.pyp.calendario', 'ars.pyp.candidatos', 'ars.pyp.inscripciones', 'ars.pyp.cancelaciones', 'ars.pyp.reportes']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.pyp.dashboard', 'ars.pyp.actividades', 'ars.pyp.grupos_riesgo', 'ars.pyp.factores_riesgo', 'ars.pyp.tipos_programas', 'ars.pyp.programas', 'ars.pyp.calendario', 'ars.pyp.candidatos', 'ars.pyp.inscripciones', 'ars.pyp.cancelaciones', 'ars.pyp.reportes']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">medical_services</span>
                    <span>Promoción & Prevención</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'pyp' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'pyp'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.pyp.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard PyP</a>
                <a href="{{ route('ars.pyp.programas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.programas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Programas de Salud</a>
                <a href="{{ route('ars.pyp.candidatos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.candidatos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Gestión de Candidatos</a>
                <a href="{{ route('ars.pyp.inscripciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.inscripciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Inscripción de Afiliados</a>
                <a href="{{ route('ars.pyp.calendario') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.calendario') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Calendario de Actividades</a>
                <a href="{{ route('ars.pyp.cancelaciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.cancelaciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Inscripciones Canceladas</a>
                <a href="{{ route('ars.pyp.reportes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pyp.reportes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes del Módulo</a>
            </div>
        </div>

        <!-- Módulo Planes de Salud y Coberturas -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'planes' ? '' : 'planes'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.planes_salud.planes', 'ars.planes_salud.catalogo_pdss', 'ars.planes_salud.catalogo_alternativos', 'ars.planes_salud.coberturas', 'ars.planes_salud.detalle_servicio', 'ars.planes_salud.derivaciones', 'ars.planes_salud.periodos_espera', 'ars.planes_salud.topes', 'ars.planes_salud.reportes']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.planes_salud.planes', 'ars.planes_salud.catalogo_pdss', 'ars.planes_salud.catalogo_alternativos', 'ars.planes_salud.coberturas', 'ars.planes_salud.detalle_servicio', 'ars.planes_salud.derivaciones', 'ars.planes_salud.periodos_espera', 'ars.planes_salud.topes', 'ars.planes_salud.reportes']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">folder_shared</span>
                    <span>Planes & Coberturas</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'planes' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'planes'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.planes_salud.planes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.planes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Planes de Salud</a>
                <a href="{{ route('ars.planes_salud.coberturas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.coberturas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Coberturas por Plan</a>
                <a href="{{ route('ars.planes_salud.catalogo_pdss') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.catalogo_pdss') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogo Cobertura PDSS</a>
                <a href="{{ route('ars.planes_salud.catalogo_alternativos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.catalogo_alternativos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Planes Alternativos</a>
                <a href="{{ route('ars.planes_salud.detalle_servicio') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.detalle_servicio') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Detalle Cobertura Servicio</a>
                <a href="{{ route('ars.planes_salud.derivaciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.derivaciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reglas de Derivación</a>
                <a href="{{ route('ars.planes_salud.periodos_espera') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.periodos_espera') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Períodos de Espera</a>
                <a href="{{ route('ars.planes_salud.topes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.planes_salud.topes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Topes y Límites</a>
            </div>
        </div>
    </div>

    <!-- GRUPO: OPERACIONES Y FACTURACIÓN -->
    @if($isAdmin || $isSupervisor)
    <div class="space-y-1">
        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
            Operaciones & Facturación
        </div>

        <!-- Sección Facturación & Reclamaciones -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'facturacion' ? '' : 'facturacion'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.reclamaciones.index', 'ars.reclamaciones.recepcion', 'ars.reclamaciones.plazos', 'ars.reclamaciones.reportes', 'ars.reclamaciones.radicaciones', 'ars.reclamaciones.correcciones', 'ars.reclamaciones.auditoria_retrospectiva', 'ars.reclamaciones.auditoria_facturacion', 'ars.reclamaciones.validaciones', 'ars.reclamaciones.ncf', 'ars.reclamaciones.lotes', 'ars.reclamaciones.ver_lote', 'ars.reclamaciones.glosas', 'ars.reclamaciones.notificaciones', 'ars.reclamaciones.plantillas', 'ars.reclamaciones.cuentas_por_pagar', 'ars.pagos.cxp', 'ars.lotes.index', 'ars.lotes.show']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.reclamaciones.index', 'ars.reclamaciones.recepcion', 'ars.reclamaciones.plazos', 'ars.reclamaciones.reportes', 'ars.reclamaciones.radicaciones', 'ars.reclamaciones.correcciones', 'ars.reclamaciones.auditoria_retrospectiva', 'ars.reclamaciones.auditoria_facturacion', 'ars.reclamaciones.validaciones', 'ars.reclamaciones.ncf', 'ars.reclamaciones.lotes', 'ars.reclamaciones.ver_lote', 'ars.reclamaciones.glosas', 'ars.reclamaciones.notificaciones', 'ars.reclamaciones.plantillas', 'ars.reclamaciones.cuentas_por_pagar', 'ars.pagos.cxp', 'ars.lotes.index', 'ars.lotes.show']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">receipt_long</span>
                    <span>Reclamaciones</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'facturacion' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'facturacion'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.reclamaciones.recepcion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.recepcion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Recepción Radicaciones</a>
                <a href="{{ route('ars.reclamaciones.correcciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.correcciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Correcciones Radicación</a>
                <a href="{{ route('ars.reclamaciones.radicaciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.radicaciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes Radicaciones</a>
                <a href="{{ route('ars.reclamaciones.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja Auditoría</a>
                <a href="{{ route('ars.reclamaciones.auditoria_retrospectiva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.auditoria_retrospectiva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Auditoría Retrospectiva</a>
                <a href="{{ route('ars.reclamaciones.auditoria_facturacion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.auditoria_facturacion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Auditoría de Facturación</a>
                <a href="{{ route('ars.reclamaciones.validaciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.validaciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Validaciones Reclamaciones</a>
                <a href="{{ route('ars.reclamaciones.ncf') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.ncf') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Corrección NCF Reclamaciones</a>
                <a href="{{ route('ars.reclamaciones.lotes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.lotes') || Route::is('ars.reclamaciones.ver_lote') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes de Reclamación</a>
                <a href="{{ route('ars.reclamaciones.glosas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.glosas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Conciliación de Glosas</a>
                <a href="{{ route('ars.reclamaciones.notificaciones') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.notificaciones') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Notificación de Glosas</a>
                <a href="{{ route('ars.reclamaciones.cuentas_por_pagar') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.cuentas_por_pagar') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes CXP</a>
                <a href="{{ route('ars.reclamaciones.plazos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.plazos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Control de Plazos</a>
                <a href="{{ route('ars.reclamaciones.reportes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.reportes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes & Indicadores</a>
                <a href="{{ route('ars.pagos.cxp') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pagos.cxp') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cuentas por Pagar (CXP)</a>
            </div>
        </div>

        <!-- Módulo Carnetización -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'carnetizacion' ? '' : 'carnetizacion'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.carnetizacion.solicitudes', 'ars.carnetizacion.impresion', 'ars.carnetizacion.tipos_carnets', 'ars.carnetizacion.conceptos', 'ars.carnetizacion.entregas', 'ars.carnetizacion.transferencias', 'ars.carnetizacion.localizaciones', 'ars.carnetizacion.centros_impresion', 'ars.carnetizacion.insumos', 'ars.carnetizacion.ajustes', 'ars.carnetizacion.despachos', 'ars.carnetizacion.devoluciones', 'ars.carnetizacion.reportes']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.carnetizacion.solicitudes', 'ars.carnetizacion.impresion', 'ars.carnetizacion.tipos_carnets', 'ars.carnetizacion.conceptos', 'ars.carnetizacion.entregas', 'ars.carnetizacion.transferencias', 'ars.carnetizacion.localizaciones', 'ars.carnetizacion.centros_impresion', 'ars.carnetizacion.insumos', 'ars.carnetizacion.ajustes', 'ars.carnetizacion.despachos', 'ars.carnetizacion.devoluciones', 'ars.carnetizacion.reportes']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">badge</span>
                    <span>Carnetización</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'carnetizacion' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'carnetizacion'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.carnetizacion.solicitudes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.solicitudes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Solicitudes Carnets</a>
                <a href="{{ route('ars.carnetizacion.impresion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.impresion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja Impresión</a>
                <a href="{{ route('ars.carnetizacion.entregas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.entregas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Entregas de Carnets</a>
                <a href="{{ route('ars.carnetizacion.transferencias') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.transferencias') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Transferencias Carnet</a>
                <a href="{{ route('ars.carnetizacion.centros_impresion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.centros_impresion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Centros de Impresión</a>
                <a href="{{ route('ars.carnetizacion.insumos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.insumos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Control Insumos</a>
                <a href="{{ route('ars.carnetizacion.ajustes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.ajustes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Ajustes e Inventario</a>
                <a href="{{ route('ars.carnetizacion.reportes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carnetizacion.reportes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes Carnetización</a>
            </div>
        </div>

        <!-- Módulo Unipago -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'unipago' ? '' : 'unipago'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.unipago.dashboard', 'ars.unipago.prevalidar', 'ars.unipago.lotes', 'ars.unipago.capitas', 'ars.unipago.cortes', 'ars.unipago.logs', 'ars.unipago.notificaciones', 'ars.unipago.procesos_afiliacion', 'ars.unipago.procesos_novedades', 'ars.unipago.consultas_ciudadanos', 'ars.unipago.notificacion_cartera', 'ars.unipago.notificacion_cobertura', 'ars.unipago.recaudo', 'ars.unipago.traspasos', 'ars.unipago.consulta_procesos', 'ars.unipago_simulador.dashboard', 'ars.unipago_simulador.consola']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.unipago.dashboard', 'ars.unipago.prevalidar', 'ars.unipago.lotes', 'ars.unipago.capitas', 'ars.unipago.cortes', 'ars.unipago.logs', 'ars.unipago.notificaciones', 'ars.unipago.procesos_afiliacion', 'ars.unipago.procesos_novedades', 'ars.unipago.consultas_ciudadanos', 'ars.unipago.notificacion_cartera', 'ars.unipago.notificacion_cobertura', 'ars.unipago.recaudo', 'ars.unipago.traspasos', 'ars.unipago.consulta_procesos', 'ars.unipago_simulador.dashboard', 'ars.unipago_simulador.consola']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">settings_input_component</span>
                    <span>Simulador Unipago</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'unipago' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'unipago'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.unipago_simulador.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago_simulador.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Simulador Avanzado V2</a>
                <a href="{{ route('ars.unipago.prevalidar') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.prevalidar') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Prevalidación</a>
                <a href="{{ route('ars.unipago_simulador.consola') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago_simulador.consola') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Consola Web Services</a>
                <a href="{{ route('ars.unipago.procesos_afiliacion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.procesos_afiliacion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Procesos Afiliación</a>
                <a href="{{ route('ars.unipago.procesos_novedades') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.procesos_novedades') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Procesos Novedades</a>
                <a href="{{ route('ars.unipago.consultas_ciudadanos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.consultas_ciudadanos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Consultas Ciudadanos</a>
                <a href="{{ route('ars.unipago.notificacion_cartera') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.notificacion_cartera') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Notificación Cartera</a>
                <a href="{{ route('ars.unipago.notificacion_cobertura') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.notificacion_cobertura') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Notificación Cobertura</a>
                <a href="{{ route('ars.unipago.recaudo') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.recaudo') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Procesos de Recaudo</a>
                <a href="{{ route('ars.unipago.traspasos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.traspasos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Traspasos</a>
                <a href="{{ route('ars.unipago.consulta_procesos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.consulta_procesos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Consulta Procesos</a>
            </div>
        </div>
        @endif

        <!-- Módulo Promotores -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'promotores' ? '' : 'promotores'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.promotores.personas_fisicas', 'ars.promotores.empresas', 'ars.promotores.tipos_contratos', 'ars.promotores.campanas', 'ars.promotores.esquemas_campana', 'ars.promotores.calculo_campana', 'ars.promotores.tipos_gestion', 'ars.promotores.esquemas_gestion', 'ars.promotores.calculo_gestion', 'ars.promotores.reportes', 'ars.promotores.tipificacion']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.promotores.personas_fisicas', 'ars.promotores.empresas', 'ars.promotores.tipos_contratos', 'ars.promotores.campanas', 'ars.promotores.esquemas_campana', 'ars.promotores.calculo_campana', 'ars.promotores.tipos_gestion', 'ars.promotores.esquemas_gestion', 'ars.promotores.calculo_gestion', 'ars.promotores.reportes', 'ars.promotores.tipificacion']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">person_pin</span>
                    <span>Promotores</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'promotores' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'promotores'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.promotores.personas_fisicas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.personas_fisicas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Personas Físicas</a>
                <a href="{{ route('ars.promotores.empresas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.empresas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Empresas / Agencias</a>
                <a href="{{ route('ars.promotores.tipos_contratos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.tipos_contratos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Contratos Promotores</a>
                <a href="{{ route('ars.promotores.campanas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.campanas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Campañas Activas</a>
                <a href="{{ route('ars.promotores.calculo_campana') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.calculo_campana') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cálculo de Comisiones</a>
                <a href="{{ route('ars.promotores.reportes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.promotores.reportes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes Promotores</a>
            </div>
        </div>

        <!-- Módulo SISALRIL -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'sisalril' ? '' : 'sisalril'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['sisalril.dashboard', 'sisalril.index', 'sisalril.catalogos', 'sisalril.configuracion', 'sisalril.generar', 'sisalril.simulador', 'sisalril.show', 'sisalril.submission_detalle']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['sisalril.dashboard', 'sisalril.index', 'sisalril.catalogos', 'sisalril.configuracion', 'sisalril.generar', 'sisalril.simulador', 'sisalril.show', 'sisalril.submission_detalle']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">gavel</span>
                    <span>SISALRIL / SIMON</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'sisalril' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'sisalril'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('sisalril.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard Regulatorio</a>
                <a href="{{ route('sisalril.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Esquemas Regulatorios</a>
                <a href="{{ route('sisalril.catalogos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.catalogos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogos SIMON</a>
                <a href="{{ route('sisalril.configuracion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.configuracion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Configuración Entidad</a>
                <a href="{{ route('sisalril.generar') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.generar') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Generador de Archivos</a>
                <a href="{{ route('sisalril.simulador') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('sisalril.simulador') || Route::is('sisalril.submission_detalle') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Portal SIMON</a>
            </div>
        </div>

        <!-- Módulo Facturación -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'facturacion_base' ? '' : 'facturacion_base'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.facturacion.index', 'ars.facturacion.planes_alternativos', 'ars.facturacion.grupos_afiliados', 'ars.facturacion.comprobantes']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.facturacion.index', 'ars.facturacion.planes_alternativos', 'ars.facturacion.grupos_afiliados', 'ars.facturacion.comprobantes']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">request_page</span>
                    <span>Facturación</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'facturacion_base' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'facturacion_base'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.facturacion.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.facturacion.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Facturación General</a>
                <a href="{{ route('ars.facturacion.planes_alternativos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.facturacion.planes_alternativos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Planes Alternativos</a>
                <a href="{{ route('ars.facturacion.grupos_afiliados') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.facturacion.grupos_afiliados') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Facturación Corporativa</a>
                <a href="{{ route('ars.facturacion.comprobantes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.facturacion.comprobantes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">NCF Comprobantes</a>
            </div>
        </div>

        <!-- Módulo Servicio al Cliente -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'servicio_cliente' ? '' : 'servicio_cliente'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.servicio_cliente.index', 'ars.servicio_cliente.casos', 'ars.servicio_cliente.seguimiento']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.servicio_cliente.index', 'ars.servicio_cliente.casos', 'ars.servicio_cliente.seguimiento']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">support_agent</span>
                    <span>Servicio al Cliente</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'servicio_cliente' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'servicio_cliente'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.servicio_cliente.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.servicio_cliente.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Casos PQR</a>
                <a href="{{ route('ars.servicio_cliente.seguimiento') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.servicio_cliente.seguimiento') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Seguimiento y SLAs</a>
            </div>
        </div>
        @endif
    </div>

    <!-- GRUPO: CONTABILIDAD Y REEMBOLSOS (Solo Admin y Supervisor) -->
    @if($isAdmin || $isSupervisor)
    <div class="space-y-1">
        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
            Finanzas & Reembolsos
        </div>

        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'contabilidad' ? '' : 'contabilidad'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.contabilidad.dashboard', 'ars.contabilidad.catalogo', 'ars.contabilidad.asientos', 'ars.contabilidad.balances', 'ars.contabilidad.mayor', 'ars.contabilidad.cierre', 'ars.reembolsos.index', 'ars.reembolsos.show', 'ars.reembolsos.create']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.contabilidad.dashboard', 'ars.contabilidad.catalogo', 'ars.contabilidad.asientos', 'ars.contabilidad.balances', 'ars.contabilidad.mayor', 'ars.contabilidad.cierre', 'ars.reembolsos.index', 'ars.reembolsos.show', 'ars.reembolsos.create']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">account_balance</span>
                    <span>Finanzas & Contabilidad</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'contabilidad' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'contabilidad'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.contabilidad.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard Financiero</a>
                <a href="{{ route('ars.contabilidad.catalogo') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.catalogo') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogo de Cuentas</a>
                <a href="{{ route('ars.contabilidad.asientos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.asientos') || Route::is('ars.contabilidad.asiento_show') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Libro Diario</a>
                <a href="{{ route('ars.contabilidad.mayor') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.mayor') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Mayor Auxiliar</a>
                <a href="{{ route('ars.contabilidad.balances') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.balances') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Estados Financieros</a>
                <a href="{{ route('ars.contabilidad.cierre') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contabilidad.cierre') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cierre Mensual</a>
                <a href="{{ route('ars.reembolsos.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reembolsos.index') || Route::is('ars.reembolsos.show') || Route::is('ars.reembolsos.create') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reembolsos Afiliados</a>
            </div>
        </div>
    </div>
    @endif

    <!-- GRUPO: REPORTES Y CONFIGURACIÓN -->
    <div class="space-y-1 pb-6">
        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
            Reportes & Configuración
        </div>

        <!-- Reportes -->
        @if($isAdmin || $isSupervisor || $isAuditor || $isConsulta)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'reportes_menu' ? '' : 'reportes_menu'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.reportes.index', 'ars.reportes_generales.index']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.reportes.index', 'ars.reportes_generales.index']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">show_chart</span>
                    <span>Reportería</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'reportes_menu' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'reportes_menu'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.reportes.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reportes.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard de Reportes</a>
                <a href="{{ route('ars.reportes_generales.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reportes_generales.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes Transversales</a>
            </div>
        </div>
        @endif

        <!-- Administración Menu -->
        @if($isAdmin || $isSupervisor)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'admin' ? '' : 'admin'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.admin.usuarios', 'ars.admin.catalogos', 'ars.admin.bitacora', 'ars.catalogos.index']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.admin.usuarios', 'ars.admin.catalogos', 'ars.admin.bitacora', 'ars.catalogos.index']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">admin_panel_settings</span>
                    <span>Administración</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'admin' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'admin'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.admin.usuarios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.admin.usuarios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Usuarios & Roles</a>
                <a href="{{ route('ars.catalogos.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.catalogos.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogos Centralizados</a>
                <a href="{{ route('ars.admin.bitacora') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.admin.bitacora') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bitácora de Auditoría</a>
            </div>
        </div>
        @endif
    </div>
</nav>
