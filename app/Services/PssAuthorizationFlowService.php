<?php

namespace App\Services;

use App\Models\Pss;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\ServicioMedico;
use App\Models\PdssService;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\PharmacyDispensation;
use App\Models\PssServiceContract;

class PssAuthorizationFlowService
{
    /**
     * Evalúa y valida una solicitud de PSS (Centro Médico, Farmacia o Laboratorio)
     */
    public static function evaluate(array $params): array
    {
        $pssId = $params['pss_id'];
        $pssType = $params['pss_type'] ?? 'medical_center'; // medical_center, pharmacy, laboratory
        $afiliadoId = $params['afiliado_id'];
        $afiliadoType = $params['afiliado_type'] ?? 'titular';
        $items = $params['service_items'] ?? []; // array of items: ['id' => X, 'requested_amount' => Y, 'quantity' => Z]

        // 1. Validar Afiliado
        $afiliado = $afiliadoType === 'titular' 
            ? Afiliado::find($afiliadoId) 
            : Dependiente::find($afiliadoId);

        if (!$afiliado) {
            return self::makeResponse('rejected', 'Afiliado no encontrado en los registros de la ARS.');
        }

        if ($afiliado->estado_afiliacion !== 'OK' && $afiliado->estado_afiliacion !== 'Activo') {
            return self::makeResponse('rejected', 'El afiliado no se encuentra activo o tiene estado rechazado.');
        }

        // 2. Validar PSS
        $pss = Pss::find($pssId);
        if (!$pss) {
            return self::makeResponse('rejected', 'La PSS proveedora no está registrada.');
        }

        if ($pss->estado !== 'Activa' && $pss->status !== 'activo') {
            return self::makeResponse('rejected', 'La PSS seleccionada no se encuentra activa.');
        }

        // 3. Validar Contrato de la PSS
        $contrato = ContratoPss::where('pss_id', $pss->id)->where('estado', 'Activo')->first();
        if (!$contrato) {
            $contrato = PssServiceContract::where('pss_id', $pss->id)->where('is_active', true)->first();
        }
        if (!$contrato) {
            return self::makeResponse('rejected', 'La PSS no posee un contrato activo vigente con la ARS.');
        }

        // 4. Evaluar según el tipo de PSS
        if ($pssType === 'pharmacy') {
            return self::evaluatePharmacy($pss, $afiliado, $items);
        } elseif ($pssType === 'laboratory') {
            return self::evaluateLaboratory($pss, $afiliado, $items);
        } else {
            return self::evaluateMedicalCenter($pss, $afiliado, $items);
        }
    }

    /**
     * Evaluación específica para Farmacias
     */
    private static function evaluatePharmacy(Pss $pss, $afiliado, array $items): array
    {
        $mensajes = [];
        $totalMontoSolicitado = 0;
        $totalMontoContratado = 0;
        $totalMontoAutorizado = 0;
        $totalCopago = 0;
        $totalMontoNoCubierto = 0;

        $requiresAuth = false;
        $requiresAudit = false;
        $requiresDoc = false;

        // Límite anual para medicamentos ambulatorios: DOP 8,000.00
        $limiteAnual = 8000.00;
        $consumidoAnual = PharmacyDispensation::where('afiliado_id', $afiliado->id)
            ->whereYear('dispensed_at', now()->year)
            ->where('status', 'Dispensada')
            ->sum('ars_amount');
        
        $disponibleAnual = max(0.00, $limiteAnual - $consumidoAnual);

        foreach ($items as $item) {
            $requestedPrice = parseFloat($item['requested_amount'] ?? 0);
            $qty = intval($item['quantity'] ?? 1);
            $totalItemPrice = $requestedPrice * $qty;
            $totalMontoSolicitado += $totalItemPrice;

            // Buscar medicamento en la base de datos (por código o ID)
            $service = ServicioMedico::where('codigo', $item['medicine_code'] ?? '')
                ->orWhere('id', $item['id'] ?? 0)
                ->first();

            if (!$service) {
                // Si no se encuentra, ver si es un servicio PDSS
                $service = PdssService::where('simon_code', $item['medicine_code'] ?? '')
                    ->orWhere('id', $item['id'] ?? 0)
                    ->first();
            }

            if (!$service) {
                $totalMontoNoCubierto += $totalItemPrice;
                $mensajes[] = "Medicamento {$item['medicine_name']} no catalogado en el PDSS (Sin Cobertura).";
                continue;
            }

            // Para farmacias, la cobertura base suele ser 70%
            $pctCobertura = 0.70;
            $montoMaxCoberturable = $totalItemPrice * $pctCobertura;

            // Validar tope anual de medicamentos ambulatorios
            if ($montoMaxCoberturable > $disponibleAnual) {
                $excedente = $montoMaxCoberturable - $disponibleAnual;
                $montoMaxCoberturable = $disponibleAnual;
                $disponibleAnual = 0; // Límite agotado para siguientes items
                $mensajes[] = "Tope anual de medicamentos excedido. Monto no cubierto por límite: DOP " . number_format($excedente, 2);
            } else {
                $disponibleAnual -= $montoMaxCoberturable;
            }

            $arsCovered = $montoMaxCoberturable;
            $copay = $totalItemPrice - $arsCovered;

            $totalMontoContratado += $totalItemPrice;
            $totalMontoAutorizado += $arsCovered;
            $totalCopago += $copay;

            if ($service instanceof ServicioMedico && $service->es_alto_costo) {
                $requiresAudit = true;
                $mensajes[] = "Medicamento de Alto Costo: {$service->descripcion} requiere auditoría médica especializada.";
            }

            if ($service instanceof ServicioMedico && $service->requiere_documento) {
                $requiresDoc = true;
            }
        }

        $result = 'approved';
        if ($requiresAudit) {
            $result = 'audit';
        } elseif ($requiresDoc) {
            $result = 'pending_document';
        }

        return [
            'result' => $result,
            'monto_solicitado' => $totalMontoSolicitado,
            'monto_contratado' => $totalMontoContratado,
            'monto_autorizado' => $totalMontoAutorizado,
            'copago' => $totalCopago,
            'monto_no_cubierto' => $totalMontoNoCubierto,
            'requiere_autorizacion' => $requiresAuth || $requiresAudit,
            'requiere_auditoria' => $requiresAudit,
            'requiere_documento' => $requiresDoc,
            'estado_sugerido' => $result === 'approved' ? 'Aprobada' : ($result === 'audit' ? 'En auditoría' : 'Pendiente Documento'),
            'mensajes' => $mensajes,
        ];
    }

