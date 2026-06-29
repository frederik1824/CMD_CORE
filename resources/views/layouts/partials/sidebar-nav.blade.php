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

        <!-- Dashboard Ejecutivo (Visible para todos) -->
        <a href="{{ route('ars.dashboard') }}" 
           class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('ars.dashboard') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
            <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('ars.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">analytics</span>
            <span>Dashboard Ejecutivo</span>
        </a>

        <!-- Sección Afiliaciones (Admin, Supervisor, Analista) -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'afiliados' ? '' : 'afiliados'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.titulares.index', 'ars.carga.masiva', 'ars.lotes.index', 'ars.contract_control.dashboard']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.titulares.index', 'ars.carga.masiva', 'ars.lotes.index', 'ars.contract_control.dashboard']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">group</span>
                    <span>Afiliaciones</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'afiliados' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'afiliados'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.titulares.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.titulares.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Afiliados</a>
                <a href="{{ route('ars.solicitudes.titulares.nueva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.solicitudes.titulares.nueva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Carga Individual Titular</a>
                <a href="{{ route('ars.solicitudes.dependientes.nueva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.solicitudes.dependientes.nueva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Afiliación Dependiente</a>
                <a href="{{ route('ars.solicitudes.titulares.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.solicitudes.titulares.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja Solicitudes</a>
                <a href="{{ route('ars.carga.masiva') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.carga.masiva') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Carga Masiva</a>
                <a href="{{ route('ars.lotes.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.lotes.index') && !request()->is('*novedades*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes Afiliación</a>
                <a href="{{ route('ars.contract_control.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.contract_control.*') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Control de Formularios</a>
            </div>
        </div>
        @endif

        <!-- Sección Novedades (Admin, Supervisor, Analista) -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'novedades' ? '' : 'novedades'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.novedades.create', 'ars.novedades.index', 'ars.novedades.lotes']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.novedades.create', 'ars.novedades.index', 'ars.novedades.lotes']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">notifications</span>
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
            </div>
        </div>
        @endif

        <!-- Sección Autorizaciones (Admin, Auditor, Autorizaciones) -->
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

        <!-- Sección PSS / Prestadoras (Admin, Auditor, Autorizaciones) -->
        @if($isAdmin || $isAuditor || $isAutorizaciones)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'pss' ? '' : 'pss'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.pss.index', 'ars.pss.contratos', 'ars.pss.servicios', 'ars.pss.contratos_tarifarios']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.pss.index', 'ars.pss.contratos', 'ars.pss.servicios', 'ars.pss.contratos_tarifarios']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">domain</span>
                    <span>Prestadoras (PSS)</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'pss' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'pss'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.pss.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pss.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Prestadoras Activas</a>
                <a href="{{ route('ars.pss.contratos_tarifarios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pss.contratos_tarifarios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Contratos & Tarifarios V2</a>
                <a href="{{ route('ars.pss.servicios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pss.servicios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogo Servicios</a>
            </div>
        </div>
        @endif
    </div>

    <!-- GRUPO: OPERACIONES Y FACTURACIÓN -->
    @if($isAdmin || $isSupervisor)
    <div class="space-y-1">
        <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider font-sans">
            Operaciones & Facturación
        </div>

        <!-- Sección Facturación & Reclamaciones (Admin, Supervisor) -->
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'facturacion' ? '' : 'facturacion'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.reclamaciones.index', 'ars.reclamaciones.recepcion', 'ars.reclamaciones.plazos', 'ars.reclamaciones.reportes', 'ars.pagos.cxp', 'ars.lotes.index', 'ars.lotes.show']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.reclamaciones.index', 'ars.reclamaciones.recepcion', 'ars.reclamaciones.plazos', 'ars.reclamaciones.reportes', 'ars.pagos.cxp', 'ars.lotes.index', 'ars.lotes.show']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">receipt_long</span>
                    <span>Facturación & Reclamaciones</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'facturacion' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'facturacion'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.reclamaciones.recepcion') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.recepcion') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Mesa de Entrada</a>
                <a href="{{ route('ars.reclamaciones.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bandeja Auditoría</a>
                <a href="{{ route('ars.reclamaciones.plazos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.plazos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Control de Plazos</a>
                <a href="{{ route('ars.reclamaciones.reportes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.reclamaciones.reportes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Reportes & Indicadores</a>
                <a href="{{ route('ars.pagos.cxp') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.pagos.cxp') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cuentas por Pagar</a>
                <a href="{{ route('ars.lotes.index') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.lotes.index') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes de Pago</a>
            </div>
        </div>

        <!-- Sección Simulador Unipago (Admin, Supervisor, Analista) -->
        @if($isAdmin || $isSupervisor || $isAnalista)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'unipago' ? '' : 'unipago'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.unipago.dashboard', 'ars.unipago.prevalidar', 'ars.unipago.lotes', 'ars.unipago.capitas', 'ars.unipago.cortes', 'ars.unipago.logs', 'ars.unipago.notificaciones']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.unipago.dashboard', 'ars.unipago.prevalidar', 'ars.unipago.lotes', 'ars.unipago.capitas', 'ars.unipago.cortes', 'ars.unipago.logs', 'ars.unipago.notificaciones']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">settings_input_component</span>
                    <span>Simulador Unipago</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'unipago' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'unipago'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.unipago_simulador.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago_simulador.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Simulador Avanzado V2</a>
                <a href="{{ route('ars.unipago_simulador.consola') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago_simulador.consola') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Consola Web Services</a>
                <a href="{{ route('ars.unipago.dashboard') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Dashboard</a>
                <a href="{{ route('ars.unipago.prevalidar') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.prevalidar') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Prevalidación Masiva</a>
                <a href="{{ route('ars.unipago.lotes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.lotes') || Route::is('ars.unipago.lotes.show') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Lotes Afiliación</a>
                <a href="{{ route('ars.unipago.capitas') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.capitas') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cápitas Recibidas</a>
                <a href="{{ route('ars.unipago.cortes') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.cortes') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Cortes Dispersión</a>
                <a href="{{ route('ars.unipago.logs') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.unipago.logs') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Logs API Mock</a>
            </div>
        </div>
        @endif
    </div>
    @endif

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

        <!-- Reportes (Admin, Supervisor, Auditor, Consulta) -->
        @if($isAdmin || $isSupervisor || $isAuditor || $isConsulta)
        <a href="{{ route('ars.reportes.index') }}" 
           class="group flex items-center px-4 py-2.5 text-xs font-bold transition-all duration-200 {{ Route::is('ars.reportes.index') ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600 rounded-r-full font-bold pl-5' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full hover:pl-5' }}">
            <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ Route::is('ars.reportes.index') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">show_chart</span>
            <span>Reportería Ejecutiva</span>
        </a>
        @endif

        <!-- Administración Menu (Admin, Supervisor) -->
        @if($isAdmin || $isSupervisor)
        <div class="space-y-0.5">
            <button @click="openMenu = (openMenu === 'admin' ? '' : 'admin'); localStorage.setItem('sidebar_menu', openMenu)" 
                    class="w-full group flex items-center justify-between px-4 py-2.5 text-xs font-bold rounded-r-full transition-all duration-200 {{ in_array(Route::currentRouteName(), ['ars.admin.usuarios', 'ars.admin.catalogos', 'ars.admin.bitacora']) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50 hover:pl-5' }} focus:outline-none">
                <span class="flex items-center">
                    <span class="material-symbols-outlined text-[18px] mr-3 text-center shrink-0 {{ in_array(Route::currentRouteName(), ['ars.admin.usuarios', 'ars.admin.catalogos', 'ars.admin.bitacora']) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-700' }}">admin_panel_settings</span>
                    <span>Administración</span>
                </span>
                <span class="material-symbols-outlined text-[14px] text-slate-400 transition-transform duration-250 mr-2" :class="openMenu === 'admin' ? 'rotate-180' : ''">keyboard_arrow_down</span>
            </button>
            
            <div x-show="openMenu === 'admin'" 
                 x-transition
                 class="ml-6 pl-4 border-l border-slate-100 my-1 space-y-1" x-cloak>
                <a href="{{ route('ars.admin.usuarios') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.admin.usuarios') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Usuarios & Roles</a>
                <a href="{{ route('ars.admin.catalogos') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.admin.catalogos') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Catálogos</a>
                <a href="{{ route('ars.admin.bitacora') }}" 
                   class="block px-3.5 py-2 text-[11px] font-bold rounded-r-full hover:bg-slate-50 hover:text-blue-600 transition-all {{ Route::is('ars.admin.bitacora') ? 'text-blue-600 font-bold' : 'text-slate-500' }}">Bitácora de Auditoría</a>
            </div>
        </div>
        @endif
    </div>
</nav>
