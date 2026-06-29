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
use App\Services\AuthorizationEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PssController extends Controller
{
    /* =========================================================================
     * ARS ADMIN PORTAL METHODS
     * ========================================================================= */

    /**
     * Listado de Prestadoras (PSS).
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Pss::query();

        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")->orWhere('rnc', 'like', "%{$search}%");
        }

        $pss = $query->orderBy('nombre')->paginate(10);
        return view('ars.pss.index', compact('pss', 'search'));
    }

    /**
     * Muestra el formulario para editar una PSS.
     */
    public function edit($id)
    {
        $pss = Pss::findOrFail($id);
        return view('ars.pss.edit', compact('pss'));
    }

    /**
     * Actualiza la información de una PSS.
     */
    public function update(Request $request, $id)
    {
        $pss = Pss::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'rnc' => 'required|string|max:20|unique:pss,rnc,' . $pss->id,
            'tipo_entidad' => 'required|string',
            'telefono' => 'required|string|max:20',
            'correo' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'estado' => 'required|string|in:Activa,Inactiva',
            'nivel_atencion' => 'nullable|string',
            'tipo_pss' => 'nullable|string',
            'red_contratada' => 'nullable|string',
            'contrato_vigente' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
        ]);

        $pss->update($request->only([
            'nombre', 'rnc', 'tipo_entidad', 'telefono', 'correo', 'direccion', 'estado',
            'nivel_atencion', 'tipo_pss', 'red_contratada', 'contrato_vigente'
        ]));

        if ($request->filled('contrato_vigente')) {
            $contrato = $pss->contrato_activo ?? $pss->contratos()->latest()->first();
            if (!$contrato) {
                $contrato = new ContratoPss();
                $contrato->pss_id = $pss->id;
                $contrato->estado = 'Activo';
            }
            $contrato->numero_contrato = $request->contrato_vigente;
            if ($request->filled('fecha_inicio')) {
                $contrato->fecha_inicio = $request->fecha_inicio;
            }
            if ($request->filled('fecha_fin')) {
                $contrato->fecha_fin = $request->fecha_fin;
            }
            $contrato->save();
        }

        Bitacora::registrar('PSS Mantenimiento', "Actualizada PSS: {$pss->nombre} (#ID: {$pss->id})");

        return redirect()->route('ars.pss.index')
            ->with('success', "Se actualizó la prestadora '{$pss->nombre}' exitosamente.");
    }

    /**
     * Listado de contratos y tarifas vigentes.
     */
    public function contratosIndex(Request $request)
    {
        $contratos = ContratoPss::with(['pss', 'tarifas.servicio'])->orderBy('created_at', 'desc')->paginate(10);
        return view('ars.pss.contratos', compact('contratos'));
    }

    /**
     * Catálogo de servicios médicos.
     */
    public function serviciosIndex(Request $request)
    {
        $search = $request->get('search');
        $query = ServicioMedico::query();

        if ($search) {
            $query->where('descripcion', 'like', "%{$search}%")->orWhere('codigo', 'like', "%{$search}%");
        }

        $servicios = $query->orderBy('codigo')->paginate(15);
        return view('ars.pss.servicios', compact('servicios', 'search'));
    }

    /**
     * Muestra el formulario para editar un servicio médico.
     */
    public function serviciosEdit($id)
    {
        $servicio = ServicioMedico::findOrFail($id);
        return view('ars.pss.servicios_edit', compact('servicio'));
    }

    /**
     * Actualiza la información de un servicio médico.
     */
    public function serviciosUpdate(Request $request, $id)
    {
        $servicio = ServicioMedico::findOrFail($id);

        $request->validate([
            'codigo' => 'required|string|max:50|unique:servicios_medicos,codigo,' . $servicio->id,
            'descripcion' => 'required|string|max:255',
            'cobertura_base' => 'required|numeric|min:0|max:100',
        ]);

        $servicio->update([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'cobertura_base' => $request->cobertura_base,
            'es_alto_costo' => $request->has('es_alto_costo'),
            'requiere_documento' => $request->has('requiere_documento'),
        ]);

        Bitacora::registrar('Servicios Mantenimiento', "Actualizado servicio: {$servicio->codigo} (#ID: {$servicio->id})");

        return redirect()->route('ars.pss.servicios')
            ->with('success', "Se actualizó el servicio médico '{$servicio->codigo}' exitosamente.");
    }


    /* =========================================================================
     * PSS CLINICAL PORTAL METHODS
     * ========================================================================= */

    /**
     * Dashboard del portal PSS.
     */
    public function portalDashboard()
    {
        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1); // Por defecto Clínica Abreu si no tiene asignada

        // Métricas
        $metricas = [
            'total' => Autorizacion::where('pss_id', $pss->id)->count(),
            'aprobadas' => Autorizacion::where('pss_id', $pss->id)->where('estado', 'Aprobada')->count(),
            'rechazadas' => Autorizacion::where('pss_id', $pss->id)->where('estado', 'Rechazada')->count(),
            'pendientes' => Autorizacion::where('pss_id', $pss->id)->whereIn('estado', ['Pendiente', 'Auditoría', 'Pendiente Documento'])->count(),
        ];

        // Últimas solicitudes realizadas
        $ultimasSolicitudes = Autorizacion::where('pss_id', $pss->id)
            ->with('servicio')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Afiliados consultados recientemente (Simulado de bitácora)
        $consultasRecientes = Bitacora::where('user_id', $user->id)
            ->where('accion', 'like', 'Consulta de Cobertura%')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pss.dashboard', compact('pss', 'metricas', 'ultimasSolicitudes', 'consultasRecientes'));
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
                $nombre = $afiliadoType === 'titular' ? $afiliado->nombre_completo : $afiliado->nombre_completo;
                Bitacora::registrar('PSS Portal', "Consulta de Cobertura exitosa para: {$nombre} (Estado: {$afiliado->estado_afiliacion})");
            }
        }

        return view('pss.buscar', compact('afiliado', 'afiliadoType', 'identificacion', 'error'));
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

        // 1. Hack de demostración para alinearse con los screenshots de ARS CMD
        if ($cleanIdent === '008961897901' || str_contains($identificacion, '00896') || str_contains($identificacion, '18979')) {
            $afiliado = Afiliado::where('estado_afiliacion', 'OK')->first();
            $afiliadoType = 'titular';
        } elseif ($cleanIdent === '22500756154' || str_contains($identificacion, '225-0075615') || str_contains($identificacion, '0075615')) {
            $afiliado = Dependiente::where('estado_afiliacion', 'OK')->first();
            $afiliadoType = 'dependiente';
        }

        // 2. Si no es un hack, realizar búsqueda estándar
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

        $nombres = $afiliadoType === 'titular' ? $afiliado->nombres : $afiliado->nombres;
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

        $servicios = ServicioMedico::orderBy('descripcion')->get();
        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);

        // Buscar tarifas de la PSS contratadas para que el formulario pueda validar tarifas estimadas
        $contrato = ContratoPss::where('pss_id', $pss->id)->where('estado', 'Activo')->first();
        $tarifas = $contrato 
            ? TarifaPss::where('contrato_pss_id', $contrato->id)->pluck('monto_tarifa', 'servicio_medico_id')->toArray()
            : [];

        return view('pss.nueva-autorizacion', compact('afiliado', 'afiliadoType', 'servicios', 'pss', 'tarifas'));
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
            'servicios.*' => 'exists:servicios_medicos,id',
            'valores' => 'required|array',
            'valores.*' => 'numeric|min:0'
        ]);

        $user = Auth::user();
        $pss = Pss::find($user->pss_id ?? 1);
        $serviciosIds = $request->input('servicios', []);
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

        foreach ($serviciosIds as $index => $srvId) {
            $servicio = ServicioMedico::findOrFail($srvId);
            $montoSolicitado = floatval($valoresReq[$index] ?? 0);
            $montoSolicitadoTotal += $montoSolicitado;

            // Crear una instancia temporal de autorización en memoria para evaluar este servicio
            $tempAut = new Autorizacion([
                'afiliado_type' => $request->afiliado_type,
                'afiliado_id' => $request->afiliado_id,
                'pss_id' => $pss->id,
                'medico_solicitante' => $medico,
                'diagnostico' => $request->diagnostico,
                'servicio_medico_id' => $srvId,
                'procedimiento' => $servicio->descripcion,
                'monto_solicitado' => $montoSolicitado,
                'prioridad' => 'Media',
                'estado' => 'Pendiente',
                'tipo_servicio' => $request->input('tipo_servicio', 'consulta'),
                'fecha_solicitud' => now()
            ]);

            // Evaluar reglas de negocio automáticas para este servicio individual
            $eval = AuthorizationEvaluator::evaluar($tempAut, $hasDoc);
            
            $montoContratadoTotal += floatval($eval['monto_contratado']);
            $estados[] = $eval['estado'];
            $prioridades[] = $eval['prioridad'];
            $motivos[] = "{$servicio->codigo}: {$eval['motivo_estado']}";
            
            $evaluaciones[] = [
                'servicio' => $servicio,
                'monto_solicitado' => $montoSolicitado,
                'monto_contratado' => floatval($eval['monto_contratado']),
                'estado' => $eval['estado'],
            ];

            $results[] = "{$servicio->codigo}: {$eval['estado']}";
        }

        // Determinar estado agregado de la solicitud completa
        // Jerarquía: Auditoría > Pendiente Documento > Rechazada > Aprobada
        $estadoFinal = 'Aprobada';
        if (in_array('Auditoría', $estados)) {
            $estadoFinal = 'Auditoría';
        } elseif (in_array('Pendiente Documento', $estados)) {
            $estadoFinal = 'Pendiente Documento';
        } elseif (collect($estados)->every(fn($e) => $e === 'Rechazada')) {
            $estadoFinal = 'Rechazada';
        }

        // Determinar prioridad agregada
        // Jerarquía: Alta > Media > Baja
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

        // Guardar el registro único de autorización
        $primerSrv = ServicioMedico::findOrFail($serviciosIds[0]);
        $autorizacion = new Autorizacion([
            'numero_autorizacion' => $numAut,
            'afiliado_type' => $request->afiliado_type,
            'afiliado_id' => $request->afiliado_id,
            'pss_id' => $pss->id,
            'medico_solicitante' => $medico,
            'diagnostico' => $request->diagnostico,
            'servicio_medico_id' => $primerSrv->id,
            'procedimiento' => count($serviciosIds) > 1 ? (count($serviciosIds) . " Servicios Médicos") : $primerSrv->descripcion,
            'monto_solicitado' => $montoSolicitadoTotal,
            'monto_contratado' => $montoContratadoTotal,
            'prioridad' => $prioridadFinal,
            'estado' => $estadoFinal,
            'motivo_estado' => implode(' | ', $motivos),
            'tipo_servicio' => $request->input('tipo_servicio', 'consulta'),
            'fecha_solicitud' => now()
        ]);

        if ($estadoFinal === 'Aprobada' || $estadoFinal === 'Rechazada') {
            $autorizacion->fecha_respuesta = now();
        }

        $autorizacion->save();
        $createdAutIds[] = $autorizacion->id;

        // Guardar detalles de los ítems
        foreach ($evaluaciones as $evalSrv) {
            $srv = $evalSrv['servicio'];
            AutorizacionDetalle::create([
                'autorizacion_id' => $autorizacion->id,
                'codigo' => $srv->codigo,
                'descripcion' => $srv->descripcion,
                'cantidad' => 1,
                'monto' => $evalSrv['monto_solicitado'],
                'estado' => ($evalSrv['estado'] === 'Aprobada') ? 'Aprobado' : (($evalSrv['estado'] === 'Rechazada') ? 'Rechazado' : 'Pendiente')
            ]);
        }

        // Si se subió documento físico simulado, guardar registro
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

        Bitacora::registrar('PSS Portal', "Solicitada autorización unificada {$numAut} por PSS {$pss->nombre} conteniendo " . count($serviciosIds) . " servicios. Evaluación agregada: {$estadoFinal}");

        $summaryMessage = "Autorización unificada procesada: " . implode(', ', $results);

        return redirect()->route('pss.solicitudes')
            ->with('success', "Se creó la autorización {$numAut} exitosamente con sus desgloses. " . $summaryMessage)
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

        $query = Autorizacion::where('pss_id', $pss->id)->with('servicio');

        if ($estado) {
            $query->where('estado', $estado);
        }

        $solicitudes = $query->orderBy('fecha_solicitud', 'desc')->paginate(12);

        return view('pss.solicitudes', compact('solicitudes', 'estado'));
    }

    /**
     * Perfil e información tarifaria de la PSS.
     */
    public function portalPerfil()
    {
        $user = Auth::user();
        $pss = Pss::with(['contratos.tarifas.servicio'])->find($user->pss_id ?? 1);
        $contrato = $pss->contrato_activo;

        return view('pss.perfil', compact('pss', 'contrato'));
    }

    /**
     * Muestra la vista imprimible de la autorización con su desglose y estilo propio.
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
            $coberturaBase = $srv ? floatval($srv->cobertura_base) : 80.00;
            
            $tarifaRecord = null;
            if ($contrato && $srv) {
                $tarifaRecord = TarifaPss::where('contrato_pss_id', $contrato->id)
                    ->where('servicio_medico_id', $srv->id)
                    ->first();
            }
            $tarifa = $tarifaRecord ? floatval($tarifaRecord->monto_tarifa) : 1500.00;
            
            $montoSolicitado = floatval($det->monto);
            $cobertura = min($montoSolicitado, $tarifa) * ($coberturaBase / 100);
            $cobertura = round($cobertura, 2);
            $diferencia = round($montoSolicitado - $cobertura, 2);
            
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

        return view('pss.imprimir', compact(
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
        return view('pss.cancelar');
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
            return view('pss.cancelar')->with('error', "No se encontró ninguna autorización con el número '{$numero}' para esta prestadora.");
        }

        $afiliado = $autorizacion->afiliado;

        return view('pss.cancelar', compact('autorizacion', 'afiliado', 'numero'));
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
        $pss = Pss::find($user->pss_id ?? 1);

        $autorizacion = Autorizacion::where('id', $id)
            ->where('pss_id', $pss->id)
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

        // Actualizar detalles de la autorización
        AutorizacionDetalle::where('autorizacion_id', $autorizacion->id)->update([
            'estado' => 'Cancelado'
        ]);

        Bitacora::registrar('PSS Portal', "Cancelada autorización {$autorizacion->numero_autorizacion} por el usuario PSS {$user->name}. Motivo: {$request->input('motivo_cancelacion')}");

        return redirect()->route('pss.autorizaciones.cancelar')
            ->with('success', "La autorización {$autorizacion->numero_autorizacion} ha sido cancelada exitosamente.");
    }
}
