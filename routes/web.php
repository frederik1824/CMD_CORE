<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\AfiliadoController;
use App\Http\Controllers\NovedadController;
use App\Http\Controllers\AutorizacionController;
use App\Http\Controllers\PssController;
use App\Http\Controllers\ReporteController;

use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\AffiliatePortalController;
use App\Http\Controllers\AuthorizationPortalController;
use App\Http\Controllers\VirtualClassroomController;
use App\Http\Controllers\PdssController;
use App\Http\Controllers\ReclamacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\UnipagoController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\ContratoTarifarioController;
use App\Http\Controllers\AutorizacionCoreController;

/*
|--------------------------------------------------------------------------
| Web Routes - Ecosistema de Salud ARS CMD
|--------------------------------------------------------------------------
*/

// 1. Web Pública Principal (Página Institucional)
Route::get('/', [PublicSiteController::class, 'index'])->name('public.index');

// 2. Core Administrativo Interno (Aislado)
Route::get('/core', [DemoController::class, 'landing'])->name('login');
Route::get('/core/login', [DemoController::class, 'landing']);
Route::get('/switch-role/{role}', [DemoController::class, 'switchRole'])->name('switch.role');
Route::post('/logout', [DemoController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->prefix('core')->name('ars.')->group(function () {
    Route::get('/dashboard', [DemoController::class, 'dashboard'])->name('dashboard');

    // Afiliados (Titulares & Dependientes)
    Route::get('/afiliados/titulares', [AfiliadoController::class, 'titularesIndex'])->name('titulares.index');
    Route::get('/afiliados/titulares/crear', [AfiliadoController::class, 'titularesCreate'])->name('titulares.create');
    Route::post('/afiliados/titulares', [AfiliadoController::class, 'titularesStore'])->name('titulares.store');
    Route::get('/afiliados/titulares/{id}', [AfiliadoController::class, 'titularesShow'])->name('titulares.show');
    Route::get('/afiliados/titulares/{id}/editar', [AfiliadoController::class, 'titularesEdit'])->name('titulares.edit');
    Route::put('/afiliados/titulares/{id}', [AfiliadoController::class, 'titularesUpdate'])->name('titulares.update');

    // Carga Individual de Afiliados (Solicitudes)
    Route::get('/afiliaciones/titulares/nuevo', [AfiliadoController::class, 'solicitudTitularNueva'])->name('solicitudes.titulares.nueva');
    Route::post('/afiliaciones/titulares/nuevo', [AfiliadoController::class, 'solicitudTitularGuardar'])->name('solicitudes.titulares.guardar');
    Route::get('/afiliaciones/titulares/solicitudes', [AfiliadoController::class, 'solicitudesTitularesIndex'])->name('solicitudes.titulares.index');
    Route::get('/afiliaciones/titulares/solicitudes/{id}', [AfiliadoController::class, 'solicitudTitularVer'])->name('solicitudes.titulares.show');
    Route::post('/afiliaciones/titulares/solicitudes/{id}/enviar', [AfiliadoController::class, 'solicitudTitularEnviarUnipago'])->name('solicitudes.titulares.enviar');

    Route::get('/afiliaciones/dependientes/nuevo', [AfiliadoController::class, 'solicitudDependienteNueva'])->name('solicitudes.dependientes.nueva');
    Route::post('/afiliaciones/dependientes/nuevo', [AfiliadoController::class, 'solicitudDependienteGuardar'])->name('solicitudes.dependientes.guardar');
    Route::get('/afiliaciones/dependientes/solicitudes', [AfiliadoController::class, 'solicitudesDependientesIndex'])->name('solicitudes.dependientes.index');
    Route::get('/afiliaciones/dependientes/buscar-titular', [AfiliadoController::class, 'buscarTitularAjax'])->name('solicitudes.dependientes.buscar_titular');

    // Control de Formularios y Contratos de Afiliación (Unipago)
    Route::get('/afiliaciones/formularios-contratos', [\App\Http\Controllers\AffiliationContractController::class, 'dashboard'])->name('contract_control.dashboard');
    Route::get('/afiliaciones/formularios-contratos/rangos', [\App\Http\Controllers\AffiliationContractController::class, 'indexRanges'])->name('contract_control.ranges.index');
    Route::get('/afiliaciones/formularios-contratos/rangos/crear', [\App\Http\Controllers\AffiliationContractController::class, 'createRange'])->name('contract_control.ranges.create');
    Route::post('/afiliaciones/formularios-contratos/rangos', [\App\Http\Controllers\AffiliationContractController::class, 'storeRange'])->name('contract_control.ranges.store');
    Route::get('/afiliaciones/formularios-contratos/rangos/{id}', [\App\Http\Controllers\AffiliationContractController::class, 'showRange'])->name('contract_control.ranges.show');
    Route::post('/afiliaciones/formularios-contratos/rangos/{id}/estado', [\App\Http\Controllers\AffiliationContractController::class, 'updateRangeStatus'])->name('contract_control.ranges.status');
    Route::post('/afiliaciones/formularios-contratos/numeros/{id}/bloquear', [\App\Http\Controllers\AffiliationContractController::class, 'blockNumber'])->name('contract_control.numbers.block');
    Route::post('/afiliaciones/formularios-contratos/numeros/{id}/liberar', [\App\Http\Controllers\AffiliationContractController::class, 'releaseNumber'])->name('contract_control.numbers.release');
    Route::get('/afiliaciones/formularios-contratos/configuracion', [\App\Http\Controllers\AffiliationContractController::class, 'configuracion'])->name('contract_control.config');
    Route::post('/afiliaciones/formularios-contratos/configuracion', [\App\Http\Controllers\AffiliationContractController::class, 'saveConfiguracion'])->name('contract_control.config.save');

    Route::post('/afiliados/titulares/{id}/dependientes', [AfiliadoController::class, 'dependientesStore'])->name('dependientes.store');
    Route::get('/afiliados/dependientes/{id}/editar', [AfiliadoController::class, 'dependientesEdit'])->name('dependientes.edit');
    Route::put('/afiliados/dependientes/{id}', [AfiliadoController::class, 'dependientesUpdate'])->name('dependientes.update');

    // Carga Masiva
    Route::get('/afiliados/carga-masiva', [AfiliadoController::class, 'cargaMasivaIndex'])->name('carga.masiva');
    Route::post('/afiliados/carga-masiva/prevalidar', [AfiliadoController::class, 'cargaMasivaPrevalidar'])->name('carga.prevalidar');
    Route::post('/afiliados/carga-masiva/procesar', [AfiliadoController::class, 'cargaMasivaProcesar'])->name('carga.procesar');

    // Lotes
    Route::get('/afiliados/lotes', [AfiliadoController::class, 'lotesIndex'])->name('lotes.index');
    Route::get('/afiliados/lotes/{id}', [AfiliadoController::class, 'lotesShow'])->name('lotes.show');
    Route::post('/afiliados/lotes/{id}/procesar', [AfiliadoController::class, 'lotesProcesar'])->name('lotes.procesar');

    // Novedades
    Route::get('/novedades', [NovedadController::class, 'index'])->name('novedades.index');
    Route::get('/novedades/registrar', [NovedadController::class, 'create'])->name('novedades.create');
    Route::post('/novedades', [NovedadController::class, 'store'])->name('novedades.store');
    Route::get('/novedades/lotes', [NovedadController::class, 'lotesIndex'])->name('novedades.lotes');
    Route::post('/novedades/generar-lote', [NovedadController::class, 'generarLote'])->name('novedades.generar_lote');

    // Autorizaciones Médicas ARS — módulo interno
    Route::get('/autorizaciones/dashboard', [AutorizacionController::class, 'moduloDashboard'])->name('autorizaciones.dashboard');
    Route::get('/autorizaciones/nueva-ars', [AutorizacionController::class, 'nueva'])->name('autorizaciones.nueva');
    Route::post('/autorizaciones/nueva-ars', [AutorizacionController::class, 'crear'])->name('autorizaciones.crear');
    Route::get('/autorizaciones/buscar-afiliado', [AutorizacionController::class, 'buscarAfiliado'])->name('autorizaciones.buscar_afiliado');
    Route::get('/autorizaciones/auditoria-medica', [AutorizacionController::class, 'auditoriaView'])->name('autorizaciones.auditoria_medica');
    Route::get('/autorizaciones/reporte', [AutorizacionController::class, 'reporte'])->name('autorizaciones.reporte');
    Route::get('/autorizaciones/reglas', [AutorizacionController::class, 'reglasIndex'])->name('autorizaciones.reglas');
    Route::post('/autorizaciones/reglas/{id}/toggle', [AutorizacionController::class, 'reglasToggle'])->name('autorizaciones.reglas.toggle');
    Route::get('/autorizaciones/pendientes', [AutorizacionController::class, 'pendientes'])->name('autorizaciones.pendientes');
    Route::get('/autorizaciones/aprobadas', [AutorizacionController::class, 'aprobadas'])->name('autorizaciones.aprobadas');
    Route::get('/autorizaciones/rechazadas', [AutorizacionController::class, 'rechazadas'])->name('autorizaciones.rechazadas');
    Route::get('/autorizaciones/auditoria', [AutorizacionController::class, 'auditoria'])->name('autorizaciones.auditoria');
    Route::get('/autorizaciones', [AutorizacionController::class, 'index'])->name('autorizaciones.index');
    Route::get('/autorizaciones/{id}', [AutorizacionController::class, 'show'])->name('autorizaciones.show');
    Route::get('/autorizaciones/{id}/imprimir', [AutorizacionController::class, 'imprimir'])->name('autorizaciones.imprimir');
    Route::post('/autorizaciones/{id}/decision', [AutorizacionController::class, 'procesarDecision'])->name('autorizaciones.decision');
    Route::post('/autorizaciones/{id}/auditar', [AutorizacionController::class, 'auditar'])->name('autorizaciones.auditar');
    Route::post('/autorizaciones/{id}/comentar', [AutorizacionController::class, 'comentar'])->name('autorizaciones.comentar');
    Route::post('/autorizaciones/{id}/anular', [AutorizacionController::class, 'anular'])->name('autorizaciones.anular');

    // PSS / Prestadoras en el Core (Mantenimiento)
    Route::get('/pss', [PssController::class, 'index'])->name('pss.index');
    Route::get('/pss/{id}/editar', [PssController::class, 'edit'])->name('pss.edit');
    Route::put('/pss/{id}', [PssController::class, 'update'])->name('pss.update');
    Route::get('/pss/contratos', [PssController::class, 'contratosIndex'])->name('pss.contratos');
    Route::get('/pss/contratos-tarifarios', [ContratoTarifarioController::class, 'index'])->name('pss.contratos_tarifarios');
    Route::get('/pss/contratos-tarifarios/{id}', [ContratoTarifarioController::class, 'show'])->name('pss.contratos_tarifarios.show');
    Route::post('/pss/contratos-tarifarios/crear', [ContratoTarifarioController::class, 'crearContrato'])->name('pss.contratos_tarifarios.crear');
    Route::post('/pss/contratos-tarifarios/{id}/version', [ContratoTarifarioController::class, 'crearVersion'])->name('pss.contratos_tarifarios.version');
    Route::post('/pss/contratos-tarifarios/{id}/tarifa', [ContratoTarifarioController::class, 'guardarTarifa'])->name('pss.contratos_tarifarios.tarifa');
    Route::post('/pss/contratos-tarifarios/{id}/importar', [ContratoTarifarioController::class, 'importarTarifario'])->name('pss.contratos_tarifarios.importar');
    Route::get('/pss/contratos-tarifarios/plantilla/descargar', [ContratoTarifarioController::class, 'descargarPlantilla'])->name('pss.contratos_tarifarios.plantilla');
    Route::get('/pss/servicios', [PssController::class, 'serviciosIndex'])->name('pss.servicios');
    Route::get('/pss/servicios/{id}/editar', [PssController::class, 'serviciosEdit'])->name('pss.servicios.edit');
    Route::put('/pss/servicios/{id}', [PssController::class, 'serviciosUpdate'])->name('pss.servicios.update');

    // Módulo Interno Autorizaciones Core ARS
    Route::get('/autorizaciones-medicas', [AutorizacionCoreController::class, 'dashboard'])->name('autorizaciones_medicas.dashboard');
    Route::get('/autorizaciones-medicas/bandeja', [AutorizacionCoreController::class, 'index'])->name('autorizaciones_medicas.index');
    Route::get('/autorizaciones-medicas/nueva', [AutorizacionCoreController::class, 'create'])->name('autorizaciones_medicas.create');
    Route::post('/autorizaciones-medicas/nueva', [AutorizacionCoreController::class, 'store'])->name('autorizaciones_medicas.store');
    Route::get('/autorizaciones-medicas/buscar-afiliado-ajax', [AutorizacionCoreController::class, 'buscarAfiliadoAjax'])->name('autorizaciones_medicas.buscar_afiliado_ajax');
    Route::get('/autorizaciones-medicas/buscar-pss-ajax', [AutorizacionCoreController::class, 'buscarPssAjax'])->name('autorizaciones_medicas.buscar_pss_ajax');
    Route::get('/autorizaciones-medicas/obtener-tarifa-ajax', [AutorizacionCoreController::class, 'obtenerTarifaAjax'])->name('autorizaciones_medicas.obtener_tarifa_ajax');
    Route::get('/autorizaciones-medicas/historial-afiliado-ajax', [AutorizacionCoreController::class, 'historialAfiliadoAjax'])->name('autorizaciones_medicas.historial_afiliado_ajax');
    Route::get('/autorizaciones-medicas/bandeja/auditoria', [AutorizacionCoreController::class, 'bandejaAuditoria'])->name('autorizaciones_medicas.bandeja_auditoria');
    Route::get('/autorizaciones-medicas/bandeja/revision', [AutorizacionCoreController::class, 'bandejaRevision'])->name('autorizaciones_medicas.bandeja_revision');
    Route::get('/autorizaciones-medicas/config/reglas', [AutorizacionCoreController::class, 'configReglas'])->name('autorizaciones_medicas.config_reglas');
    Route::post('/autorizaciones-medicas/config/reglas', [AutorizacionCoreController::class, 'guardarConfigReglas'])->name('autorizaciones_medicas.guardar_config_reglas');
    Route::get('/autorizaciones-medicas/{id}', [AutorizacionCoreController::class, 'show'])->name('autorizaciones_medicas.show');
    Route::post('/autorizaciones-medicas/{id}/override', [AutorizacionCoreController::class, 'aplicarOverride'])->name('autorizaciones_medicas.override');
    Route::post('/autorizaciones-medicas/{id}/auditar', [AutorizacionCoreController::class, 'procesarAuditoria'])->name('autorizaciones_medicas.auditar');
    Route::post('/autorizaciones-medicas/{id}/anular', [AutorizacionCoreController::class, 'anularAutorizacion'])->name('autorizaciones_medicas.anular');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Administración
    Route::get('/administracion/usuarios', [DemoController::class, 'usuariosIndex'])->name('admin.usuarios');
    Route::get('/administracion/catalogos', [DemoController::class, 'catalogosIndex'])->name('admin.catalogos');
    Route::get('/administracion/bitacora', [DemoController::class, 'bitacoraIndex'])->name('admin.bitacora');

    // PDSS Catálogo y Reglas
    Route::get('/pdss/buscar-servicio', [PdssController::class, 'buscarServicioAjax'])->name('pdss.buscar_servicio');
    Route::get('/pdss/grupos', [PdssController::class, 'grupos'])->name('pdss.grupos');
    Route::get('/pdss/catalogo', [PdssController::class, 'catalogo'])->name('pdss.catalogo');
    Route::get('/pdss/catalogo/{id}', [PdssController::class, 'show'])->name('pdss.show');
    Route::post('/pdss/importar-pdf', [PdssController::class, 'importarPdf'])->name('pdss.importar_pdf');
    Route::post('/pdss/importar-csv', [PdssController::class, 'importarCsv'])->name('pdss.importar_csv');
    Route::get('/autorizaciones/reglas-pdss', [PdssController::class, 'reglasPdss'])->name('autorizaciones.reglas_pdss');
    Route::post('/autorizaciones/reglas-pdss', [PdssController::class, 'guardarReglasPdss'])->name('autorizaciones.guardar_reglas_pdss');

    // Reclamaciones
    Route::get('/reclamaciones', [ReclamacionController::class, 'index'])->name('reclamaciones.index');
    Route::get('/reclamaciones/recepcion', [ReclamacionController::class, 'recepcionIndex'])->name('reclamaciones.recepcion');
    Route::post('/reclamaciones/{id}/dar-entrada', [ReclamacionController::class, 'darEntrada'])->name('reclamaciones.dar_entrada');
    Route::post('/reclamaciones/{id}/devolver', [ReclamacionController::class, 'devolverDocumental'])->name('reclamaciones.devolver');
    Route::get('/reclamaciones/control/plazos', [ReclamacionController::class, 'controlPlazos'])->name('reclamaciones.plazos');
    Route::get('/reclamaciones/control/reportes', [ReclamacionController::class, 'reportes'])->name('reclamaciones.reportes');
    Route::get('/reclamaciones/{id}', [ReclamacionController::class, 'show'])->name('reclamaciones.show');
    Route::post('/reclamaciones/{id}/auditar', [ReclamacionController::class, 'auditar'])->name('reclamaciones.auditar');

    // Reclamaciones (Ampliación)
    Route::get('/reclamaciones/radicaciones/lista', [ReclamacionController::class, 'radicaciones'])->name('reclamaciones.radicaciones');
    Route::get('/reclamaciones/radicaciones/correcciones', [ReclamacionController::class, 'correcciones'])->name('reclamaciones.correcciones');
    Route::post('/reclamaciones/radicaciones/correcciones/{id}', [ReclamacionController::class, 'corregirRadicacion'])->name('reclamaciones.corregir_radicacion');
    Route::get('/reclamaciones/auditoria-retrospectiva', [ReclamacionController::class, 'auditoriaRetrospectiva'])->name('reclamaciones.auditoria_retrospectiva');
    Route::get('/reclamaciones/auditoria-facturacion', [ReclamacionController::class, 'auditoriaFacturacion'])->name('reclamaciones.auditoria_facturacion');
    Route::get('/reclamaciones/validaciones/lista', [ReclamacionController::class, 'validaciones'])->name('reclamaciones.validaciones');
    Route::get('/reclamaciones/ncf/lista', [ReclamacionController::class, 'ncfIndex'])->name('reclamaciones.ncf');
    Route::post('/reclamaciones/ncf/{id}', [ReclamacionController::class, 'corregirNcf'])->name('reclamaciones.corregir_ncf');
    Route::get('/reclamaciones/lotes/lista', [ReclamacionController::class, 'lotesIndex'])->name('reclamaciones.lotes');
    Route::post('/reclamaciones/lotes/generar', [ReclamacionController::class, 'generarLoteClaims'])->name('reclamaciones.generar_lote');
    Route::get('/reclamaciones/lotes/ver/{id}', [ReclamacionController::class, 'verLoteClaims'])->name('reclamaciones.ver_lote');
    Route::post('/reclamaciones/lotes/ncf/{id}', [ReclamacionController::class, 'corregirLoteNcf'])->name('reclamaciones.corregir_lote_ncf');
    Route::get('/reclamaciones/glosas/lista', [ReclamacionController::class, 'glosasIndex'])->name('reclamaciones.glosas');
    Route::get('/reclamaciones/notificaciones/lista', [ReclamacionController::class, 'notificaciones'])->name('reclamaciones.notificaciones');
    Route::get('/reclamaciones/plantillas/lista', [ReclamacionController::class, 'plantillas'])->name('reclamaciones.plantillas');
    Route::get('/reclamaciones/cuentas-por-pagar/lista', [ReclamacionController::class, 'cuentasPorPagar'])->name('reclamaciones.cuentas_por_pagar');

    // PyP (Promoción y Prevención)
    Route::get('/pyp/dashboard', [\App\Http\Controllers\PypController::class, 'dashboard'])->name('pyp.dashboard');
    Route::get('/pyp/actividades-no-asistenciales', [\App\Http\Controllers\PypController::class, 'actividades'])->name('pyp.actividades');
    Route::post('/pyp/actividades-no-asistenciales', [\App\Http\Controllers\PypController::class, 'guardarActividad'])->name('pyp.guardar_actividad');
    Route::get('/pyp/grupos-riesgo', [\App\Http\Controllers\PypController::class, 'gruposRiesgo'])->name('pyp.grupos_riesgo');
    Route::post('/pyp/grupos-riesgo', [\App\Http\Controllers\PypController::class, 'guardarGrupoRiesgo'])->name('pyp.guardar_grupo_riesgo');
    Route::get('/pyp/factores-riesgo', [\App\Http\Controllers\PypController::class, 'factoresRiesgo'])->name('pyp.factores_riesgo');
    Route::post('/pyp/factores-riesgo', [\App\Http\Controllers\PypController::class, 'guardarFactorRiesgo'])->name('pyp.guardar_factor_riesgo');
    Route::get('/pyp/tipos-programas', [\App\Http\Controllers\PypController::class, 'tiposProgramas'])->name('pyp.tipos_programas');
    Route::get('/pyp/programas', [\App\Http\Controllers\PypController::class, 'programas'])->name('pyp.programas');
    Route::post('/pyp/programas', [\App\Http\Controllers\PypController::class, 'guardarPrograma'])->name('pyp.guardar_programa');
    Route::get('/pyp/calendario', [\App\Http\Controllers\PypController::class, 'calendario'])->name('pyp.calendario');
    Route::post('/pyp/calendario', [\App\Http\Controllers\PypController::class, 'guardarEventoCalendar'])->name('pyp.guardar_evento');
    Route::get('/pyp/candidatos', [\App\Http\Controllers\PypController::class, 'candidatos'])->name('pyp.candidatos');
    Route::post('/pyp/candidatos/{id}/enrolar', [\App\Http\Controllers\PypController::class, 'enrolarCandidato'])->name('pyp.enrolar_candidato');
    Route::post('/pyp/candidatos/{id}/descartar', [\App\Http\Controllers\PypController::class, 'descartarCandidato'])->name('pyp.descartar_candidato');
    Route::get('/pyp/inscripciones', [\App\Http\Controllers\PypController::class, 'inscripciones'])->name('pyp.inscripciones');
    Route::post('/pyp/inscripciones', [\App\Http\Controllers\PypController::class, 'inscribirManual'])->name('pyp.inscribir_manual');
    Route::get('/pyp/cancelaciones', [\App\Http\Controllers\PypController::class, 'cancelaciones'])->name('pyp.cancelaciones');
    Route::post('/pyp/inscripciones/{id}/cancelar', [\App\Http\Controllers\PypController::class, 'cancelarInscripcion'])->name('pyp.cancelar_inscripcion');
    Route::get('/pyp/reportes', [\App\Http\Controllers\PypController::class, 'reportes'])->name('pyp.reportes');

    // Planes de Salud y Coberturas
    Route::get('/planes-salud/planes', [\App\Http\Controllers\PlanesSaludController::class, 'planes'])->name('planes_salud.planes');
    Route::post('/planes-salud/planes', [\App\Http\Controllers\PlanesSaludController::class, 'guardarPlan'])->name('planes_salud.guardar_plan');
    Route::get('/planes-salud/catalogo-pdss', [\App\Http\Controllers\PlanesSaludController::class, 'catalogoPdss'])->name('planes_salud.catalogo_pdss');
    Route::get('/planes-salud/catalogo-planes-alternativos', [\App\Http\Controllers\PlanesSaludController::class, 'catalogoAlternativos'])->name('planes_salud.catalogo_alternativos');
    Route::get('/planes-salud/coberturas', [\App\Http\Controllers\PlanesSaludController::class, 'coberturas'])->name('planes_salud.coberturas');
    Route::post('/planes-salud/coberturas', [\App\Http\Controllers\PlanesSaludController::class, 'guardarCobertura'])->name('planes_salud.guardar_cobertura');
    Route::get('/planes-salud/detalle-servicio', [\App\Http\Controllers\PlanesSaludController::class, 'detalleServicio'])->name('planes_salud.detalle_servicio');
    Route::get('/planes-salud/derivaciones', [\App\Http\Controllers\PlanesSaludController::class, 'derivaciones'])->name('planes_salud.derivaciones');
    Route::post('/planes-salud/derivaciones', [\App\Http\Controllers\PlanesSaludController::class, 'guardarDerivacion'])->name('planes_salud.guardar_derivacion');
    Route::get('/planes-salud/periodos-espera', [\App\Http\Controllers\PlanesSaludController::class, 'periodosEspera'])->name('planes_salud.periodos_espera');
    Route::get('/planes-salud/topes', [\App\Http\Controllers\PlanesSaludController::class, 'topes'])->name('planes_salud.topes');
    Route::post('/planes-salud/topes', [\App\Http\Controllers\PlanesSaludController::class, 'guardarTope'])->name('planes_salud.guardar_tope');
    Route::get('/planes-salud/reportes', [\App\Http\Controllers\PlanesSaludController::class, 'reportes'])->name('planes_salud.reportes');

    // Prestadores (Fortalecimiento)
    Route::get('/prestadores/personas-fisicas', [\App\Http\Controllers\PrestadoresController::class, 'personasFisicas'])->name('prestadores.personas_fisicas');
    Route::get('/prestadores/personas-juridicas', [\App\Http\Controllers\PrestadoresController::class, 'personasJuridicas'])->name('prestadores.personas_juridicas');
    Route::post('/prestadores/guardar', [\App\Http\Controllers\PrestadoresController::class, 'guardarPrestador'])->name('prestadores.guardar');
    Route::get('/prestadores/auditores-medicos', [\App\Http\Controllers\PrestadoresController::class, 'auditoresMedicos'])->name('prestadores.auditores_medicos');
    Route::post('/prestadores/auditores-medicos', [\App\Http\Controllers\PrestadoresController::class, 'guardarAuditor'])->name('prestadores.guardar_auditor');
    Route::get('/prestadores/servicios-contratados', [\App\Http\Controllers\PrestadoresController::class, 'serviciosContratados'])->name('prestadores.servicios_contratados');
    Route::post('/prestadores/servicios-contratados', [\App\Http\Controllers\PrestadoresController::class, 'guardarServicioContratado'])->name('prestadores.guardar_servicio_contratado');
    Route::get('/prestadores/convenios-precios', [\App\Http\Controllers\PrestadoresController::class, 'conveniosPrecios'])->name('prestadores.convenios_precios');
    Route::get('/prestadores/precios-convenidos', [\App\Http\Controllers\PrestadoresController::class, 'preciosConvenidos'])->name('prestadores.precios_convenidos');
    Route::post('/prestadores/precios-convenidos', [\App\Http\Controllers\PrestadoresController::class, 'guardarPrecioConvenido'])->name('prestadores.guardar_precio_convenido');
    Route::get('/prestadores/grupos', [\App\Http\Controllers\PrestadoresController::class, 'grupos'])->name('prestadores.grupos');
    Route::post('/prestadores/grupos', [\App\Http\Controllers\PrestadoresController::class, 'guardarGrupo'])->name('prestadores.guardar_grupo');
    Route::get('/prestadores/red-por-plan', [\App\Http\Controllers\PrestadoresController::class, 'redPorPlan'])->name('prestadores.red_por_plan');
    Route::post('/prestadores/red-por-plan', [\App\Http\Controllers\PrestadoresController::class, 'guardarRedPorPlan'])->name('prestadores.guardar_red_por_plan');
    Route::get('/prestadores/habilitacion-servicios', [\App\Http\Controllers\PrestadoresController::class, 'habilitacionServicios'])->name('prestadores.habilitacion_servicios');
    Route::get('/prestadores/georreferencial', [\App\Http\Controllers\PrestadoresController::class, 'georreferencial'])->name('prestadores.georreferencial');
    Route::get('/prestadores/servicios-capitados/contratos', [\App\Http\Controllers\PrestadoresController::class, 'capitadosContratos'])->name('prestadores.capitados_contratos');
    Route::post('/prestadores/servicios-capitados/contratos', [\App\Http\Controllers\PrestadoresController::class, 'guardarCapitadoContrato'])->name('prestadores.guardar_capitado_contrato');
    Route::get('/prestadores/servicios-capitados/pagos', [\App\Http\Controllers\PrestadoresController::class, 'capitadosPagos'])->name('prestadores.capitados_pagos');
    Route::post('/prestadores/servicios-capitados/pagos', [\App\Http\Controllers\PrestadoresController::class, 'guardarCapitadoPago'])->name('prestadores.guardar_capitado_pago');

    // Afiliaciones (Completo)
    Route::get('/afiliaciones/mantenimiento', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'mantenimiento'])->name('afiliaciones.mantenimiento');
    Route::get('/afiliaciones/tipos-contratos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'tiposContratos'])->name('afiliaciones.tipos_contratos');
    Route::post('/afiliaciones/tipos-contratos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'guardarTipoContrato'])->name('afiliaciones.guardar_tipo_contrato');
    Route::get('/afiliaciones/titulares', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'solicitudesTitularesIndex'])->name('afiliaciones.titulares');
    Route::get('/afiliaciones/dependientes', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'solicitudesDependientesIndex'])->name('afiliaciones.dependientes');
    Route::get('/afiliaciones/traspasos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'traspasos'])->name('afiliaciones.traspasos');
    Route::post('/afiliaciones/traspasos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'registrarTraspaso'])->name('afiliaciones.registrar_traspaso');
    Route::get('/afiliaciones/consultas', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'consultas'])->name('afiliaciones.consultas');
    Route::get('/afiliaciones/grupos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'grupos'])->name('afiliaciones.grupos');
    Route::post('/afiliaciones/grupos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'guardarGrupo'])->name('afiliaciones.guardar_grupo');
    Route::get('/afiliaciones/unidades-negocio', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'unidadesNegocio'])->name('afiliaciones.unidades_negocio');
    Route::post('/afiliaciones/unidades-negocio', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'guardarUnidadNegocio'])->name('afiliaciones.guardar_unidad_negocio');
    Route::get('/afiliaciones/transacciones', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'transacciones'])->name('afiliaciones.transacciones');
    Route::get('/afiliaciones/archivos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'archivos'])->name('afiliaciones.archivos');
    Route::post('/afiliaciones/archivos/generar', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'generarArchivoNovedad'])->name('afiliaciones.generar_archivo');
    Route::get('/afiliaciones/parentescos', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'parentescos'])->name('afiliaciones.parentescos');
    Route::get('/afiliaciones/tipos-afiliacion', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'tiposAfiliacion'])->name('afiliaciones.tipos_afiliacion');
    Route::get('/afiliaciones/codificacion-geografica', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'codificacionGeografica'])->name('afiliaciones.codificacion_geografica');
    Route::post('/afiliaciones/codificacion-geografica', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'guardarCodificacionGeografica'])->name('afiliaciones.guardar_codificacion_geografica');
    Route::get('/afiliaciones/reportes', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'reportes'])->name('afiliaciones.reportes');
    Route::get('/afiliaciones/tipificacion', [\App\Http\Controllers\AfiliacionesCompletoController::class, 'tipificacion'])->name('afiliaciones.tipificacion');

    // Carnetización
    Route::get('/carnetizacion/solicitudes', [\App\Http\Controllers\CarnetizacionController::class, 'solicitudes'])->name('carnetizacion.solicitudes');
    Route::post('/carnetizacion/solicitudes', [\App\Http\Controllers\CarnetizacionController::class, 'crearSolicitud'])->name('carnetizacion.crear_solicitud');
    Route::get('/carnetizacion/impresion', [\App\Http\Controllers\CarnetizacionController::class, 'impresion'])->name('carnetizacion.impresion');
    Route::post('/carnetizacion/impresion/procesar', [\App\Http\Controllers\CarnetizacionController::class, 'procesarImpresion'])->name('carnetizacion.procesar_impresion');
    Route::get('/carnetizacion/tipos-carnets', [\App\Http\Controllers\CarnetizacionController::class, 'tiposCarnets'])->name('carnetizacion.tipos_carnets');
    Route::get('/carnetizacion/conceptos', [\App\Http\Controllers\CarnetizacionController::class, 'conceptos'])->name('carnetizacion.conceptos');
    Route::get('/carnetizacion/entregas', [\App\Http\Controllers\CarnetizacionController::class, 'entregas'])->name('carnetizacion.entregas');
    Route::post('/carnetizacion/entregas', [\App\Http\Controllers\CarnetizacionController::class, 'registrarEntrega'])->name('carnetizacion.registrar_entrega');
    Route::get('/carnetizacion/transferencias', [\App\Http\Controllers\CarnetizacionController::class, 'transferencias'])->name('carnetizacion.transferencias');
    Route::post('/carnetizacion/transferencias', [\App\Http\Controllers\CarnetizacionController::class, 'registrarTransferencia'])->name('carnetizacion.registrar_transferencia');
    Route::get('/carnetizacion/localizaciones', [\App\Http\Controllers\CarnetizacionController::class, 'localizaciones'])->name('carnetizacion.localizaciones');
    Route::get('/carnetizacion/centros-impresion', [\App\Http\Controllers\CarnetizacionController::class, 'centrosImpresion'])->name('carnetizacion.centros_impresion');
    Route::post('/carnetizacion/centros-impresion', [\App\Http\Controllers\CarnetizacionController::class, 'guardarCentroImpresion'])->name('carnetizacion.guardar_centro_impresion');
    Route::get('/carnetizacion/insumos', [\App\Http\Controllers\CarnetizacionController::class, 'insumos'])->name('carnetizacion.insumos');
    Route::post('/carnetizacion/insumos', [\App\Http\Controllers\CarnetizacionController::class, 'guardarInsumo'])->name('carnetizacion.guardar_insumo');
    Route::post('/carnetizacion/insumos/movimiento', [\App\Http\Controllers\CarnetizacionController::class, 'registrarMovimiento'])->name('carnetizacion.registrar_movimiento');
    Route::get('/carnetizacion/ajustes', [\App\Http\Controllers\CarnetizacionController::class, 'ajustes'])->name('carnetizacion.ajustes');
    Route::post('/carnetizacion/ajustes', [\App\Http\Controllers\CarnetizacionController::class, 'registrarAjuste'])->name('carnetizacion.registrar_ajuste');
    Route::get('/carnetizacion/despachos', [\App\Http\Controllers\CarnetizacionController::class, 'despachos'])->name('carnetizacion.despachos');
    Route::get('/carnetizacion/devoluciones', [\App\Http\Controllers\CarnetizacionController::class, 'devoluciones'])->name('carnetizacion.devoluciones');
    Route::get('/carnetizacion/reportes', [\App\Http\Controllers\CarnetizacionController::class, 'reportes'])->name('carnetizacion.reportes');

    // Unipago (Completo)
    Route::get('/unipago/procesos-afiliacion', [\App\Http\Controllers\UnipagoController::class, 'procesosAfiliacion'])->name('unipago.procesos_afiliacion');
    Route::get('/unipago/procesos-novedades', [\App\Http\Controllers\UnipagoController::class, 'procesosNovedades'])->name('unipago.procesos_novedades');
    Route::get('/unipago/consultas-ciudadanos', [\App\Http\Controllers\UnipagoController::class, 'consultasCiudadanos'])->name('unipago.consultas_ciudadanos');
    Route::get('/unipago/notificacion-cartera', [\App\Http\Controllers\UnipagoController::class, 'notificacionCartera'])->name('unipago.notificacion_cartera');
    Route::get('/unipago/notificacion-cobertura', [\App\Http\Controllers\UnipagoController::class, 'notificacionCobertura'])->name('unipago.notificacion_cobertura');
    Route::get('/unipago/recaudo', [\App\Http\Controllers\UnipagoController::class, 'recaudo'])->name('unipago.recaudo');
    Route::post('/unipago/recaudo/procesar', [\App\Http\Controllers\UnipagoController::class, 'procesarRecaudo'])->name('unipago.procesar_recaudo');
    Route::get('/unipago/traspasos', [\App\Http\Controllers\UnipagoController::class, 'traspasos'])->name('unipago.traspasos');
    Route::get('/unipago/consulta-processes', [\App\Http\Controllers\UnipagoController::class, 'consultaProcesos'])->name('unipago.consulta_procesos');
    Route::get('/unipago/simulador', [\App\Http\Controllers\UnipagoController::class, 'simulador'])->name('unipago.simulador_core');

    // Promotores
    Route::get('/promotores/personas-fisicas', [\App\Http\Controllers\PromotoresController::class, 'personasFisicas'])->name('promotores.personas_fisicas');
    Route::get('/promotores/empresas', [\App\Http\Controllers\PromotoresController::class, 'empresas'])->name('promotores.empresas');
    Route::post('/promotores/guardar', [\App\Http\Controllers\PromotoresController::class, 'guardarPromotor'])->name('promotores.guardar');
    Route::get('/promotores/tipos-contratos', [\App\Http\Controllers\PromotoresController::class, 'tiposContratos'])->name('promotores.tipos_contratos');
    Route::post('/promotores/tipos-contratos', [\App\Http\Controllers\PromotoresController::class, 'guardarContrato'])->name('promotores.guardar_contrato');
    Route::get('/promotores/campanas', [\App\Http\Controllers\PromotoresController::class, 'campanas'])->name('promotores.campanas');
    Route::post('/promotores/campanas', [\App\Http\Controllers\PromotoresController::class, 'guardarCampana'])->name('promotores.guardar_campana');
    Route::get('/promotores/esquemas-comisiones-campana', [\App\Http\Controllers\PromotoresController::class, 'esquemasCampana'])->name('promotores.esquemas_campana');
    Route::get('/promotores/calculo-comisiones-campana', [\App\Http\Controllers\PromotoresController::class, 'calculoCampana'])->name('promotores.calculo_campana');
    Route::post('/promotores/comisiones/calcular', [\App\Http\Controllers\PromotoresController::class, 'calcularComisiones'])->name('promotores.calcular_commisiones');
    Route::get('/promotores/tipos-gestion', [\App\Http\Controllers\PromotoresController::class, 'tiposGestion'])->name('promotores.tipos_gestion');
    Route::get('/promotores/esquemas-comisiones-gestion', [\App\Http\Controllers\PromotoresController::class, 'esquemasGestion'])->name('promotores.esquemas_gestion');
    Route::get('/promotores/calculo-comisiones-gestion', [\App\Http\Controllers\PromotoresController::class, 'calculoGestion'])->name('promotores.calculo_gestion');
    Route::get('/promotores/reportes', [\App\Http\Controllers\PromotoresController::class, 'reportes'])->name('promotores.reportes');
    Route::get('/promotores/tipificacion', [\App\Http\Controllers\PromotoresController::class, 'tipificacion'])->name('promotores.tipificacion');

    // SISALRIL
    Route::get('/sisalril/esquema-31', [\App\Http\Controllers\SisalrilController::class, 'esquema31'])->name('sisalril.esquema31');
    Route::get('/sisalril/esquema-33', [\App\Http\Controllers\SisalrilController::class, 'esquema33'])->name('sisalril.esquema33');
    Route::get('/sisalril/esquema-34', [\App\Http\Controllers\SisalrilController::class, 'esquema34'])->name('sisalril.esquema34');
    Route::get('/sisalril/reportes', [\App\Http\Controllers\SisalrilController::class, 'reportes'])->name('sisalril.reportes');
    Route::get('/sisalril/exportaciones', [\App\Http\Controllers\SisalrilController::class, 'exportaciones'])->name('sisalril.exportaciones');
    Route::post('/sisalril/exportar/{esquema}', [\App\Http\Controllers\SisalrilController::class, 'exportarEsquema'])->name('sisalril.exportar');

    // Facturación
    Route::get('/facturacion', [\App\Http\Controllers\FacturacionBaseController::class, 'index'])->name('facturacion.index');
    Route::get('/facturacion/planes-alternativos', [\App\Http\Controllers\FacturacionBaseController::class, 'planesAlternativos'])->name('facturacion.planes_alternativos');
    Route::get('/facturacion/grupos-afiliados', [\App\Http\Controllers\FacturacionBaseController::class, 'gruposAfiliados'])->name('facturacion.grupos_afiliados');
    Route::get('/facturacion/comprobantes', [\App\Http\Controllers\FacturacionBaseController::class, 'comprobantes'])->name('facturacion.comprobantes');
    Route::post('/facturacion/emitir', [\App\Http\Controllers\FacturacionBaseController::class, 'emitirFactura'])->name('facturacion.emitir');

    // Servicio al Cliente
    Route::get('/servicio-cliente', [\App\Http\Controllers\ServicioClienteBaseController::class, 'index'])->name('servicio_cliente.index');
    Route::get('/servicio-cliente/casos', [\App\Http\Controllers\ServicioClienteBaseController::class, 'casos'])->name('servicio_cliente.casos');
    Route::post('/servicio-cliente/casos', [\App\Http\Controllers\ServicioClienteBaseController::class, 'registrarCaso'])->name('servicio_cliente.registrar_caso');
    Route::get('/servicio-cliente/seguimiento', [\App\Http\Controllers\ServicioClienteBaseController::class, 'seguimiento'])->name('servicio_cliente.seguimiento');
    // Corregido nombre de ruta para consistencia con el controlador
    Route::post('/servicio-cliente/casos/{id}/resolver', [\App\Http\Controllers\ServicioClienteBaseController::class, 'resolverCaso'])->name('servicio_cliente.resolver_caso');

    // Catálogos Generales
    Route::get('/catalogos', [\App\Http\Controllers\CatalogosGeneralesController::class, 'index'])->name('catalogos.index');
    Route::get('/catalogos/ver/{grupo}', [\App\Http\Controllers\CatalogosGeneralesController::class, 'verGrupo'])->name('catalogos.ver');
    Route::post('/catalogos/guardar', [\App\Http\Controllers\CatalogosGeneralesController::class, 'guardarItem'])->name('catalogos.guardar');
    Route::post('/catalogos/toggle/{id}', [\App\Http\Controllers\CatalogosGeneralesController::class, 'toggleStatus'])->name('catalogos.toggle');

    // Reportes Generales
    Route::get('/reportes-generales', [\App\Http\Controllers\ReportesGeneralesController::class, 'index'])->name('reportes_generales.index');
    Route::post('/reportes-generales/programar', [\App\Http\Controllers\ReportesGeneralesController::class, 'programarReporte'])->name('reportes_generales.programar');

    // Pagos
    Route::get('/pagos/cxp', [PagoController::class, 'cxpIndex'])->name('pagos.cxp');
    Route::get('/pagos/lotes', [PagoController::class, 'lotesIndex'])->name('lotes.index');
    Route::get('/pagos/lotes/{id}', [PagoController::class, 'loteShow'])->name('lotes.show');
    Route::post('/pagos/lotes/crear', [PagoController::class, 'crearLote'])->name('lotes.crear');
    Route::post('/pagos/lotes/{id}/aprobar', [PagoController::class, 'aprobarLote'])->name('lotes.aprobar');
    Route::post('/pagos/lotes/{id}/pagar', [PagoController::class, 'pagarLote'])->name('lotes.pagar');
    Route::post('/pagos/lotes/{id}/conciliar', [PagoController::class, 'conciliarLote'])->name('lotes.conciliar');

    // Unipago Mock
    Route::get('/unipago/dashboard', [UnipagoController::class, 'dashboard'])->name('unipago.dashboard');
    Route::get('/unipago/prevalidar', [UnipagoController::class, 'prevalidar'])->name('unipago.prevalidar');
    Route::post('/unipago/prevalidar', [UnipagoController::class, 'prevalidar'])->name('unipago.prevalidar.post');
    Route::get('/unipago/lotes', [UnipagoController::class, 'lotesIndex'])->name('unipago.lotes');
    Route::get('/unipago/lotes/{id}', [UnipagoController::class, 'loteShow'])->name('unipago.lotes.show');
    Route::post('/unipago/lotes/subir', [UnipagoController::class, 'subirLote'])->name('unipago.lotes.subir');
    Route::post('/unipago/lotes/{id}/procesar', [UnipagoController::class, 'procesarLote'])->name('unipago.lotes.procesar');
    Route::get('/unipago/capitas', [UnipagoController::class, 'capitasIndex'])->name('unipago.capitas');
    Route::post('/unipago/capitas/{id}/{accion}', [UnipagoController::class, 'capitaAccion'])->name('unipago.capitas.accion');
    Route::get('/unipago/cortes', [UnipagoController::class, 'cortesIndex'])->name('unipago.cortes');
    Route::post('/unipago/cortes/generar', [UnipagoController::class, 'generarCorte'])->name('unipago.cortes.generar');
    Route::get('/unipago/logs', [UnipagoController::class, 'mockLogs'])->name('unipago.logs');
    Route::get('/unipago/consultar-cedula', [UnipagoController::class, 'consultarCedula'])->name('unipago.consultar_cedula');

    // Unipago Simulador Avanzado
    Route::get('/unipago-simulador/dashboard', [\App\Http\Controllers\UnipagoSimuladorController::class, 'dashboard'])->name('unipago_simulador.dashboard');
    Route::get('/unipago-simulador/consola', [\App\Http\Controllers\UnipagoSimuladorController::class, 'consola'])->name('unipago_simulador.consola');
    Route::post('/unipago-simulador/ejecutar-ws', [\App\Http\Controllers\UnipagoSimuladorController::class, 'ejecutarWS'])->name('unipago_simulador.ejecutar_ws');
    Route::post('/unipago-simulador/guardar-config-ws', [\App\Http\Controllers\UnipagoSimuladorController::class, 'guardarConfigWS'])->name('unipago_simulador.guardar_config_ws');

    // Contabilidad ARS
    Route::get('/contabilidad', [AccountingController::class, 'dashboard'])->name('contabilidad.dashboard');
    Route::get('/contabilidad/catalogo', [AccountingController::class, 'catalogo'])->name('contabilidad.catalogo');
    Route::get('/contabilidad/asientos', [AccountingController::class, 'asientos'])->name('contabilidad.asientos');
    Route::get('/contabilidad/asientos/{id}', [AccountingController::class, 'asientoShow'])->name('contabilidad.asiento_show');
    Route::get('/contabilidad/mayor', [AccountingController::class, 'mayor'])->name('contabilidad.mayor');
    Route::get('/contabilidad/balances', [AccountingController::class, 'balances'])->name('contabilidad.balances');
    Route::get('/contabilidad/cierre', [AccountingController::class, 'cierreIndex'])->name('contabilidad.cierre');
    Route::post('/contabilidad/cierre', [AccountingController::class, 'ejecutarCierre'])->name('contabilidad.ejecutar_cierre');

    // Reembolsos de Afiliados
    Route::get('/reembolsos', [ReimbursementController::class, 'index'])->name('reembolsos.index');
    Route::get('/reembolsos/crear', [ReimbursementController::class, 'create'])->name('reembolsos.create');
    Route::post('/reembolsos', [ReimbursementController::class, 'store'])->name('reembolsos.store');
    Route::get('/reembolsos/{id}', [ReimbursementController::class, 'show'])->name('reembolsos.show');
    Route::post('/reembolsos/{id}/estado', [ReimbursementController::class, 'updateStatus'])->name('reembolsos.estado');
    Route::post('/reembolsos/{id}/documentos', [ReimbursementController::class, 'uploadDocument'])->name('reembolsos.documentos');

    // Conciliaciones de Glosas
    Route::post('/reclamaciones/{id}/conciliar', [ReclamacionController::class, 'conciliacionStore'])->name('reclamaciones.conciliar');
});

