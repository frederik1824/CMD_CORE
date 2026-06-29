<?php

namespace App\Http\Controllers;

use App\Models\Autorizacion;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\PdssService;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use App\Models\AuthorizationOverride;
use App\Models\AuthorizationTimelineEvent;
use App\Models\AutorizacionComentario;
use App\Models\AutorizacionDetalle;
use App\Models\Documento;
use App\Services\AuthorizationEvaluator;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AutorizacionCoreController extends Controller
{
    /**
     * Dashboard analítico de autorizaciones del Core ARS.
     */
    public function dashboard()
    {
        $hoy = Carbon::today();
        
        $autorizacionesHoy = Autorizacion::whereDate('fecha_solicitud', $hoy)->count();
        $aprobadasAuto = Autorizacion::whereDate('fecha_solicitud', $hoy)->where('estado', 'Aprobada')->whereNull('internal_notes')->count();
        $aprobadasManual = Autorizacion::whereDate('fecha_solicitud', $hoy)->where('estado', 'Aprobada')->whereNotNull('internal_notes')->count();
        $enAuditoria = Autorizacion::whereIn('estado', ['Auditoría', 'Pendiente'])->count();
        $pendientesDoc = Autorizacion::where('estado', 'Pendiente Documento')->count();
        $rechazadas = Autorizacion::whereDate('fecha_solicitud', $hoy)->where('estado', 'Rechazada')->count();
        
        // Alertas de contratos y tarifas
        $pssSinContratoCount = Autorizacion::where('estado', 'Rechazada')
            ->where('motivo_estado', 'like', '%contrato%')
            ->distinct('pss_id')
            ->count('pss_id');

        $overridesHoy = AuthorizationOverride::whereDate('created_at', $hoy)->count();

        return view('ars.autorizaciones_medicas.dashboard', compact(
            'autorizacionesHoy', 'aprobadasAuto', 'aprobadasManual', 
            'enAuditoria', 'pendientesDoc', 'rechazadas', 'pssSinContratoCount', 'overridesHoy'
        ));
    }

    /**
     * Bandeja general de búsqueda y filtrado de autorizaciones.
     */
    public function index(Request $request)
    {
        $numero = $request->get('numero_autorizacion');
        $estado = $request->get('estado');
        $canal = $request->get('channel');
        $origen = $request->get('origin');

        $query = Autorizacion::with(['pss']);

        if ($numero) $query->where('numero_autorizacion', 'like', "%{$numero}%");
        if ($estado) $query->where('estado', $estado);
        if ($canal) $query->where('channel', $canal);
        if ($origen) $query->where('origin', $origen);

        $autorizaciones = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);

        return view('ars.autorizaciones_medicas.bandeja', compact('autorizaciones', 'numero', 'estado', 'canal', 'origen'));
    }

    /**
     * Formulario de nueva autorización en pasos.
     */
    public function create()
    {
        // Ya no cargamos miles de afiliados, PSS o servicios médicos en Blade,
        // la búsqueda predictiva se realiza en tiempo real vía AJAX/fetch.
        return view('ars.autorizaciones_medicas.nueva');
    }

    /**
     * Guarda la autorización ejecutando el motor de reglas de contratos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id',
            'pss_id' => 'required|exists:pss,id',
            'pdss_service_id' => 'required|exists:pdss_services,id',
            'channel' => 'required|in:llamada,correo,whatsapp,presencial,interno',
            'medico_solicitante' => 'required|string',
            'diagnostico' => 'required|string',
            'monto_solicitado' => 'required|numeric|min:0',
            'prioridad' => 'required|in:Alta,Media,Baja'
        ]);

        $userId = Auth::id() ?? 1;

        // Intentar resolver servicio_medico_id
        $servicioMedico = DB::table('servicios_medicos')->first();
        $servicioMedicoId = $servicioMedico ? $servicioMedico->id : 1;

        // Generar número correlativo
        $fechaHoy = Carbon::now()->format('Ymd');
        $autHoyCount = Autorizacion::whereDate('fecha_solicitud', Carbon::today())->count() + 1;
        $numAut = 'AUT-' . $fechaHoy . '-' . str_pad($autHoyCount, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $servicios = [];
            if ($request->filled('servicios_json')) {
                $servicios = json_decode($request->servicios_json, true);
            }

            if (count($servicios) > 0) {
                // Totalizadores y estados
                $totalSolicitado = 0.00;
                $totalContratado = 0.00;
                $totalCopago = 0.00;
                $totalExceso = 0.00;
                $totalArs = 0.00;

                $algunoEnAuditoria = false;
                $algunoRechazado = false;
                $algunoAprobado = false;
                $motivosEstados = [];
                $evaluaciones = [];

                // 1. Evaluar cada servicio de forma individual
                foreach ($servicios as $s) {
                    $itemAut = new Autorizacion([
                        'afiliado_type' => 'titular',
                        'afiliado_id' => $request->afiliado_id,
                        'pss_id' => $request->pss_id,
                        'pdss_service_id' => $s['id'],
                        'monto_solicitado' => $s['monto_solicitado'],
                        'fecha_solicitud' => now(),
                        'tipo_servicio' => 'consulta'
                    ]);

                    $evalItem = AuthorizationEvaluator::evaluar($itemAut, $request->hasFile('documento_soporte'));

                    $totalSolicitado += (float)$s['monto_solicitado'];
                    $totalContratado += (float)($evalItem['monto_contratado'] ?? 0.00);
                    $totalCopago += (float)($evalItem['copago'] ?? 0.00);
                    $totalExceso += (float)($evalItem['exceso'] ?? 0.00);
                    $totalArs += (float)($evalItem['monto_ars'] ?? 0.00);

                    if (($evalItem['estado'] ?? '') === 'Auditoría') {
                        $algunoEnAuditoria = true;
                    } elseif (($evalItem['estado'] ?? '') === 'Rechazada') {
                        $algunoRechazado = true;
                    } else {
                        $algunoAprobado = true;
                    }

                    if (isset($evalItem['motivo_estado'])) {
                        $motivosEstados[] = '[' . $s['simon_code'] . ']: ' . $evalItem['motivo_estado'];
                    }

                    $evaluaciones[] = [
                        'servicio' => $s,
                        'eval' => $evalItem
                    ];
                }

                // 2. Definir estado global de la autorización
                $estadoGlobal = 'Aprobada';
                if ($algunoEnAuditoria) {
                    $estadoGlobal = 'Auditoría';
                } elseif ($algunoRechazado && !$algunoAprobado) {
                    $estadoGlobal = 'Rechazada';
                }

                // 3. Crear cabecera de la autorización (usando el primer servicio para campos legacy)
                $primerServicio = $servicios[0];
                $primerEval = $evaluaciones[0]['eval'];

                $aut = Autorizacion::create([
                    'numero_autorizacion' => $numAut,
                    'afiliado_type' => 'titular',
                    'afiliado_id' => $request->afiliado_id,
                    'pss_id' => $request->pss_id,
                    'medico_solicitante' => $request->medico_solicitante,
                    'diagnostico' => $request->diagnostico,
                    'servicio_medico_id' => $servicioMedicoId,
                    'procedimiento' => $primerServicio['description'] ?? 'Servicios Múltiples',
                    'monto_solicitado' => $totalSolicitado,
                    'monto_contratado' => $totalContratado,
                    'copago' => $totalCopago,
                    'exceso' => $totalExceso,
                    'monto_ars' => $totalArs,
                    'monto_afiliado' => $totalCopago + $totalExceso,
                    'prioridad' => $request->prioridad,
                    'fecha_solicitud' => now(),
                    'origin' => 'core_ars',
                    'channel' => $request->channel,
                    'pdss_service_id' => $primerServicio['id'],
                    'estado' => $estadoGlobal,
                    'motivo_estado' => implode(' | ', $motivosEstados),
                    'usuario_responsable_id' => $userId,
                    // Snapshots de contratación (para compatibilidad)
                    'pss_contract_id' => $primerEval['pss_contract_id'] ?? null,
                    'pss_contract_version_id' => $primerEval['pss_contract_version_id'] ?? null,
                    'pss_tariff_schedule_id' => $primerEval['pss_tariff_schedule_id'] ?? null,
                    'pss_tariff_item_id' => $primerEval['pss_tariff_item_id'] ?? null,
                    'contracted_amount_snapshot' => $totalContratado,
                    'affiliate_copay_amount' => $totalCopago,
                    'ars_amount' => $totalArs,
                    'non_covered_amount' => $totalExceso,
                    'internal_notes' => $request->internal_notes
                ]);

                // 4. Crear registros en la tabla autorizacion_detalles
                foreach ($evaluaciones as $ev) {
                    AutorizacionDetalle::create([
                        'autorizacion_id' => $aut->id,
                        'codigo' => $ev['servicio']['simon_code'],
                        'descripcion' => $ev['servicio']['description'],
                        'cantidad' => 1,
                        'monto' => $ev['servicio']['monto_solicitado'],
                        'estado' => $ev['eval']['estado'] ?? 'Aprobada'
                    ]);
                }
            } else {
                // Fallback para una autorización plana individual
                $aut = new Autorizacion([
                    'numero_autorizacion' => $numAut,
                    'afiliado_type' => 'titular',
                    'afiliado_id' => $request->afiliado_id,
                    'pss_id' => $request->pss_id,
                    'medico_solicitante' => $request->medico_solicitante,
                    'diagnostico' => $request->diagnostico,
                    'servicio_medico_id' => $servicioMedicoId,
                    'procedimiento' => PdssService::find($request->pdss_service_id)?->coverage_description,
                    'monto_solicitado' => $request->monto_solicitado,
                    'prioridad' => $request->prioridad,
                    'fecha_solicitud' => now(),
                    'origin' => 'core_ars',
                    'channel' => $request->channel,
                    'pdss_service_id' => $request->pdss_service_id,
                ]);

                $eval = AuthorizationEvaluator::evaluar($aut, $request->hasFile('documento_soporte'));

                $aut->estado = $eval['estado'];
                $aut->motivo_estado = $eval['motivo_estado'];
                $aut->monto_contratado = $eval['monto_contratado'] ?? 0.00;
                $aut->copago = $eval['copago'] ?? 0.00;
                $aut->exceso = $eval['exceso'] ?? 0.00;
                $aut->usuario_responsable_id = $userId;

                if (isset($eval['pss_contract_id'])) {
                    $aut->pss_contract_id = $eval['pss_contract_id'];
                    $aut->pss_contract_version_id = $eval['pss_contract_version_id'];
                    $aut->pss_tariff_schedule_id = $eval['pss_tariff_schedule_id'];
                    $aut->pss_tariff_item_id = $eval['pss_tariff_item_id'];
                    $aut->contracted_amount_snapshot = $eval['monto_contratado'];
                    $aut->affiliate_copay_amount = $eval['copago'];
                    $aut->ars_amount = $eval['monto_ars'];
                    $aut->non_covered_amount = $eval['exceso'];
                }

                if ($request->filled('internal_notes')) {
                    $aut->internal_notes = $request->internal_notes;
                }

                $aut->save();
            }

            // Guardar documento si se subió
            if ($request->hasFile('documento_soporte')) {
                $file = $request->file('documento_soporte');
                $path = $file->store('autorizaciones_soporte');
                Documento::create([
                    'entidad_type' => 'autorizacion',
                    'entidad_id' => $aut->id,
                    'nombre_archivo' => $file->getClientOriginalName(),
                    'ruta_archivo' => $path,
                    'tipo_documento' => 'Soporte Médico'
                ]);
            }

            // Registrar Timeline
            AuthorizationTimelineEvent::create([
                'authorization_id' => $aut->id,
                'event_type' => 'CREATED',
                'title' => 'Creada en Core ARS',
                'description' => "Se registró la solicitud vía {$request->channel} por el representante.",
                'new_status' => $aut->estado,
                'user_id' => $userId
            ]);

            if ($aut->estado === 'Aprobada') {
                \App\Services\AccountingPostingService::registrarPreautorizacionAprobada($aut);
            }

            DB::commit();
            return redirect()->route('ars.autorizaciones_medicas.show', $aut->id)->with('success', "Autorización {$aut->numero_autorizacion} procesada con estado: {$aut->estado}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la autorización: ' . $e->getMessage());
        }
    }

    /**
     * Detalle completo e interno de una autorización en el Core ARS.
     */
    public function show($id)
    {
        $autorizacion = Autorizacion::with(['pss', 'contract', 'tariffItem'])->findOrFail($id);
        
        $overrides = AuthorizationOverride::where('authorization_id', $autorizacion->id)->with('approver')->get();
        
        $comments = AutorizacionComentario::where('autorizacion_id', $autorizacion->id)->with('usuario')->orderBy('created_at', 'desc')->get();
        
        $timeline = AuthorizationTimelineEvent::where('authorization_id', $autorizacion->id)->with('user')->orderBy('created_at', 'asc')->get();

        $docs = Documento::where('entidad_type', 'autorizacion')->where('entidad_id', $autorizacion->id)->get();

        return view('ars.autorizaciones_medicas.show', compact('autorizacion', 'overrides', 'comments', 'timeline', 'docs'));
    }

    /**
     * Aplica un override manual de aprobación a una solicitud rechazada o en auditoría.
     */
    public function aplicarOverride(Request $request, $id)
    {
        $aut = Autorizacion::findOrFail($id);
        $request->validate([
            'reason' => 'required|string|min:8',
            'override_type' => 'required|string'
        ]);

        $userId = Auth::id() ?? 1;

        DB::transaction(function() use ($aut, $request, $userId) {
            $oldStatus = $aut->estado;

            // Registrar Override
            AuthorizationOverride::create([
                'authorization_id' => $aut->id,
                'override_type' => $request->override_type,
                'original_result' => $oldStatus,
                'new_result' => 'Aprobada',
                'reason' => $request->reason,
                'approved_by' => $userId,
                'approved_at' => now()
            ]);

            // Actualizar Autorización
            $aut->estado = 'Aprobada';
            $aut->motivo_estado = 'Aprobación por override manual autorizado: ' . $request->reason;
            $aut->internal_notes = ($aut->internal_notes ? $aut->internal_notes . "\n" : '') . "Override manual aplicado. Motivo: " . $request->reason;
            $aut->save();

            \App\Services\AccountingPostingService::registrarPreautorizacionAprobada($aut);

            // Timeline
            AuthorizationTimelineEvent::create([
                'authorization_id' => $aut->id,
                'event_type' => 'OVERRIDE',
                'title' => 'Override Aplicado',
                'description' => "Se forzó la aprobación manual. Motivo: {$request->reason}",
                'old_status' => $oldStatus,
                'new_status' => 'Aprobada',
                'user_id' => $userId
            ]);

            Bitacora::registrar('Autorizaciones Médicas', "Override manual en {$aut->numero_autorizacion} por el usuario ID {$userId}.");
        });

        return redirect()->back()->with('success', 'Override aplicado exitosamente.');
    }

    /**
     * Procesa auditoría clínica (Aprobar/Rechazar).
     */
    public function procesarAuditoria(Request $request, $id)
    {
        $aut = Autorizacion::findOrFail($id);
        $request->validate([
            'decision' => 'required|in:Aprobada,Rechazada',
            'motivo' => 'required|string|min:5'
        ]);

        $userId = Auth::id() ?? 1;
        $oldStatus = $aut->estado;

        DB::transaction(function() use ($aut, $request, $userId, $oldStatus) {
            $aut->estado = $request->decision;
            $aut->motivo_estado = $request->motivo;
            $aut->save();

            if ($aut->estado === 'Aprobada') {
                \App\Services\AccountingPostingService::registrarPreautorizacionAprobada($aut);
            }

            // Timeline
            AuthorizationTimelineEvent::create([
                'authorization_id' => $aut->id,
                'event_type' => 'AUDIT_RESOLVED',
                'title' => "Resolución de Auditoría: {$request->decision}",
                'description' => $request->motivo,
                'old_status' => $oldStatus,
                'new_status' => $request->decision,
                'user_id' => $userId
            ]);
        });

        return redirect()->back()->with('success', 'Dictamen de auditoría guardado con éxito.');
    }

    /**
     * Bandeja de auditoría médica.
     */
    public function bandejaAuditoria()
    {
        $autorizaciones = Autorizacion::where('estado', 'Auditoría')
            ->where(function($q) {
                $q->whereHas('tariffItem', function($ti) {
                    $ti->where('is_high_cost', true)->orWhere('is_surgery', true)->orWhere('requires_medical_audit', true);
                })->orWhereNull('pss_contract_id');
            })->paginate(15);

        return view('ars.autorizaciones_medicas.bandeja_auditoria', compact('autorizaciones'));
    }

    /**
     * Bandeja de revisión administrativa (excesos de tarifas y PSS sin contrato).
     */
    public function bandejaRevision()
    {
        $autorizaciones = Autorizacion::where('estado', 'Auditoría')
            ->where(function($q) {
                $q->whereRaw('monto_solicitado > contracted_amount_snapshot')
                  ->orWhereNull('pss_contract_id');
            })->paginate(15);

        return view('ars.autorizaciones_medicas.bandeja_revision', compact('autorizaciones'));
    }

    /**
     * Configuración de reglas (Vista).
     */
    public function configReglas()
    {
        return view('ars.autorizaciones_medicas.config_reglas');
    }

    /**
     * Guarda la configuración de reglas.
     */
    public function guardarConfigReglas(Request $request)
    {
        return redirect()->back()->with('success', 'Reglas operativas actualizadas exitosamente.');
    }

    /**
     * Busca afiliados por nombre o cédula por AJAX.
     */
    public function buscarAfiliadoAjax(Request $request)
    {
        $term = $request->get('q', '');
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $afiliados = Afiliado::where('estado_afiliacion', 'OK')
            ->where(function($q) use ($term) {
                $q->where('nombres', 'like', "%{$term}%")
                  ->orWhere('primer_apellido', 'like', "%{$term}%")
                  ->orWhere('segundo_apellido', 'like', "%{$term}%")
                  ->orWhere('cedula', 'like', "%{$term}%");
            })
            ->take(15)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'nombres' => $a->nombres,
                'primer_apellido' => $a->primer_apellido,
                'segundo_apellido' => $a->segundo_apellido,
                'cedula' => $a->cedula,
                'nss' => $a->nss,
                'estado_afiliacion' => $a->estado_afiliacion,
                'tipo_afiliado' => $a->tipo_afiliado ?? 'Titular',
                'plan' => 'Plan PDSS 11.0'
            ]);

        return response()->json($afiliados);
    }

    /**
     * Obtiene el historial de autorizaciones recientes del afiliado.
     */
    public function historialAfiliadoAjax(Request $request)
    {
        $afiliadoId = $request->get('afiliado_id');
        if (!$afiliadoId) return response()->json([]);

        $autorizaciones = Autorizacion::where('afiliado_id', $afiliadoId)
            ->with(['pss'])
            ->orderBy('fecha_solicitud', 'desc')
            ->take(5)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'numero_autorizacion' => $a->numero_autorizacion,
                'procedimiento' => $a->procedimiento ?? 'Múltiples Servicios',
                'fecha_solicitud' => $a->fecha_solicitud ? $a->fecha_solicitud->format('d/m/Y') : 'N/A',
                'estado' => $a->estado,
                'pss_nombre' => $a->pss->nombre ?? 'N/A',
                'monto_solicitado' => floatval($a->monto_solicitado)
            ]);

        return response()->json($autorizaciones);
    }

    /**
     * Busca prestadoras por nombre o RNC por AJAX.
     */
    public function buscarPssAjax(Request $request)
    {
        $term = $request->get('q', '');
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $pss = Pss::where('estado', 'Activa')
            ->where(function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('rnc', 'like', "%{$term}%");
            })
            ->take(15)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'rnc' => $p->rnc,
                'tipo_entidad' => $p->tipo_entidad ?? 'Clínica'
            ]);

        return response()->json($pss);
    }

    /**
     * Obtiene la tarifa contratada para una PSS y un Servicio PDSS específicos.
     */
    public function obtenerTarifaAjax(Request $request)
    {
        $pssId = $request->get('pss_id');
        $serviceId = $request->get('pdss_service_id');

        if (!$pssId || !$serviceId) {
            return response()->json(['error' => 'Parámetros incompletos.'], 400);
        }

        // 1. Buscar contrato vigente
        $contract = PssContract::where('pss_id', $pssId)
            ->where('status', 'vigente')
            ->first();

        if (!$contract) {
            return response()->json([
                'success' => false,
                'message' => 'La prestadora no posee un contrato vigente en el sistema.'
            ]);
        }

        // 2. Buscar versión vigente
        $version = PssContractVersion::where('pss_contract_id', $contract->id)
            ->where('status', 'vigente')
            ->first();

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'El contrato no tiene una versión de tarifario vigente configurada.'
            ]);
        }

        // 3. Buscar esquema de tarifas
        $schedule = PssTariffSchedule::where('pss_contract_version_id', $version->id)
            ->where('status', 'vigente')
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'No hay esquema tarifario activo para la versión contractual.'
            ]);
        }

        // 4. Buscar ítem de tarifa
        $tariffItem = PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
            ->where('pdss_service_id', $serviceId)
            ->first();

        if (!$tariffItem) {
            return response()->json([
                'success' => false,
                'message' => 'El servicio seleccionado no se encuentra contratado en el tarifario de este prestador.'
            ]);
        }

        return response()->json([
            'success' => true,
            'contracted_amount' => $tariffItem->contracted_amount,
            'copay_percent' => $tariffItem->copay_percent,
            'ars_covered_percent' => $tariffItem->ars_covered_percent,
            'requires_authorization' => (bool)$tariffItem->requires_authorization,
            'requires_medical_audit' => (bool)$tariffItem->requires_medical_audit,
            'is_high_cost' => (bool)$tariffItem->is_high_cost,
            'is_surgery' => (bool)$tariffItem->is_surgery,
            'is_hospitalization' => (bool)$tariffItem->is_hospitalization
        ]);
    }
}
