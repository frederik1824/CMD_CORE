<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Lote;
use App\Models\LoteDetalle;
use App\Models\AffiliationBatch;
use App\Models\AffiliationBatchDetail;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\DispersionCutDetail;
use App\Models\UnipagoMockRequest;
use App\Models\UnipagoMockNotification;
use App\Models\UnipagoMockScenario;
use App\Models\UnipagoResponseCode;
use App\Models\HolderAffiliationRequest;
use App\Models\DependentAffiliationRequest;
use App\Models\AffiliationContractNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class UnipagoMockService
{
    /**
     * Consulta un ciudadano buscando PRIMERO en la BD real de afiliados,
     * luego aplica reglas de simulación para ciudadanos no registrados.
     */
    public static function consultarCiudadanoDB(string $cedula): array
    {
        $cleanCedula = preg_replace('/[^0-9]/', '', $cedula);

        self::registrarMockCall('CONSULTA_CIUDADANO', 'Consulta Ciudadano', '/api/unipago/ciudadano', ['cedula' => $cleanCedula]);

        // 1. Buscar en escenarios configurados (Custom Scenarios)
        $scenario = UnipagoMockScenario::where('service_code', 'CONSULTA_CIUDADANO')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->first(function($sc) use ($cleanCedula) {
                $cond = $sc->conditions;
                return isset($cond['cedula']) && $cond['cedula'] === $cleanCedula;
            });

        if ($scenario) {
            return $scenario->response_payload_template;
        }

        // 2. Buscar en afiliados titulares
        $afiliado = Afiliado::where('cedula', $cleanCedula)->first();
        if ($afiliado) {
            $edad = $afiliado->fecha_nacimiento ? Carbon::parse($afiliado->fecha_nacimiento)->age : null;
            $regimen = $afiliado->regimen_actual ?? 'Contributivo';

            return [
                'found_in_db'       => true,
                'type'              => 'titular',
                'exists'            => true,
                'cedula'            => $cleanCedula,
                'nss'               => $afiliado->nss ?? 'N/D',
                'nui'               => $afiliado->nui ?? 'N/D',
                'nombres'           => $afiliado->nombres,
                'primer_apellido'   => $afiliado->primer_apellido,
                'segundo_apellido'  => $afiliado->segundo_apellido ?? '',
                'nombre_completo'   => trim("{$afiliado->nombres} {$afiliado->primer_apellido} " . ($afiliado->segundo_apellido ?? '')),
                'sexo'              => $afiliado->sexo ?? 'N/D',
                'edad'              => $edad,
                'fecha_nacimiento'  => $afiliado->fecha_nacimiento ? Carbon::parse($afiliado->fecha_nacimiento)->format('d/m/Y') : 'N/D',
                'regimen'           => $regimen,
                'es_subsidiado'     => $regimen === 'Subsidiado',
                'activo_nomina'     => (bool) $afiliado->activo_nomina,
                'tiene_aporte'      => (bool) $afiliado->tiene_aporte,
                'estado_afiliacion' => $afiliado->estado_afiliacion ?? 'N/D',
                'status'            => $afiliado->estado_afiliacion === 'OK' ? 'Activo' : ($afiliado->estado_afiliacion ?? 'Pendiente'),
                'numero_contrato'   => $afiliado->contract_number ?? 'Sin contrato',
                'ultimo_periodo'    => $afiliado->ultimo_periodo_pagado ?? 'N/D',
                'provincia'         => $afiliado->provincia ?? 'N/D',
                'dependientes_count'=> $afiliado->dependientes()->count(),
                'clasificacion'     => self::clasificarAfiliado($afiliado),
                'codigo_respuesta'  => self::codigoRespuesta($afiliado),
                'motivo'            => self::motivoClasificacion($afiliado),
            ];
        }

        // 3. Buscar en dependientes
        $dep = Dependiente::where('cedula', $cleanCedula)->with('titular')->first();
        if ($dep) {
            $edad = $dep->fecha_nacimiento ? Carbon::parse($dep->fecha_nacimiento)->age : null;
            return [
                'found_in_db'       => true,
                'type'              => 'dependiente',
                'exists'            => true,
                'cedula'            => $cleanCedula,
                'nss'               => $dep->nss ?? 'N/D',
                'nui'               => 'N/D',
                'nombres'           => $dep->nombres,
                'primer_apellido'   => $dep->apellidos ?? '',
                'segundo_apellido'  => '',
                'nombre_completo'   => trim("{$dep->nombres} " . ($dep->apellidos ?? '')),
                'sexo'              => $dep->sexo ?? 'N/D',
                'edad'              => $edad,
                'fecha_nacimiento'  => $dep->fecha_nacimiento ? Carbon::parse($dep->fecha_nacimiento)->format('d/m/Y') : 'N/D',
                'regimen'           => 'Contributivo (Dependiente)',
                'es_subsidiado'     => false,
                'activo_nomina'     => false,
                'tiene_aporte'      => false,
                'estado_afiliacion' => $dep->estado_afiliacion ?? 'N/D',
                'status'            => $dep->estado_afiliacion === 'OK' ? 'Activo' : ($dep->estado_afiliacion ?? 'Pendiente'),
                'numero_contrato'   => 'Titular: ' . ($dep->titular?->contract_number ?? 'N/D'),
                'ultimo_periodo'    => 'N/D',
                'provincia'         => 'N/D',
                'dependientes_count'=> 0,
                'clasificacion'     => $dep->estado_afiliacion === 'OK' ? 'Apto (Dependiente)' : 'Pendiente de verificación',
                'codigo_respuesta'  => $dep->estado_afiliacion === 'OK' ? 'OK' : 'PE64',
                'motivo'            => $dep->estado_afiliacion === 'OK' ? 'Dependiente activo en el sistema' : 'Dependiente pendiente de individualización',
            ];
        }

        // 4. No está en BD — simulación por dígito final
        return self::simularCiudadanoExterno($cleanCedula);
    }

    /**
     * Simula respuesta de Unipago para cédulas no registradas en la BD local.
     */
    private static function simularCiudadanoExterno(string $cedula): array
    {
        $base = [
            'found_in_db'       => false,
            'type'              => 'externo',
            'cedula'            => $cedula,
            'nss'               => 'N/D',
            'nui'               => 'N/D',
            'nombre_completo'   => 'CIUDADANO NO REGISTRADO',
            'nombres'           => 'CIUDADANO',
            'primer_apellido'   => 'SIM-' . substr($cedula, -4),
            'segundo_apellido'  => '',
            'sexo'              => 'N/D',
            'edad'              => null,
            'fecha_nacimiento'  => 'N/D',
            'activo_nomina'     => false,
            'tiene_aporte'      => false,
            'ultimo_periodo'    => 'N/D',
            'provincia'         => 'Santo Domingo',
            'dependientes_count'=> 0,
            'numero_contrato'   => 'N/A',
        ];

        $last = substr($cedula, -1);

        $scenarios = [
            '9' => ['exists' => false, 'status' => 'No Existe', 'regimen' => 'N/D', 'es_subsidiado' => false, 'estado_afiliacion' => 'N/E', 'clasificacion' => 'Inactivo en maestro', 'codigo_respuesta' => 'PE75', 'motivo' => 'Ciudadano no existe en el Maestro de Ciudadanos de la JCE'],
            '8' => ['exists' => true, 'status' => 'Fallecido', 'regimen' => 'N/D', 'es_subsidiado' => false, 'estado_afiliacion' => 'FA', 'clasificacion' => 'Fallecido', 'codigo_respuesta' => 'RE', 'motivo' => 'Ciudadano registrado como fallecido en el padrón de la JCE'],
            '7' => ['exists' => true, 'status' => 'Activo en Otra ARS', 'regimen' => 'Contributivo', 'es_subsidiado' => false, 'estado_afiliacion' => 'OA', 'clasificacion' => 'En otra ARS', 'codigo_respuesta' => 'RE001', 'motivo' => 'Ciudadano afiliado a ARS Competidora - Traspaso no autorizado'],
            '6' => ['exists' => true, 'status' => 'Activo', 'regimen' => 'Subsidiado', 'es_subsidiado' => true, 'estado_afiliacion' => 'SB', 'clasificacion' => 'En subsidiado', 'codigo_respuesta' => 'RE', 'motivo' => 'Pertenece al Régimen Subsidiado SENASA - Incompatible con contributivo'],
            '5' => ['exists' => true, 'status' => 'Sin Nómina', 'regimen' => 'Contributivo', 'es_subsidiado' => false, 'activo_nomina' => false, 'estado_afiliacion' => 'PE', 'clasificacion' => 'Sin nómina', 'codigo_respuesta' => 'PE64', 'motivo' => 'No se encuentra registrado en la nómina activa de la TSS'],
            '4' => ['exists' => true, 'status' => 'Aporte insuficiente', 'regimen' => 'Contributivo', 'es_subsidiado' => false, 'activo_nomina' => true, 'tiene_aporte' => false, 'estado_afiliacion' => 'PE', 'clasificacion' => 'Sin aporte', 'codigo_respuesta' => 'PE64', 'motivo' => 'Aportes por debajo del salario mínimo cotizable establecido'],
        ];

        if (isset($scenarios[$last])) {
            return array_merge($base, $scenarios[$last]);
        }

        // Apto — generar datos plausibles simulados
        return array_merge($base, [
            'exists'            => true,
            'status'            => 'Apto',
            'regimen'           => 'Contributivo',
            'es_subsidiado'     => false,
            'activo_nomina'     => true,
            'tiene_aporte'      => true,
            'nombre_completo'   => 'CIUDADANO SIM-' . substr($cedula, -4),
            'nombres'           => 'SIM',
            'primer_apellido'   => 'CIUDADANO-' . substr($cedula, -4),
            'sexo'              => (intval(substr($cedula, -2, 1)) % 2 === 0) ? 'M' : 'F',
            'edad'              => rand(25, 55),
            'estado_afiliacion' => 'EXT',
            'clasificacion'     => 'Apto para cargar',
            'codigo_respuesta'  => 'OK',
            'motivo'            => 'Ciudadano verificado en JCE y TSS - Listo para procesar',
        ]);
    }

    private static function clasificarAfiliado(Afiliado $a): string
    {
        if ($a->regimen_actual === 'Subsidiado') return 'En subsidiado';
        if (!$a->activo_nomina) return 'Sin nómina';
        if (!$a->tiene_aporte) return 'Sin aporte';
        if ($a->estado_afiliacion === 'OK') return 'Apto para cargar';
        return 'Pendiente de verificación';
    }

    private static function codigoRespuesta(Afiliado $a): string
    {
        if ($a->regimen_actual === 'Subsidiado') return 'RE';
        if (!$a->activo_nomina || !$a->tiene_aporte) return 'PE64';
        if ($a->estado_afiliacion === 'OK') return 'OK';
        return 'PE75';
    }

    private static function motivoClasificacion(Afiliado $a): string
    {
        if ($a->regimen_actual === 'Subsidiado') return 'Pertenece al Régimen Subsidiado';
        if (!$a->activo_nomina) return 'No registrado en nómina activa de TSS';
        if (!$a->tiene_aporte) return 'Aportes por debajo del salario mínimo';
        if ($a->estado_afiliacion === 'OK') return 'Afiliado activo y verificado en padrón';
        return 'Pendiente de individualización con Unipago';
    }

    /**
     * Prevalida un lote de afiliaciones buscando datos reales en la BD.
     */
    public static function prevalidarLote(array $records): array
    {
        $results = [];
        foreach ($records as $index => $rec) {
            $cedula = preg_replace('/[^0-9]/', '', $rec['cedula'] ?? '');

            if (empty($cedula)) {
                $results[] = array_merge($rec, [
                    'index' => $index, 'found_in_db' => false, 'clasificacion' => 'Requiere revisión',
                    'codigo_respuesta' => 'ERR', 'motivo' => 'Cédula en blanco o inválida',
                    'sexo' => 'N/D', 'edad' => null, 'regimen' => 'N/D',
                    'activo_nomina' => false, 'tiene_aporte' => false, 'es_subsidiado' => false,
                ]);
                continue;
            }

            $lookup = self::consultarCiudadanoDB($cedula);

            $nomCompleto = !empty(trim(($rec['nombres'] ?? '') . ' ' . ($rec['apellidos'] ?? '')))
                ? trim(($rec['nombres'] ?? '') . ' ' . ($rec['apellidos'] ?? ''))
                : ($lookup['nombre_completo'] ?? $cedula);

            $results[] = array_merge($rec, [
                'index'             => $index,
                'cedula'            => $cedula,
                'nombre_completo'   => $nomCompleto,
                'found_in_db'       => $lookup['found_in_db'] ?? false,
                'type'              => $lookup['type'] ?? 'externo',
                'nss'               => $lookup['nss'] ?? 'N/D',
                'sexo'              => $lookup['sexo'] ?? 'N/D',
                'edad'              => $lookup['edad'] ?? null,
                'fecha_nacimiento'  => $lookup['fecha_nacimiento'] ?? 'N/D',
                'regimen'           => $lookup['regimen'] ?? 'N/D',
                'activo_nomina'     => $lookup['activo_nomina'] ?? false,
                'tiene_aporte'      => $lookup['tiene_aporte'] ?? false,
                'es_subsidiado'     => $lookup['es_subsidiado'] ?? false,
                'estado_afiliacion' => $lookup['estado_afiliacion'] ?? 'N/D',
                'clasificacion'     => $lookup['clasificacion'] ?? 'Requiere revisión',
                'codigo_respuesta'  => $lookup['codigo_respuesta'] ?? 'ERR',
                'motivo'            => $lookup['motivo'] ?? 'Sin datos disponibles',
            ]);
        }
        return $results;
    }

    /** Alias legacy */
    public static function consultarCiudadano(string $cedula): array
    {
        return self::consultarCiudadanoDB($cedula);
    }

    /**
     * Procesa un lote de afiliación.
     * Soporta tanto Lote (Core: tabla lotes) como AffiliationBatch (Simulador: tabla affiliation_batches) de manera transparente.
     */
    public static function procesarLote($batchOrId): void
    {
        if (is_numeric($batchOrId)) {
            // Es un ID del lote de la tabla lotes del core
            $lote = Lote::with('detalles')->findOrFail($batchOrId);
            self::procesarLoteCore($lote);
            return;
        }

        if ($batchOrId instanceof Lote) {
            self::procesarLoteCore($batchOrId);
            return;
        }

        if ($batchOrId instanceof AffiliationBatch) {
            self::procesarLoteSimulador($batchOrId);
            return;
        }
    }

    /**
     * Procesa lote de la tabla 'lotes' del core (Resolución del problema de 0%).
     */
    private static function procesarLoteCore(Lote $lote): void
    {
        $lote->update(['estado_lote' => 'PC', 'fecha_procesamiento' => now()]);

        self::registrarMockCall('ENVIO_LOTE_AFILIACION', 'Envío Lote Afiliación', '/api/unipago/lote/cargar', [
            'batch_id' => $lote->id, 'batch_number' => $lote->numero_lote, 'records_count' => $lote->total_records
        ]);

        DB::transaction(function() use ($lote) {
            $detalles = $lote->detalles;
            $okCount = $rejectedCount = 0;

            foreach ($detalles as $detail) {
                $cedula = '';
                $afiliado = null;
                $dependiente = null;

                if ($detail->entidad_type === 'titular') {
                    $afiliado = Afiliado::find($detail->entidad_id);
                    $cedula = $afiliado?->cedula ?? '';
                } elseif ($detail->entidad_type === 'dependiente') {
                    $dependiente = Dependiente::find($detail->entidad_id);
                    $cedula = $dependiente?->cedula ?? '';
                }

                // Consumir o actualizar contrato
                if ($afiliado && $afiliado->contract_number_id) {
                    $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                    if ($contract) {
                        $contract->update(['status' => 'enviado_unipago', 'sent_to_unipago_at' => now(), 'unipago_lote_id' => $lote->id]);
                    }
                }

                $lookup = self::consultarCiudadanoDB($cedula);
                $code = $lookup['codigo_respuesta'] ?? 'PE75';
                $desc = $lookup['motivo'] ?? 'Sin datos';

                if ($code === 'OK') {
                    $okCount++;
                    if ($afiliado) {
                        $afiliado->update([
                            'estado_afiliacion' => 'OK',
                            'activo_nomina' => true,
                            'tiene_aporte' => true,
                        ]);
                        if ($afiliado->contract_number_id) {
                            $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                            if ($contract) $contract->update(['status' => 'ok', 'unipago_response_status' => 'OK', 'unipago_response_code' => 'OK']);
                        }
                        // Registrar Grupo Familiar e individualización
                        self::generarCapitaPendiente($afiliado->id, 'titular');
                    } elseif ($dependiente) {
                        $dependiente->update(['estado_afiliacion' => 'OK']);
                        // Registrar integrante familiar
                        $titularGroup = \App\Models\FamilyGroup::firstOrCreate(['holder_affiliate_id' => $dependiente->titular_id]);
                        \App\Models\FamilyGroupMember::updateOrCreate(
                            ['family_group_id' => $titularGroup->id, 'affiliate_id' => $dependiente->id],
                            ['relationship' => $dependiente->parentesco?->nombre ?? 'Hijo', 'status' => 'activo', 'start_date' => now()->toDateString()]
                        );
                    }
                } else {
                    $rejectedCount++;
                    if ($afiliado) {
                        $afiliado->update([
                            'estado_afiliacion' => 'RE',
                            'motivo_estado' => $desc,
                        ]);
                        if ($afiliado->contract_number_id) {
                            $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                            if ($contract) $contract->update(['status' => 're', 'unipago_response_status' => 'RE', 'unipago_response_code' => $code, 'unipago_response_message' => $desc]);
                        }
                    } elseif ($dependiente) {
                        $dependiente->update([
                            'estado_afiliacion' => 'RE',
                            'motivo_estado' => $desc,
                        ]);
                    }
                }
            }

            $finalStatus = 'EV'; // Procesado OK
            if ($rejectedCount === $lote->total_records) $finalStatus = 'RE'; // Todo rechazado
            elseif ($rejectedCount > 0) $finalStatus = 'PE'; // Con errores

            $lote->update([
                'estado_lote' => $finalStatus,
                'registros_ok' => $okCount,
                'registros_re' => $rejectedCount,
                'fecha_procesamiento' => now()
            ]);

            UnipagoMockNotification::enviar(
                'Lote procesado',
                'batch',
                $lote->id,
                'Lote de Afiliación Procesado (' . $lote->numero_lote . ')',
                "El lote fue completado. Aceptados: {$okCount}, Rechazados: {$rejectedCount}. Estatus Final: {$finalStatus}."
            );
        });
    }

    /**
     * Procesa lote de la tabla 'affiliation_batches' (Simulador).
     */
    private static function procesarLoteSimulador(AffiliationBatch $batch): void
    {
        $batch->update(['status' => 'VE']);

        self::registrarMockCall('ENVIO_LOTE_AFILIACION', 'Envío Lote Afiliación', '/api/unipago/lote/cargar', [
            'batch_id' => $batch->id, 'batch_number' => $batch->batch_number, 'records_count' => $batch->total_records
        ]);

        DB::transaction(function() use ($batch) {
            $details = $batch->details;
            $okCount = $pendingCount = $rejectedCount = 0;

            foreach ($details as $detail) {
                $cedula = '';
                $afiliado = null;
                if ($detail->afiliado_id) {
                    $afiliado = Afiliado::find($detail->afiliado_id);
                    $cedula = $afiliado?->cedula ?? '';
                } elseif ($detail->dependiente_id) {
                    $cedula = Dependiente::find($detail->dependiente_id)?->cedula ?? '';
                }

                // Registrar en contratos
                if ($afiliado && $afiliado->contract_number_id) {
                    $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                    if ($contract) {
                        $contract->update(['status' => 'enviado_unipago', 'sent_to_unipago_at' => now(), 'unipago_lote_id' => $batch->id]);
                    }
                }

                $lookup = self::consultarCiudadanoDB($cedula);
                $code = $lookup['codigo_respuesta'] ?? 'PE75';
                $desc = $lookup['motivo'] ?? 'Sin datos';

                if ($code === 'OK') {
                    $okCount++;
                    if ($afiliado) {
                        $afiliado->update([
                            'estado_afiliacion' => 'OK', 'activo_nomina' => true, 'tiene_aporte' => true
                        ]);
                        if ($afiliado->contract_number_id) {
                            $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                            if ($contract) $contract->update(['status' => 'ok', 'unipago_response_status' => 'OK', 'unipago_response_code' => 'OK']);
                        }
                    } elseif ($detail->dependiente_id) {
                        Dependiente::where('id', $detail->dependiente_id)->update(['estado_afiliacion' => 'OK']);
                    }
                } elseif ($code === 'RE' || str_contains($code, 'RE')) {
                    $rejectedCount++;
                    if ($afiliado && $afiliado->contract_number_id) {
                        $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                        if ($contract) $contract->update(['status' => 're', 'unipago_response_status' => 'RE', 'unipago_response_code' => $code, 'unipago_response_message' => $desc]);
                    }
                } else {
                    $pendingCount++;
                    if ($afiliado && $afiliado->contract_number_id) {
                        $contract = AffiliationContractNumber::find($afiliado->contract_number_id);
                        if ($contract) $contract->update(['status' => 'pe', 'unipago_response_status' => 'PE', 'unipago_response_code' => $code]);
                    }
                }

                $detail->update([
                    'status' => $code === 'OK' ? 'OK' : ($code === 'RE' ? 'RE' : $code),
                    'reason_code' => $code, 'reason_description' => $desc,
                    'raw_response' => ['processed_at' => now()->toDateTimeString(), 'unipago_transaction_id' => 'TX-' . rand(100000, 999999), 'found_in_local_db' => $lookup['found_in_db'] ?? false]
                ]);
            }

            $finalStatus = 'PC';
            if ($rejectedCount === $batch->total_records) $finalStatus = 'RE';
            elseif ($rejectedCount > 0 || $pendingCount > 0) $finalStatus = 'PE';

            $batch->update([
                'status' => $finalStatus,
                'total_ok' => $okCount,
                'total_pending' => $pendingCount,
                'total_rejected' => $rejectedCount,
                'processed_at' => now()
            ]);

            UnipagoMockNotification::enviar('Lote procesado', 'batch', $batch->id,
                'Lote de Afiliación Procesado (' . $batch->batch_number . ')',
                "Resultado {$finalStatus}. Aceptados: {$okCount}, Pendientes: {$pendingCount}, Rechazados: {$rejectedCount}."
            );

            foreach ($details as $detail) {
                if ($detail->status === 'OK' && $detail->afiliado_id) {
                    self::generarCapitaPendiente($detail->afiliado_id, 'titular');
                }
            }
        });
    }

    public static function generarCapitaPendiente(int $afiliadoId, string $type = 'titular'): void
    {
        $period = date('Ym');
        $afiliado = Afiliado::find($afiliadoId);
        $monto = $afiliado?->regimen_actual === 'Contributivo' ? 1580.75 : (($type === 'titular') ? 1450.50 : 1100.20);

        CapitationNotification::updateOrCreate(
            ['afiliado_id' => $afiliadoId, 'period' => $period],
            ['notification_number' => 'CAP-' . date('Ymd') . '-' . str_pad($afiliadoId, 6, '0', STR_PAD_LEFT) . rand(10, 99), 'capitation_amount' => $monto, 'individualization_type' => 'Capita Normal', 'status' => 'NT', 'notified_at' => now()]
        );
    }

    public static function procesarCapita(int $id, string $decision, ?string $motivo = null): void
    {
        $capita = CapitationNotification::findOrFail($id);

        if ($decision === 'confirmar') {
            $capita->update(['status' => 'IC', 'confirmed_at' => now()]);
            UnipagoMockNotification::enviar('Cápita confirmada', 'capitation', $capita->id, 'Individualización de Cápita Confirmada', "La cápita de DOP {$capita->capitation_amount} fue confirmada.");
        } else {
            $capita->update(['status' => 'IR', 'rejected_at' => now(), 'rejection_reason' => $motivo ?: 'Rechazado por inconsistencia de datos']);
            UnipagoMockNotification::enviar('Cápita rechazada', 'capitation', $capita->id, 'Individualización de Cápita Rechazada', "La cápita fue rechazada. Motivo: " . ($motivo ?: 'Sin especificar'));
        }
    }

    public static function generarCorteDispersion(string $period, string $type = 'operativo'): ?DispersionCut
    {
        $cutNum = 'DISP-' . $period . '-' . strtoupper(str_replace(' ', '-', $type)) . '-' . rand(10, 99);
        $capitas = CapitationNotification::where('period', $period)->where('status', 'IC')->get();
        if ($capitas->isEmpty()) return null;

        return DB::transaction(function() use ($cutNum, $period, $type, $capitas) {
            $totalAmount = $capitas->sum('capitation_amount');
            $holdersCount = $dependentsCount = 0;

            $cut = DispersionCut::create(['cut_number' => $cutNum, 'period' => $period, 'cut_type' => $type, 'status' => 'Generado', 'total_affiliates' => $capitas->count(), 'total_capitations' => $capitas->count(), 'total_amount' => $totalAmount, 'generated_at' => now()]);

            foreach ($capitas as $c) {
                Afiliado::find($c->afiliado_id) ? $holdersCount++ : $dependentsCount++;
                DispersionCutDetail::create(['dispersion_cut_id' => $cut->id, 'capitation_notification_id' => $c->id, 'afiliado_id' => $c->afiliado_id, 'amount' => $c->capitation_amount, 'status' => 'DI']);
                $c->update(['status' => 'DI']);
            }

            $cut->update(['total_holders' => $holdersCount, 'total_dependents' => $dependentsCount, 'status' => 'Dispersado', 'dispersed_at' => now(), 'certified_at' => now(), 'closed_at' => now()]);

            UnipagoMockNotification::enviar('Dispersión generada', 'dispersion', $cut->id, 'Corte de Dispersión Ejecutado', "Corte {$cutNum} dispersado con DOP {$totalAmount} para {$capitas->count()} cápitas.");

            return $cut;
        });
    }

    public static function consultarDisponibilidadAfiliacion(string $cedula): array
    {
        $cleanCedula = preg_replace('/[^0-9]/', '', $cedula);
        $last = substr($cleanCedula, -1);

        $apto = false;
        $motivo_codigo = 'RE';
        $motivo_descripcion = '';

        if ($last == '0' || $last == '1' || $last == '2' || $last == '3') {
            $apto = true;
            $motivo_codigo = 'OK';
            $motivo_descripcion = 'Apto para afiliación';
        } elseif ($last == '9') {
            $apto = false;
            $motivo_codigo = 'PE75';
            $motivo_descripcion = 'No existe en Maestro Ciudadanos';
        } elseif ($last == '8') {
            $apto = false;
            $motivo_codigo = 'RE';
            $motivo_descripcion = 'Ciudadano fallecido';
        } elseif ($last == '7') {
            $apto = false;
            $motivo_codigo = 'RE001';
            $motivo_descripcion = 'Ciudadano afiliado a otra ARS';
        } elseif ($last == '6') {
            $apto = false;
            $motivo_codigo = 'RE';
            $motivo_descripcion = 'Régimen Subsidiado - Incompatible';
        } else {
            $apto = false;
            $motivo_codigo = 'PE64';
            $motivo_descripcion = 'Sin nómina activa o aportes';
        }

        return [
            'apto' => $apto,
            'motivo_codigo' => $motivo_codigo,
            'motivo_descripcion' => $motivo_descripcion,
            'status' => $motivo_codigo
        ];
    }

    private static function registrarMockCall(string $serviceCode, string $serviceName, string $endpoint, array $payload): void
    {
        try {
            UnipagoMockRequest::create([
                'service_code' => $serviceCode,
                'service_name' => $serviceName,
                'endpoint_mock' => $endpoint,
                'request_payload' => $payload,
                'response_payload' => ['status' => 'success', 'code' => 200, 'response_date' => now()->toDateTimeString()],
                'status' => 'Processed',
                'created_by' => auth()->id() ?: 1,
                'processed_at' => now()
            ]);
        } catch (\Exception $e) {
            // No interrumpir el flujo si falla el log
        }
    }

    /**
     * Generar Lote desde registros de afiliación core
     */
    public static function generarLote(string $tipo, array $items, int $userId): Lote
    {
        $year = now()->year;
        $countLotes = Lote::whereYear('created_at', $year)->count();
        $loteNum = 'LOTE-CMD-' . $year . '-' . str_pad($countLotes + 1, 6, '0', STR_PAD_LEFT);

        return DB::transaction(function() use ($loteNum, $tipo, $items, $userId) {
            $lote = Lote::create([
                'numero_lote' => $loteNum,
                'tipo_lote' => $tipo,
                'estado_lote' => 'VE', // Validado Estructuralmente
                'total_records' => count($items),
                'creado_por' => $userId,
                'fecha_creacion' => now()
            ]);

            foreach ($items as $item) {
                LoteDetalle::create([
                    'lote_id' => $lote->id,
                    'entidad_type' => $item['type'],
                    'entidad_id' => $item['id']
                ]);
            }

            return $lote;
        });
    }
}
