<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Lote;
use App\Models\LoteDetalle;
use App\Models\Catalogo;
use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Autorizacion;
use App\Models\AuthorizationClaim;
use App\Services\UnipagoMockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AfiliadoController extends Controller
{
    /**
     * Buscar afiliados por AJAX (para optimizar selectores grandes).
     */
    public function buscarAfiliadoAjax(Request $request)
    {
        $q = $request->get('q');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $afiliados = Afiliado::where('estado_afiliacion', 'OK')
            ->where(function ($query) use ($q) {
                $query->where('nombres', 'like', "%{$q}%")
                      ->orWhere('primer_apellido', 'like', "%{$q}%")
                      ->orWhere('segundo_apellido', 'like', "%{$q}%")
                      ->orWhere('cedula', 'like', "%{$q}%")
                      ->orWhere('nss', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get(['id', 'nombres', 'primer_apellido', 'segundo_apellido', 'cedula', 'nss'])
            ->map(fn($af) => [
                'id' => $af->id,
                'nombre' => $af->nombre_completo,
                'cedula' => $af->cedula,
                'nss' => $af->nss,
            ]);

        return response()->json($afiliados);
    }

    /**
     * Listado de titulares con filtros.
     */
    public function titularesIndex(Request $request)
    {
        $search = $request->get('search');
        $estado = $request->get('estado');
        $regimen = $request->get('regimen');

        $query = Afiliado::with('tipoIdentificacion');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('primer_apellido', 'like', "%{$search}%")
                  ->orWhere('segundo_apellido', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('nss', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado_afiliacion', $estado);
        }

        if ($regimen) {
            $query->where('regimen_actual', $regimen);
        }

        $titulares = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('ars.afiliados.titulares.index', compact('titulares', 'search', 'estado', 'regimen'));
    }

    /**
     * Formulario de creación de titulares.
     */
    public function titularesCreate()
    {
        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $regimenes = Catalogo::getByGrupo('tipo_regimen');
        return view('ars.afiliados.titulares.create', compact('tiposIdentificacion', 'regimenes'));
    }

    /**
     * Almacena e inicia la clasificación de un titular.
     */
    public function titularesStore(Request $request)
    {
        $request->validate([
            'tipo_identificacion_id' => 'required|exists:catalogos,id',
            'cedula' => 'nullable|string',
            'nss' => 'nullable|string',
            'nombres' => 'required|string',
            'primer_apellido' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:1',
            'regimen_actual' => 'required|string'
        ]);

        // Simular preclasificación en Unipago
        $disp = UnipagoMockService::consultarDisponibilidadAfiliacion($request->cedula, $request->nss);

        // Procesar número de contrato/formulario de afiliación
        $contractNumberId = null;
        $contractNumber = $request->input('contract_number');
        $contractRangeId = null;

        if ($contractNumber) {
            $numRecord = \App\Models\AffiliationContractNumber::where('contract_number', $contractNumber)->first();
            if (!$numRecord || $numRecord->status !== 'disponible') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "El número de contrato/formulario '{$contractNumber}' ingresado manualmente no está disponible, está bloqueado o no existe.");
            }
            $contractNumberId = $numRecord->id;
            $contractRangeId = $numRecord->affiliation_contract_range_id;
        } else {
            // Asignación automática si no se especificó manual
            $nextNumber = \App\Services\AffiliationContractNumberService::getNextAvailableNumber();
            if ($nextNumber) {
                $contractNumberId = $nextNumber->id;
                $contractNumber = $nextNumber->contract_number;
                $contractRangeId = $nextNumber->affiliation_contract_range_id;
            }
        }

        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => $request->tipo_identificacion_id,
            'contract_number_id' => $contractNumberId,
            'contract_number' => $contractNumber,
            'contract_range_id' => $contractRangeId,
            'cedula' => $request->cedula,
            'nss' => $request->nss,
            'nui' => $request->nui,
            'nombres' => $request->nombres,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'provincia' => $request->provincia,
            'municipio' => $request->municipio,
            'sector' => $request->sector,
            'direccion' => $request->direccion,
            'esta_carnetizado' => $request->has('esta_carnetizado') ? (bool)$request->esta_carnetizado : false,
            'tiene_formulario' => $request->has('tiene_formulario') ? (bool)$request->tiene_formulario : false,
            'ubicacion_formulario' => $request->ubicacion_formulario,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'regimen_actual' => $request->regimen_actual,
            'estado_afiliacion' => 'Pendiente', // Guardado como pendiente para que sea procesado en lote
            'motivo_estado' => 'Preclasificación Unipago: ' . $disp['motivo_descripcion'],
            'activo_nomina' => $disp['apto'],
            'tiene_aporte' => $disp['apto'],
        ]);

        // Si se asignó un contrato, consumirlo transaccionalmente para asociarlo al afiliado creado
        if ($contractNumberId) {
            \App\Services\AffiliationContractNumberService::consumeNumber($contractNumberId, $afiliado->id, Auth::id() ?: 1);
        }

        Bitacora::registrar('Afiliados', "Registrado titular individual: {$afiliado->nombre_completo}. Formulario Asignado: {$contractNumber}. Clasificación: {$disp['status']}");

        return redirect()->route('ars.titulares.show', $afiliado->id)
            ->with('success', 'Titular registrado con éxito en estado Pendiente. Formulario Asignado: ' . ($contractNumber ?: 'Ninguno') . '. Preclasificación: ' . $disp['motivo_descripcion']);
    }

    /**
     * Muestra la ficha detallada del titular y el formulario para agregar dependientes.
     */
    public function titularesShow($id)
    {
        $titular = Afiliado::with([
            'tipoIdentificacion',
            'dependientes.parentesco',
            'dependientes.tipoIdentificacion',
            'capitationNotifications',
            'dispersionCutDetails.cut',
            'claims.pss',
        ])->findOrFail($id);

        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $parentescos = Catalogo::getByGrupo('parentesco');

        $novedades = \App\Models\Novedad::where('afiliado_type', 'titular')
            ->where('afiliado_id', $id)
            ->with('tipoNovedad')
            ->orderBy('created_at', 'desc')
            ->get();

        $autorizaciones = Autorizacion::where('afiliado_type', 'titular')
            ->where('afiliado_id', $id)
            ->with(['pss', 'servicio', 'detalles'])
            ->orderBy('created_at', 'desc')
            ->get();

        $documentos = Documento::where('entidad_type', 'titular')
            ->where('entidad_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $diasAfiliado = 0;
        if ($titular->fecha_afiliacion) {
            try {
                $fecha = \Carbon\Carbon::parse($titular->fecha_afiliacion);
                $diasAfiliado = (int)abs(now()->diffInDays($fecha));
            } catch (\Exception $e) {
                $diasAfiliado = 0;
            }
        }

        // Cálculos acumulados de Coberturas Anuales del Plan
        $autorizacionesAprobadas = $autorizaciones->filter(fn($a) => in_array($a->estado, ['Aprobada', 'Aprobado']));
        $claimsPagados = $titular->claims->filter(fn($c) => in_array($c->status, ['Pagada', 'Cerrada']));

        // Farmacia: DOP 8,000 tope (usamos un base de 2,450 + autorizaciones/claims con item de farmacia)
        $consumoFarmacia = 2450.00 + $autorizacionesAprobadas->filter(fn($a) => str_contains(strtolower($a->procedimiento ?? ''), 'farmacia') || str_contains(strtolower($a->diagnostico ?? ''), 'farmacia'))->sum('monto_aprobado');
        
        // Ambulatorio: DOP 150,000 tope (base de 32,800 + total aprobado de autorizaciones y reclamaciones)
        $consumoAmbulatorio = 32800.00 + $autorizacionesAprobadas->sum('monto_aprobado') + $claimsPagados->sum('approved_amount');

        // Hospitalización: DOP 1,000,000 tope (base de 120,000 + cirugías/internamiento)
        $consumoHospitalizacion = 120000.00 + $autorizacionesAprobadas->filter(fn($a) => str_contains(strtolower($a->procedimiento ?? ''), 'cirugia') || str_contains(strtolower($a->procedimiento ?? ''), 'hospital') || str_contains(strtolower($a->procedimiento ?? ''), 'internamiento'))->sum('monto_aprobado');

        return view('ars.afiliados.titulares.show', compact(
            'titular', 'tiposIdentificacion', 'parentescos',
            'novedades', 'autorizaciones', 'documentos',
            'diasAfiliado', 'consumoFarmacia', 'consumoAmbulatorio', 'consumoHospitalizacion'
        ));
    }

    public function titularesEdit($id)
    {
        $titular = Afiliado::findOrFail($id);
        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $regimenes = Catalogo::getByGrupo('tipo_regimen');
        return view('ars.afiliados.titulares.edit', compact('titular', 'tiposIdentificacion', 'regimenes'));
    }

    public function titularesUpdate(Request $request, $id)
    {
        $titular = Afiliado::findOrFail($id);
        $titular->update($request->all());

        Bitacora::registrar('Afiliados', "Actualizados datos del titular: {$titular->nombre_completo}");

        return redirect()->route('ars.titulares.show', $titular->id)->with('success', 'Datos actualizados con éxito.');
    }

    /**
     * Agrega dependiente a titular con validaciones de parentesco y negocio.
     */
    public function dependientesStore(Request $request, $id)
    {
        $titular = Afiliado::findOrFail($id);

        $request->validate([
            'tipo_identificacion_id' => 'required|exists:catalogos,id',
            'parentesco_id' => 'required|exists:catalogos,id',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:1',
            'tipo_dependiente' => 'required|string'
        ]);

        $parentesco = Catalogo::findOrFail($request->parentesco_id);
        $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;

        // 1. Validar cónyuge repetido
        if ($parentesco->codigo === 'CONYUGE') {
            $hasConyuge = Dependiente::where('titular_id', $titular->id)
                ->whereHas('parentesco', function($q) {
                    $q->where('codigo', 'CONYUGE');
                })
                ->where('estado_afiliacion', '!=', 'RE')
                ->exists();

            if ($hasConyuge) {
                return redirect()->back()->withErrors(['parentesco_id' => 'El titular ya posee un cónyuge registrado y activo en su núcleo familiar.']);
            }
        }

        // 2. Validar duplicidad de cédula/NSS en el núcleo
        if ($request->cedula) {
            $dupCed = Dependiente::where('titular_id', $titular->id)->where('cedula', $request->cedula)->exists();
            if ($dupCed) {
                return redirect()->back()->withErrors(['cedula' => 'Esta identificación ya está registrada para otro dependiente en el núcleo familiar.']);
            }
        }

        // 3. Validar edad según tipo de dependiente (Ej: Hijo mayor de 21)
        if ($parentesco->codigo === 'HIJO' && $edad > 21 && !$request->has('estudiante') && !$request->has('discapacitado')) {
            return redirect()->back()->withInput()->with('error_dep', 'Hijo mayor de 21 años requiere certificar que es estudiante o discapacitado para calificar.');
        }

        // 4. Documento soporte requerido
        $requiereDoc = ($parentesco->codigo === 'CONYUGE' || $parentesco->codigo === 'OTROS' || $edad > 18);

        $dep = Dependiente::create([
            'titular_id' => $titular->id,
            'tipo_identificacion_id' => $request->tipo_identificacion_id,
            'parentesco_id' => $request->parentesco_id,
            'cedula' => $request->cedula,
            'nss' => $request->nss,
            'nui' => $request->nui,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'tipo_dependiente' => $request->tipo_dependiente,
            'estudiante' => $request->has('estudiante'),
            'discapacitado' => $request->has('discapacitado'),
            'requiere_documento' => $requiereDoc,
            'estado_afiliacion' => 'Pendiente',
            'motivo_estado' => 'Pendiente de generación de lote y validación Unipago.'
        ]);

        // Simular carga de documento soporte si el usuario marcó que subió uno
        if ($request->hasFile('documento_simulado')) {
            $file = $request->file('documento_simulado');
            $fileName = time() . '_' . $file->getClientOriginalName();
            Documento::create([
                'entidad_type' => 'dependiente',
                'entidad_id' => $dep->id,
                'nombre_archivo' => $fileName,
                'ruta_archivo' => 'documentos/dependientes/' . $fileName,
                'tipo_documento' => 'Identificación o Acta',
                'fecha_carga' => now()
            ]);
        }

        Bitacora::registrar('Afiliados', "Registrado dependiente {$dep->nombre_completo} para titular {$titular->nombre_completo}.");

        return redirect()->route('ars.titulares.show', $titular->id)->with('success', 'Dependiente registrado con éxito (estado Pendiente).');
    }

    public function dependientesEdit($id)
    {
        $dependiente = Dependiente::findOrFail($id);
        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $parentescos = Catalogo::getByGrupo('parentesco');
        return view('ars.afiliados.dependientes.edit', compact('dependiente', 'tiposIdentificacion', 'parentescos'));
    }

    public function dependientesUpdate(Request $request, $id)
    {
        $dep = Dependiente::findOrFail($id);
        $dep->update($request->all());

        Bitacora::registrar('Afiliados', "Actualizados datos del dependiente: {$dep->nombre_completo}");

        return redirect()->route('ars.titulares.show', $dep->titular_id)->with('success', 'Dependiente actualizado con éxito.');
    }

    /**
     * Carga Masiva: Muestra la pantalla.
     */
    public function cargaMasivaIndex()
    {
        return view('ars.afiliados.carga-masiva');
    }

    /**
     * Simula la carga masiva mediante pegado de texto tipo CSV o un archivo de muestra.
     */
    public function cargaMasivaPrevalidar(Request $request)
    {
        $request->validate([
            'csv_content' => 'required|string'
        ]);

        $lines = explode("\n", str_replace("\r", "", trim($request->csv_content)));
        $headers = str_getcsv(array_shift($lines));

        // Columnas mínimas requeridas: cedula, nombres, primer_apellido, fecha_nacimiento, sexo
        $required = ['cedula', 'nombres', 'primer_apellido', 'fecha_nacimiento', 'sexo'];
        $missing = [];

        foreach ($required as $req) {
            if (!in_array($req, $headers)) {
                $missing[] = $req;
            }
        }

        if (count($missing) > 0) {
            return redirect()->back()->withErrors(['csv_content' => 'Faltan columnas requeridas en el CSV: ' . implode(', ', $missing)]);
        }

        // Mapear registros y prevalidar
        $registros = [];
        $resumen = [
            'total' => 0,
            'aptos' => 0,
            'rechazados' => 0,
            'pendientes' => 0,
            'duplicados' => 0
        ];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $row = array_combine($headers, str_getcsv($line));
            $resumen['total']++;

            // Verificar si ya existe en la base de datos local (cédula o nss)
            $cedulaClean = preg_replace('/[^0-9]/', '', $row['cedula'] ?? '');
            $nssClean = preg_replace('/[^0-9]/', '', $row['nss'] ?? '');
            
            $existe = false;
            if (!empty($cedulaClean)) {
                $existe = Afiliado::where('cedula', $cedulaClean)->exists();
            }
            if (!$existe && !empty($nssClean)) {
                $existe = Afiliado::where('nss', $nssClean)->exists();
            }

            if ($existe) {
                $row['preclasificacion'] = 'RE';
                $row['motivo'] = 'Afiliado ya registrado en la base de datos local (Duplicado)';
                $row['apto'] = false;
                $resumen['duplicados']++;
                $resumen['rechazados']++;
            } else {
                // Validar en UnipagoMockService
                $disp = UnipagoMockService::consultarDisponibilidadAfiliacion($row['cedula'] ?? '', $row['nss'] ?? null);
                
                $row['preclasificacion'] = $disp['status'];
                $row['motivo'] = $disp['motivo_descripcion'];
                $row['apto'] = $disp['apto'];

                if ($disp['apto']) {
                    $resumen['aptos']++;
                } else {
                    $resumen['rechazados']++;
                }
            }

            $registros[] = $row;
        }

        // Guardar en sesión para procesar en el siguiente paso
        session(['carga_masiva_preview' => $registros]);
        session(['carga_masiva_resumen' => $resumen]);

        return view('ars.afiliados.carga-masiva-preview', compact('registros', 'resumen'));
    }

    /**
     * Confirma la carga masiva y genera el lote con los registros aptos.
     */
    public function cargaMasivaProcesar(Request $request)
    {
        $preview = session('carga_masiva_preview');
        $resumen = session('carga_masiva_resumen');

        if (!$preview) {
            return redirect()->route('ars.carga.masiva')->with('error', 'No hay datos en sesión para procesar.');
        }

        $tipoIdCed = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CED')->first()->id;
        $itemsLote = [];

        foreach ($preview as $row) {
            // Evitar duplicados si ya existen en la base de datos (por si se reenvía el formulario)
            $cedulaClean = preg_replace('/[^0-9]/', '', $row['cedula'] ?? '');
            $nssClean = preg_replace('/[^0-9]/', '', $row['nss'] ?? '');
            
            $existe = false;
            if (!empty($cedulaClean)) {
                $existe = Afiliado::where('cedula', $cedulaClean)->first();
            }
            if (!$existe && !empty($nssClean)) {
                $existe = Afiliado::where('nss', $nssClean)->first();
            }

            if ($existe) {
                // Si ya existe y es APTO, agregarlo al lote pero no volver a crearlo
                if ($row['apto']) {
                    $itemsLote[] = [
                        'type' => 'titular',
                        'id' => $existe->id
                    ];
                }
                continue;
            }

            // Guardar en la base de datos (tanto aptos como no aptos, pero con estado correspondiente)
            $estado = 'Pendiente'; // Todos inician pendiente en base local
            $motivo = 'Preclasificación Unipago: ' . $row['motivo'];

            $afiliado = Afiliado::create([
                'tipo_identificacion_id' => $tipoIdCed,
                'cedula' => $row['cedula'],
                'nss' => $row['nss'] ?? null,
                'nombres' => $row['nombres'],
                'primer_apellido' => $row['primer_apellido'],
                'segundo_apellido' => $row['segundo_apellido'] ?? null,
                'fecha_nacimiento' => $row['fecha_nacimiento'],
                'sexo' => $row['sexo'],
                'estado_afiliacion' => $estado,
                'motivo_estado' => $motivo,
                'activo_nomina' => $row['apto'],
                'tiene_aporte' => $row['apto'],
                'regimen_actual' => 'Contributivo',
            ]);

            // Solo los APTOS se suben al lote de afiliación activa
            if ($row['apto']) {
                $itemsLote[] = [
                    'type' => 'titular',
                    'id' => $afiliado->id
                ];
            } else {
                // Los no aptos quedan en BD directamente marcados como RE (Rechazado)
                $afiliado->update([
                    'estado_afiliacion' => 'RE',
                    'motivo_estado' => 'Rechazado en prevalidación: ' . $row['motivo']
                ]);
            }
        }

        $lote = null;
        if (count($itemsLote) > 0) {
            $lote = UnipagoMockService::generarLote('afiliacion_titulares', $itemsLote, Auth::id());
        }

        // Limpiar sesión
        session()->forget(['carga_masiva_preview', 'carga_masiva_resumen']);

        Bitacora::registrar('Afiliados', "Procesada carga masiva. Total cargados: {$resumen['total']}. Generado lote " . ($lote ? $lote->numero_lote : 'N/A') . " con " . count($itemsLote) . " titulares aptos.");

        return view('ars.afiliados.carga-masiva-resumen', compact('resumen', 'lote'));
    }

    /**
     * Pantalla de listado de lotes.
     */
    public function lotesIndex()
    {
        $lotes = Lote::with('creador')->orderBy('created_at', 'desc')->paginate(10);
        return view('ars.afiliados.lotes.index', compact('lotes'));
    }

    /**
     * Ver detalle del lote.
     */
    public function lotesShow($id)
    {
        $lote = Lote::with(['creador', 'detalles'])->findOrFail($id);
        return view('ars.afiliados.lotes.show', compact('lote'));
    }

    /**
     * Simula el procesamiento del lote en Unipago.
     */
    public function lotesProcesar($id)
    {
        UnipagoMockService::procesarLote($id);

        return redirect()->route('ars.lotes.show', $id)
            ->with('success', "Lote procesado exitosamente por el simulador de Unipago. Resultados actualizados.");
    }

    /**
     * Carga individual de titulares (Formulario)
     */
    public function solicitudTitularNueva()
    {
        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $regimenes = Catalogo::getByGrupo('tipo_regimen');
        return view('ars.afiliados.titulares.nuevo', compact('tiposIdentificacion', 'regimenes'));
    }

    /**
     * Almacena solicitud de titular
     */
    public function solicitudTitularGuardar(Request $request)
    {
        $request->validate([
            'tipo_identificacion_id' => 'required',
            'cedula' => 'required|string',
            'nombres' => 'required|string',
            'primer_apellido' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:1',
            'regimen_actual' => 'required|string',
        ]);

        $contractNumber = $request->contract_number;
        $contractNumberId = null;

        if ($contractNumber) {
            $numRecord = \App\Models\AffiliationContractNumber::where('contract_number', $contractNumber)->first();
            if (!$numRecord || $numRecord->status !== 'disponible') {
                $nextNumber = \App\Services\AffiliationContractNumberService::getNextAvailableNumber();
                if ($nextNumber) {
                    $contractNumberId = $nextNumber->id;
                    $contractNumber = $nextNumber->contract_number;
                    session()->flash('warning', "El número de contrato '{$request->contract_number}' no estaba disponible. Se asignó automáticamente el contrato disponible '{$contractNumber}'.");
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "El número de contrato/formulario '{$contractNumber}' no está disponible y no hay contratos disponibles en el inventario.")
                        ->with('current_step', 2);
                }
            } else {
                $contractNumberId = $numRecord->id;
            }
        } else {
            $nextNumber = \App\Services\AffiliationContractNumberService::getNextAvailableNumber();
            if ($nextNumber) {
                $contractNumberId = $nextNumber->id;
                $contractNumber = $nextNumber->contract_number;
            }
        }

        $reqNum = 'REQ-TIT-' . date('Ymd') . '-' . rand(1000, 9999);
        
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => $request->tipo_identificacion_id,
            'contract_number_id' => $contractNumberId,
            'contract_number' => $contractNumber,
            'cedula' => $request->cedula,
            'nss' => $request->nss,
            'nombres' => $request->nombres,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'provincia' => $request->provincia,
            'municipio' => $request->municipio,
            'sector' => $request->sector,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'regimen_actual' => $request->regimen_actual,
            'estado_afiliacion' => 'PE',
        ]);

        $solicitud = \App\Models\HolderAffiliationRequest::create([
            'request_number' => $reqNum,
            'affiliate_id' => $afiliado->id,
            'contract_number_id' => $contractNumberId,
            'contract_number' => $contractNumber,
            'employer_name' => $request->employer_name,
            'employer_rnc' => $request->employer_rnc,
            'salary_amount' => $request->salary_amount ?: 0,
            'regime_type' => $request->regimen_actual,
            'status' => 'borrador',
            'created_by' => Auth::id() ?: 1,
        ]);

        if ($contractNumberId) {
            $numRecord = \App\Models\AffiliationContractNumber::find($contractNumberId);
            $numRecord->update([
                'status' => 'reservado',
                'assigned_to_user_id' => Auth::id() ?: 1,
                'assigned_to_affiliate_id' => $afiliado->id,
                'reservation_token' => $reqNum,
                'reserved_at' => now(),
            ]);
        }

        Bitacora::registrar('Afiliados', "Creada solicitud de titular individual {$reqNum} en estado Borrador.");

        return redirect()->route('ars.solicitudes.titulares.index')
            ->with('success', "Solicitud individual {$reqNum} creada correctamente en estado Borrador. Contrato reservado: " . ($contractNumber ?: 'Ninguno'));
    }

    /**
     * Bandeja de solicitudes de titulares
     */
    public function solicitudesTitularesIndex(Request $request)
    {
        $status = $request->get('status', 'borrador');
        $solicitudes = \App\Models\HolderAffiliationRequest::with(['affiliate', 'contractNumber', 'creator'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('ars.afiliados.titulares.solicitudes-index', compact('solicitudes', 'status'));
    }

    /**
     * Detalle de solicitud de titular
     */
    public function solicitudTitularVer($id)
    {
        $solicitud = \App\Models\HolderAffiliationRequest::with(['affiliate.tipoIdentificacion', 'contractNumber', 'documents', 'creator'])->findOrFail($id);
        return view('ars.afiliados.titulares.solicitudes-show', compact('solicitud'));
    }

    /**
     * Enviar solicitud titular individual a Unipago
     */
    public function solicitudTitularEnviarUnipago($id)
    {
        $solicitud = \App\Models\HolderAffiliationRequest::with(['affiliate', 'contractNumber'])->findOrFail($id);
        $afiliado = $solicitud->affiliate;

        $solicitud->update(['status' => 'enviado_unipago', 'sent_at' => now()]);

        // Simular validación
        $lookup = UnipagoMockService::consultarCiudadanoDB($afiliado->cedula);
        $code = $lookup['codigo_respuesta'] ?? 'PE75';
        $desc = $lookup['motivo'] ?? 'Sin datos';

        if ($code === 'OK') {
            $solicitud->update([
                'status' => 'procesado_ok',
                'processed_at' => now(),
                'unipago_response_code' => 'OK',
                'unipago_response_message' => $desc,
            ]);

            $afiliado->update([
                'estado_afiliacion' => 'OK',
                'activo_nomina' => true,
                'tiene_aporte' => true,
            ]);

            if ($solicitud->contract_number_id) {
                $contract = \App\Models\AffiliationContractNumber::find($solicitud->contract_number_id);
                if ($contract) $contract->update(['status' => 'ok', 'used_at' => now()]);
            }

            // Registrar grupo familiar
            \App\Models\FamilyGroup::firstOrCreate(['holder_affiliate_id' => $afiliado->id]);

            // Generar cápita individual
            UnipagoMockService::generarCapitaPendiente($afiliado->id, 'titular');

            return redirect()->route('ars.solicitudes.titulares.show', $id)
                ->with('success', "La solicitud de afiliación fue aceptada por Unipago. El afiliado se encuentra Activo.");
        } else {
            $solicitud->update([
                'status' => 'rechazado_re',
                'processed_at' => now(),
                'unipago_response_code' => $code,
                'unipago_response_message' => $desc,
            ]);

            $afiliado->update([
                'estado_afiliacion' => 'RE',
                'motivo_estado' => $desc,
            ]);

            if ($solicitud->contract_number_id) {
                $contract = \App\Models\AffiliationContractNumber::find($solicitud->contract_number_id);
                if ($contract) $contract->update(['status' => 're', 'block_reason' => $desc]);
            }

            return redirect()->route('ars.solicitudes.titulares.show', $id)
                ->with('error', "La solicitud fue rechazada por Unipago: " . $desc);
        }
    }

    /**
     * Carga individual de dependientes
     */
    public function solicitudDependienteNueva()
    {
        $tiposIdentificacion = Catalogo::getByGrupo('tipo_identificacion');
        $parentescos = Catalogo::getByGrupo('parentesco');
        return view('ars.afiliados.dependientes.nuevo', compact('tiposIdentificacion', 'parentescos'));
    }

    /**
     * Guarda la solicitud de dependiente
     */
    public function solicitudDependienteGuardar(Request $request)
    {
        $request->validate([
            'holder_id' => 'required|exists:afiliados,id',
            'tipo_identificacion_id' => 'required',
            'cedula' => 'required|string',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|max:1',
            'relationship' => 'required|string',
        ]);

        $titular = Afiliado::findOrFail($request->holder_id);

        $reqNum = 'REQ-DEP-' . date('Ymd') . '-' . rand(1000, 9999);

        // Crear dependiente
        $dep = Dependiente::create([
            'titular_id' => $titular->id,
            'tipo_identificacion_id' => $request->tipo_identificacion_id,
            'parentesco_id' => Catalogo::where('grupo', 'parentesco')->where('codigo', $request->relationship)->first()?->id ?? 2,
            'cedula' => $request->cedula,
            'nss' => $request->nss,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'tipo_dependiente' => 'Directo',
            'estado_afiliacion' => 'PE',
        ]);

        $solicitud = \App\Models\DependentAffiliationRequest::create([
            'request_number' => $reqNum,
            'holder_affiliate_id' => $titular->id,
            'dependent_affiliate_id' => $dep->id,
            'relationship' => $request->relationship,
            'document_type' => 'Copia de Identificación',
            'document_number' => $request->cedula,
            'status' => 'enviado_unipago',
            'sent_at' => now(),
            'created_by' => Auth::id() ?: 1,
        ]);

        // Simular llamada a Unipago
        $lookup = UnipagoMockService::consultarCiudadanoDB($dep->cedula);
        $code = $lookup['codigo_respuesta'] ?? 'PE75';
        $desc = $lookup['motivo'] ?? 'Sin datos';

        if ($code === 'OK') {
            $solicitud->update([
                'status' => 'procesado_ok',
                'processed_at' => now(),
                'unipago_response_code' => 'OK',
                'unipago_response_message' => $desc,
            ]);

            $dep->update(['estado_afiliacion' => 'OK']);

            // Agregar al grupo familiar
            $grupo = \App\Models\FamilyGroup::firstOrCreate(['holder_affiliate_id' => $titular->id]);
            \App\Models\FamilyGroupMember::create([
                'family_group_id' => $grupo->id,
                'affiliate_id' => $dep->id,
                'relationship' => $request->relationship,
                'status' => 'activo',
                'start_date' => now()->toDateString(),
            ]);

            // Generar cápita
            UnipagoMockService::generarCapitaPendiente($dep->id, 'dependiente');

            return redirect()->route('ars.titulares.show', $titular->id)
                ->with('success', "Dependiente afiliado correctamente. La solicitud fue aprobada por Unipago.");
        } else {
            $solicitud->update([
                'status' => 'rechazado_re',
                'processed_at' => now(),
                'unipago_response_code' => $code,
                'unipago_response_message' => $desc,
            ]);

            $dep->update([
                'estado_afiliacion' => 'RE',
                'motivo_estado' => $desc,
            ]);

            return redirect()->route('ars.titulares.show', $titular->id)
                ->with('error', "La solicitud del dependiente fue rechazada por Unipago: " . $desc);
        }
    }

    /**
     * Bandeja de solicitudes de dependientes
     */
    public function solicitudesDependientesIndex(Request $request)
    {
        $status = $request->get('status', 'enviado_unipago');
        $solicitudes = \App\Models\DependentAffiliationRequest::with(['holder', 'dependent', 'creator'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('ars.afiliados.dependientes.solicitudes-index', compact('solicitudes', 'status'));
    }

    /**
     * Buscar titular AJAX para dependientes
     */
    public function buscarTitularAjax(Request $request)
    {
        $search = $request->get('q');
        if (empty($search)) {
            return response()->json([]);
        }

        $titulares = Afiliado::where('estado_afiliacion', 'OK')
            ->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('primer_apellido', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            })
            ->take(5)
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'text' => $t->cedula . ' - ' . $t->nombres . ' ' . $t->primer_apellido,
                ];
            });

        return response()->json($titulares);
    }
}
