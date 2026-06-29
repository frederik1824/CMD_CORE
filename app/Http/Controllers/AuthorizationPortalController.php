<?php

namespace App\Http\Controllers;

use App\Models\Pss;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\ServicioMedico;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Autorizacion;
use App\Models\AutorizacionDetalle;
use App\Models\Documento;
use App\Models\Bitacora;
use App\Models\PdssService;
use App\Models\AuthorizationClaim;
use App\Models\ClaimDocument;
use App\Models\AccountPayable;
use App\Models\AuthorizationTimelineEvent;
use App\Models\PharmacyPrescription;
use App\Models\PharmacyDispensation;
use App\Models\PharmacyDispensationItem;
use App\Models\LabOrder;
use App\Models\LabOrderItem;
use App\Models\LabResult;
use App\Models\PssUser;
use App\Services\AuthorizationEvaluator;
use App\Services\PssAuthorizationFlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthorizationPortalController extends Controller
{
    /**
     * Muestra la pantalla de login del Portal de Autorizaciones.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'Usuario PSS') {
                return redirect()->route('pss.dashboard');
            }
            return redirect()->route('ars.dashboard');
        }

        return view('authorization-portal.login');
    }

    /**
     * Procesa el login para el Portal de Autorizaciones PSS.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'access_type' => 'required|in:medical_center,pharmacy,laboratory',
        ]);

        $credentials = $request->only('email', 'password');
        $accessType = $request->input('access_type');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'Usuario PSS') {
                // Check if user has access to this type of PSS
                $hasAccess = false;
                if ($accessType === 'medical_center') {
                    // Legacy user defaults to medical_center if pss_id is set
                    $hasAccess = true;
                } else {
                    $hasAccess = PssUser::where('user_id', $user->id)
                        ->where('access_type', $accessType)
                        ->where('status', 'activo')
                        ->exists();
                }

                if (!$hasAccess) {
                    Auth::logout();
                    return redirect()->back()
                        ->with('error', 'Este usuario no pertenece al tipo de prestador seleccionado.');
                }

                // Get active PSS assignment
                $activePssUser = PssUser::where('user_id', $user->id)
                    ->where('access_type', $accessType)
                    ->where('status', 'activo')
                    ->first();

                $pssId = $activePssUser ? $activePssUser->pss_id : ($user->pss_id ?? 1);

                session([
                    'active_access_type' => $accessType,
                    'active_pss_id' => $pssId
                ]);

                Bitacora::registrar('PSS Portal', "Inicio de sesión de prestador: {$user->name} como {$accessType}");
                return redirect()->route('pss.dashboard')
                    ->with('success', 'Sesión iniciada correctamente en el Portal.');
            }

            // Si es otro rol but entered here, redirect to core dashboard
            session([
                'active_access_type' => 'medical_center',
                'active_pss_id' => 1
            ]);
            return redirect()->route('ars.dashboard');
        }

        return redirect()->back()
            ->with('error', 'Las credenciales proporcionadas no son válidas para el Portal PSS.');
    }

    /**
     * Dashboard del portal PSS.
     */
    public function portalDashboard()
    {
        $user = Auth::user();
        $accessType = session('active_access_type', 'medical_center');
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $pss = Pss::find($pssId);

        if ($accessType === 'pharmacy') {
            return $this->portalDashboardFarmacia($pss);
        } elseif ($accessType === 'laboratory') {
            return $this->portalDashboardLaboratorio($pss);
        } else {
            return $this->portalDashboardCentroMedico($pss);
        }
    }

    private function portalDashboardCentroMedico(Pss $pss)
    {
        $user = Auth::user();
        $metricas = [
            'total' => Autorizacion::where('pss_id', $pss->id)->count(),
            'aprobadas' => Autorizacion::where('pss_id', $pss->id)->where('estado', 'Aprobada')->count(),
            'rechazadas' => Autorizacion::where('pss_id', $pss->id)->where('estado', 'Rechazada')->count(),
            'pendientes' => Autorizacion::where('pss_id', $pss->id)->whereIn('estado', ['Pendiente', 'Auditoría', 'Pendiente Documento'])->count(),
        ];

        $ultimasSolicitudes = Autorizacion::where('pss_id', $pss->id)
            ->with('servicio')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $consultasRecientes = Bitacora::where('user_id', $user->id)
            ->where('accion', 'like', 'Consulta de Cobertura%')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('authorization-portal.dashboard', compact('pss', 'metricas', 'ultimasSolicitudes', 'consultasRecientes'));
    }

    private function portalDashboardFarmacia(Pss $pss)
    {
        $user = Auth::user();
        $metricas = [
            'recetas' => PharmacyPrescription::where('pss_id', $pss->id)->count(),
            'dispensaciones' => PharmacyDispensation::where('pss_id', $pss->id)->count(),
            'autorizadas' => PharmacyDispensation::where('pss_id', $pss->id)->where('status', 'Dispensada')->count(),
            'pendientes' => PharmacyDispensation::where('pss_id', $pss->id)->where('status', 'Pendiente de autorización')->count(),
            'monto_reclamado' => AuthorizationClaim::where('pss_id', $pss->id)->where('claim_origin_type', 'pharmacy')->sum('claimed_amount'),
            'monto_aprobado' => AuthorizationClaim::where('pss_id', $pss->id)->where('claim_origin_type', 'pharmacy')->sum('approved_amount'),
        ];

        $ultimasDispensaciones = PharmacyDispensation::where('pss_id', $pss->id)
            ->with('afiliado')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('authorization-portal.dashboard_farmacia', compact('pss', 'metricas', 'ultimasDispensaciones'));
    }

    private function portalDashboardLaboratorio(Pss $pss)
    {
        $user = Auth::user();
        $metricas = [
            'ordenes' => LabOrder::where('pss_id', $pss->id)->count(),
            'pruebas_realizadas' => LabOrderItem::whereHas('order', fn($q) => $q->where('pss_id', $pss->id))->where('status', 'Realizada')->count(),
            'pendientes' => LabOrderItem::whereHas('order', fn($q) => $q->where('pss_id', $pss->id))->where('status', 'Pendiente')->count(),
            'resultados' => LabResult::whereHas('order', fn($q) => $q->where('pss_id', $pss->id))->count(),
            'monto_reclamado' => AuthorizationClaim::where('pss_id', $pss->id)->where('claim_origin_type', 'laboratory')->sum('claimed_amount'),
            'monto_aprobado' => AuthorizationClaim::where('pss_id', $pss->id)->where('claim_origin_type', 'laboratory')->sum('approved_amount'),
        ];

        $ultimasOrdenes = LabOrder::where('pss_id', $pss->id)
            ->with('afiliado')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('authorization-portal.dashboard_laboratory', compact('pss', 'metricas', 'ultimasOrdenes'));
    }

    /**
     * Buscar afiliado por identificación en el Portal PSS.
     */
    public function portalBuscarAfiliado(Request $request)
    {
        $identificacion = $request->get('identificacion');
        $afiliado = null;
        $afiliadoType = 'titular';
        $error = null;

        if ($identificacion) {
            $cleanIdent = preg_replace('/[^0-9]/', '', $identificacion);

            // Buscar en titulares
            $afiliado = Afiliado::where('cedula', $cleanIdent)->orWhere('nss', $cleanIdent)->first();

            // Si no está en titulares, buscar en dependientes
            if (!$afiliado) {
                $afiliado = Dependiente::where('cedula', $cleanIdent)->orWhere('nss', $cleanIdent)->first();
                $afiliadoType = 'dependiente';
            }

            if (!$afiliado) {
                $error = 'No se encontró ningún afiliado activo o registrado con esa identificación en la base de datos de la ARS.';
                Bitacora::registrar('PSS Portal', "Consulta de Cobertura fallida para ID: {$identificacion}");
            } else {
                $nombre = $afiliado->nombre_completo;
                Bitacora::registrar('PSS Portal', "Consulta de Cobertura exitosa para: {$nombre} (Estado: {$afiliado->estado_afiliacion})");
            }
        }

        return view('authorization-portal.buscar', compact('afiliado', 'afiliadoType', 'identificacion', 'error'));
    }

    /**
     * Valida un afiliado por Cédula o Póliza y retorna un JSON con sus detalles para el flujo unificado.
     */
    public function portalValidarJson(Request $request)
    {
        $identificacion = $request->get('identificacion');
        $tipoBusqueda = $request->get('tipo_busqueda'); // 'poliza' or 'cedula'

        if (empty($identificacion)) {
            return response()->json([
                'success' => false,
                'message' => 'El campo de identificación es requerido.'
            ]);
        }

        $cleanIdent = preg_replace('/[^a-zA-Z0-9]/', '', $identificacion);
        $afiliado = null;
        $afiliadoType = 'titular';

        // 1. Hack de demostración
        if ($cleanIdent === '008961897901' || str_contains($identificacion, '00896') || str_contains($identificacion, '18979')) {
            $afiliado = Afiliado::where('estado_afiliacion', 'OK')->first();
            $afiliadoType = 'titular';
        } elseif ($cleanIdent === '22500756154' || str_contains($identificacion, '225-0075615') || str_contains($identificacion, '0075615')) {
            $afiliado = Dependiente::where('estado_afiliacion', 'OK')->first();
            $afiliadoType = 'dependiente';
        }

        // 2. Búsqueda estándar
        if (!$afiliado) {
            if ($tipoBusqueda === 'cedula') {
                $afiliado = Afiliado::where('cedula', $cleanIdent)->orWhere('nss', $cleanIdent)->first();
                if ($afiliado) {
                    $afiliadoType = 'titular';
                } else {
                    $afiliado = Dependiente::where('cedula', $cleanIdent)->orWhere('nss', $cleanIdent)->first();
                    if ($afiliado) {
                        $afiliadoType = 'dependiente';
                    }
                }
            } else {
                $afiliado = Afiliado::where('numero_contrato', 'like', "%{$cleanIdent}%")
                    ->orWhere('numero_contrato', 'like', "%{$identificacion}%")
                    ->orWhere('nss', 'like', "%{$cleanIdent}%")
                    ->orWhere('nui', 'like', "%{$cleanIdent}%")
                    ->first();
                if ($afiliado) {
                    $afiliadoType = 'titular';
                } else {
                    $afiliado = Dependiente::where('nss', 'like', "%{$cleanIdent}%")
                        ->orWhere('nui', 'like', "%{$cleanIdent}%")
                        ->orWhereHas('titular', function($q) use ($cleanIdent, $identificacion) {
                            $q->where('numero_contrato', 'like', "%{$cleanIdent}%")
                              ->orWhere('numero_contrato', 'like', "%{$identificacion}%");
                        })
                        ->first();
                    if ($afiliado) {
                        $afiliadoType = 'dependiente';
                    }
                }
            }
        }

        if (!$afiliado) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún afiliado activo o registrado con esa identificación en la base de datos de la ARS.'
            ]);
        }

        if ($afiliado->estado_afiliacion !== 'OK') {
            return response()->json([
                'success' => false,
                'message' => 'El afiliado se encuentra inactivo o rechazado (' . ($afiliado->motivo_estado ?? 'Sin motivo registrado') . ').'
            ]);
        }

        $poliza = $afiliadoType === 'titular' ? $afiliado->numero_contrato : ($afiliado->titular ? $afiliado->titular->numero_contrato : 'N/A');
        
        $cedulaRaw = $afiliado->cedula;
        $cedulaFormateada = $cedulaRaw;
        if ($cedulaRaw && strlen($cedulaRaw) === 11) {
            $cedulaFormateada = substr($cedulaRaw, 0, 3) . '-' . substr($cedulaRaw, 3, 7) . '-' . substr($cedulaRaw, 10, 1);
        }

        $polizaFormateada = $poliza;
        if ($poliza && strlen($poliza) === 12 && is_numeric($poliza)) {
            $polizaFormateada = substr($poliza, 0, 5) . '-' . substr($poliza, 5, 5) . '-' . substr($poliza, 10, 2);
        }

        $nombres = $afiliado->nombres;
        $apellidos = $afiliadoType === 'titular' ? ($afiliado->primer_apellido . ' ' . $afiliado->segundo_apellido) : $afiliado->apellidos;

        Bitacora::registrar('PSS Portal', "Consulta AJAX de Cobertura exitosa para: " . $afiliado->nombre_completo);

        return response()->json([
            'success' => true,
            'afiliado' => [
                'id' => $afiliado->id,
                'type' => $afiliadoType,
                'tipo' => $afiliadoType === 'titular' ? 'Titular Cotizante' : 'Dependiente Beneficiario',
                'nombres' => strtoupper($nombres),
                'apellidos' => strtoupper($apellidos),
                'cedula' => $cedulaFormateada,
                'poliza' => $polizaFormateada ? strtoupper($polizaFormateada) : 'N/A',
                'sexo' => $afiliado->sexo,
                'edad' => $afiliado->edad . ' años',
                'plan1' => $afiliadoType === 'titular' ? $afiliado->regimen_actual : ($afiliado->titular ? $afiliado->titular->regimen_actual : 'Contributivo'),
                'plan2' => 'PDSS 11.0',
                'estado' => ($afiliado->estado_afiliacion === 'OK' || $afiliado->estado_afiliacion === 'Activo') ? 'Activo' : 'Inactivo',
                'telefono' => $afiliadoType === 'titular' ? $afiliado->telefono : ($afiliado->titular ? $afiliado->titular->telefono : '')
            ]
        ]);
    }

    /**
     * Muestra el formulario para crear una solicitud de autorización.
     */
    /**
     * Muestra el formulario para crear una solicitud de autorización.
     */
    public function portalNuevaAutorizacion(Request $request)
    {
        $afiliadoId = $request->get('afiliado_id');
        $afiliadoType = $request->get('afiliado_type', 'titular');
        
        $afiliado = null;
        if ($afiliadoId) {
            $afiliado = $afiliadoType === 'titular' 
                ? Afiliado::find($afiliadoId) 
                : Dependiente::find($afiliadoId);
        }

        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);

        // 1. Servicios simulados (Demo)
        $simulados = ServicioMedico::all()->map(fn($s) => [
            'id' => 'simulado-' . $s->id,
            'real_id' => $s->id,
            'codigo' => $s->codigo,
            'descripcion' => $s->descripcion,
            'cobertura_base' => $s->cobertura_base ?? 80.00,
            'catalogo_tipo' => 'simulado',
            'grupo' => substr($s->codigo, 0, 3)
        ]);

        // 2. Tarifas simuladas
        $contratoSim = ContratoPss::where('pss_id', $pss->id)->where('estado', 'Activo')->first();
        $tarifasSim = $contratoSim 
            ? TarifaPss::where('contrato_pss_id', $contratoSim->id)->pluck('monto_tarifa', 'servicio_medico_id')->toArray()
            : [];

        // 3. Servicios reales (PDSS)
        $pdss = PdssService::all()->map(fn($s) => [
            'id' => 'pdss-' . $s->id,
            'real_id' => $s->id,
            'codigo' => $s->simon_code,
            'descripcion' => $s->coverage_description,
            'cobertura_base' => 80.00,
            'catalogo_tipo' => 'pdss',
            'grupo' => $s->is_high_cost ? 'ATC' : ($s->is_medicine ? 'MED' : ($s->is_emergency ? 'EME' : ($s->is_surgery ? 'CIR' : ($s->is_hospitalization ? 'HOS' : ($s->coverage_type === 'Consultas' ? 'CON' : ($s->coverage_type === 'Laboratorio' ? 'LAB' : ($s->coverage_type === 'Imágenes' ? 'IMA' : 'PRO')))))))
        ]);

        // 4. Tarifas reales (PDSS)
        $tarifasReal = \App\Models\PssServiceContract::where('pss_id', $pss->id)
            ->where('is_active', true)
            ->pluck('contracted_amount', 'pdss_service_id')
            ->toArray();

        // Mezclar todo
        $servicios = $simulados->concat($pdss);

        // Construir tarifas combinadas
        $tarifas = [];
        foreach ($tarifasSim as $srvId => $monto) {
            $tarifas['simulado-' . $srvId] = $monto;
        }
        foreach ($tarifasReal as $srvId => $monto) {
            $tarifas['pdss-' . $srvId] = $monto;
        }

        $diagnosticos = \App\Models\Catalogo::where('grupo', 'diagnostico')
            ->select('codigo', 'descripcion')
            ->get()
            ->map(fn($d) => $d->codigo . ' - ' . $d->descripcion)
            ->toArray();

        return view('authorization-portal.nueva-autorizacion', compact('afiliado', 'afiliadoType', 'servicios', 'pss', 'tarifas', 'diagnosticos'));
    }

    /**
     * Almacena y procesa automáticamente la autorización médica en el portal PSS.
     */
    public function portalGuardarAutorizacion(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required|integer',
            'afiliado_type' => 'required|string',
            'diagnostico' => 'required|string',
            'telefono' => 'required|string',
            'tipo_servicio' => 'nullable|string',
            'servicios' => 'required|array',
            'valores' => 'required|array',
            'valores.*' => 'numeric|min:0'
        ]);

        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);
        $serviciosIdsKeys = $request->input('servicios', []);
        $valoresReq = $request->input('valores', []);
        $medico = $request->input('medico_solicitante', 'Dr. Prescriptor PSS');

        $hasDoc = $request->hasFile('documento_soporte');
        $uploadedFile = null;
        if ($hasDoc) {
            $uploadedFile = $request->file('documento_soporte');
        }

        $results = [];
        $createdAutIds = [];
        $estados = [];
        $prioridades = [];
        $motivos = [];
        $montoSolicitadoTotal = 0;
        $montoContratadoTotal = 0;
        
        $evaluaciones = [];

        foreach ($serviciosIdsKeys as $index => $srvIdKey) {
            $tipo = 'simulado';
            $srvId = $srvIdKey;
            if (str_contains($srvIdKey, '-')) {
                list($tipo, $srvId) = explode('-', $srvIdKey);
            }

            // Resolver si es servicio del Catálogo PDSS o Simulado
            $servicioPdss = null;
            $servicioSimulado = null;
            if ($tipo === 'pdss') {
                $servicioPdss = PdssService::find($srvId);
            } else {
                $servicioSimulado = ServicioMedico::find($srvId);
            }

            $montoSolicitado = floatval($valoresReq[$index] ?? 0);
            $montoSolicitadoTotal += $montoSolicitado;

            // Crear una instancia temporal de autorización en memoria para evaluar este servicio
            $tempAutData = [
                'afiliado_type' => $request->afiliado_type,
                'afiliado_id' => $request->afiliado_id,
                'pss_id' => $pss->id,
                'medico_solicitante' => $medico,
                'diagnostico' => $request->diagnostico,
                'monto_solicitado' => $montoSolicitado,
                'prioridad' => 'Media',
                'estado' => 'Pendiente',
                'tipo_servicio' => $request->input('tipo_servicio', 'consulta'),
                'fecha_solicitud' => now()
            ];

            if ($servicioPdss) {
                $tempAutData['pdss_service_id'] = $servicioPdss->id;
                $tempAutData['procedimiento'] = $servicioPdss->coverage_description;
                $codigoServ = $servicioPdss->simon_code;
                $descServ = $servicioPdss->coverage_description;
            } else {
                $tempAutData['servicio_medico_id'] = $srvId;
                $tempAutData['procedimiento'] = $servicioSimulado->descripcion ?? 'Servicio Médico';
                $codigoServ = $servicioSimulado->codigo ?? 'SRV-001';
                $descServ = $servicioSimulado->descripcion ?? 'Servicio Médico';
            }

            $tempAut = new Autorizacion($tempAutData);

            // Evaluar reglas de negocio automáticas para este servicio individual
            $eval = AuthorizationEvaluator::evaluar($tempAut, $hasDoc);
            
            $montoContratadoTotal += floatval($eval['monto_contratado']);
            $estados[] = $eval['estado'];
            $prioridades[] = $eval['prioridad'];
            $motivos[] = "{$codigoServ}: {$eval['motivo_estado']}";
            
            $evaluaciones[] = [
                'pdss_service' => $servicioPdss,
                'servicio_simulado' => $servicioSimulado,
                'codigo' => $codigoServ,
                'descripcion' => $descServ,
                'monto_solicitado' => $montoSolicitado,
                'monto_contratado' => floatval($eval['monto_contratado']),
                'estado' => $eval['estado'],
            ];

            $results[] = "{$codigoServ}: {$eval['estado']}";
        }

        // Determinar estado agregado de la solicitud completa
        $estadoFinal = 'Aprobada';
        if (in_array('Auditoría', $estados)) {
            $estadoFinal = 'Auditoría';
        } elseif (in_array('Pendiente Documento', $estados)) {
            $estadoFinal = 'Pendiente Documento';
        } elseif (collect($estados)->every(fn($e) => $e === 'Rechazada')) {
            $estadoFinal = 'Rechazada';
        }

        // Determinar prioridad agregada
        $prioridadFinal = 'Baja';
        if (in_array('Alta', $prioridades)) {
            $prioridadFinal = 'Alta';
        } elseif (in_array('Media', $prioridades)) {
            $prioridadFinal = 'Media';
        }

        // Generar número de autorización único
        $fecha = now()->format('Ymd');
        $autHoy = Autorizacion::whereDate('created_at', now()->toDateString())->count() + 1;
        $numAut = 'AUT-' . $fecha . '-' . str_pad($autHoy, 5, '0', STR_PAD_LEFT);

        // Guardar el registro único de autorización (usando el primero como principal)
        $primeraEval = $evaluaciones[0];
        $primerSrvPdss = $primeraEval['pdss_service'];
        $primerSrvSimulado = $primeraEval['servicio_simulado'];

        $autData = [
            'numero_autorizacion' => $numAut,
            'afiliado_type' => $request->afiliado_type,
            'afiliado_id' => $request->afiliado_id,
            'pss_id' => $pss->id,
            'medico_solicitante' => $medico,
            'diagnostico' => $request->diagnostico,
            'monto_solicitado' => $montoSolicitadoTotal,
            'monto_contratado' => $montoContratadoTotal,
            'prioridad' => $prioridadFinal,
            'estado' => $estadoFinal,
            'motivo_estado' => implode(' | ', $motivos),
            'tipo_servicio' => $request->input('tipo_servicio', 'consulta'),
            'fecha_solicitud' => now()
        ];

        if ($primerSrvPdss) {
            $autData['pdss_service_id'] = $primerSrvPdss->id;
            $autData['procedimiento'] = count($serviciosIdsKeys) > 1 ? (count($serviciosIdsKeys) . " Servicios Médicos") : $primerSrvPdss->coverage_description;
            
            // Snapshots
            $autData['simon_code_snapshot'] = $primerSrvPdss->simon_code;
            $autData['cups_code_snapshot'] = $primerSrvPdss->cups_code;
            $autData['service_description_snapshot'] = $primerSrvPdss->coverage_description;
            $autData['coverage_type_snapshot'] = $primerSrvPdss->coverage_type;
            $autData['pdss_group_snapshot'] = $primerSrvPdss->group->name ?? '';
            $autData['pdss_subgroup_snapshot'] = $primerSrvPdss->subgroup->name ?? '';
            $autData['level_requested'] = 1;
            $autData['coverage_allowed'] = $primerSrvPdss->amount_coverage ?? 'Ilimitada';
            $autData['copay_type_snapshot'] = $primerSrvPdss->copay_type ?? 'No';
            $autData['amount_coverage_snapshot'] = $primerSrvPdss->amount_coverage ?? '';
        } else {
            $autData['servicio_medico_id'] = $primerSrvSimulado->id ?? null;
            $autData['procedimiento'] = count($serviciosIdsKeys) > 1 ? (count($serviciosIdsKeys) . " Servicios Médicos") : ($primerSrvSimulado->descripcion ?? 'Servicio Médico');
        }

        $autorizacion = new Autorizacion($autData);

        if ($primerSrvPdss) {
            $eval = AuthorizationEvaluator::evaluar($autorizacion, $hasDoc);
            $autorizacion->monto_ars = $eval['monto_ars'] ?? 0;
            $autorizacion->monto_afiliado = $eval['monto_afiliado'] ?? 0;
            $autorizacion->copago = $eval['copago'] ?? 0;
            $autorizacion->exceso = $eval['exceso'] ?? 0;
            $autorizacion->monto_no_cubierto = $eval['monto_no_cubierto'] ?? 0;
            $autorizacion->exception_coverage_type = $eval['exception_coverage_type'] ?? 'N/A';

            if ($eval['estado'] === 'Rechazada') {
                $autorizacion->estado = 'Rechazada';
                $autorizacion->motivo_estado = $eval['motivo_estado'];
            }

            if ($autorizacion->estado === 'Aprobada') {
                \App\Services\PdssCoverageEngine::acumularConsumo($autorizacion->afiliado, $primerSrvPdss, $autorizacion->monto_ars);
            }
        }

        if ($autorizacion->estado === 'Aprobada' || $autorizacion->estado === 'Rechazada') {
            $autorizacion->fecha_respuesta = now();
        }

        $autorizacion->save();
        $createdAutIds[] = $autorizacion->id;

        // Guardar detalles de los ítems
        foreach ($evaluaciones as $evalSrv) {
            AutorizacionDetalle::create([
                'autorizacion_id' => $autorizacion->id,
                'codigo' => $evalSrv['codigo'],
                'descripcion' => $evalSrv['descripcion'],
                'cantidad' => 1,
                'monto' => $evalSrv['monto_solicitado'],
                'estado' => ($evalSrv['estado'] === 'Aprobada') ? 'Aprobado' : (($evalSrv['estado'] === 'Rechazada') ? 'Rechazado' : 'Pendiente')
            ]);
        }

        // Si se subió documento soporte, guardar
        if ($hasDoc && $uploadedFile) {
            $fileName = time() . '_soporte_' . $uploadedFile->getClientOriginalName();
            Documento::create([
                'entidad_type' => 'autorizacion',
                'entidad_id' => $autorizacion->id,
                'nombre_archivo' => $fileName,
                'ruta_archivo' => 'documentos/autorizaciones/' . $fileName,
                'tipo_documento' => 'Soporte Médico',
                'fecha_carga' => now()
            ]);
        }

        Bitacora::registrar('PSS Portal', "Solicitada autorización unificada {$numAut} por PSS {$pss->nombre} conteniendo " . count($serviciosIdsKeys) . " servicios. Evaluación agregada: {$estadoFinal}");

        $summaryMessage = "Autorización unificada procesada: " . implode(', ', $results);

        return redirect()->route('pss.solicitudes')
            ->with('success', "Se creó la autorización {$numAut} exitosamente. " . $summaryMessage)
            ->with('created_aut_ids', $createdAutIds);
    }

    /**
     * Listado de solicitudes de autorizaciones enviadas por esta PSS.
     */
    public function portalMisSolicitudes(Request $request)
    {
        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);
        $estado = $request->get('estado');

        $query = Autorizacion::where('pss_id', $pss->id)->with(['servicio', 'servicioPdss']);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $solicitudes = $query->orderBy('fecha_solicitud', 'desc')->paginate(12);

        return view('authorization-portal.solicitudes', compact('solicitudes', 'estado'));
    }

    /**
     * Perfil e información tarifaria de la PSS.
     */
    public function portalPerfil()
    {
        $user = Auth::user();
        $pss = Pss::with(['contratos.tarifas.servicio'])->find($user->pss_id ?? 1);
        $contrato = $pss->contrato_activo;

        return view('authorization-portal.perfil', compact('pss', 'contrato'));
    }

    /**
     * Muestra la vista imprimible de la autorización.
     */
    public function portalImprimirAutorizacion($id)
    {
        $autorizacion = Autorizacion::with(['pss', 'servicio', 'detalles'])->findOrFail($id);
        $afiliado = $autorizacion->afiliado;
        $afiliadoType = $autorizacion->afiliado_type;
        
        $contrato = ContratoPss::where('pss_id', $autorizacion->pss_id)->where('estado', 'Activo')->first();
        $poliza = $afiliadoType === 'titular' ? $afiliado->numero_contrato : ($afiliado->titular ? $afiliado->titular->numero_contrato : 'N/A');
        
        $detallesCalculados = [];
        $totalSolicitado = 0;
        $totalCobertura = 0;
        $totalDiferencia = 0;

        foreach ($autorizacion->detalles as $det) {
            $srv = ServicioMedico::where('codigo', $det->codigo)->first();
            
            // 1. Cobertura Base dinámica por origen
            if ($autorizacion->origen === 'farmacia') {
                $coberturaBase = 70.00;
            } elseif ($autorizacion->origen === 'laboratorio') {
                $coberturaBase = 80.00;
            } else {
                $coberturaBase = $srv ? floatval($srv->cobertura_base) : 80.00;
            }
            
            // 2. Tarifa dinámica (evitar tope procedimiento en medicamentos y laboratorio)
            $montoSolicitado = floatval($det->monto);
            if ($autorizacion->origen === 'farmacia' || $autorizacion->origen === 'laboratorio') {
                $tarifa = $montoSolicitado;
            } else {
                $tarifaRecord = null;
                if ($contrato && $srv) {
                    $tarifaRecord = TarifaPss::where('contrato_pss_id', $contrato->id)
                        ->where('servicio_medico_id', $srv->id)
                        ->first();
                }
                $tarifa = $tarifaRecord ? floatval($tarifaRecord->monto_tarifa) : 1500.00;
            }
            
            // 3. Calcular montos finales de cobertura y copago
            if ($det->estado === 'Rechazado' || $autorizacion->estado === 'Rechazada') {
                $cobertura = 0.00;
                $diferencia = $montoSolicitado;
            } else {
                $cobertura = min($montoSolicitado, $tarifa) * ($coberturaBase / 100);
                $cobertura = round($cobertura, 2);
                $diferencia = round($montoSolicitado - $cobertura, 2);
            }
            
            $detallesCalculados[] = (object)[
                'codigo' => $det->codigo,
                'descripcion' => $det->descripcion,
                'cantidad' => $det->cantidad,
                'porcentaje' => round($coberturaBase),
                'monto' => $montoSolicitado,
                'cobertura' => $cobertura,
                'diferencia' => $diferencia,
                'estado' => $det->estado
            ];

            $totalSolicitado += $montoSolicitado;
            $totalCobertura += $cobertura;
            $totalDiferencia += $diferencia;
        }

        return view('authorization-portal.imprimir', compact(
            'autorizacion',
            'afiliado',
            'afiliadoType',
            'poliza',
            'detallesCalculados',
            'totalSolicitado',
            'totalCobertura',
            'totalDiferencia'
        ));
    }

    /**
     * Muestra la pantalla de búsqueda para cancelar una autorización.
     */
    public function portalCancelarIndex()
    {
        return view('authorization-portal.cancelar');
    }

    /**
     * Busca la autorización por su número para cancelación.
     */
    public function portalCancelarBuscar(Request $request)
    {
        $numero = $request->input('numero_autorizacion');
        $request->validate([
            'numero_autorizacion' => 'required|string'
        ]);

        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);
        $numero = trim($numero);

        $autorizacion = Autorizacion::where('numero_autorizacion', $numero)
            ->where('pss_id', $pss->id)
            ->with(['servicio', 'detalles'])
            ->first();

        if (!$autorizacion) {
            return view('authorization-portal.cancelar')->with('error', "No se encontró ninguna autorización con el número '{$numero}' para esta prestadora.");
        }

        $afiliado = $autorizacion->afiliado;

        return view('authorization-portal.cancelar', compact('autorizacion', 'afiliado', 'numero'));
    }

    /**
     * Procesa la cancelación física de la autorización.
     */
    public function portalCancelarProcesar(Request $request, $id)
    {
        $request->validate([
            'motivo_cancelacion' => 'required|string|min:5'
        ]);

        $user = Auth::user();
        $autorizacion = Autorizacion::where('id', $id)
            ->where('pss_id', $user->pss_id ?? 1)
            ->firstOrFail();

        if (in_array($autorizacion->estado, ['Cancelada', 'Rechazada'])) {
            return redirect()->route('pss.autorizaciones.cancelar')
                ->with('error', "La autorización {$autorizacion->numero_autorizacion} no puede ser cancelada porque su estado actual es: {$autorizacion->estado}.");
        }

        $motivo = "Cancelada por PSS: " . $request->input('motivo_cancelacion');

        $autorizacion->update([
            'estado' => 'Cancelada',
            'motivo_estado' => $motivo,
            'fecha_respuesta' => now(),
            'usuario_responsable_id' => $user->id
        ]);

        // Actualizar detalles
        AutorizacionDetalle::where('autorizacion_id', $autorizacion->id)->update([
            'estado' => 'Cancelado'
        ]);

        Bitacora::registrar('PSS Portal', "Cancelada autorización {$autorizacion->numero_autorizacion} por prestador {$user->name}. Motivo: {$request->input('motivo_cancelacion')}");

        return redirect()->route('pss.autorizaciones.cancelar')
            ->with('success', "La autorización {$autorizacion->numero_autorizacion} ha sido cancelada exitosamente.");
    }

    /**
     * Marca una autorización como servicio prestado.
     */
    public function portalPrestarServicio($id)
    {
        $user = Auth::user();
        $autorizacion = Autorizacion::where('id', $id)
            ->where('pss_id', $user->pss_id ?? 1)
            ->firstOrFail();

        if ($autorizacion->estado !== 'Aprobada') {
            return redirect()->back()->with('error', 'El servicio solo puede marcarse como prestado para autorizaciones en estado Aprobada.');
        }

        DB::transaction(function() use ($autorizacion) {
            $autorizacion->update(['estado' => 'Servicio prestado']);

            AuthorizationTimelineEvent::registrar(
                $autorizacion->id,
                'SERVICE_RENDERED',
                'Servicio Prestado',
                'El prestador ha marcado la asistencia o procedimiento como realizado. Listo para facturación/reclamación.',
                'Aprobada',
                'Servicio prestado'
            );
        });

        return redirect()->back()->with('success', 'El estado de la autorización ha sido actualizado a: Servicio prestado.');
    }

    /**
     * Formulario para que la PSS reclame / facture una autorización.
     */
    public function portalFormReclamar($id)
    {
        $user = Auth::user();
        $autorizacion = Autorizacion::where('id', $id)
            ->where('pss_id', $user->pss_id ?? 1)
            ->firstOrFail();

        if (!in_array($autorizacion->estado, ['Aprobada', 'Servicio prestado'])) {
            return redirect()->route('pss.solicitudes')
                ->with('error', "La autorización {$autorizacion->numero_autorizacion} no se puede reclamar en su estado actual ({$autorizacion->estado}).");
        }

        $afiliado = $autorizacion->afiliado;
        return view('authorization-portal.reclamar', compact('autorizacion', 'afiliado'));
    }

    /**
     * Guarda la reclamación de la PSS.
     */
    public function portalGuardarReclamar(Request $request, $id)
    {
        $user = Auth::user();
        $autorizacion = Autorizacion::where('id', $id)
            ->where('pss_id', $user->pss_id ?? 1)
            ->firstOrFail();

        if (!in_array($autorizacion->estado, ['Aprobada', 'Servicio prestado'])) {
            return redirect()->route('pss.solicitudes')
                ->with('error', "La autorización no es elegible para facturación.");
        }

        $request->validate([
            'invoice_number' => 'required|string',
            'ncf' => 'required|string',
            'service_date' => 'required|date',
            'claimed_amount' => 'required|numeric|min:0.01',
            'documento_factura' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ]);

        $claimedAmount = floatval($request->claimed_amount);
        $authorizedAmount = floatval($autorizacion->monto_contratado ?: $autorizacion->monto_solicitado);

        // Regla: Validar plazo máximo de 90 días calendario desde la prestación
        $serviceDate = \Illuminate\Support\Carbon::parse($request->service_date);
        $daysDiff = $serviceDate->diffInDays(now());

        DB::transaction(function() use ($autorizacion, $request, $claimedAmount, $authorizedAmount, $user, $daysDiff) {
            $year = now()->year;
            $countClaims = AuthorizationClaim::whereYear('created_at', $year)->count();
            $claimNum = 'REC-' . $year . '-' . str_pad($countClaims + 1, 6, '0', STR_PAD_LEFT);

            // Regla de desvío automático a auditoría si el monto reclamado excede el autorizado o si está fuera de plazo
            $status = 'Reclamación recibida';
            if ($daysDiff > 90) {
                $status = 'Fuera de plazo'; // Enviado a revisión administrativa por extemporáneo
            } elseif ($claimedAmount > $authorizedAmount) {
                $status = 'En auditoría de reclamación';
            }

            // 1. Crear Reclamación
            $claim = AuthorizationClaim::create([
                'claim_number' => $claimNum,
                'authorization_id' => $autorizacion->id,
                'pss_id' => $autorizacion->pss_id,
                'afiliado_id' => $autorizacion->afiliado_id,
                'invoice_number' => $request->invoice_number,
                'ncf' => $request->ncf,
                'service_date' => $request->service_date,
                'received_at' => now(),
                'claimed_amount' => $claimedAmount,
                'authorized_amount' => $authorizedAmount,
                'approved_amount' => 0,
                'objected_amount' => 0,
                'status' => $status,
                'submitted_by' => $user->id,
                'observations' => $daysDiff > 90 ? "Sometida fuera de plazo ({$daysDiff} días de diferencia)." : null,
            ]);

            // Registrar reserva contable por reclamación recibida (por devengo)
            \App\Services\AccountingPostingService::registrarReclamacionRecibida($claim);

            // 2. Adjuntar archivo
            if ($request->hasFile('documento_factura')) {
                $file = $request->file('documento_factura');
                $path = $file->store('claims', 'public');
                ClaimDocument::create([
                    'claim_id' => $claim->id,
                    'document_type' => 'Factura / NCF',
                    'file_path' => $path,
                    'uploaded_by' => $user->id,
                    'uploaded_at' => now()
                ]);
            }

            // 3. Actualizar autorización
            $autorizacion->update(['estado' => $status]);

            // 4. Registrar Timeline
            AuthorizationTimelineEvent::registrar(
                $autorizacion->id,
                'CLAIM_SUBMITTED',
                'Reclamación Sometida',
                "Factura {$request->invoice_number} (NCF: {$request->ncf}) enviada por un monto de DOP {$claimedAmount}. Estado: {$status}.",
                'Servicio prestado',
                $status,
                ['claim_id' => $claim->id]
            );

            Bitacora::registrar('PSS Portal', "Reclamación {$claimNum} creada por PSS {$user->name} para autorización {$autorizacion->numero_autorizacion}.");
        });

        return redirect()->route('pss.reclamaciones.index')
            ->with('success', 'La reclamación se ha presentado con éxito.');
    }

    /**
     * Listado de reclamaciones de la PSS.
     */
    public function portalReclamacionesIndex()
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $accessType = session('active_access_type', 'medical_center');

        $reclamaciones = AuthorizationClaim::where('pss_id', $pssId)
            ->where('claim_origin_type', $accessType)
            ->with(['authorization'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('authorization-portal.reclamaciones_index', compact('reclamaciones'));
    }

    /**
     * Detalle de reclamación en el portal PSS.
     */
    public function portalReclamacionShow($id)
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);

        $reclamacion = AuthorizationClaim::where('id', $id)
            ->where('pss_id', $pssId)
            ->with(['authorization', 'audits', 'payables', 'glosses'])
            ->firstOrFail();

        return view('authorization-portal.reclamacion_show', compact('reclamacion'));
    }

    /**
     * Cuentas y pagos liquidados para la PSS.
     */
    public function portalPagosIndex()
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $accessType = session('active_access_type', 'medical_center');

        $cxpList = AccountPayable::where('pss_id', $pssId)
            ->whereHas('claim', function($q) use ($accessType) {
                $q->where('claim_origin_type', $accessType);
            })
            ->with(['claim', 'authorization'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('authorization-portal.pagos_index', compact('cxpList'));
    }

    /**
     * Permite a la PSS responder a una glosa médica o administrativa.
     */
    public function portalResponderGlosa(Request $request, $id, $glosaId)
    {
        $request->validate([
            'pss_response' => 'required|string|min:10',
        ]);

        $glosa = \App\Models\ClaimGlosa::findOrFail($glosaId);
        $glosa->update([
            'status' => 'En conciliación',
            'pss_response' => $request->input('pss_response')
        ]);

        // Registrar evento en timeline
        \App\Models\AuthorizationTimelineEvent::registrar(
            $glosa->claim->authorization_id,
            'GLOSA_RESPONDED',
            'Glosa Respondida por PSS',
            "La PSS ingresó una justificación de glosa: " . $request->input('pss_response'),
            $glosa->claim->status,
            $glosa->claim->status
        );

        return redirect()->back()->with('success', 'Tu descargo y respuesta a la objeción han sido guardados. El estatus de la glosa ha cambiado a En conciliación.');
    }

    /**
     * Permite al usuario alternar entre perfiles activos.
     */
    public function portalSwitchAccessType(Request $request)
    {
        $accessType = $request->input('access_type');
        $user = Auth::user();

        if ($user->role === 'Usuario PSS' && $accessType === 'medical_center') {
            session([
                'active_access_type' => 'medical_center',
                'active_pss_id' => $user->pss_id ?? 1
            ]);
            return redirect()->route('pss.dashboard')->with('success', 'Perfil cambiado a Centro Médico exitosamente.');
        }

        $exists = PssUser::where('user_id', $user->id)
            ->where('access_type', $accessType)
            ->where('status', 'activo')
            ->first();

        if ($exists) {
            session([
                'active_access_type' => $accessType,
                'active_pss_id' => $exists->pss_id
            ]);
            return redirect()->route('pss.dashboard')->with('success', "Perfil cambiado a {$accessType} exitosamente.");
        }

        return redirect()->back()->with('error', 'No tienes acceso a este perfil.');
    }

    /**
     * Muestra la pantalla para crear una nueva dispensación de farmacia.
     */
    public function portalNuevaDispensacion(Request $request)
    {
        $afiliadoId = $request->get('afiliado_id');
        $afiliadoType = $request->get('afiliado_type', 'titular');
        $afiliado = null;
        if ($afiliadoId) {
            $afiliado = $afiliadoType === 'titular' ? Afiliado::find($afiliadoId) : Dependiente::find($afiliadoId);
        }
        
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $pss = Pss::find($pssId);

        // Cargar medicamentos (Servicios Médicos o PDSS de tipo medicamentos/ambulatorio)
        $medicamentos = ServicioMedico::all()->map(fn($s) => [
            'id' => $s->id,
            'codigo' => $s->codigo,
            'descripcion' => $s->descripcion,
            'precio' => $s->cobertura_base ?? 350.00,
        ])->toArray();

        // Límite anual para medicamentos ambulatorios: DOP 8,000.00
        $limiteAnual = 8000.00;
        $consumidoAnual = 0.00;
        if ($afiliado) {
            $consumidoAnual = PharmacyDispensation::where('afiliado_id', $afiliado->id)
                ->whereYear('dispensed_at', now()->year)
                ->where('status', 'Dispensada')
                ->sum('ars_amount');
        }
        $disponibleAnual = max(0.00, $limiteAnual - $consumidoAnual);

        // Diagnósticos
        $diagnosticos = \App\Models\Catalogo::where('grupo', 'diagnostico')
            ->select('codigo', 'descripcion')
            ->get()
            ->map(fn($d) => $d->codigo . ' - ' . $d->descripcion)
            ->toArray();

        return view('authorization-portal.nueva_dispensacion', compact('afiliado', 'afiliadoType', 'pss', 'medicamentos', 'disponibleAnual', 'limiteAnual', 'diagnosticos'));
    }

    /**
     * Guarda la dispensación de farmacia.
     */
    public function portalGuardarDispensacion(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required',
            'afiliado_type' => 'required',
            'doctor_name' => 'required|string',
            'doctor_exequatur' => 'required|string',
            'prescription_date' => 'required|date',
            'diagnostico' => 'required|string',
            'medicamentos' => 'required|array',
            'cantidades' => 'required|array',
            'precios' => 'required|array',
        ]);

        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $afiliadoId = $request->afiliado_id;
        $afiliadoType = $request->afiliado_type;

        $items = [];
        $meds = $request->input('medicamentos', []);
        $cants = $request->input('cantidades', []);
        $precios = $request->input('precios', []);

        foreach ($meds as $idx => $medId) {
            $qty = intval($cants[$idx] ?? 1);
            $price = floatval($precios[$idx] ?? 0);
            
            $medModel = ServicioMedico::find($medId);
            $items[] = [
                'id' => $medId,
                'medicine_code' => $medModel ? $medModel->codigo : 'MED-' . $medId,
                'medicine_name' => $medModel ? $medModel->descripcion : 'Medicamento ' . $medId,
                'requested_amount' => $price,
                'quantity' => $qty
            ];
        }

        // Evaluar reglas del motor
        $eval = PssAuthorizationFlowService::evaluate([
            'pss_id' => $pssId,
            'pss_type' => 'pharmacy',
            'afiliado_id' => $afiliadoId,
            'afiliado_type' => $afiliadoType,
            'service_items' => $items
        ]);

        $authId = null;

        DB::transaction(function() use ($request, $pssId, $afiliadoId, $afiliadoType, $items, $eval, $user, &$authId) {
            // 1. Crear Receta
            $prescCount = PharmacyPrescription::count();
            $prescription = PharmacyPrescription::create([
                'pss_id' => $pssId,
                'afiliado_id' => $afiliadoId,
                'prescription_number' => 'RX-' . now()->year . '-' . str_pad($prescCount + 1, 6, '0', STR_PAD_LEFT),
                'doctor_name' => $request->doctor_name,
                'doctor_exequatur' => $request->doctor_exequatur,
                'specialty' => $request->specialty ?? 'Médico General',
                'diagnosis' => $request->diagnostico,
                'prescription_date' => $request->prescription_date,
                'status' => $eval['estado_sugerido'] === 'Aprobada' ? 'Validada' : 'Pendiente',
                'created_by' => $user->id,
            ]);

            if ($request->hasFile('documento_receta')) {
                $path = $request->file('documento_receta')->store('prescriptions', 'public');
                $prescription->update(['document_path' => $path]);
            }

            // 2. Crear autorización ARS si es necesario
            if ($eval['requiere_autorizacion'] || $eval['estado_sugerido'] === 'Aprobada') {
                $authCount = Autorizacion::count();
                $auth = Autorizacion::create([
                    'numero_autorizacion' => 'AUT-RX-' . now()->year . '-' . str_pad($authCount + 1, 6, '0', STR_PAD_LEFT),
                    'afiliado_id' => $afiliadoId,
                    'afiliado_type' => $afiliadoType,
                    'pss_id' => $pssId,
                    'diagnostico' => $request->diagnostico,
                    'medico_solicitante' => $request->doctor_name,
                    'telefono_contacto' => $request->telefono ?? '809-555-0199',
                    'procedimiento' => 'Dispensación RX: ' . count($items) . ' medicamento(s)',
                    'monto_solicitado' => $eval['monto_solicitado'],
                    'monto_contratado' => $eval['monto_contratado'],
                    'monto_aprobado' => $eval['monto_autorizado'],
                    'copago_afiliado' => $eval['copago'],
                    'monto_no_cubierto' => $eval['monto_no_cubierto'],
                    'estado' => $eval['estado_sugerido'],
                    'origen' => 'farmacia',
                    'creado_por' => $user->id,
                ]);
                $authId = $auth->id;

                foreach ($items as $item) {
                    AutorizacionDetalle::create([
                        'autorizacion_id' => $auth->id,
                        'codigo' => $item['medicine_code'],
                        'descripcion' => $item['medicine_name'],
                        'cantidad' => $item['quantity'],
                        'monto' => $item['requested_amount'] * $item['quantity'],
                        'estado' => $eval['estado_sugerido'] === 'Aprobada' ? 'Aprobado' : 'Pendiente',
                    ]);
                }
            }

            // 3. Crear Dispensación
            $dispCount = PharmacyDispensation::count();
            $dispensation = PharmacyDispensation::create([
                'prescription_id' => $prescription->id,
                'pss_id' => $pssId,
                'afiliado_id' => $afiliadoId,
                'authorization_id' => $authId,
                'dispensation_number' => 'DISP-' . now()->year . '-' . str_pad($dispCount + 1, 6, '0', STR_PAD_LEFT),
                'dispensed_at' => now(),
                'total_amount' => $eval['monto_solicitado'],
                'ars_amount' => $eval['monto_autorizado'],
                'affiliate_copay_amount' => $eval['copago'],
                'non_covered_amount' => $eval['monto_no_cubierto'],
                'status' => $eval['estado_sugerido'] === 'Aprobada' ? 'Dispensada' : 'En auditoría',
                'created_by' => $user->id,
            ]);

            foreach ($items as $item) {
                PharmacyDispensationItem::create([
                    'dispensation_id' => $dispensation->id,
                    'pdss_service_id' => null,
                    'medicine_code' => $item['medicine_code'],
                    'medicine_name' => $item['medicine_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['requested_amount'],
                    'total_price' => $item['requested_amount'] * $item['quantity'],
                    'ars_covered_amount' => $eval['estado_sugerido'] === 'Aprobada' ? ($item['requested_amount'] * $item['quantity'] * 0.70) : 0,
                    'copay_amount' => $eval['estado_sugerido'] === 'Aprobada' ? ($item['requested_amount'] * $item['quantity'] * 0.30) : 0,
                    'requires_authorization' => $eval['requiere_autorizacion'],
                    'status' => 'Activo',
                ]);
            }

            // 4. Crear Reclamación automática si la dispensación fue exitosa
            if ($eval['estado_sugerido'] === 'Aprobada') {
                $claimCount = AuthorizationClaim::count();
                $claim = AuthorizationClaim::create([
                    'claim_number' => 'REC-RX-' . now()->year . '-' . str_pad($claimCount + 1, 6, '0', STR_PAD_LEFT),
                    'authorization_id' => $authId,
                    'pss_id' => $pssId,
                    'afiliado_id' => $afiliadoId,
                    'invoice_number' => 'INV-' . $dispensation->dispensation_number,
                    'ncf' => 'B01' . str_pad($claimCount + 1, 8, '0', STR_PAD_LEFT),
                    'service_date' => now(),
                    'received_at' => now(),
                    'claimed_amount' => $eval['monto_solicitado'],
                    'authorized_amount' => $eval['monto_autorizado'],
                    'approved_amount' => $eval['monto_autorizado'],
                    'status' => 'Pagada',
                    'claim_origin_type' => 'pharmacy',
                    'submitted_by' => $user->id,
                ]);

                \App\Services\AccountingPostingService::registrarReclamacionRecibida($claim);

                $cxpCount = AccountPayable::count();
                AccountPayable::create([
                    'payable_number' => 'CXP-RX-' . now()->year . '-' . str_pad($cxpCount + 1, 6, '0', STR_PAD_LEFT),
                    'claim_id' => $claim->id,
                    'authorization_id' => $authId,
                    'pss_id' => $pssId,
                    'amount' => $eval['monto_autorizado'],
                    'net_amount' => $eval['monto_autorizado'],
                    'status' => 'Generada',
                    'generated_by' => $user->id,
                    'generated_at' => now(),
                ]);
            }
        });

        return redirect()->route('pss.solicitudes')
            ->with('success', 'Receta y dispensación registradas con éxito en el portal.')
            ->with('created_aut_ids', $authId ? [$authId] : []);
    }

    /**
     * Listado de recetas / prescripciones médicas de farmacia.
     */
    public function portalRecetasIndex(Request $request)
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);

        $recetas = PharmacyPrescription::where('pss_id', $pssId)
            ->with(['afiliado'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('authorization-portal.recetas', compact('recetas'));
    }

    /**
     * Muestra la pantalla para crear una nueva orden de laboratorio.
     */
    public function portalNuevaOrdenLab(Request $request)
    {
        $afiliadoId = $request->get('afiliado_id');
        $afiliadoType = $request->get('afiliado_type', 'titular');
        $afiliado = null;
        if ($afiliadoId) {
            $afiliado = $afiliadoType === 'titular' ? Afiliado::find($afiliadoId) : Dependiente::find($afiliadoId);
        }
        
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $pss = Pss::find($pssId);

        $diagnosticos = \App\Models\Catalogo::where('grupo', 'diagnostico')
            ->select('codigo', 'descripcion')
            ->get()
            ->map(fn($d) => $d->codigo . ' - ' . $d->descripcion)
            ->toArray();

        $pruebas = ServicioMedico::all()->map(fn($s) => [
            'id' => $s->id,
            'codigo' => $s->codigo,
            'descripcion' => $s->descripcion,
            'precio' => $s->cobertura_base ?? 1200.00,
        ])->toArray();

        return view('authorization-portal.nueva_orden_lab', compact('afiliado', 'afiliadoType', 'pss', 'pruebas', 'diagnosticos'));
    }

    /**
     * Guarda la orden de laboratorio.
     */
    public function portalGuardarOrdenLab(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required',
            'afiliado_type' => 'required',
            'doctor_name' => 'required|string',
            'doctor_exequatur' => 'required|string',
            'order_date' => 'required|date',
            'diagnostico' => 'required|string',
            'pruebas' => 'required|array',
            'precios' => 'required|array',
        ]);

        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);
        $afiliadoId = $request->afiliado_id;
        $afiliadoType = $request->afiliado_type;

        $items = [];
        $tests = $request->input('pruebas', []);
        $precios = $request->input('precios', []);

        foreach ($tests as $idx => $testId) {
            $price = floatval($precios[$idx] ?? 0);
            $service = ServicioMedico::find($testId);
            $items[] = [
                'id' => $testId,
                'simon_code_snapshot' => $service ? $service->codigo : 'SIM-' . $testId,
                'test_name' => $service ? $service->descripcion : 'Prueba ' . $testId,
                'requested_amount' => $price,
            ];
        }

        $eval = PssAuthorizationFlowService::evaluate([
            'pss_id' => $pssId,
            'pss_type' => 'laboratory',
            'afiliado_id' => $afiliadoId,
            'afiliado_type' => $afiliadoType,
            'service_items' => $items
        ]);

        $authId = null;

        DB::transaction(function() use ($request, $pssId, $afiliadoId, $afiliadoType, $items, $eval, $user, &$authId) {
            // 1. Crear Orden de Laboratorio
            $orderCount = LabOrder::count();
            $order = LabOrder::create([
                'pss_id' => $pssId,
                'afiliado_id' => $afiliadoId,
                'order_number' => 'ORD-' . now()->year . '-' . str_pad($orderCount + 1, 6, '0', STR_PAD_LEFT),
                'doctor_name' => $request->doctor_name,
                'doctor_exequatur' => $request->doctor_exequatur,
                'specialty' => $request->specialty ?? 'Patólogo Clínico',
                'diagnosis' => $request->diagnostico,
                'order_date' => $request->order_date,
                'status' => $eval['estado_sugerido'] === 'Aprobada' ? 'Orden recibida' : 'Pendiente',
                'created_by' => $user->id,
            ]);

            if ($request->hasFile('documento_orden')) {
                $path = $request->file('documento_orden')->store('lab_orders', 'public');
                $order->update(['document_path' => $path]);
            }

            // 2. Crear autorización ARS
            if ($eval['requiere_autorizacion'] || $eval['estado_sugerido'] === 'Aprobada') {
                $authCount = Autorizacion::count();
                $auth = Autorizacion::create([
                    'numero_autorizacion' => 'AUT-LAB-' . now()->year . '-' . str_pad($authCount + 1, 6, '0', STR_PAD_LEFT),
                    'afiliado_id' => $afiliadoId,
                    'afiliado_type' => $afiliadoType,
                    'pss_id' => $pssId,
                    'diagnostico' => $request->diagnostico,
                    'medico_solicitante' => $request->doctor_name,
                    'telefono_contacto' => $request->telefono ?? '809-555-0199',
                    'procedimiento' => 'Orden Lab: ' . count($items) . ' prueba(s)',
                    'monto_solicitado' => $eval['monto_solicitado'],
                    'monto_contratado' => $eval['monto_contratado'],
                    'monto_aprobado' => $eval['monto_autorizado'],
                    'copago_afiliado' => $eval['copago'],
                    'monto_no_cubierto' => $eval['monto_no_cubierto'],
                    'estado' => $eval['estado_sugerido'],
                    'origen' => 'laboratorio',
                    'creado_por' => $user->id,
                ]);
                $authId = $auth->id;

                foreach ($items as $item) {
                    AutorizacionDetalle::create([
                        'autorizacion_id' => $auth->id,
                        'codigo' => $item['simon_code_snapshot'],
                        'descripcion' => $item['test_name'],
                        'cantidad' => 1,
                        'monto' => $item['requested_amount'],
                        'estado' => $eval['estado_sugerido'] === 'Aprobada' ? 'Aprobado' : 'Pendiente',
                    ]);
                }
            }

            // 3. Crear Items
            foreach ($items as $item) {
                LabOrderItem::create([
                    'lab_order_id' => $order->id,
                    'pdss_service_id' => null,
                    'simon_code_snapshot' => $item['simon_code_snapshot'],
                    'cups_code_snapshot' => null,
                    'test_name' => $item['test_name'],
                    'coverage_type' => 'laboratorio',
                    'contracted_amount' => $eval['monto_contratado'] / count($items),
                    'requested_amount' => $item['requested_amount'],
                    'authorized_amount' => $eval['estado_sugerido'] === 'Aprobada' ? ($item['requested_amount'] * 0.80) : 0,
                    'requires_authorization' => $eval['requiere_autorizacion'],
                    'requires_audit' => $eval['requiere_auditoria'],
                    'status' => $eval['estado_sugerido'] === 'Aprobada' ? 'Aprobada' : 'Pendiente',
                ]);
            }

            // 4. Crear Reclamación automática
            if ($eval['estado_sugerido'] === 'Aprobada') {
                $claimCount = AuthorizationClaim::count();
                $claim = AuthorizationClaim::create([
                    'claim_number' => 'REC-LAB-' . now()->year . '-' . str_pad($claimCount + 1, 6, '0', STR_PAD_LEFT),
                    'authorization_id' => $authId,
                    'pss_id' => $pssId,
                    'afiliado_id' => $afiliadoId,
                    'invoice_number' => 'INV-' . $order->order_number,
                    'ncf' => 'B01' . str_pad($claimCount + 1, 8, '0', STR_PAD_LEFT),
                    'service_date' => now(),
                    'received_at' => now(),
                    'claimed_amount' => $eval['monto_solicitado'],
                    'authorized_amount' => $eval['monto_autorizado'],
                    'approved_amount' => $eval['monto_autorizado'],
                    'status' => 'Pagada',
                    'claim_origin_type' => 'laboratory',
                    'submitted_by' => $user->id,
                ]);

                \App\Services\AccountingPostingService::registrarReclamacionRecibida($claim);

                $cxpCount = AccountPayable::count();
                AccountPayable::create([
                    'payable_number' => 'CXP-LAB-' . now()->year . '-' . str_pad($cxpCount + 1, 6, '0', STR_PAD_LEFT),
                    'claim_id' => $claim->id,
                    'authorization_id' => $authId,
                    'pss_id' => $pssId,
                    'amount' => $eval['monto_autorizado'],
                    'net_amount' => $eval['monto_autorizado'],
                    'status' => 'Generada',
                    'generated_by' => $user->id,
                    'generated_at' => now(),
                ]);
            }
        });

        return redirect()->route('pss.solicitudes')
            ->with('success', 'Orden de laboratorio y pruebas registradas con éxito en el portal.')
            ->with('created_aut_ids', $authId ? [$authId] : []);
    }

    /**
     * Listado de órdenes de laboratorio.
     */
    public function portalOrdenesIndex(Request $request)
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);

        $ordenes = LabOrder::where('pss_id', $pssId)
            ->with(['afiliado', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('authorization-portal.ordenes', compact('ordenes'));
    }

    /**
     * Listado de carga de resultados de laboratorio.
     */
    public function portalResultadosIndex(Request $request)
    {
        $user = Auth::user();
        $pssId = session('active_pss_id', $user->pss_id ?? 1);

        $ordenes = LabOrder::where('pss_id', $pssId)
            ->with(['afiliado', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('authorization-portal.resultados', compact('ordenes'));
    }

    /**
     * Sube un resultado de laboratorio asociado a una orden.
     */
    public function portalSubirResultado(Request $request)
    {
        $request->validate([
            'lab_order_id' => 'required|exists:lab_orders,id',
            'lab_order_item_id' => 'required|exists:lab_order_items,id',
            'resultado_archivo' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        $user = Auth::user();
        $path = $request->file('resultado_archivo')->store('lab_results', 'public');
        
        $resCount = LabResult::count();
        
        LabResult::create([
            'lab_order_id' => $request->lab_order_id,
            'lab_order_item_id' => $request->lab_order_item_id,
            'result_number' => 'RES-' . now()->year . '-' . str_pad($resCount + 1, 6, '0', STR_PAD_LEFT),
            'result_status' => 'Resultado disponible',
            'result_file_path' => $path,
            'result_date' => now(),
            'uploaded_by' => $user->id,
            'observations' => $request->observaciones,
        ]);

        // Actualizar estatus
        LabOrderItem::find($request->lab_order_item_id)->update(['status' => 'Realizada']);

        return redirect()->back()->with('success', 'Resultado de prueba cargado con éxito.');
    }
}
