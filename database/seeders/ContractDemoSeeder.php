<?php

namespace Database\Seeders;

use App\Models\Pss;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use App\Models\Autorizacion;
use App\Models\Afiliado;
use App\Models\User;
use App\Models\AuthorizationOverride;
use App\Models\AuthorizationTimelineEvent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar tablas para evitar duplicidad
        DB::statement('PRAGMA foreign_keys = OFF;');
        AuthorizationOverride::truncate();
        PssTariffItem::truncate();
        PssTariffSchedule::truncate();
        PssContractVersion::truncate();
        PssContract::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $userArs = User::where('role', 'Administrador ARS')->first() ?? User::first();
        $userId = $userArs ? $userArs->id : 1;

        // 2. Garantizar 20 PSS
        $pssList = Pss::all();
        if ($pssList->count() < 20) {
            $nombresPss = [
                'Clínica Abreu', 'Plaza de la Salud', 'CEDIMAT', 'Clínica Unión Médica', 
                'Homs Santiago', 'Centro Médico Bournigal', 'Clínica Corominas', 'Centro Médico Cibao', 
                'Clínica Independencia', 'Hospiten Santo Domingo', 'Centro Médico UCE', 
                'Clínica Ginecología y Obstetricia', 'Clínica Cruz Jiminián', 'Centro Médico Moderno', 
                'Clínica Alcántara & González', 'Centro de Medicina Avanzada Abel González', 
                'Clínica Dr. Perozo', 'Centro Médico Dominicano', 'Hospital General Plaza de la Salud', 
                'Clínica Rodríguez Santos'
            ];

            foreach ($nombresPss as $index => $nombre) {
                if (Pss::where('nombre', $nombre)->exists()) continue;

                $pssList->push(Pss::create([
                    'rnc' => '101' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'nombre' => $nombre,
                    'tipo_entidad' => $index % 3 === 0 ? 'Hospital' : ($index % 3 === 1 ? 'Clínica' : 'Centro Médico'),
                    'estado' => 'Activa'
                ]));
            }
        }
        $pssList = Pss::all();

        // 3. Garantizar Afiliados
        $afiliados = Afiliado::all();
        if ($afiliados->isEmpty()) {
            return;
        }

        // Obtener servicios PDSS para poblar el tarifario
        $pdssServices = DB::table('pdss_services')->get();
        if ($pdssServices->isEmpty()) {
            // Si la tabla está vacía, crear servicios de prueba temporales en pdss_services para el seeder
            $planId = DB::table('pdss_plans')->insertGetId([
                'plan_number' => 'PDSS-11.0',
                'name' => 'Plan de Servicios de Salud 11.0',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $groupId = DB::table('pdss_groups')->insertGetId([
                'pdss_plan_id' => $planId,
                'code' => 'GP1',
                'name' => 'Grupo Servicios Comunes',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $subgroupId = DB::table('pdss_subgroups')->insertGetId([
                'pdss_group_id' => $groupId,
                'code' => 'SGP1',
                'name' => 'Subgrupo Diagnósticos y Consultas',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $serviciosDemo = [
                ['simon_code' => '900010', 'cups_code' => '90.0.1.0', 'coverage_description' => 'Consulta Médica General', 'requires_authorization' => false],
                ['simon_code' => '900020', 'cups_code' => '90.0.2.0', 'coverage_description' => 'Consulta Especializada de Ginecología', 'requires_authorization' => false],
                ['simon_code' => '900030', 'cups_code' => '90.0.3.0', 'coverage_description' => 'Consulta de Cardiología', 'requires_authorization' => false],
                ['simon_code' => '901010', 'cups_code' => '90.1.0.1', 'coverage_description' => 'Hemograma Completo', 'requires_authorization' => true],
                ['simon_code' => '901020', 'cups_code' => '90.1.0.2', 'coverage_description' => 'Glucosa en Sangre', 'requires_authorization' => false],
                ['simon_code' => '901030', 'cups_code' => '90.1.0.3', 'coverage_description' => 'Examen General de Orina', 'requires_authorization' => false],
                ['simon_code' => '902010', 'cups_code' => '90.2.0.1', 'coverage_description' => 'Radiografía de Tórax AP/Lateral', 'requires_authorization' => true],
                ['simon_code' => '902020', 'cups_code' => '90.2.0.2', 'coverage_description' => 'Electrocardiograma de 12 Derivaciones', 'requires_authorization' => true],
                ['simon_code' => '902030', 'cups_code' => '90.2.0.3', 'coverage_description' => 'Ultrasonido Abdominal Completo', 'requires_authorization' => true],
                ['simon_code' => '903010', 'cups_code' => '90.3.0.1', 'coverage_description' => 'Tomografía Axial Computarizada (TAC) de Cráneo', 'requires_authorization' => true, 'requires_medical_audit' => true, 'is_high_cost' => true],
                ['simon_code' => '903020', 'cups_code' => '90.3.0.2', 'coverage_description' => 'Resonancia Magnética de Cerebro con Contraste', 'requires_authorization' => true, 'requires_medical_audit' => true, 'is_high_cost' => true],
                ['simon_code' => '904010', 'cups_code' => '90.4.0.1', 'coverage_description' => 'Apendicectomía Laparoscópica', 'requires_authorization' => true, 'requires_medical_audit' => true, 'is_surgery' => true],
                ['simon_code' => '904020', 'cups_code' => '90.4.0.2', 'coverage_description' => 'Colecistectomía por Video', 'requires_authorization' => true, 'requires_medical_audit' => true, 'is_surgery' => true],
                ['simon_code' => '905010', 'cups_code' => '90.5.0.1', 'coverage_description' => 'Habitación de Internamiento por Día', 'requires_authorization' => true, 'is_hospitalization' => true],
                ['simon_code' => '905020', 'cups_code' => '90.5.0.2', 'coverage_description' => 'Medicamento del PDSS (Ambulatorio)', 'requires_authorization' => false, 'is_medicine' => true],
            ];

            foreach ($serviciosDemo as $index => $serv) {
                DB::table('pdss_services')->insert([
                    'pdss_plan_id' => $planId,
                    'pdss_group_id' => $groupId,
                    'pdss_subgroup_id' => $subgroupId,
                    'simon_code' => $serv['simon_code'],
                    'cups_code' => $serv['cups_code'],
                    'coverage_type' => $index % 3 === 0 ? 'Consultas' : ($index % 3 === 1 ? 'Apoyo Diagnóstico' : 'Cirugía'),
                    'coverage_description' => $serv['coverage_description'],
                    'requires_authorization' => $serv['requires_authorization'],
                    'requires_medical_audit' => $serv['requires_medical_audit'] ?? false,
                    'is_high_cost' => $serv['is_high_cost'] ?? false,
                    'is_surgery' => $serv['is_surgery'] ?? false,
                    'is_hospitalization' => $serv['is_hospitalization'] ?? false,
                    'is_medicine' => $serv['is_medicine'] ?? false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $pdssServices = DB::table('pdss_services')->get();
        }

        $tariffItemsData = [];
        $contractsMap = [];

        // 4. Mapear Contratos por PSS
        // 15 PSS vigentes, 3 vencidas, 2 sin contrato
        foreach ($pssList as $index => $pss) {
            if ($index >= 18) {
                // Las últimas 2 PSS quedan sin contrato vigente
                continue;
            }

            $isVigente = $index < 15; // Las primeras 15 son vigentes, 15,16,17 son vencidas
            $startDate = $isVigente ? Carbon::now()->subMonths(6) : Carbon::now()->subYears(2);
            $endDate = $isVigente ? Carbon::now()->addMonths(6) : Carbon::now()->subMonths(2);
            $status = $isVigente ? 'vigente' : 'vencido';

            $contractNum = 'CONTRATO-CMD-' . Carbon::now()->format('Y') . '-' . str_pad($pss->id, 4, '0', STR_PAD_LEFT);
            
            // A. Crear Contrato
            $contract = PssContract::create([
                'pss_id' => $pss->id,
                'contract_number' => $contractNum,
                'contract_name' => "Convenio de Servicios Médicos CMD - {$pss->nombre}",
                'contract_type' => $index % 3 === 0 ? 'general' : ($index % 3 === 1 ? 'mixto' : 'evento'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'auto_renewal' => true,
                'status' => $status,
                'signed_at' => $startDate->copy()->addDays(2),
                'signed_by' => $userId,
                'document_path' => 'contracts/dummy_contract_' . $pss->id . '.pdf',
                'observations' => "Contrato demo para simulación de auditoría y facturación {$pss->nombre}."
            ]);

            // B. Crear Versión de Contrato
            $version = PssContractVersion::create([
                'pss_contract_id' => $contract->id,
                'version_number' => '1.0.0',
                'effective_from' => $startDate,
                'effective_to' => $endDate,
                'status' => $status,
                'approved_by' => $userId,
                'approved_at' => $startDate->copy()->addDays(1),
                'change_reason' => 'Configuración inicial de tarifarios pactados.'
            ]);

            // C. Crear Esquema de Tarifas
            $schedule = PssTariffSchedule::create([
                'pss_contract_id' => $contract->id,
                'pss_contract_version_id' => $version->id,
                'name' => "Tarifario Base - {$pss->nombre}",
                'effective_from' => $startDate,
                'effective_to' => $endDate,
                'status' => $status,
                'imported_from_file' => false,
                'imported_by' => $userId,
                'approved_by' => $userId,
                'approved_at' => $startDate->copy()->addDays(1)
            ]);

            // D. Acumular tarifas para bulk insert
            foreach ($pdssServices as $pdss) {
                $factorTarifa = 1.0 + (rand(-15, 30) / 100);
                $montoBase = 1000;
                
                if ($pdss->is_high_cost) {
                    $montoBase = rand(5000, 25000);
                } elseif ($pdss->is_surgery) {
                    $montoBase = rand(15000, 80000);
                } elseif ($pdss->is_hospitalization) {
                    $montoBase = 4500;
                } else {
                    $montoBase = rand(300, 2500);
                }

                $contractedAmount = round($montoBase * $factorTarifa, 2);

                $tariffItemsData[] = [
                    'pss_tariff_schedule_id' => $schedule->id,
                    'pss_id' => $pss->id,
                    'pdss_service_id' => $pdss->id,
                    'simon_code_snapshot' => $pdss->simon_code,
                    'cups_code_snapshot' => $pdss->cups_code,
                    'service_description_snapshot' => $pdss->coverage_description,
                    'service_group_snapshot' => $pdss->coverage_type,
                    'service_subgroup_snapshot' => 'Diagnóstico y Tratamiento',
                    'coverage_type_snapshot' => $pdss->coverage_type,
                    'contracted_amount' => $contractedAmount,
                    'currency' => 'DOP',
                    'copay_percent' => 20.00,
                    'affiliate_copay_amount' => 0.00,
                    'ars_covered_percent' => 80.00,
                    'requires_authorization' => $pdss->requires_authorization,
                    'requires_medical_audit' => $pdss->requires_medical_audit,
                    'requires_document' => $pdss->is_high_cost || $pdss->is_surgery,
                    'frequency_limit' => $pdss->is_high_cost ? 2 : 12,
                    'frequency_period' => 'año',
                    'max_amount_per_event' => $contractedAmount * 1.5,
                    'max_amount_per_year' => $contractedAmount * 5,
                    'level_1_allowed' => true,
                    'level_2_allowed' => true,
                    'level_3_allowed' => true,
                    'is_high_cost' => $pdss->is_high_cost,
                    'is_emergency' => $pdss->is_emergency,
                    'is_hospitalization' => $pdss->is_hospitalization,
                    'is_surgery' => $pdss->is_surgery,
                    'is_diagnostic_support' => $pdss->is_diagnostic_support,
                    'is_medicine' => $pdss->is_medicine,
                    'status' => 'activo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $contractsMap[$pss->id] = [
                'contract' => $contract,
                'version' => $version,
                'schedule' => $schedule
            ];
        }

        // Insertar tarifas en lotes para velocidad óptima
        $chunks = array_chunk($tariffItemsData, 100);
        foreach ($chunks as $chunk) {
            PssTariffItem::insert($chunk);
        }

        // 5. Crear Autorizaciones de Prueba (200 Core ARS y 150 Portal PSS)
        // Jalamos los ítems tarifados guardados
        $insertedTariffItems = PssTariffItem::all()->groupBy('pss_tariff_schedule_id');
        
        $servicioMedico = DB::table('servicios_medicos')->first();
        $servicioMedicoId = $servicioMedico ? $servicioMedico->id : 1;

        DB::beginTransaction();
        try {
            for ($i = 0; $i < 350; $i++) {
                $afiliado = $afiliados->random();
                $origen = $i < 200 ? 'core_ars' : 'portal_pss';

                $pss = $pssList->random();
                $hasContract = isset($contractsMap[$pss->id]) && $contractsMap[$pss->id]['contract']->status === 'vigente';
                $contractInfo = $hasContract ? $contractsMap[$pss->id] : null;

                $tariffItem = null;
                if ($contractInfo) {
                    $scheduleItems = $insertedTariffItems->get($contractInfo['schedule']->id);
                    if ($scheduleItems && $scheduleItems->isNotEmpty()) {
                        $tariffItem = $scheduleItems->random();
                    }
                }

                $montoSolicitado = $tariffItem ? $tariffItem->contracted_amount : rand(800, 15000);
                
                $status = 'Aprobada';
                $copago = 0;
                $montoArs = $montoSolicitado;
                $montoExcedente = 0;
                $rejectionReason = null;

                if ($tariffItem) {
                    $copago = $montoSolicitado * ($tariffItem->copay_percent / 100);
                    $montoArs = $montoSolicitado - $copago;

                    if ($tariffItem->requires_medical_audit || $tariffItem->is_high_cost) {
                        $status = rand(1, 2) === 1 ? 'Auditoría' : 'Aprobada';
                    }
                } else {
                    $status = 'Rechazada';
                    $rejectionReason = 'El prestador no posee un contrato o tarifario vigente configurado para la fecha de solicitud.';
                }

                if ($tariffItem && rand(1, 10) > 8) {
                    $montoSolicitado = $montoSolicitado * 1.25;
                    $status = 'En revisión administrativa';
                }

                $hasOverride = false;
                if ($status === 'Rechazada' && $i < 20 && $hasContract) {
                    $status = 'Aprobada';
                    $hasOverride = true;
                }

                $fechaSol = Carbon::now()->subDays(rand(1, 90));
                $numAut = 'AUT-' . $fechaSol->format('Ymd') . '-' . str_pad(1000 + $i, 5, '0', STR_PAD_LEFT);

                $aut = Autorizacion::create([
                    'numero_autorizacion' => $numAut,
                    'afiliado_type' => 'titular',
                    'afiliado_id' => $afiliado->id,
                    'pss_id' => $pss->id,
                    'medico_solicitante' => 'Dr. Prescriptor de Prueba ' . rand(1, 100),
                    'diagnostico' => 'I10 - HTA Esencial',
                    'servicio_medico_id' => $servicioMedicoId,
                    'procedimiento' => $tariffItem ? $tariffItem->service_description_snapshot : 'Servicio general',
                    'monto_solicitado' => $montoSolicitado,
                    'monto_contratado' => $tariffItem ? $tariffItem->contracted_amount : 0.00,
                    'copago' => $copago,
                    'exceso' => $montoExcedente,
                    'prioridad' => rand(1, 3) === 1 ? 'Alta' : (rand(1, 3) === 2 ? 'Media' : 'Baja'),
                    'estado' => $status === 'Auditoría' ? 'Auditoría' : ($status === 'En revisión administrativa' ? 'Auditoría' : $status),
                    'motivo_estado' => $rejectionReason,
                    'fecha_solicitud' => $fechaSol,
                    'fecha_respuesta' => $status !== 'Auditoría' ? $fechaSol->copy()->addMinutes(rand(5, 30)) : null,
                    'usuario_responsable_id' => $userId,
                    'origin' => $origen,
                    'channel' => $origen === 'core_ars' ? ['llamada', 'correo', 'presencial', 'whatsapp'][rand(0, 3)] : 'portal',
                    'pss_contract_id' => $contractInfo ? $contractInfo['contract']->id : null,
                    'pss_contract_version_id' => $contractInfo ? $contractInfo['version']->id : null,
                    'pss_tariff_schedule_id' => $tariffItem ? $tariffItem->pss_tariff_schedule_id : null,
                    'pss_tariff_item_id' => $tariffItem ? $tariffItem->id : null,
                    'contracted_amount_snapshot' => $tariffItem ? $tariffItem->contracted_amount : 0.00,
                    'affiliate_copay_amount' => $copago,
                    'ars_amount' => $montoArs,
                    'non_covered_amount' => $montoExcedente,
                    'internal_notes' => $hasOverride ? 'Aprobado manualmente mediante override por el auditor.' : null,
                    'created_at' => $fechaSol,
                    'updated_at' => $fechaSol
                ]);

                if ($hasOverride) {
                    AuthorizationOverride::create([
                        'authorization_id' => $aut->id,
                        'override_type' => 'monto_excedido',
                        'original_result' => 'Rechazada',
                        'new_result' => 'Aprobada',
                        'reason' => 'Liberación por excepción de urgencia del afiliado bajo autorización médica.',
                        'approved_by' => $userId,
                        'approved_at' => $fechaSol->copy()->addMinutes(10),
                        'requires_supervisor_approval' => false
                    ]);
                }

                AuthorizationTimelineEvent::create([
                    'authorization_id' => $aut->id,
                    'event_type' => 'CREATED',
                    'title' => 'Solicitud de Autorización',
                    'description' => "Se ha recibido la solicitud por el canal " . ($origen === 'core_ars' ? 'Core ARS' : 'Portal PSS'),
                    'old_status' => null,
                    'new_status' => $aut->estado,
                    'user_id' => $userId,
                    'created_at' => $fechaSol
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
