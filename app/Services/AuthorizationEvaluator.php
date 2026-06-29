<?php

namespace App\Services;

use App\Models\Autorizacion;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Pss;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\ServicioMedico;
use App\Models\Documento;
use App\Models\Bitacora;
use App\Models\PdssService;
use App\Models\PssServiceContract;
use App\Models\AuthorizationServiceValidation;

class AuthorizationEvaluator
{
    /**
     * Evalúa una solicitud de autorización médica aplicando las reglas de negocio automáticas.
     */
    public static function evaluar(Autorizacion $autorizacion, bool $hasDocument = false): array
    {
        $afiliado = $autorizacion->afiliado;
        $pss = $autorizacion->pss;

        // Si es una autorización basada en el catálogo PDSS dinámico
        if ($autorizacion->pdss_service_id) {
            return self::evaluarPdss($autorizacion, $hasDocument);
        }

        // --- FALLBACK: Lógica de simulación original ---
        $servicio = $autorizacion->servicio;

        // Regla 1: Afiliado Activo
        if (!$afiliado || $afiliado->estado_afiliacion !== 'OK') {
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'El afiliado no se encuentra activo o hábil en la ARS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // Regla 2: PSS Activa
        if (!$pss || $pss->estado !== 'Activa') {
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'La prestadora de servicios de salud (PSS) seleccionada se encuentra inactiva.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // Regla 3: Contrato Activo de la PSS
        $contrato = ContratoPss::where('pss_id', $pss->id)
            ->where('estado', 'Activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->first();

        if (!$contrato) {
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'La PSS no posee un contrato vigente activo (Revisión Administrativa).',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // Regla 4: Documento Soporte
        if ($servicio && $servicio->requiere_documento && !$hasDocument) {
            $docCount = Documento::where('entidad_type', 'autorizacion')
                ->where('entidad_id', $autorizacion->id ?? 0)
                ->count();
            
            if ($docCount == 0) {
                return [
                    'estado' => 'Pendiente Documento',
                    'motivo_estado' => 'El servicio solicitado requiere adjuntar documento de soporte clínico (Receta/Indicación).',
                    'monto_contratado' => 0.00,
                    'prioridad' => 'Media'
                ];
            }
        }

        // Regla 5: Alto Costo
        if ($servicio && $servicio->es_alto_costo) {
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'Servicio catalogado como de Alto Costo. Requiere auditoría médica especializada.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Alta'
            ];
        }

        // Regla 6: Tarifa Contratada & Monto
        $tarifa = null;
        if ($servicio) {
            $tarifa = TarifaPss::where('contrato_pss_id', $contrato->id)
                ->where('servicio_medico_id', $servicio->id)
                ->first();
        }

        if (!$tarifa) {
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'Servicio solicitado no está incluido en el tarifario contratado con esta PSS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if ($autorizacion->monto_solicitado > $tarifa->monto_tarifa) {
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'Monto solicitado ($' . number_format($autorizacion->monto_solicitado, 2) . ') supera la tarifa contratada ($' . number_format($tarifa->monto_tarifa, 2) . ').',
                'monto_contratado' => $tarifa->monto_tarifa,
                'prioridad' => 'Media'
            ];
        }

        // Regla 7: Frecuencia Excedida (últimos 30 días)
        $solicitudesPrevias = Autorizacion::where('afiliado_type', $autorizacion->afiliado_type)
            ->where('afiliado_id', $autorizacion->afiliado_id)
            ->where('servicio_medico_id', $servicio->id)
            ->where('fecha_solicitud', '>=', now()->subDays(30))
            ->whereIn('estado', ['Aprobada', 'Auditoría', 'Pendiente'])
            ->where('id', '!=', $autorizacion->id ?? 0)
            ->count();

        if ($solicitudesPrevias > 0) {
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'Frecuencia de servicio excedida. Ya existe una solicitud de este servicio en los últimos 30 días.',
                'monto_contratado' => $tarifa->monto_tarifa,
                'prioridad' => 'Media'
            ];
        }

        // Aprobación Automática
        return [
            'estado' => 'Aprobada',
            'motivo_estado' => 'Aprobación automática por motor de reglas. Cobertura del ' . ($servicio->cobertura_base ?? 80) . '%.',
            'monto_contratado' => $tarifa->monto_tarifa,
            'prioridad' => 'Baja'
        ];
    }

    /**
     * Evalúa una autorización médica con el Catálogo PDSS real.
     */
    private static function evaluarPdss(Autorizacion $autorizacion, bool $hasDocument): array
    {
        $afiliado = $autorizacion->afiliado;
        $pss = $autorizacion->pss;
        $servicioPdss = PdssService::find($autorizacion->pdss_service_id);

        if (!$servicioPdss) {
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'El servicio solicitado no existe en el Catálogo PDSS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // Limpiar validaciones previas si el ID de autorización ya existe
        if ($autorizacion->id) {
            AuthorizationServiceValidation::where('authorization_id', $autorizacion->id)->delete();
        }

        // 0. Validación de Reglas de Negocio: Tipo de Servicio vs Tipo de Autorización
        $tipoServicio = $autorizacion->tipo_servicio ?? 'consulta';
        $serviceTypeValidation = self::validateServiceTypeVsAuthorizationType($servicioPdss, $tipoServicio);
        if (!$serviceTypeValidation['valid']) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Tipo de Servicio', 'Rechazado', $serviceTypeValidation['message']);
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => $serviceTypeValidation['message'],
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // 0b. Validación de Disponibilidad de Medicamentos
        if ($servicioPdss->is_medicine) {
            $availabilityCheck = self::validateMedicineAvailability($servicioPdss, $autorizacion);
            if (!$availabilityCheck['available']) {
                self::logValidation($autorizacion->id, $servicioPdss->id, 'Medicamento', 'Rechazado', $availabilityCheck['message']);
                return [
                    'estado' => 'Rechazada',
                    'motivo_estado' => $availabilityCheck['message'],
                    'monto_contratado' => 0.00,
                    'prioridad' => 'Media'
                ];
            }
        }

        // 1. Validaciones del Afiliado
        if (!$afiliado) {
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'El afiliado no está registrado en el padrón de la ARS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if ($afiliado->estado_afiliacion === 'FALLECIDO' || (isset($afiliado->motivo_estado) && strtolower($afiliado->motivo_estado) === 'fallecido')) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Afiliado', 'Rechazado', 'Afiliado fallecido.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'Rechazada automáticamente: El afiliado se registra como fallecido en el padrón.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if ($afiliado->estado_afiliacion === 'PE') {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Afiliado', 'Auditoria', 'Afiliado con estatus pendiente (PE).');
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'Sometido a revisión administrativa: El afiliado se encuentra en estatus de aprobación pendiente (PE).',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if ($afiliado->estado_afiliacion !== 'OK') {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Afiliado', 'Rechazado', 'Afiliado inactivo.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'El afiliado no se encuentra activo o hábil en la ARS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }
        self::logValidation($autorizacion->id, $servicioPdss->id, 'Afiliado', 'Aprobado', 'Afiliado activo y hábil.');

        // 2. Determinar si es derivación especial (Accidentes / Trabajo)
        $diagLower = strtolower($autorizacion->diagnostico);
        $tipoExcepcion = 'N/A';
        if (str_contains($diagLower, 'accidente de tránsito') || str_contains($diagLower, 'transito')) {
            $tipoExcepcion = 'FONAMAT';
        } elseif (str_contains($diagLower, 'accidente laboral') || str_contains($diagLower, 'trabajo') || str_contains($diagLower, 'arl') || str_contains($diagLower, 'enfermedad profesional')) {
            $tipoExcepcion = 'SRL';
        }

        // 3. Validaciones de la PSS
        if (!$pss || $pss->estado !== 'Activa') {
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'La prestadora de servicios de salud (PSS) seleccionada se encuentra inactiva.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if (isset($pss->red_contratada) && !$pss->red_contratada) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'PSS', 'Rechazado', 'PSS fuera de la red contratada.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'La prestadora seleccionada no pertenece a la red contratada de esta ARS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // 4. Validación de Contrato y Tarifas PSS (Versión 2 Dinámica)
        $contract = \App\Models\PssContract::where('pss_id', $pss->id)
            ->where('status', 'vigente')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$contract) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Contrato', 'Rechazado', 'La PSS no posee un contrato vigente activo.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'La prestadora de servicios de salud (PSS) seleccionada no posee un contrato vigente activo.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media',
                'contract_error' => true
            ];
        }

        // Buscar versión de contrato vigente
        $version = $contract->versions()->where('status', 'vigente')->first();
        if (!$version) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Contrato', 'Rechazado', 'No hay versión de contrato vigente.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'No se encuentra configurada ninguna versión activa vigente del contrato de la PSS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media',
                'contract_error' => true
            ];
        }

        // Buscar esquema tarifario vigente
        $schedule = $contract->tariffSchedules()->where('status', 'vigente')->first();
        if (!$schedule) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Tarifario', 'Rechazado', 'No hay esquema tarifario vigente.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'No se encuentra configurado ningún tarifario activo vigente para esta prestadora.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media',
                'contract_error' => true
            ];
        }