    /**
     * Evaluación específica para Laboratorios / Centros de Diagnóstico
     */
    private static function evaluateLaboratory(Pss $pss, $afiliado, array $items): array
    {
        $mensajes = [];
        $totalMontoSolicitado = 0;
        $totalMontoContratado = 0;
        $totalMontoAutorizado = 0;
        $totalCopago = 0;
        $totalMontoNoCubierto = 0;

        $requiresAuth = false;
        $requiresAudit = false;
        $requiresDoc = false;

        // Obtener tarifario contratado para esta PSS
        $contrato = ContratoPss::where('pss_id', $pss->id)->where('estado', 'Activo')->first();
        $tarifas = $contrato 
            ? TarifaPss::where('contrato_pss_id', $contrato->id)->pluck('monto_tarifa', 'servicio_medico_id')->toArray()
            : [];

        foreach ($items as $item) {
            $requestedPrice = parseFloat($item['requested_amount'] ?? 0);
            $totalMontoSolicitado += $requestedPrice;

            // Buscar servicio médico
            $service = ServicioMedico::where('codigo', $item['simon_code_snapshot'] ?? '')
                ->orWhere('id', $item['id'] ?? 0)
                ->first();

            if (!$service) {
                // Ver si es un servicio PDSS
                $service = PdssService::where('simon_code', $item['simon_code_snapshot'] ?? '')
                    ->orWhere('id', $item['id'] ?? 0)
                    ->first();
            }

            if (!$service) {
                $totalMontoNoCubierto += $requestedPrice;
                $mensajes[] = "Prueba {$item['test_name']} no está catalogada en el PDSS.";
                continue;
            }

            // Obtener tarifa contratada
            $tarifa = $service instanceof ServicioMedico ? ($tarifas[$service->id] ?? null) : null;
            if (!$tarifa) {
                // Buscar en contratos reales de PSS
                $realContract = PssServiceContract::where('pss_id', $pss->id)->where('is_active', true)->first();
                if ($realContract) {
                    $tarifa = \App\Models\PssServiceContract::where('pss_id', $pss->id)
                        ->where('is_active', true)
                        ->pluck('contracted_amount', 'pdss_service_id')
                        ->get($service->id);
                }
            }

            if (!$tarifa) {
                $tarifa = 1500.00; // default tarif
            }

            // Cobertura del examen de apoyo diagnóstico suele ser 80% o 85%
            $pctCobertura = ($service->cobertura_base ?? 80.00) / 100;
            $montoBase = min($requestedPrice, $tarifa);
            $arsCovered = $montoBase * $pctCobertura;
            $copay = $requestedPrice - $arsCovered;

            $totalMontoContratado += $tarifa;
            $totalMontoAutorizado += $arsCovered;
            $totalCopago += $copay;

            if ($requestedPrice > $tarifa) {
                $excedente = $requestedPrice - $tarifa;
                $mensajes[] = "Monto de la prueba {$service->descripcion} excede la tarifa contratada. Diferencia de DOP " . number_format($excedente, 2) . " a cargo del afiliado.";
            }

            if ($service instanceof ServicioMedico && $service->es_alto_costo) {
                $requiresAudit = true;
                $mensajes[] = "Prueba especial de Alto Costo: {$service->descripcion} requiere auditoría médica previa.";
            }

            if ($service instanceof ServicioMedico && $service->requiere_documento) {
                $requiresDoc = true;
            }
        }

        $result = 'approved';
        if ($requiresAudit) {
            $result = 'audit';
        } elseif ($requiresDoc) {
            $result = 'pending_document';
        }

        return [
            'result' => $result,
            'monto_solicitado' => $totalMontoSolicitado,
            'monto_contratado' => $totalMontoContratado,
            'monto_autorizado' => $totalMontoAutorizado,
            'copago' => $totalCopago,
            'monto_no_cubierto' => $totalMontoNoCubierto,
            'requiere_autorizacion' => $requiresAuth || $requiresAudit,
            'requiere_auditoria' => $requiresAudit,
            'requiere_documento' => $requiresDoc,
            'estado_sugerido' => $result === 'approved' ? 'Aprobada' : ($result === 'audit' ? 'En auditoría' : 'Pendiente Documento'),
            'mensajes' => $mensajes,
        ];
    }

