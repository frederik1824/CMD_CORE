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