// 3. Plataforma Virtual del Afiliado
Route::redirect('/plataforma-virtual', '/plataforma-virtual/login');
Route::prefix('plataforma-virtual')->name('affiliate.')->group(function () {
    Route::get('/login', [AffiliatePortalController::class, 'showLogin'])->name('login');
    Route::post('/login', [AffiliatePortalController::class, 'login'])->name('login.post');
    Route::get('/logout', [AffiliatePortalController::class, 'logout'])->name('logout');
    
    Route::middleware(['web'])->group(function() {
        Route::get('/dashboard', [AffiliatePortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/dependientes', [AffiliatePortalController::class, 'dependientes'])->name('dependientes');
        Route::get('/autorizaciones', [AffiliatePortalController::class, 'autorizaciones'])->name('autorizaciones');
        Route::get('/carnet', [AffiliatePortalController::class, 'carnet'])->name('carnet');
        Route::get('/prestadores', [AffiliatePortalController::class, 'prestadores'])->name('prestadores');
        Route::get('/solicitudes', [AffiliatePortalController::class, 'solicitudes'])->name('solicitudes');
        Route::post('/solicitudes', [AffiliatePortalController::class, 'solicitudes'])->name('solicitudes.post');
    });
});

// 4. Portal de Autorizaciones PSS
Route::redirect('/portal-autorizaciones', '/portal-autorizaciones/login');

Route::middleware(['web'])->group(function () {
    Route::get('/portal-autorizaciones/login', [AuthorizationPortalController::class, 'showLogin'])->name('pss.login');
    Route::post('/portal-autorizaciones/login', [AuthorizationPortalController::class, 'login'])->name('pss.login.post');
});

Route::middleware(['web', 'auth'])->prefix('portal-autorizaciones')->name('pss.')->group(function () {
    Route::get('/dashboard', [AuthorizationPortalController::class, 'portalDashboard'])->name('dashboard');
    Route::get('/afiliados/buscar', [AuthorizationPortalController::class, 'portalBuscarAfiliado'])->name('buscar');
    Route::get('/afiliados/validar-json', [AuthorizationPortalController::class, 'portalValidarJson'])->name('afiliados.validar_json');
    Route::get('/autorizaciones/nueva', [AuthorizationPortalController::class, 'portalNuevaAutorizacion'])->name('autorizaciones.nueva');
    Route::post('/autorizaciones/nueva', [AuthorizationPortalController::class, 'portalGuardarAutorizacion'])->name('autorizaciones.guardar');
    Route::get('/autorizaciones/mis-solicitudes', [AuthorizationPortalController::class, 'portalMisSolicitudes'])->name('solicitudes');
    Route::get('/autorizaciones/{id}/imprimir', [AuthorizationPortalController::class, 'portalImprimirAutorizacion'])->name('autorizaciones.imprimir');
    Route::get('/autorizaciones/cancelar', [AuthorizationPortalController::class, 'portalCancelarIndex'])->name('autorizaciones.cancelar');
    Route::post('/autorizaciones/cancelar/buscar', [AuthorizationPortalController::class, 'portalCancelarBuscar'])->name('autorizaciones.cancelar_buscar');
    Route::post('/autorizaciones/cancelar/{id}', [AuthorizationPortalController::class, 'portalCancelarProcesar'])->name('autorizaciones.cancelar_procesar');
    Route::get('/perfil', [AuthorizationPortalController::class, 'portalPerfil'])->name('perfil');
    Route::post('/perfil/switch', [AuthorizationPortalController::class, 'portalSwitchAccessType'])->name('perfil.switch');

    // Rutas específicas de Farmacia
    Route::get('/farmacia/nueva-dispensacion', [AuthorizationPortalController::class, 'portalNuevaDispensacion'])->name('farmacia.nueva_dispensacion');
    Route::post('/farmacia/nueva-dispensacion', [AuthorizationPortalController::class, 'portalGuardarDispensacion'])->name('farmacia.guardar_dispensacion');
    Route::get('/farmacia/recetas', [AuthorizationPortalController::class, 'portalRecetasIndex'])->name('farmacia.recetas');

    // Rutas específicas de Laboratorio
    Route::get('/laboratorio/nueva-orden', [AuthorizationPortalController::class, 'portalNuevaOrdenLab'])->name('laboratorio.nueva_orden');
    Route::post('/laboratorio/nueva-orden', [AuthorizationPortalController::class, 'portalGuardarOrdenLab'])->name('laboratorio.guardar_orden');
    Route::get('/laboratorio/ordenes', [AuthorizationPortalController::class, 'portalOrdenesIndex'])->name('laboratorio.ordenes');
    Route::get('/laboratorio/resultados', [AuthorizationPortalController::class, 'portalResultadosIndex'])->name('laboratorio.resultados');
    Route::post('/laboratorio/resultados/subir', [AuthorizationPortalController::class, 'portalSubirResultado'])->name('laboratorio.subir_resultado');

    // Facturación y Reclamaciones PSS
    Route::post('/autorizaciones/{id}/prestar-servicio', [AuthorizationPortalController::class, 'portalPrestarServicio'])->name('autorizaciones.prestar_servicio');
    Route::get('/autorizaciones/{id}/reclamar', [AuthorizationPortalController::class, 'portalFormReclamar'])->name('autorizaciones.reclamar');
    Route::post('/autorizaciones/{id}/reclamar', [AuthorizationPortalController::class, 'portalGuardarReclamar'])->name('autorizaciones.reclamar.store');
    Route::get('/reclamaciones', [AuthorizationPortalController::class, 'portalReclamacionesIndex'])->name('reclamaciones.index');
    Route::get('/reclamaciones/{id}', [AuthorizationPortalController::class, 'portalReclamacionShow'])->name('reclamaciones.show');
    Route::post('/reclamaciones/{id}/glosa/{glosaId}/responder', [AuthorizationPortalController::class, 'portalResponderGlosa'])->name('reclamaciones.glosa.responder');
    Route::get('/pagos', [AuthorizationPortalController::class, 'portalPagosIndex'])->name('pagos.index');
});

// 5. Aula Virtual
Route::redirect('/aula-virtual', '/aula-virtual/login');
Route::get('/aula-virtual/login', [VirtualClassroomController::class, 'showLogin'])->name('classroom.login');
Route::post('/aula-virtual/login', [VirtualClassroomController::class, 'login'])->name('classroom.login.post');
Route::get('/aula-virtual/logout', [VirtualClassroomController::class, 'logout'])->name('classroom.logout');

Route::middleware(['auth'])->prefix('aula-virtual')->name('classroom.')->group(function () {
    Route::get('/dashboard', [VirtualClassroomController::class, 'dashboard'])->name('dashboard');
    Route::get('/cursos', [VirtualClassroomController::class, 'cursos'])->name('cursos');
    Route::get('/cursos/{id}', [VirtualClassroomController::class, 'curso'])->name('curso');
    Route::post('/cursos/{id}/matricular', [VirtualClassroomController::class, 'matricular'])->name('matricular');
    Route::get('/cursos/{course_id}/lecciones/{lesson_id}', [VirtualClassroomController::class, 'leccion'])->name('leccion');
    Route::post('/cursos/{course_id}/lecciones/{lesson_id}/completar', [VirtualClassroomController::class, 'completarLeccion'])->name('leccion.completar');
    Route::get('/cursos/{course_id}/evaluacion', [VirtualClassroomController::class, 'evaluacion'])->name('evaluacion');
    Route::post('/cursos/{course_id}/evaluacion/procesar', [VirtualClassroomController::class, 'procesarEvaluacion'])->name('evaluacion.procesar');
    Route::get('/certificados', [VirtualClassroomController::class, 'certificados'])->name('certificados');
});