    /**
     * Evaluación específica para Centro Médicos (Clínicas/Hospitales)
     */
    private static function evaluateMedicalCenter(Pss $pss, $afiliado, array $items): array
    {
        // Reutiliza la lógica estándar para Centro Médico
        $mensajes = [];
        $totalMontoSolicitado = 0;
        $totalMontoContratado = 0;
        $totalMontoAutorizado = 0;
        $totalCopago = 0;
        $totalMontoNoCubierto = 0;

        $requiresAuth = false;
        $requiresAudit = false;
        $requiresDoc = false;

        $contrato = ContratoPss::where('pss_id', $pss->id)->where('estado', 'Activo')->first();
        $tarifas = $contrato 
            ? TarifaPss::where('contrato_pss_id', $contrato->id)->pluck('monto_tarifa', 'servicio_medico_id')->toArray()
            : [];

        foreach ($items as $item) {
            $requestedPrice = parseFloat($item['requested_amount'] ?? 0);
            $totalMontoSolicitado += $requestedPrice;

            $service = ServicioMedico::find($item['id'] ?? 0);
            if (!$service) {
                $totalMontoNoCubierto += $requestedPrice;
                continue;
            }

            $tarifa = $tarifas[$service->id] ?? 1500.00;
            $pctCobertura = ($service->cobertura_base ?? 80.00) / 100;
            $montoBase = min($requestedPrice, $tarifa);
            $arsCovered = $montoBase * $pctCobertura;
            $copay = $requestedPrice - $arsCovered;

            $totalMontoContratado += $tarifa;
            $totalMontoAutorizado += $arsCovered;
            $totalCopago += $copay;

            if ($service->es_alto_costo) {
                $requiresAudit = true;
            }
            if ($service->requiere_documento) {
                $requiresDoc = true;
            }
        }

        $result = 'approved';
        if ($requiresAudit) {
            $result = 'audit';
        } elseif ($requiresDoc) {
            $result = 'pending_document';
        }

        return [
            'result' => $result,
            'monto_solicitado' => $totalMontoSolicitado,
            'monto_contratado' => $totalMontoContratado,
            'monto_autorizado' => $totalMontoAutorizado,
            'copago' => $totalCopago,
            'monto_no_cubierto' => $totalMontoNoCubierto,
            'requiere_autorizacion' => $requiresAuth || $requiresAudit,
            'requiere_auditoria' => $requiresAudit,
            'requiere_documento' => $requiresDoc,
            'estado_sugerido' => $result === 'approved' ? 'Aprobada' : ($result === 'audit' ? 'En auditoría' : 'Pendiente Documento'),
            'mensajes' => $mensajes,
        ];
    }

    /**
     * Construye una respuesta estandarizada de rechazo
     */
    private static function makeResponse(string $result, string $message): array
    {
        return [
            'result' => $result,
            'monto_solicitado' => 0.00,
            'monto_contratado' => 0.00,
            'monto_autorizado' => 0.00,
            'copago' => 0.00,
            'monto_no_cubierto' => 0.00,
            'requiere_autorizacion' => false,
            'requiere_auditoria' => false,
            'requiere_documento' => false,
            'estado_sugerido' => 'Rechazada',
            'mensajes' => [$message],
        ];
    }
}

// Helper to safely parse decimal values
if (!function_exists('parseFloat')) {
    function parseFloat($val) {
        return floatval(str_replace(',', '', $val));
    }
}
