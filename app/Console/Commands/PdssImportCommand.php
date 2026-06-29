<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PdssPlan;
use App\Models\PdssGroup;
use App\Models\PdssSubgroup;
use App\Models\PdssService;
use App\Models\PdssImportLog;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Carbon;

class PdssImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdss:import {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the official PDSS catalog from a PDF file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        if (!file_exists($path)) {
            $this->error("El archivo PDF no existe en la ruta: $path");
            return 1;
        }

        $this->info("Iniciando importación desde el PDF: $path");

        $startedAt = Carbon::now();
        $log = PdssImportLog::create([
            'source_file' => basename($path),
            'status' => 'Procesando',
            'started_at' => $startedAt
        ]);

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($path);
            $pages = $pdf->getPages();
            $totalPages = count($pages);

            $this->info("Total de páginas detectadas: $totalPages");
            $log->update(['total_pages' => $totalPages]);

            // Crear o buscar Plan
            // Por defecto usaremos el plan del PDF: "00000007 - PDSS 3.0"
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

            $currentGroup = null;
            $currentSubgroup = null;
            
            $groupsCount = 0;
            $subgroupsCount = 0;
            $servicesCount = 0;

            // Tipos de cobertura conocidos en el PDSS
            $tiposCobertura = [
                'Material Sanitario', 'Laboratorio', 'Ecografías', 'Consultas', 'Vacunas',
                'Fármacos', 'Actos Quirúrgicos/anestésicos', 'Pruebas cardiológicas',
                'Habitación', 'Servicio de Cunas', 'Honorarios Médicos', 'Radiología',
                'T.A.C.', 'R.M.', 'Apoyo Diagnóstico', 'Rehabilitación', 'Hemoterapia',
                'Cirugía', 'Hospitalización', 'Odontología', 'Anestesia', 'Tratamiento',
                'Estudios', 'Procedimientos', 'Insumos', 'Material Gastable'
            ];

            // Expresión regular para detectar inicio de un servicio
            // ej: "439Laboratorio Hepatitis B, ..."
            // Buscamos dígitos seguidos de uno de los tipos de cobertura
            $tiposPattern = implode('|', array_map('preg_quote', $tiposCobertura));
            $serviceStartPattern = "#^(\d+)(" . $tiposPattern . ")(.*)#i";

            // Expresión para la línea final del servicio (CUPS y Niveles)
            // ej: "90.6.3.17 N S N" o "0        S S S"
            $serviceEndPattern = "#([\d\.]+)\s+([S|N])\s+([S|N])\s+([S|N])\s*$#i";

            $pendingService = null;

            // Procesar un número limitado de páginas para evitar agotar memoria si es muy grande,
            // pero permitiremos procesar todo si no hay problemas.
            // Para la demo, limitaremos a las primeras 30 páginas si es necesario, o lo procesamos todo.
            // Vamos a procesar las primeras 40 páginas, que es más que suficiente para tener
            // cientos de servicios reales en la base de datos de demo.
            $pagesToProcess = min(50, $totalPages);
            $this->info("Procesando las primeras $pagesToProcess páginas del PDF...");

