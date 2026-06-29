<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\PdssService;
use App\Models\PdssCoverageRule;
use App\Models\PdssCoverageAccumulator;
use App\Models\Autorizacion;
use Illuminate\Support\Carbon;

class PdssCoverageEngine
{
    /**
     * Evalúa la cobertura de una solicitud de servicio médico según las reglas del PDSS 11.0.
     *
     * Retorna un array con el desglose financiero del cálculo:
     * - aprobado: boolean
     * - monto_solicitado: float
     * - monto_ars: float
     * - monto_afiliado: float
     * - copago: float
     * - exceso: float
     * - monto_no_cubierto: float
     * - exception_coverage_type: string ('SRL' | 'FONAMAT' | 'N/A')
     * - motivo: string
     */
    public static function evaluarServicio(Afiliado $afiliado, $service, float $montoSolicitado, string $tipoExcepcion = 'N/A'): array
    {
        // Inicializar desglose
        $resultado = [
            'aprobado' => true,
            'monto_solicitado' => $montoSolicitado,
            'monto_ars' => 0.0,
            'monto_afiliado' => 0.0,
            'copago' => 0.0,
            'exceso' => 0.0,
            'monto_no_cubierto' => 0.0,
            'exception_coverage_type' => $tipoExcepcion,
            'motivo' => 'Aprobado según catálogo PDSS 11.0.'
        ];

        // 1. Validar derivaciones especiales (Accidente laboral / Tránsito)
        if ($tipoExcepcion === 'SRL' || $tipoExcepcion === 'FONAMAT') {
            $resultado['aprobado'] = true;
            $resultado['monto_ars'] = 0.0;
            $resultado['monto_afiliado'] = 0.0;
            $resultado['copago'] = 0.0;
            $resultado['exceso'] = 0.0;
            $resultado['monto_no_cubierto'] = $montoSolicitado;
            $resultado['motivo'] = "Servicio derivado a " . ($tipoExcepcion === 'SRL' ? 'Seguro de Riesgos Laborales (SRL)' : 'FONAMAT (Accidente de Tránsito)') . ". Fuera de SFS/PDSS.";
            return $resultado;
        }

        // Obtener grupo y subgrupo del servicio
        $serviceGroup = '';
        $serviceSubgroup = '';

        if ($service instanceof PdssService) {
            $serviceGroup = $service->group->name ?? '';
            $serviceSubgroup = $service->subgroup->name ?? '';
        } else {
            // Si es un servicio médico simulado legacy
            $serviceGroup = $service->grupo ?? 'General';
            $serviceSubgroup = $service->subgrupo ?? '';
        }

        // 2. Buscar regla aplicable en pdss_coverage_rules
        $rule = PdssCoverageRule::where('is_active', true)
            ->where(function ($query) use ($serviceGroup, $serviceSubgroup) {
                $query->where('service_group', $serviceGroup)
                      ->where('service_subgroup', $serviceSubgroup);
            })
            ->orWhere(function ($query) use ($serviceGroup) {
                $query->where('service_group', $serviceGroup)
                      ->whereNull('service_subgroup');
            })
            ->first();

        if (!$rule) {
            // Si no hay regla específica, intentar leer del raw_text del servicio PDSS 10.0
            $rawData = null;
            if ($service instanceof PdssService && $service->raw_text) {
                $rawData = json_decode($service->raw_text, true);
            }

            if ($rawData && isset($rawData['cuota_ars_pct'])) {
                $arsPct = (float) ($rawData['cuota_ars_pct'] ?? 80);
                $afilPct = (float) ($rawData['cuota_afil_pct'] ?? 20);
                $tope = (float) ($rawData['cobertura_tope'] ?? 99999999);
                $topeAfil = (float) ($rawData['cuota_afil_tope'] ?? 0);
                $cuotaTipoDesc = $rawData['cuota_tipo_desc'] ?? $service->copay_type ?? 'No';

                $rule = new PdssCoverageRule([
                    'coverage_percent_ars' => $arsPct,
                    'copay_percent_affiliate' => $afilPct,
                    'coverage_limit_type' => ($tope >= 99999999) ? 'ilimitada' : 'tope',
                    'annual_limit' => ($tope < 99999999) ? $tope : 0,
                    'copay_fixed_amount' => (stripos($cuotaTipoDesc, 'fija') !== false) ? $topeAfil : 0,
                    'copay_cap_amount' => $topeAfil,
                    'requires_continuity_validation' => stripos($service->group->name ?? '', 'trasplante') !== false,
                    'requires_seniority_validation' => stripos($service->group->name ?? '', 'trasplante') !== false,
                ]);
            } else {
                // Regla por defecto del 80% ARS y 20% Afiliado
                $rule = new PdssCoverageRule([
                    'coverage_percent_ars' => 80.00,
                    'copay_percent_affiliate' => 20.00,
                    'coverage_limit_type' => 'ilimitada'
                ]);
            }
        }

        // 3. Validar Antigüedad y Continuidad si la regla lo requiere (Alto Costo y Trasplantes)
        if ($rule->requires_continuity_validation || $rule->requires_seniority_validation) {
            $mesesAfiliado = $afiliado->fecha_afiliacion ? Carbon::parse($afiliado->fecha_afiliacion)->diffInMonths(now()) : 0;
            
            // Simular gradualidad para trasplante renal / alto costo
            if ($mesesAfiliado < 12) {
                $resultado['aprobado'] = false;
                $resultado['monto_no_cubierto'] = $montoSolicitado;
                $resultado['motivo'] = 'Rechazada: No cumple con el mínimo de 12 meses de antigüedad y continuidad requeridos para trasplantes o alto costo.';
                return $resultado;
            }
        }

        // 4. Régimen Subsidiado: No aplica cuota moderadora (copago = 0)
        $esSubsidiado = $afiliado->regimen_actual === 'Subsidiado';

        // 5. Validar Acumuladores Anuales (especialmente Medicamentos Ambulatorios)
        $periodYear = date('Y');
        $accumulatedAmount = 0.0;
        
        $accumulator = PdssCoverageAccumulator::where('afiliado_id', $afiliado->id)
            ->where('service_group', $serviceGroup)
            ->where('period_year', $periodYear)
            ->first();

        if ($accumulator) {
            $accumulatedAmount = $accumulator->accumulated_authorized_amount;
        }

        // 6. Aplicar límites de Cobertura de la Regla
        $montoMaximoAutorizable = $montoSolicitado;
        $exceso = 0.0;

        if ($rule->annual_limit > 0) {
            $disponible = $rule->annual_limit - $accumulatedAmount;
            if ($disponible <= 0) {
                $resultado['aprobado'] = false;
                $resultado['monto_no_cubierto'] = $montoSolicitado;
                $resultado['motivo'] = "Rechazada: Se ha excedido el límite acumulado anual de DOP " . number_format($rule->annual_limit, 2) . " para este grupo de servicios.";
                return $resultado;
            }

            if ($montoSolicitado > $disponible) {
                $montoMaximoAutorizable = $disponible;
                $exceso = $montoSolicitado - $disponible;
                $resultado['motivo'] = "Aprobada parcial: El servicio supera el límite anual disponible de DOP " . number_format($disponible, 2) . ". Exceso a cargo del afiliado.";
            }
        }

        if ($rule->event_limit > 0 && $montoMaximoAutorizable > $rule->event_limit) {
            $remanente = $montoMaximoAutorizable - $rule->event_limit;
            $montoMaximoAutorizable = $rule->event_limit;
            $exceso += $remanente;
            $resultado['motivo'] = "Aprobada parcial: Supera el límite por evento de DOP " . number_format($rule->event_limit, 2) . ". Exceso a cargo del afiliado.";
        }

        // 7. Calcular copagos
        $copago = 0.0;
        if (!$esSubsidiado) {
            if ($rule->copay_fixed_amount > 0) {
                $copago = $rule->copay_fixed_amount;
            } elseif ($rule->copay_percent_affiliate > 0) {
                $copago = $montoMaximoAutorizable * ($rule->copay_percent_affiliate / 100.0);
            }

            // Aplicar tope de copago (cap) si aplica
            if ($rule->copay_cap_amount > 0 && $copago > $rule->copay_cap_amount) {
                $copago = $rule->copay_cap_amount;
            }
        }

        // El monto que la ARS cubre
        $montoArs = $montoMaximoAutorizable - $copago;

        // Si es consulta ambulatoria, verificar tope por evento de RD$750
        if (stripos($serviceGroup, 'Consulta') !== false || stripos($serviceGroup, 'Consultas') !== false) {
            if ($montoArs > 750.00) {
                $diferencia = $montoArs - 750.00;
                $montoArs = 750.00;
                $copago += $diferencia;
            }
        }

        // Si es habitación, verificar límites diarios
        if (stripos($serviceGroup, 'Habitación') !== false || stripos($serviceGroup, 'Habitacion') !== false) {
            // RD$1,725 al 100%, 90% del exceso entre 1726 y 2415
            if ($montoSolicitado > 1725.00) {
                $excesoHabitacion = min($montoSolicitado, 2415.00) - 1725.00;
                $montoArs = 1725.00 + ($excesoHabitacion * 0.90);
                $copago = $montoSolicitado - $montoArs;
                $exceso = 0.0;
                if ($montoSolicitado > 2415.00) {
                    $exceso = $montoSolicitado - 2415.00;
                    $copago = 2415.00 - $montoArs;
                }
            } else {
                $montoArs = $montoSolicitado;
                $copago = 0.0;
                $exceso = 0.0;
            }
        }

        // Rellenar desgloses
        $resultado['monto_ars'] = round($montoArs, 2);
        $resultado['copago'] = round($copago, 2);
        $resultado['exceso'] = round($exceso, 2);
        $resultado['monto_afiliado'] = round($copago + $exceso, 2);
        $resultado['monto_no_cubierto'] = round($resultado['monto_solicitado'] - $resultado['monto_ars'], 2);

        return $resultado;
    }

