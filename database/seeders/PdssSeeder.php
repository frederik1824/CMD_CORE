<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pss;
use App\Models\PdssService;
use App\Models\PdssGroup;
use App\Models\PssServiceContract;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PdssSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Configurar niveles de atención y tipos para las 10 PSS
        $pssConfigs = [
            1 => ['nivel_atencion' => 3, 'tipo_pss' => 'Clínica', 'red_contratada' => true, 'contrato_vigente' => true],
            2 => ['nivel_atencion' => 3, 'tipo_pss' => 'Centro Médico', 'red_contratada' => true, 'contrato_vigente' => true],
            3 => ['nivel_atencion' => 2, 'tipo_pss' => 'Clínica', 'red_contratada' => true, 'contrato_vigente' => true],
            4 => ['nivel_atencion' => 3, 'tipo_pss' => 'Hospital', 'red_contratada' => true, 'contrato_vigente' => true],
            5 => ['nivel_atencion' => 2, 'tipo_pss' => 'Centro Médico', 'red_contratada' => true, 'contrato_vigente' => true],
            6 => ['nivel_atencion' => 3, 'tipo_pss' => 'Clínica', 'red_contratada' => true, 'contrato_vigente' => true],
            7 => ['nivel_atencion' => 1, 'tipo_pss' => 'Clínica', 'red_contratada' => true, 'contrato_vigente' => true],
            8 => ['nivel_atencion' => 2, 'tipo_pss' => 'Centro Médico', 'red_contratada' => true, 'contrato_vigente' => true],
            9 => ['nivel_atencion' => 3, 'tipo_pss' => 'Hospital', 'red_contratada' => true, 'contrato_vigente' => true],
            10 => ['nivel_atencion' => 1, 'tipo_pss' => 'Clínica', 'red_contratada' => false, 'contrato_vigente' => false],
        ];

        DB::transaction(function() use ($pssConfigs) {
            foreach ($pssConfigs as $id => $config) {
                Pss::where('id', $id)->update($config);
            }
        });

        // 2. Crear contratos de servicios PDSS para las PSS activas
        $pssList = Pss::where('estado', 'Activa')
            ->where('red_contratada', true)
            ->where('contrato_vigente', true)
            ->get();

        $pdssServices = PdssService::where('is_active', true)->get();

        if ($pdssServices->isEmpty()) {
            $this->command?->warn('No hay servicios PDSS importados. Ejecute pdss:import-excel primero.');
            return;
        }

        $this->command?->info("Creando contratos para " . $pssList->count() . " PSS con " . $pdssServices->count() . " servicios...");

        DB::transaction(function() use ($pssList, $pdssServices) {
            foreach ($pssList as $pss) {
                $created = 0;
                $contractsToInsert = [];
                
                foreach ($pdssServices as $service) {
                    // Verificar si la PSS cubre este nivel de atención
                    $nivel = $pss->nivel_atencion ?? 1;
                    $isLevelCovered = false;
                    if ($nivel == 1 && $service->level_1_covered === 'S') $isLevelCovered = true;
                    if ($nivel == 2 && $service->level_2_covered === 'S') $isLevelCovered = true;
                    if ($nivel == 3 && $service->level_3_covered === 'S') $isLevelCovered = true;

                    if (!$isLevelCovered) continue;

                    // Calcular tarifa base según tipo de servicio
                    $amount = $this->calculateBaseAmount($service);

                    // Variación por PSS
                    $contractedAmount = $amount * (1 + (($pss->id % 3) * 0.05));

                    $contractsToInsert[] = [
                        'pss_id'                 => $pss->id,
                        'pdss_service_id'        => $service->id,
                        'contracted_amount'      => round($contractedAmount, 2),
                        'authorization_required'  => $service->requires_authorization,
                        'audit_required'          => $service->requires_medical_audit,
                        'is_active'               => true,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];
                    $created++;
                    
                    if (count($contractsToInsert) >= 500) {
                        DB::table('pss_service_contracts')->insert($contractsToInsert);
                        $contractsToInsert = [];
                    }
                }

                if (count($contractsToInsert) > 0) {
                    DB::table('pss_service_contracts')->insert($contractsToInsert);
                }

                $this->command?->info("  PSS #{$pss->id} ({$pss->nombre}): $created servicios contratados");
            }
        });
    }

    /**
     * Calcula la tarifa base según el tipo de servicio del PDSS
     */
    private function calculateBaseAmount(PdssService $service): float
    {
        // Tarifa base según grupo
        $groupName = strtolower($service->group->name ?? '');

        if (str_contains($groupName, 'alto costo') || str_contains($groupName, 'trasplante')) {
            return 180000.00;
        }
        if (str_contains($groupName, 'cirugía') || str_contains($groupName, 'cirugia')) {
            return 45000.00;
        }
        if (str_contains($groupName, 'hospitalización') || str_contains($groupName, 'hospitalizacion') || str_contains($groupName, 'parto')) {
            return 5500.00;
        }
        if (str_contains($groupName, 'emergencia')) {
            return 2500.00;
        }
        if (str_contains($groupName, 'medicamentos')) {
            return 1500.00;
        }
        if (str_contains($groupName, 'apoyo diagnóstico') || str_contains($groupName, 'diagnóstico')) {
            return 650.00;
        }
        if (str_contains($groupName, 'odontológico') || str_contains($groupName, 'odontolog')) {
            return 1800.00;
        }
        if (str_contains($groupName, 'rehabilitación') || str_contains($groupName, 'rehabilitacion')) {
            return 1200.00;
        }
        if (str_contains($groupName, 'hemoterapia')) {
            return 3500.00;
        }
        if (str_contains($groupName, 'prevención') || str_contains($groupName, 'prevencion') || str_contains($groupName, 'promoción')) {
            return 800.00;
        }
        if (str_contains($groupName, 'ambulatoria')) {
            return 1800.00;
        }

        // Default
        return 1200.00;
    }
}