        // Buscar ítem tarifado
        $tariffItem = \App\Models\PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
            ->where('pdss_service_id', $servicioPdss->id)
            ->where('status', 'activo')
            ->first();

        if (!$tariffItem) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Tarifario', 'Rechazado', 'Servicio no contratado.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'El servicio solicitado no está incluido en el tarifario contratado con esta PSS.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media',
                'tariff_error' => true
            ];
        }

        // Validar Nivel de Atención de la PSS según complejidad del servicio
        $nivelAtencionPss = intval($pss->nivel_atencion ?? 1);
        $nivelRequerido = 1;
        if ($servicioPdss->level_3_covered === 'S' && $servicioPdss->level_2_covered !== 'S' && $servicioPdss->level_1_covered !== 'S') {
            $nivelRequerido = 3;
        } elseif ($servicioPdss->level_2_covered === 'S' && $servicioPdss->level_1_covered !== 'S') {
            $nivelRequerido = 2;
        }

        if ($nivelAtencionPss < $nivelRequerido) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Nivel Atención', 'Rechazado', "PSS de Nivel {$nivelAtencionPss} no cumple nivel requerido {$nivelRequerido}.");
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => "El servicio solicitado requiere un nivel de atención de complejidad {$nivelRequerido}, pero la prestadora seleccionada es de Nivel {$nivelAtencionPss}.",
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        if ($servicioPdss->level_3_covered === 'S' && !$tariffItem->level_3_allowed) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Nivel Atención', 'Rechazado', 'Nivel de complejidad 3 no autorizado.');
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => 'La PSS no tiene autorizado el nivel de atención complejo (Nivel 3) requerido por este servicio médico en su contrato.',
                'monto_contratado' => 0.00,
                'prioridad' => 'Media'
            ];
        }

        // Validar límites de frecuencia contratados (V2)
        if ($tariffItem->frequency_limit > 0) {
            $periodDays = match ($tariffItem->frequency_period) {
                'dia' => 1,
                'mes' => 30,
                'año' => 365,
                default => 30
            };

            $fechaInicioPeriodo = now()->subDays($periodDays);

            $solicitudesPrevias = Autorizacion::where('afiliado_type', $autorizacion->afiliado_type)
                ->where('afiliado_id', $autorizacion->afiliado_id)
                ->where('pdss_service_id', $servicioPdss->id)
                ->where('fecha_solicitud', '>=', $fechaInicioPeriodo)
                ->whereIn('estado', ['Aprobada', 'Auditoría', 'Pendiente'])
                ->where('id', '!=', $autorizacion->id ?? 0)
                ->count();

            if ($solicitudesPrevias >= $tariffItem->frequency_limit) {
                self::logValidation($autorizacion->id, $servicioPdss->id, 'Frecuencia', 'Auditoría', "Límite de frecuencia excedido: {$solicitudesPrevias} solicitudes en {$tariffItem->frequency_period}.");
                return [
                    'estado' => 'Auditoría',
                    'motivo_estado' => "Frecuencia de servicio excedida. El tarifario contratado limita este servicio a {$tariffItem->frequency_limit} por {$tariffItem->frequency_period} (Consumido: {$solicitudesPrevias}).",
                    'monto_contratado' => $tariffItem->contracted_amount,
                    'prioridad' => 'Media',
                    'frequency_exceeded' => true
                ];
            }
        }

        // Validar si el monto solicitado supera la tarifa contratada
        if ($autorizacion->monto_solicitado > $tariffItem->contracted_amount) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Monto', 'Auditoria', 'Monto solicitado supera la tarifa.');
            return [
                'estado' => 'Auditoría',
                'motivo_estado' => 'El monto solicitado ($' . number_format($autorizacion->monto_solicitado, 2) . ') supera la tarifa contratada ($' . number_format($tariffItem->contracted_amount, 2) . ').',
                'monto_contratado' => $tariffItem->contracted_amount,
                'prioridad' => 'Media',
                'monto_excedido' => true
            ];
        }

        // 5. Aplicar motor de reglas contables-financieras PDSS 11.0
        $evalPDSS = \App\Services\PdssCoverageEngine::evaluarServicio($afiliado, $servicioPdss, $autorizacion->monto_solicitado, $tipoExcepcion);

        if (!$evalPDSS['aprobado']) {
            self::logValidation($autorizacion->id, $servicioPdss->id, 'Reglas PDSS', 'Rechazado', $evalPDSS['motivo']);
            return [
                'estado' => 'Rechazada',
                'motivo_estado' => $evalPDSS['motivo'],
                'monto_contratado' => 0.00,
                'prioridad' => 'Media',
                'monto_ars' => 0.0,
                'monto_afiliado' => $autorizacion->monto_solicitado,
                'copago' => 0.0,
                'exceso' => 0.0,
                'monto_no_cubierto' => $autorizacion->monto_solicitado,
                'exception_coverage_type' => $tipoExcepcion
            ];
        }

        // 6. Validación de Documento soporte (si la regla lo exige o es cirugía/hospitalización)
        $needsDocument = $servicioPdss->is_high_cost || $servicioPdss->is_hospitalization || $servicioPdss->is_surgery;
        if ($needsDocument && !$hasDocument) {
            $docCount = 0;
            if ($autorizacion->id) {
                $docCount = Documento::where('entidad_type', 'autorizacion')
                    ->where('entidad_id', $autorizacion->id)
                    ->count();
            }
            if ($docCount == 0) {
                self::logValidation($autorizacion->id, $servicioPdss->id, 'Documento', 'Auditoria', 'Requiere documento de soporte.');
                return [
                    'estado' => 'Pendiente Documento',
                    'motivo_estado' => 'El servicio solicitado requiere adjuntar documento de soporte clínico (Receta/Indicación firmada y sellada).',
                    'monto_contratado' => 0.00,
                    'prioridad' => 'Media',
                    'monto_ars' => $evalPDSS['monto_ars'],
                    'monto_afiliado' => $evalPDSS['monto_afiliado'],
                    'copago' => $evalPDSS['copago'],
                    'exceso' => $evalPDSS['exceso'],
                    'monto_no_cubierto' => $evalPDSS['monto_no_cubierto'],
                    'exception_coverage_type' => $tipoExcepcion
                ];
            }
        }

        self::logValidation($autorizacion->id, $servicioPdss->id, 'Reglas PDSS', 'Aprobado', $evalPDSS['motivo']);

        // Aprobación
        return [
            'estado' => 'Aprobada',
            'motivo_estado' => $evalPDSS['motivo'],
            'monto_contratado' => $tariffItem->contracted_amount,
            'prioridad' => $servicioPdss->is_emergency ? 'Alta' : 'Baja',
            'monto_ars' => $evalPDSS['monto_ars'],
            'monto_afiliado' => $evalPDSS['monto_afiliado'],
            'copago' => $evalPDSS['copago'],
            'exceso' => $evalPDSS['exceso'],
            'monto_no_cubierto' => $evalPDSS['monto_no_cubierto'],
            'exception_coverage_type' => $tipoExcepcion,
            // Retornar llaves de snapshots para guardar en BD
            'pss_contract_id' => $contract->id,
            'pss_contract_version_id' => $version->id,
            'pss_tariff_schedule_id' => $schedule->id,
            'pss_tariff_item_id' => $tariffItem->id,
        ];
    }

    /**
     * Auxiliar para guardar log de validaciones mecánicas
     */
    private static function logValidation($authId, $serviceId, $type, $status, $message)
    {
        if (!$authId) return;

        AuthorizationServiceValidation::create([
            'authorization_id' => $authId,
            'pdss_service_id' => $serviceId,
            'validation_type' => $type,
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * Valida que el tipo de servicio del catálogo sea compatible con el tipo de autorización solicitada.
     * REGLA DE NEGOCIO: No se puede autorizar un procedimiento quirúrgico en una consulta general, etc.
     */
    private static function validateServiceTypeVsAuthorizationType(PdssService $service, string $tipoServicio): array
    {
        $groupName = strtolower($service->group->name ?? '');

        // Mapeo de grupo del catálogo -> tipo de autorización permitido
        $allowedTypes = [
            // Consultas solo permiten servicios de consulta/ambulatoria
            'consulta' => ['prevención y promoción', 'atención ambulatoria', 'servicios odontológicos', 'rehabilitación'],
            // Cirugía solo permite servicios quirúrgicos
            'cirugia' => ['cirugía', 'cirugia', 'partos', 'alto costo', 'hospitalización'],
            // Hospitalización solo permite servicios de internamiento
            'internamiento' => ['hospitalización', 'partos', 'cirugía', 'alto costo'],
            // Emergencia permite casi todo excepto consultas programadas
            'emergencia' => ['emergencia', 'hospitalización', 'cirugía', 'alto costo', 'apoyo diagnóstico', 'medicamentos'],
            // Laboratorio solo permite servicios diagnósticos
            'laboratorio' => ['apoyo diagnóstico'],
            // Imagen solo permite servicios de imagenología
            'imagen' => ['apoyo diagnóstico'],
            // Medicamentos solo permite medicamentos
            'medicamento' => ['medicamentos ambulatorios', 'atenciones de alto costo'],
            // Alto costo
            'alto_costo' => ['atenciones de alto costo', 'trasplante renal'],
        ];

        $tipoLower = strtolower($tipoServicio);

        if (isset($allowedTypes[$tipoLower])) {
            $allowed = $allowedTypes[$tipoLower];
            $matches = false;
            foreach ($allowed as $allowedGroup) {
                if (str_contains($groupName, $allowedGroup)) {
                    $matches = true;
                    break;
                }
            }

            if (!$matches) {
                return [
                    'valid' => false,
                    'message' => "El servicio seleccionado (Grupo: {$service->group->name}) no es compatible con el tipo de autorización \"{$tipoServicio}\". No se puede autorizar este servicio en esta categoría.",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Valida la disponibilidad de medicamentos antes de autorizar.
     * REGLA DE NEGOCIO: No se pueden autorizar medicamentos sin disponibilidad en la red.
     */
    private static function validateMedicineAvailability(PdssService $service, Autorizacion $autorizacion): array
    {
        // Verificar que el medicamento tenga cobertura en el nivel del afiliado
        $afiliado = $autorizacion->afiliado;
        if (!$afiliado) {
            return ['available' => false, 'message' => 'No se puede validar disponibilidad: afiliado no encontrado.'];
        }

        // Verificar nivel de atención
        $nivel = 1; // Default nivel 1
        if (method_exists($afiliado, 'regimen_actual')) {
            // Nivel según tipo de afiliación
            if (stripos($afiliado->regimen_actual ?? '', 'contributivo') !== false) {
                $nivel = 2; // Contributivo = nivel 2-3
            }
        }

        $nivelCovered = false;
        if ($nivel == 1 && $service->level_1_covered === 'S') $nivelCovered = true;
        if ($nivel == 2 && $service->level_2_covered === 'S') $nivelCovered = true;
        if ($nivel == 3 && $service->level_3_covered === 'S') $nivelCovered = true;

        if (!$nivelCovered) {
            return [
                'available' => false,
                'message' => "El medicamento \"{$service->coverage_description}\" no está disponible en el nivel de atención del afiliado (Nivel {$nivel}).",
            ];
        }

        // Verificar que exista un contrato activo con una PSS que tenga el medicamento
        $pss = $autorizacion->pss;
        if ($pss) {
            $hasContract = PssServiceContract::where('pss_id', $pss->id)
                ->where('pdss_service_id', $service->id)
                ->where('is_active', true)
                ->exists();

            if (!$hasContract) {
                return [
                    'available' => false,
                    'message' => "El medicamento \"{$service->coverage_description}\" no se encuentra disponible en la prestadora seleccionada ({$pss->nombre}). No existe contrato activo para este medicamento.",
                ];
            }
        }

        return ['available' => true];
    }
}