    /**
     * Registra el consumo en el acumulador del afiliado tras aprobar una autorización o reclamación.
     */
    public static function acumularConsumo(Afiliado $afiliado, $service, float $montoAprobado): void
    {
        $serviceGroup = '';
        $pdssServiceId = null;

        if ($service instanceof PdssService) {
            $serviceGroup = $service->group->name ?? '';
            $pdssServiceId = $service->id;
        } else {
            $serviceGroup = $service->grupo ?? 'General';
            $pdssServiceId = $service->id ?? null;
        }

        $periodYear = date('Y');

        $accumulator = PdssCoverageAccumulator::firstOrCreate(
            [
                'afiliado_id' => $afiliado->id,
                'service_group' => $serviceGroup,
                'period_year' => $periodYear
            ],
            [
                'pdss_service_id' => $pdssServiceId,
                'accumulated_authorized_amount' => 0.0,
                'accumulated_claimed_amount' => 0.0,
                'accumulated_paid_amount' => 0.0,
                'available_amount' => 0.0
            ]
        );

        // Buscar límite anual de la regla para actualizar disponible
        $rule = PdssCoverageRule::where('is_active', true)
            ->where('service_group', $serviceGroup)
            ->first();

        $limiteAnual = $rule ? $rule->annual_limit : 0.0;

        $accumulator->accumulated_authorized_amount += $montoAprobado;
        if ($limiteAnual > 0) {
            $accumulator->available_amount = max(0.0, $limiteAnual - $accumulator->accumulated_authorized_amount);
        } else {
            $accumulator->available_amount = 9999999.99; // Ilimitado
        }

        $accumulator->save();
    }
}
