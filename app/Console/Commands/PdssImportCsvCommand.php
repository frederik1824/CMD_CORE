<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PdssPlan;
use App\Models\PdssGroup;
use App\Models\PdssSubgroup;
use App\Models\PdssService;
use App\Models\PdssImportLog;
use Illuminate\Support\Carbon;

class PdssImportCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdss:import-csv {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the official PDSS catalog from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("El archivo CSV no existe en la ruta: $path");
            return 1;
        }

        $this->info("Iniciando importación desde el CSV: $path");

        $startedAt = Carbon::now();
        $log = PdssImportLog::create([
            'source_file' => basename($path),
            'status' => 'Procesando',
            'started_at' => $startedAt
        ]);

        try {
            $file = fopen($path, 'r');
            $header = fgetcsv($file); // Leer cabecera

            $groupsCount = 0;
            $subgroupsCount = 0;
            $servicesCount = 0;

            // Plan por defecto
            $plan = PdssPlan::firstOrCreate(
                ['plan_number' => '00000007'],
                [
                    'name' => 'PDSS 3.0',
                    'resolution' => 'Resolución 375-2 del CNSS',
                    'version' => '3.0',
                    'source_file' => basename($path),
                    'imported_at' => Carbon::now(),
                    'is_active' => true
                ]
            );

            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 14) continue;

                // Mapeo de columnas:
                // 0: plan_number, 1: grupo_codigo, 2: grupo_nombre, 3: subgrupo_codigo, 4: subgrupo_nombre
                // 5: monto_cobertura, 6: cuota_moderadora_copago, 7: codigo_simon, 8: tipo_cobertura
                // 9: descripcion_cobertura, 10: codigo_cups, 11: nivel_1, 12: nivel_2, 13: nivel_3
                
                $grupoCodigo = trim($row[1]);
                $grupoNombre = trim($row[2]);
                $subgrupoCodigo = trim($row[3]);
                $subgrupoNombre = trim($row[4]);
                $montoCobertura = trim($row[5]);
                $copago = trim($row[6]);
                $simonCode = trim($row[7]);
                $tipoCobertura = trim($row[8]);
                $descripcion = trim($row[9]);
                $cupsCode = trim($row[10]);
                $nivel1 = trim($row[11]);
                $nivel2 = trim($row[12]);
                $nivel3 = trim($row[13]);

                // 1. Crear/Actualizar Grupo
                $group = PdssGroup::updateOrCreate(
                    ['pdss_plan_id' => $plan->id, 'code' => $grupoCodigo],
                    ['name' => $grupoNombre, 'is_active' => true]
                );
                $groupsCount++;

                // 2. Crear/Actualizar Subgrupo
                $subgroup = PdssSubgroup::updateOrCreate(
                    ['pdss_group_id' => $group->id, 'code' => $subgrupoCodigo],
                    [
                        'name' => $subgrupoNombre,
                        'amount_coverage' => $montoCobertura,
                        'copay_type' => $copago,
                        'is_active' => true
                    ]
                );
                $subgroupsCount++;

                // 3. Crear/Actualizar Servicio
                $isHighCost = $grupoCodigo == '9' || str_contains(strtolower($grupoNombre), 'alto costo');
                $isEmergency = $grupoCodigo == '4' || str_contains(strtolower($grupoNombre), 'emergencia');
                $isHospitalization = $grupoCodigo == '5' || str_contains(strtolower($grupoNombre), 'hospitalización');
                $isSurgery = $grupoCodigo == '7' || str_contains(strtolower($grupoNombre), 'cirugía');
                $isDiagnosticSupport = $grupoCodigo == '8' || str_contains(strtolower($grupoNombre), 'apoyo diagnóstico');
                $isMedicine = $grupoCodigo == '12' || str_contains(strtolower($grupoNombre), 'medicamentos');

                $requiresAudit = $isHighCost || $isHospitalization || $isSurgery || str_contains(strtolower($descripcion), 'quimioterapia') || str_contains(strtolower($descripcion), 'resonancia');

                PdssService::updateOrCreate(
                    [
                        'pdss_plan_id' => $plan->id,
                        'simon_code' => $simonCode,
                        'pdss_subgroup_id' => $subgroup->id,
                        'cups_code' => $cupsCode
                    ],
                    [
                        'pdss_group_id' => $group->id,
                        'coverage_type' => $tipoCobertura,
                        'coverage_description' => $descripcion,
                        'level_1_covered' => $nivel1,
                        'level_2_covered' => $nivel2,
                        'level_3_covered' => $nivel3,
                        'amount_coverage' => $montoCobertura,
                        'copay_type' => $copago,
                        'requires_authorization' => true,
                        'requires_medical_audit' => $requiresAudit,
                        'is_high_cost' => $isHighCost,
                        'is_emergency' => $isEmergency,
                        'is_hospitalization' => $isHospitalization,
                        'is_surgery' => $isSurgery,
                        'is_diagnostic_support' => $isDiagnosticSupport,
                        'is_medicine' => $isMedicine,
                        'is_active' => true,
                        'source_page' => 1
                    ]
                );
                $servicesCount++;
            }
            fclose($file);

            $log->update([
                'total_groups' => PdssGroup::count(),
                'total_subgroups' => PdssSubgroup::count(),
                'total_services' => PdssService::count(),
                'status' => 'Completado',
                'finished_at' => Carbon::now()
            ]);

            $this->info("Importación CSV completada con éxito!");
            $this->info("Grupos procesados: $groupsCount");
            $this->info("Subgrupos procesados: $subgroupsCount");
            $this->info("Servicios procesados: $servicesCount");

            return 0;

        } catch (\Exception $e) {
            $log->update([
                'status' => 'Error',
                'errors' => $e->getMessage() . "\n" . $e->getTraceAsString(),
                'finished_at' => Carbon::now()
            ]);
            $this->error("Error durante la importación CSV: " . $e->getMessage());
            return 1;
        }
    }
}
