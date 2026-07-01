<?php
namespace App\Http\Controllers;

use App\Models\Autorizacion;
use App\Models\AutorizacionComentario;
use App\Models\AutorizacionEstadoLog;
use App\Models\AutorizacionDetalle;
use App\Models\ReglaAutorizacion;
use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Pss;
use App\Models\ServicioMedico;
use App\Models\User;
use App\Models\PdssService;
use App\Models\AuthorizationTimelineEvent;
use App\Services\AuthorizationEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutorizacionController extends Controller
{
    // ────────────────────────────────────────────────────
    // MÓDULO DASHBOARD
    // ────────────────────────────────────────────────────
    public function moduloDashboard()
    {
        $hoy = now()->toDateString();
        $kpis = [
            'total_dia'        => Autorizacion::whereDate('fecha_solicitud', $hoy)->count(),
            'pendientes'       => Autorizacion::whereIn('estado',['Pendiente','Pendiente Documento'])->count(),
            'aprobadas'        => Autorizacion::where('estado','Aprobada')->whereDate('fecha_solicitud', $hoy)->count(),
            'rechazadas'       => Autorizacion::where('estado','Rechazada')->whereDate('fecha_solicitud', $hoy)->count(),
            'auditoria'        => Autorizacion::where('estado','Auditoría')->count(),
            'pend_documento'   => Autorizacion::where('estado','Pendiente Documento')->count(),
            'total_mes'        => Autorizacion::whereMonth('fecha_solicitud', now()->month)->count(),
            'aprobadas_mes'    => Autorizacion::where('estado','Aprobada')->whereMonth('fecha_solicitud', now()->month)->count(),
        ];

        // Últimas 10 autorizaciones
        $recientes = Autorizacion::with(['pss','servicio','representante'])
            ->orderBy('created_at','desc')->take(10)->get();

        // Por estado
        $porEstado = Autorizacion::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')->get();

        // Por PSS (top 5)
        $porPss = Autorizacion::select('pss_id', DB::raw('count(*) as total'))
            ->with('pss')->groupBy('pss_id')->orderByDesc('total')->take(5)->get();

        // Por tipo de servicio
        $porTipoServicio = Autorizacion::select('tipo_servicio', DB::raw('count(*) as total'))
            ->groupBy('tipo_servicio')->orderByDesc('total')->get();

        return view('ars.autorizaciones.dashboard', compact('kpis','recientes','porEstado','porPss','porTipoServicio'));
    }

    // ────────────────────────────────────────────────────
    // BANDEJA GENERAL
    // ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $estado     = $request->get('estado');
        $search     = $request->get('search');
        $pss_id     = $request->get('pss_id');
        $prioridad  = $request->get('prioridad');
        $fecha_desde= $request->get('fecha_desde');
        $fecha_hasta= $request->get('fecha_hasta');
        $tipo_srv   = $request->get('tipo_servicio');

        $query = Autorizacion::with(['pss','servicio','representante','auditor']);

        if ($estado)      $query->where('estado', $estado);
        if ($pss_id)      $query->where('pss_id', $pss_id);
        if ($prioridad)   $query->where('prioridad', $prioridad);
        if ($tipo_srv)    $query->where('tipo_servicio', $tipo_srv);
        if ($fecha_desde) $query->whereDate('fecha_solicitud','>=', $fecha_desde);
        if ($fecha_hasta) $query->whereDate('fecha_solicitud','<=', $fecha_hasta);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_autorizacion','like',"%{$search}%")
                  ->orWhere('medico_solicitante','like',"%{$search}%")
                  ->orWhere('diagnostico','like',"%{$search}%")
                  ->orWhere('persona_contacto','like',"%{$search}%");
            });
        }

        $autorizaciones = $query->orderBy('fecha_solicitud','desc')->paginate(15);
        $pssList = Pss::where('estado','Activa')->orderBy('nombre')->get();
        $estadoTabs = ['Pendiente','Aprobada','Rechazada','Auditoría','Pendiente Documento','Anulada','Vencida'];

        return view('ars.autorizaciones.index', compact('autorizaciones','estado','search','pss_id','prioridad','pssList','estadoTabs','fecha_desde','fecha_hasta','tipo_srv'));
    }

    public function pendientes(Request $request)
    {
        return $this->index(new Request(['estado' => 'Pendiente']));
    }
    public function aprobadas(Request $request)
    {
        return $this->index(new Request(['estado' => 'Aprobada']));
    }
    public function rechazadas(Request $request)
    {
        return $this->index(new Request(['estado' => 'Rechazada']));
    }
    public function auditoria(Request $request)
    {
        return $this->index(new Request(['estado' => 'Auditoría']));
    }

    // ────────────────────────────────────────────────────
    // NUEVA AUTORIZACIÓN (ARS-SIDE)
    // ────────────────────────────────────────────────────
    public function nueva()
    {
        $pssList     = Pss::where('estado','Activa')->orderBy('nombre')->get();
        $servicios   = ServicioMedico::orderBy('codigo')->get();
        $canales     = ['llamada','correo','whatsapp','portal','presencial'];
        $tiposServicio = ['consulta','laboratorio','imagen','cirugia','internamiento','emergencia','medicamento','alto_costo','otro'];
        $especialidades = ['Medicina General','Cardiología','Ortopedia','Pediatría','Ginecología','Neurología','Oftalmología','Dermatología','Urología','Oncología','Endocrinología','Gastroenterología','Neumología','Psiquiatría','Reumatología'];

        return view('ars.autorizaciones.nueva', compact('pssList','servicios','canales','tiposServicio','especialidades'));
    }

    public function crear(Request $request)
    {
        $request->validate([
            'pss_id'             => 'required|exists:pss,id',
            'afiliado_type'      => 'required|in:titular,dependiente',
            'afiliado_id'        => 'required|integer',
            'medico_solicitante' => 'required|string|max:120',
            'diagnostico'        => 'required|string|max:200',
            'servicio_medico_id' => 'nullable|exists:servicios_medicos,id',
            'pdss_service_id'    => 'nullable|exists:pdss_services,id',
            'monto_solicitado'   => 'required|numeric|min:0',
            'prioridad'          => 'required|in:Baja,Media,Alta,Emergencia',
            'canal_recepcion'    => 'required|string',
            'tipo_servicio'      => 'required|string',
        ]);

        // Snapshots de PDSS
        $simonCodeSnapshot = null;
        $cupsCodeSnapshot = null;
        $serviceDescriptionSnapshot = null;
        $coverageTypeSnapshot = null;
        $pdssGroupSnapshot = null;
        $pdssSubgroupSnapshot = null;
        $levelRequested = $request->level_requested ?? 1;
        $coverageAllowed = null;
        $copayTypeSnapshot = null;
        $amountCoverageSnapshot = null;

        if ($request->pdss_service_id) {
            $servicioPdss = PdssService::with(['group', 'subgroup'])->find($request->pdss_service_id);
            if ($servicioPdss) {
                $simonCodeSnapshot = $servicioPdss->simon_code;
                $cupsCodeSnapshot = $servicioPdss->cups_code;
                $serviceDescriptionSnapshot = $servicioPdss->coverage_description;
                $coverageTypeSnapshot = $servicioPdss->coverage_type;
                $pdssGroupSnapshot = $servicioPdss->group->name ?? '';
                $pdssSubgroupSnapshot = $servicioPdss->subgroup->name ?? '';
                $coverageAllowed = $servicioPdss->amount_coverage ?? 'Ilimitada';
                $copayTypeSnapshot = $servicioPdss->copay_type ?? 'No';
                $amountCoverageSnapshot = $servicioPdss->amount_coverage ?? '';
            }
        }

        // Generar número único de autorización
        $year = now()->year;
        $ultimo = Autorizacion::whereYear('created_at', $year)->count();
        $numero = 'AUT-' . $year . '-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);

        $procText = $request->procedimiento ?: ($serviceDescriptionSnapshot ?? 'Servicio médico');

        // Crear la autorización con estado inicial Pendiente
        $autorizacion = Autorizacion::create([
            'numero_autorizacion'  => $numero,
            'afiliado_type'        => $request->afiliado_type,
            'afiliado_id'          => $request->afiliado_id,
            'pss_id'               => $request->pss_id,
            'medico_solicitante'   => $request->medico_solicitante,
            'diagnostico'          => $request->diagnostico,
            'codigo_diagnostico'   => $request->codigo_diagnostico,
            'servicio_medico_id'   => $request->servicio_medico_id,
            'pdss_service_id'      => $request->pdss_service_id,
            
            // Snapshots
            'simon_code_snapshot'        => $simonCodeSnapshot,
            'cups_code_snapshot'         => $cupsCodeSnapshot,
            'service_description_snapshot'=> $serviceDescriptionSnapshot,
            'coverage_type_snapshot'     => $coverageTypeSnapshot,
            'pdss_group_snapshot'        => $pdssGroupSnapshot,
            'pdss_subgroup_snapshot'     => $pdssSubgroupSnapshot,
            'level_requested'            => $levelRequested,
            'coverage_allowed'           => $coverageAllowed,
            'copay_type_snapshot'        => $copayTypeSnapshot,
            'amount_coverage_snapshot'   => $amountCoverageSnapshot,

            'procedimiento'        => $procText,
            'monto_solicitado'     => $request->monto_solicitado,
            'monto_contratado'     => 0,
            'prioridad'            => $request->prioridad,
            'canal_recepcion'      => $request->canal_recepcion,
            'persona_contacto'     => $request->persona_contacto,
            'telefono_contacto'    => $request->telefono_contacto,
            'tipo_servicio'        => $request->tipo_servicio,
            'especialidad'         => $request->especialidad,
            'tipo_afiliado_display'=> ucfirst($request->afiliado_type),
            'estado'               => 'Pendiente',
            'fecha_solicitud'      => now(),
            'representante_id'     => Auth::id(),
            'usuario_responsable_id'=> Auth::id(),
        ]);

        // Evaluar con el motor de reglas
        $resultado = AuthorizationEvaluator::evaluar($autorizacion, (bool)$request->hasDocument);

        // Generar código de respuesta
        $codigoResp = match($resultado['estado']) {
            'Aprobada'           => 'AUT-' . str_pad(rand(100,999), 3, '0', STR_PAD_LEFT),
            'Rechazada'          => 'RECH-' . str_pad(rand(100,999), 3, '0', STR_PAD_LEFT),
            'Auditoría'          => 'AUD-' . str_pad(rand(100,999), 3, '0', STR_PAD_LEFT),
            'Pendiente Documento'=> 'PDOC-' . str_pad(rand(100,999), 3, '0', STR_PAD_LEFT),
            default              => 'PEND-' . str_pad(rand(100,999), 3, '0', STR_PAD_LEFT),
        };

        $autorizacion->update([
            'estado'          => $resultado['estado'],
            'motivo_estado'   => $resultado['motivo_estado'],
            'monto_contratado'=> $resultado['monto_contratado'],
            'prioridad'       => $resultado['prioridad'] ?? $request->prioridad,
            'fecha_respuesta' => now(),
            'codigo_respuesta'=> $codigoResp,
            'monto_ars'              => $resultado['monto_ars'] ?? 0,
            'monto_afiliado'         => $resultado['monto_afiliado'] ?? 0,
            'copago'                 => $resultado['copago'] ?? 0,
            'exceso'                 => $resultado['exceso'] ?? 0,
            'monto_no_cubierto'      => $resultado['monto_no_cubierto'] ?? 0,
            'exception_coverage_type' => $resultado['exception_coverage_type'] ?? 'N/A',
        ]);

        if ($resultado['estado'] === 'Aprobada' && $request->pdss_service_id && isset($servicioPdss)) {
            \App\Services\PdssCoverageEngine::acumularConsumo($autorizacion->afiliado, $servicioPdss, $resultado['monto_ars'] ?? 0);
        }

        // Registrar log de estado
        AutorizacionEstadoLog::create([
            'autorizacion_id' => $autorizacion->id,
            'user_id'         => Auth::id(),
            'estado_anterior' => 'Pendiente',
            'estado_nuevo'    => $resultado['estado'],
            'motivo'          => 'Evaluación automática por motor de reglas.',
            'ip_address'      => request()->ip(),
        ]);

        // Crear detalle básico
        $codigoDetalle = $simonCodeSnapshot ?: ($autorizacion->servicio->codigo ?? 'SRV-001');
        $descDetalle = $serviceDescriptionSnapshot ?: ($autorizacion->servicio->descripcion ?? 'Servicio médico');

        AutorizacionDetalle::create([
            'autorizacion_id' => $autorizacion->id,
            'codigo'          => $codigoDetalle,
            'descripcion'     => $descDetalle,
            'cantidad'        => 1,
            'monto'           => $autorizacion->monto_contratado ?: $autorizacion->monto_solicitado,
            'estado'          => $resultado['estado'] === 'Aprobada' ? 'Aprobado' : 'Pendiente',
        ]);

        // Registrar en timeline
        AuthorizationTimelineEvent::registrar(
            $autorizacion->id,
            'CREATION',
            'Solicitud recibida',
            "Se creó la solicitud de autorización {$numero} desde canal {$request->canal_recepcion}.",
            null,
            'Pendiente'
        );

        AuthorizationTimelineEvent::registrar(
            $autorizacion->id,
            'EVALUATION',
            'Evaluación automática: ' . $resultado['estado'],
            $resultado['motivo_estado'],
            'Pendiente',
            $resultado['estado']
        );

        // Bitácora
        Bitacora::registrar('Autorizaciones', "Nueva autorización {$numero} registrada. Estado: {$resultado['estado']}. PSS: {$autorizacion->pss->nombre}.");

        return redirect()->route('ars.autorizaciones.show', $autorizacion->id)
            ->with('success', "Autorización {$numero} registrada y procesada: {$resultado['estado']}.");
    }

    // ────────────────────────────────────────────────────
    // BÚSQUEDA AJAX DE AFILIADO
    // ────────────────────────────────────────────────────
    public function buscarAfiliado(Request $request)
    {
        $term = $request->get('q', '');
        if (strlen($term) < 2) return response()->json([]);

        $titulares = Afiliado::where(function($q) use ($term) {
            $q->where('cedula','like',"%{$term}%")
              ->orWhere('nss','like',"%{$term}%")
              ->orWhere('nombres','like',"%{$term}%")
              ->orWhere('primer_apellido','like',"%{$term}%");
        })->take(8)->get()->map(fn($a) => [
            'id'             => $a->id,
            'type'           => 'titular',
            'label'          => $a->nombres.' '.$a->primer_apellido.' '.($a->segundo_apellido ?? ''),
            'cedula'         => $a->cedula,
            'nss'            => $a->nss,
            'estado'         => $a->estado_afiliacion,
            'tipo'           => 'Titular',
            'regimen'        => $a->regimen_actual,
            'fecha_afiliacion'=> $a->fecha_afiliacion,
        ]);

        $dependientes = Dependiente::where(function($q) use ($term) {
            $q->where('cedula','like',"%{$term}%")
              ->orWhere('nss','like',"%{$term}%")
              ->orWhere('nombres','like',"%{$term}%")
              ->orWhere('apellidos','like',"%{$term}%");
        })->with('titular')->take(5)->get()->map(fn($d) => [
            'id'    => $d->id,
            'type'  => 'dependiente',
            'label' => $d->nombres.' '.$d->apellidos,
            'cedula'=> $d->cedula,
            'nss'   => $d->nss,
            'estado'=> $d->estado_afiliacion,
            'tipo'  => 'Dependiente de '.($d->titular ? $d->titular->nombres.' '.$d->titular->primer_apellido : 'Titular'),
            'regimen'=> null,
            'fecha_afiliacion'=> null,
        ]);

        return response()->json($titulares->merge($dependientes)->values());
    }

    // ────────────────────────────────────────────────────
    // DETALLE DE AUTORIZACIÓN
    // ────────────────────────────────────────────────────
    public function show($id)
    {
        $autorizacion = Autorizacion::with(['pss','servicio','detalles','usuarioResponsable','representante','auditor','comentarios.usuario','estadoLogs.usuario', 'claims', 'payables', 'timelineEvents'])
            ->findOrFail($id);
        $afiliado = $autorizacion->afiliado;

        $historialClinico = Autorizacion::where('afiliado_type', $autorizacion->afiliado_type)
            ->where('afiliado_id', $autorizacion->afiliado_id)
            ->where('id','!=',$autorizacion->id)
            ->with('servicio')->orderBy('fecha_solicitud','desc')->take(10)->get();

        $documentos = Documento::where('entidad_type','autorizacion')->where('entidad_id',$autorizacion->id)->get();

        return view('ars.autorizaciones.show', compact('autorizacion','afiliado','historialClinico','documentos'));
    }

    // ────────────────────────────────────────────────────
    // IMPRIMIR AUTORIZACIÓN (Módulo ARS Interno)
    // ────────────────────────────────────────────────────
    public function imprimir($id)
    {
        $autorizacion = Autorizacion::with(['pss','servicio','servicioPdss','detalles','usuarioResponsable','auditor'])
            ->findOrFail($id);

        $afiliado = $autorizacion->afiliado;

        // Calcular datos de cobertura para la tabla de desglose
        $detallesCalculados = [];
        if ($autorizacion->detalles->isNotEmpty()) {
            foreach ($autorizacion->detalles as $det) {
                $monto    = floatval($det->monto_solicitado ?? 0);
                $cobertura= floatval($det->monto_aprobado  ?? $monto);
                $detallesCalculados[] = (object)[
                    'codigo'      => optional($det->servicio)->codigo ?? $autorizacion->servicio?->codigo ?? 'SVC',
                    'descripcion' => optional($det->servicio)->descripcion ?? $det->procedimiento ?? $autorizacion->procedimiento ?? 'Servicio médico',
                    'cantidad'    => $det->cantidad ?? 1,
                    'porcentaje'  => 100,
                    'monto'       => $monto,
                    'cobertura'   => $cobertura,
                    'diferencia'  => max(0, $monto - $cobertura),
                ];
            }
        } else {
            // Si no hay detalles, usar el monto general
            $monto     = floatval($autorizacion->monto_solicitado);
            $cobertura = floatval($autorizacion->monto_contratado > 0 ? $autorizacion->monto_contratado : $monto);
            $detallesCalculados[] = (object)[
                'codigo'      => optional($autorizacion->servicio)->codigo ?? optional($autorizacion->servicioPdss)->code ?? 'SVC-GEN',
                'descripcion' => optional($autorizacion->servicio)->descripcion ?? optional($autorizacion->servicioPdss)->coverage_description ?? $autorizacion->procedimiento ?? 'Servicio médico general',
                'cantidad'    => 1,
                'porcentaje'  => 100,
                'monto'       => $monto,
                'cobertura'   => $cobertura,
                'diferencia'  => max(0, $monto - $cobertura),
            ];
        }

        $totalSolicitado = collect($detallesCalculados)->sum('monto');
        $totalCobertura  = collect($detallesCalculados)->sum('cobertura');
        $totalDiferencia = collect($detallesCalculados)->sum('diferencia');

        $poliza = $afiliado?->numero_contrato ?? $afiliado?->nss ?? 'N/D';

        return view('ars.autorizaciones.imprimir', compact(
            'autorizacion', 'afiliado',
            'detallesCalculados', 'totalSolicitado', 'totalCobertura', 'totalDiferencia',
            'poliza'
        ));
    }



    // ────────────────────────────────────────────────────
    // DECISIÓN MANUAL (REPRESENTANTE / SUPERVISOR)
    // ────────────────────────────────────────────────────
    public function procesarDecision(Request $request, $id)
    {
        $autorizacion = Autorizacion::findOrFail($id);
        $request->validate([
            'decision'       => 'required|in:Aprobada,Rechazada,Auditoría,Pendiente Documento',
            'motivo_estado'  => 'required|string',
            'monto_contratado' => 'nullable|numeric',
        ]);

        $estadoAnterior = $autorizacion->estado;
        $montoContratado = $request->decision === 'Aprobada' ? ($request->monto_contratado ?? $autorizacion->monto_solicitado) : 0.00;

        $autorizacion->update([
            'estado'               => $request->decision,
            'motivo_estado'        => $request->motivo_estado,
            'monto_contratado'     => $montoContratado,
            'fecha_respuesta'      => now(),
            'usuario_responsable_id'=> Auth::id(),
        ]);

        AutorizacionDetalle::where('autorizacion_id',$id)->update(['estado' => $request->decision === 'Aprobada' ? 'Aprobado' : ($request->decision === 'Rechazada' ? 'Rechazado' : 'Pendiente')]);

        AutorizacionEstadoLog::create([
            'autorizacion_id'=> $autorizacion->id,
            'user_id'        => Auth::id(),
            'estado_anterior'=> $estadoAnterior,
            'estado_nuevo'   => $request->decision,
            'motivo'         => $request->motivo_estado,
            'ip_address'     => request()->ip(),
        ]);

        // Registrar en timeline
        AuthorizationTimelineEvent::registrar(
            $autorizacion->id,
            'MANUAL_DECISION',
            'Decisión administrativa: ' . $request->decision,
            "Procesada decisión de forma manual. Motivo: {$request->motivo_estado}.",
            $estadoAnterior,
            $request->decision
        );

        Bitacora::registrar('Autorizaciones', "Decisión sobre {$autorizacion->numero_autorizacion}: {$request->decision}. Motivo: {$request->motivo_estado}");

        return redirect()->route('ars.autorizaciones.show', $autorizacion->id)
            ->with('success', "Autorización {$autorizacion->numero_autorizacion} procesada: {$request->decision}.");
    }

    // ────────────────────────────────────────────────────
    // AUDITORÍA MÉDICA — BANDEJA
    // ────────────────────────────────────────────────────
    public function auditoriaView(Request $request)
    {
        $search = $request->get('search');
        $query = Autorizacion::with(['pss','servicio','representante'])
            ->where('estado','Auditoría');
        if ($search) $query->where('numero_autorizacion','like',"%{$search}%");

        $autorizaciones = $query->orderBy('prioridad','asc')->orderBy('fecha_solicitud','asc')->paginate(15);
        $kpisAuditoria = [
            'total'      => Autorizacion::where('estado','Auditoría')->count(),
            'alto_costo' => Autorizacion::where('estado','Auditoría')->where('tipo_servicio','alto_costo')->count(),
            'urgentes'   => Autorizacion::where('estado','Auditoría')->whereIn('prioridad',['Alta','Emergencia'])->count(),
            'hoy'        => Autorizacion::where('estado','Auditoría')->whereDate('fecha_solicitud',now())->count(),
        ];

        return view('ars.autorizaciones.auditoria', compact('autorizaciones','kpisAuditoria','search'));
    }

    // ────────────────────────────────────────────────────
    // DECISIÓN AUDITOR MÉDICO
    // ────────────────────────────────────────────────────
    public function auditar(Request $request, $id)
    {
        $autorizacion = Autorizacion::findOrFail($id);
        $request->validate([
            'decision'          => 'required|in:Aprobada,Rechazada,Pendiente Documento',
            'motivo_clinico'    => 'required|string',
            'diagnostico_revisado' => 'nullable|string',
            'monto_contratado'  => 'nullable|numeric',
        ]);

        $estadoAnterior = $autorizacion->estado;
        $autorizacion->update([
            'estado'          => $request->decision,
            'motivo_estado'   => $request->motivo_clinico,
            'monto_contratado'=> $request->decision === 'Aprobada' ? ($request->monto_contratado ?? $autorizacion->monto_solicitado) : 0,
            'diagnostico'     => $request->diagnostico_revisado ?? $autorizacion->diagnostico,
            'fecha_respuesta' => now(),
            'auditor_id'      => Auth::id(),
        ]);

        AutorizacionEstadoLog::create([
            'autorizacion_id'=> $autorizacion->id,
            'user_id'        => Auth::id(),
            'estado_anterior'=> $estadoAnterior,
            'estado_nuevo'   => $request->decision,
            'motivo'         => '[AUDITORÍA MÉDICA] '.$request->motivo_clinico,
            'ip_address'     => request()->ip(),
        ]);

        if ($request->observacion_auditor) {
            AutorizacionComentario::create([
                'autorizacion_id'=> $autorizacion->id,
                'user_id'        => Auth::id(),
                'comentario'     => '[Auditor Médico] '.$request->observacion_auditor,
                'es_interno'     => true,
            ]);
        }

        // Registrar en timeline
        AuthorizationTimelineEvent::registrar(
            $autorizacion->id,
            'AUDIT_DECISION',
            'Decisión de Auditoría: ' . $request->decision,
            "Auditoría médica completada. Motivo clínico: {$request->motivo_clinico}. Monto contratado final: DOP {$autorizacion->monto_contratado}.",
            $estadoAnterior,
            $request->decision
        );

        Bitacora::registrar('Auditoría Médica', "Decisión médica sobre {$autorizacion->numero_autorizacion}: {$request->decision}.");

        return redirect()->route('ars.autorizaciones.show', $autorizacion->id)
            ->with('success', "Auditoría completada. Resultado: {$request->decision}.");
    }

    // ────────────────────────────────────────────────────
    // COMENTAR
    // ────────────────────────────────────────────────────
    public function comentar(Request $request, $id)
    {
        $autorizacion = Autorizacion::findOrFail($id);
        $request->validate(['comentario' => 'required|string|max:1000']);

        AutorizacionComentario::create([
            'autorizacion_id'=> $autorizacion->id,
            'user_id'        => Auth::id(),
            'comentario'     => $request->comentario,
            'es_interno'     => true,
        ]);

        Bitacora::registrar('Autorizaciones', "Comentario añadido en {$autorizacion->numero_autorizacion}.");

        return back()->with('success', 'Comentario registrado.');
    }

    // ────────────────────────────────────────────────────
    // ANULAR
    // ────────────────────────────────────────────────────
    public function anular(Request $request, $id)
    {
        $autorizacion = Autorizacion::findOrFail($id);
        $request->validate(['motivo_anulacion' => 'required|string|min:10']);

        $estadoAnterior = $autorizacion->estado;
        $autorizacion->update([
            'estado'        => 'Anulada',
            'motivo_estado' => 'ANULADA: '.$request->motivo_anulacion,
            'fecha_respuesta'=> now(),
        ]);

        AutorizacionEstadoLog::create([
            'autorizacion_id'=> $autorizacion->id,
            'user_id'        => Auth::id(),
            'estado_anterior'=> $estadoAnterior,
            'estado_nuevo'   => 'Anulada',
            'motivo'         => $request->motivo_anulacion,
            'ip_address'     => request()->ip(),
        ]);

        // Registrar en timeline
        AuthorizationTimelineEvent::registrar(
            $autorizacion->id,
            'CANCELLATION',
            'Autorización Anulada',
            "La autorización fue anulada de forma definitiva. Motivo: {$request->motivo_anulacion}.",
            $estadoAnterior,
            'Anulada'
        );

        Bitacora::registrar('Autorizaciones', "Autorización {$autorizacion->numero_autorizacion} anulada.");

        return redirect()->route('ars.autorizaciones.index')->with('success', "Autorización {$autorizacion->numero_autorizacion} anulada.");
    }

    // ────────────────────────────────────────────────────
    // REPORTE
    // ────────────────────────────────────────────────────
    public function reporte(Request $request)
    {
        $fecha_desde = $request->get('fecha_desde', now()->startOfMonth()->toDateString());
        $fecha_hasta = $request->get('fecha_hasta', now()->toDateString());
        $pss_id      = $request->get('pss_id');
        $estado      = $request->get('estado');

        $query = Autorizacion::with(['pss','servicio','representante'])
            ->whereDate('fecha_solicitud','>=',$fecha_desde)
            ->whereDate('fecha_solicitud','<=',$fecha_hasta);
        if ($pss_id) $query->where('pss_id',$pss_id);
        if ($estado) $query->where('estado',$estado);

        $autorizaciones = $query->orderBy('fecha_solicitud','desc')->get();

        $resumen = [
            'total'          => $autorizaciones->count(),
            'aprobadas'      => $autorizaciones->where('estado','Aprobada')->count(),
            'rechazadas'     => $autorizaciones->where('estado','Rechazada')->count(),
            'auditoria'      => $autorizaciones->where('estado','Auditoría')->count(),
            'monto_total'    => $autorizaciones->where('estado','Aprobada')->sum('monto_contratado'),
        ];

        $pssList = Pss::orderBy('nombre')->get();

        return view('ars.autorizaciones.reporte', compact('autorizaciones','resumen','pssList','fecha_desde','fecha_hasta','pss_id','estado'));
    }

    // ────────────────────────────────────────────────────
    // REGLAS (existentes)
    // ────────────────────────────────────────────────────
    public function reglasIndex()
    {
        $count = ReglaAutorizacion::count();
        if ($count === 0) {
            $reglasBase = [
                ['codigo'=>'R-AFIL-ACT', 'descripcion'=>'Verificar que el afiliado solicitante esté activo en el padrón de afiliados.','tipo_regla'=>'Afiliado','estado'=>'Activa'],
                ['codigo'=>'R-PSS-CONTR','descripcion'=>'Verificar que la PSS cuente con un contrato activo con la ARS.','tipo_regla'=>'Contrato','estado'=>'Activa'],
                ['codigo'=>'R-TARIFA',   'descripcion'=>'Comprobar que el servicio médico esté cubierto y que el monto solicitado no exceda la tarifa contratada.','tipo_regla'=>'Tarifa','estado'=>'Activa'],
                ['codigo'=>'R-ALTO-COST','descripcion'=>'Desviar servicios calificados como de Alto Costo a Auditoría Médica especializada.','tipo_regla'=>'Alto Costo','estado'=>'Activa'],
                ['codigo'=>'R-DOC-REQUER','descripcion'=>'Verificar existencia de receta/indicación médica adjunta para exámenes de imágenes y procedimientos quirúrgicos.','tipo_regla'=>'Documentación','estado'=>'Activa'],
                ['codigo'=>'R-FRECUENCIA','descripcion'=>'Evaluar duplicidad. Marcar solicitudes recurrentes del mismo servicio en un período menor a 30 días.','tipo_regla'=>'Frecuencia','estado'=>'Activa'],
            ];
            foreach ($reglasBase as $r) ReglaAutorizacion::create($r);
        }
        $reglas = ReglaAutorizacion::all();
        return view('ars.autorizaciones.reglas', compact('reglas'));
    }

    public function reglasToggle($id)
    {
        $regla = ReglaAutorizacion::findOrFail($id);
        $nuevoEstado = $regla->estado === 'Activa' ? 'Inactiva' : 'Activa';
        $regla->update(['estado' => $nuevoEstado]);
        Bitacora::registrar('Administracion', "Cambiado estado de la regla {$regla->codigo} a {$nuevoEstado}.");
        return redirect()->route('ars.autorizaciones.reglas')->with('success', "Regla {$regla->codigo} cambiada a {$nuevoEstado}.");
    }

    public function reglasMotorIndex()
    {
        $reglas = \App\Models\AuthorizationEngineRule::orderBy('priority')->get();
        $planes = \App\Models\HealthPlan::all();
        $gruposPdss = \App\Models\PdssGroup::orderBy('code')->get();
        $subgruposPdss = \App\Models\PdssSubgroup::orderBy('code')->get();
        $pssList = Pss::where('estado', 'Activa')->orderBy('nombre')->get();
        $afiliados = Afiliado::where('estado_afiliacion', 'OK')->orderBy('nombres')->limit(15)->get();
        $servicios = PdssService::orderBy('simon_code')->limit(15)->get();

        return view('ars.autorizaciones.reglas_motor', compact(
            'reglas', 'planes', 'gruposPdss', 'subgruposPdss', 'pssList', 'afiliados', 'servicios'
        ));
    }

    public function guardarReglaMotor(Request $request)
    {
        $request->validate([
            'rule_code' => 'required|string|unique:authorization_engine_rules,rule_code',
            'name' => 'required|string',
            'process' => 'required|string',
            'severity' => 'required|string',
            'priority' => 'required|integer',
        ]);

        $condition = [
            'field' => $request->condition_field,
            'operator' => $request->condition_operator,
            'value' => $request->condition_value
        ];

        $action = [
            'type' => $request->action_type,
            'params' => [
                'severity' => $request->severity,
                'message' => $request->action_message
            ]
        ];

        \App\Models\AuthorizationEngineRule::create([
            'rule_code' => $request->rule_code,
            'name' => $request->name,
            'description' => $request->description,
            'process' => $request->process,
            'severity' => $request->severity,
            'priority' => $request->priority,
            'origin' => $request->origin ?? 'Core ARS',
            'condition_json' => $condition,
            'action_json' => $action,
            'status' => 'Activa',
            'created_by' => Auth::id() ?? 1
        ]);

        Bitacora::registrar('Reglas Motor', "Nueva regla {$request->rule_code} creada.");

        return redirect()->route('ars.autorizaciones.reglas_motor')->with('success', "Regla {$request->rule_code} creada con éxito.");
    }

    public function toggleReglaMotor($id)
    {
        $regla = \App\Models\AuthorizationEngineRule::findOrFail($id);
        $nuevoEstado = $regla->status === 'Activa' ? 'Inactiva' : 'Activa';
        $regla->update(['status' => $nuevoEstado]);
        
        Bitacora::registrar('Reglas Motor', "Cambiado estado de la regla {$regla->rule_code} a {$nuevoEstado}.");
        
        return redirect()->route('ars.autorizaciones.reglas_motor')->with('success', "Regla {$regla->rule_code} cambiada a {$nuevoEstado}.");
    }

    public function eliminarReglaMotor($id)
    {
        $regla = \App\Models\AuthorizationEngineRule::findOrFail($id);
        $code = $regla->rule_code;
        $regla->delete();

        Bitacora::registrar('Reglas Motor', "Eliminada la regla {$code}.");

        return redirect()->route('ars.autorizaciones.reglas_motor')->with('success', "Regla {$code} eliminada con éxito.");
    }

    public function testReglaMotor(Request $request, $id)
    {
        $regla = \App\Models\AuthorizationEngineRule::findOrFail($id);
        
        $afiliadoId = $request->get('afiliado_id');
        $pssId = $request->get('pss_id');
        $servicioId = $request->get('pdss_service_id');
        $monto = floatval($request->get('monto', 0));

        $afiliado = Afiliado::find($afiliadoId);
        $pss = Pss::find($pssId);
        $servicio = PdssService::find($servicioId);

        // Evaluar la regla contra la condición
        $condition = $regla->condition_json;
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? '';
        $value = $condition['value'] ?? '';

        $matchValue = null;
        if ($field === 'afiliado_estado') {
            $matchValue = $afiliado ? $afiliado->estado_afiliacion : 'INACTIVO';
        } elseif ($field === 'monto_solicitado') {
            $matchValue = $monto;
        } elseif ($field === 'servicio_alto_costo') {
            $matchValue = $servicio ? (bool)$servicio->is_high_cost : false;
        } elseif ($field === 'tipo_pss') {
            $matchValue = $pss ? $pss->tipo_entidad : '';
        }

        $isMatch = false;
        if ($operator === '==') {
            $isMatch = ($matchValue == $value);
        } elseif ($operator === '!=') {
            $isMatch = ($matchValue != $value);
        } elseif ($operator === '>') {
            $isMatch = ($matchValue > floatval($value));
        } elseif ($operator === '<') {
            $isMatch = ($matchValue < floatval($value));
        } elseif ($operator === 'contains') {
            $isMatch = str_contains(strtolower($matchValue), strtolower($value));
        }

        $veredicto = 'Aprobada';
        $observacion = 'Regla aprobada (Condición no aplica).';
        
        if ($isMatch) {
            $veredicto = match($regla->severity) {
                'blocking' => 'Rechazada',
                'audit_required' => 'Auditoría',
                'warning' => 'Advertencia',
                default => 'Aprobada'
            };
            $observacion = $regla->action_json['params']['message'] ?? 'Acción ejecutada por severidad.';
        }

        // Registrar prueba
        \App\Models\AuthorizationEngineRuleTest::create([
            'rule_id' => $regla->id,
            'test_payload' => [
                'afiliado' => $afiliado?->nombre_completo,
                'pss' => $pss?->nombre,
                'servicio' => $servicio?->coverage_description,
                'monto' => $monto
            ],
            'result_payload' => [
                'is_match' => $isMatch,
                'veredicto' => $veredicto,
                'observacion' => $observacion
            ],
            'executed_by' => Auth::id() ?? 1,
            'executed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'is_match' => $isMatch,
            'veredicto' => $veredicto,
            'observacion' => $observacion
        ]);
    }
}