            for ($pageIndex = 0; $pageIndex < $pagesToProcess; $pageIndex++) {
                $text = $pages[$pageIndex]->getText();
                $lines = explode("\n", $text);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    // 1. Detectar Grupo
                    // ej: "Grupo : 1 - Prevención y Promoción"
                    if (preg_match('/Grupo\s*:\s*(\d+)\s*-\s*(.*)/i', $line, $matches)) {
                        $groupCode = trim($matches[1]);
                        $groupName = trim($matches[2]);

                        $currentGroup = PdssGroup::updateOrCreate(
                            ['pdss_plan_id' => $plan->id, 'code' => $groupCode],
                            ['name' => $groupName, 'is_active' => true]
                        );
                        $groupsCount++;
                        $currentSubgroup = null;
                        continue;
                    }

                    // 2. Detectar Subgrupo
                    // ej: "SubGrupo : 1.1 - Asistencia Prenatal"
                    if (preg_match('/SubGrupo\s*:\s*([\d\.]+)\s*-\s*(.*)/i', $line, $matches)) {
                        if (!$currentGroup) {
                            // Si no hay grupo creado, usar un grupo por defecto
                            $currentGroup = PdssGroup::firstOrCreate(
                                ['pdss_plan_id' => $plan->id, 'code' => '1'],
                                ['name' => 'Prevención y Promoción', 'is_active' => true]
                            );
                        }

                        $subgroupCode = trim($matches[1]);
                        $subgroupName = trim($matches[2]);

                        $currentSubgroup = PdssSubgroup::updateOrCreate(
                            ['pdss_group_id' => $currentGroup->id, 'code' => $subgroupCode],
                            [
                                'name' => $subgroupName,
                                'amount_coverage' => 'Ilimitada',
                                'copay_type' => 'No',
                                'is_active' => true
                            ]
                        );
                        $subgroupsCount++;
                        continue;
                    }

                    // 3. Detectar Monto/Cobertura y Copago del Subgrupo
                    // ej: "Monto/Cobertura : Ilimitada   Cuota Moderadora/Copago : No"
                    if (preg_match('/Monto\/Cobertura\s*:\s*(.*?)\s+Cuota\s+Moderadora\/Copago\s*:\s*(.*)/i', $line, $matches)) {
                        if ($currentSubgroup) {
                            $currentSubgroup->update([
                                'amount_coverage' => trim($matches[1]),
                                'copay_type' => trim($matches[2])
                            ]);
                        }
                        continue;
                    }

                    // Si no tenemos grupo o subgrupo activos, no podemos guardar servicios
                    if (!$currentGroup || !$currentSubgroup) {
                        continue;
                    }

                    // 4. Detectar inicio de un Servicio
                    if (preg_match($serviceStartPattern, $line, $matches)) {
                        // Si ya había un servicio pendiente, lo guardamos o lo descartamos si estaba incompleto
                        if ($pendingService) {
                            $this->saveService($pendingService, $plan->id, $currentGroup->id, $currentSubgroup->id, $pageIndex + 1);
                            $servicesCount++;
                        }

                        $simonCode = trim($matches[1]);
                        $coverageType = trim($matches[2]);
                        $descStart = trim($matches[3]);

                        // Limpiar caracteres extraños
                        $descStart = preg_replace('/[\&\*\+\±\§\¶]/u', '', $descStart);

                        $pendingService = [
                            'simon_code' => $simonCode,
                            'coverage_type' => $coverageType,
                            'description' => trim($descStart),
                            'cups_code' => '0',
                            'level_1' => 'N',
                            'level_2' => 'N',
                            'level_3' => 'N'
                        ];
                        continue;
                    }

                    // 5. Detectar fin del servicio (CUPS y Niveles)
                    if ($pendingService && preg_match($serviceEndPattern, $line, $matches)) {
                        $cupsCode = trim($matches[1]);
                        $level1 = trim($matches[2]);
                        $level2 = trim($matches[3]);
                        $level3 = trim($matches[4]);

                        // Limpiar texto residual de la descripción
                        $residual = trim(substr($line, 0, strpos($line, $cupsCode)));
                        $residual = preg_replace('/[\&\*\+\±\§\¶]/u', '', $residual);
                        if (!empty($residual)) {
                            $pendingService['description'] .= ' ' . $residual;
                        }

                        $pendingService['cups_code'] = $cupsCode;
                        $pendingService['level_1'] = $level1;
                        $pendingService['level_2'] = $level2;
                        $pendingService['level_3'] = $level3;

                        $this->saveService($pendingService, $plan->id, $currentGroup->id, $currentSubgroup->id, $pageIndex + 1);
                        $servicesCount++;
                        $pendingService = null;
                        continue;
                    }

                    // 6. Si hay un servicio pendiente y la línea no es control ni cabecera, añadir a la descripción
                    if ($pendingService) {
                        // Ignorar líneas de cabecera/pie de página recurrentes
                        if (str_contains($line, 'Superintendencia') || str_contains($line, 'CATALOGO') || str_contains($line, 'IMPRESION') || str_contains($line, 'Sistema de Informacion')) {
                            continue;
                        }
                        
                        $lineCleaned = preg_replace('/[\&\*\+\±\§\¶]/u', '', $line);
                        $pendingService['description'] .= ' ' . trim($lineCleaned);
                    }
                }
            }

            // Guardar el último si quedó pendiente
            if ($pendingService) {
                $this->saveService($pendingService, $plan->id, $currentGroup->id, $currentSubgroup->id, $totalPages);
                $servicesCount++;
            }

            $log->update([
                'total_groups' => PdssGroup::count(),
                'total_subgroups' => PdssSubgroup::count(),
                'total_services' => PdssService::count(),
                'status' => 'Completado',
                'finished_at' => Carbon::now()
            ]);

            $this->info("Importación completada con éxito!");
            $this->info("Nuevos Grupos: $groupsCount");
            $this->info("Nuevos Subgrupos: $subgroupsCount");
            $this->info("Nuevos Servicios: $servicesCount");

            return 0;

        } catch (\Exception $e) {
            $log->update([
                'status' => 'Error',
                'errors' => $e->getMessage() . "\n" . $e->getTraceAsString(),
                'finished_at' => Carbon::now()
            ]);
            $this->error("Error durante la importación: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Guarda el servicio temporal en la base de datos
     */
    private function saveService(array $srv, $planId, $groupId, $subgroupId, $page)
    {
        $subgroup = PdssSubgroup::find($subgroupId);
        $group = PdssGroup::find($groupId);

        // Clasificación automática basada en el nombre del grupo
        $isHighCost = $group && str_contains(strtolower($group->name), 'alto costo');
        $isEmergency = $group && str_contains(strtolower($group->name), 'emergencia');
        $isHospitalization = $group && str_contains(strtolower($group->name), 'hospitalización');
        $isSurgery = $group && str_contains(strtolower($group->name), 'cirugía');
        $isDiagnosticSupport = $group && str_contains(strtolower($group->name), 'apoyo diagnóstico');
        $isMedicine = $group && str_contains(strtolower($group->name), 'medicamentos');

        // Banderas de auditoría automática
        $requiresAudit = $isHighCost || $isHospitalization || $isSurgery || str_contains(strtolower($srv['description']), 'quimioterapia') || str_contains(strtolower($srv['description']), 'resonancia');

        PdssService::updateOrCreate(
            [
                'pdss_plan_id' => $planId,
                'simon_code' => $srv['simon_code'],
                'pdss_subgroup_id' => $subgroupId,
                'cups_code' => $srv['cups_code']
            ],
            [
                'pdss_group_id' => $groupId,
                'coverage_type' => $srv['coverage_type'],
                'coverage_description' => trim($srv['description']),
                'level_1_covered' => $srv['level_1'],
                'level_2_covered' => $srv['level_2'],
                'level_3_covered' => $srv['level_3'],
                'amount_coverage' => $subgroup ? $subgroup->amount_coverage : 'Ilimitada',
                'copay_type' => $subgroup ? $subgroup->copay_type : 'No',
                'requires_authorization' => true,
                'requires_medical_audit' => $requiresAudit,
                'is_high_cost' => $isHighCost,
                'is_emergency' => $isEmergency,
                'is_hospitalization' => $isHospitalization,
                'is_surgery' => $isSurgery,
                'is_diagnostic_support' => $isDiagnosticSupport,
                'is_medicine' => $isMedicine,
                'is_active' => true,
                'source_page' => $page
            ]
        );
    }
}
