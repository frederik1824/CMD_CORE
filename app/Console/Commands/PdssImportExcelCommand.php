<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PdssPlan;
use App\Models\PdssGroup;
use App\Models\PdssSubgroup;
use App\Models\PdssService;
use App\Models\PdssImportLog;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PdssImportExcelCommand extends Command
{
    protected $signature = 'pdss:import-excel {path?}';
    protected $description = 'Import the official PDSS 10.0 catalog from an Excel (.xlsx) file';

    public function handle()
    {
        $path = $this->argument('path') ?? base_path('Catalogo-Prestaciones-del-PDSS-10.0-Reg.-Contributivo-y-Planes-de-Pensionados-y-Jubilados.xlsx');

        if (!file_exists($path)) {
            $this->error("El archivo Excel no existe en la ruta: $path");
            return 1;
        }

        $this->info("Leyendo catálogo PDSS 10.0 desde: " . basename($path));

        $startedAt = now();
        $log = PdssImportLog::create([
            'source_file' => basename($path),
            'status' => 'Procesando',
            'started_at' => $startedAt,
        ]);

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $totalRows = $sheet->getHighestRow();

            $this->info("Total de filas detectadas: $totalRows");

            // Crear o actualizar Plan
            $plan = PdssPlan::updateOrCreate(
                ['plan_number' => '00000014'],
                [
                    'name' => 'PDSS 10.0 RÉGIMEN CONTRIBUTIVO',
                    'resolution' => 'Resolución CNSS - PDSS 10.0',
                    'version' => '10.0',
                    'source_file' => basename($path),
                    'imported_at' => now(),
                    'is_active' => true,
                ]
            );

            $groupsCount = 0;
            $subgroupsCount = 0;
            $servicesCount = 0;
            $skippedRows = 0;

            // Cache de grupos y subgrupos para evitar queries repetitivos
            $groupCache = [];
            $subgroupCache = [];

            // Mapeo de reglas de negocio por grupo
            $businessRules = $this->getBusinessRules();

            DB::transaction(function () use ($sheet, $totalRows, $plan, &$groupsCount, &$subgroupsCount, &$servicesCount, &$skippedRows, $groupCache, $subgroupCache, $businessRules) {

                for ($row = 2; $row <= $totalRows; $row++) {
                    // Leer columnas del Excel
                    $planNum       = trim((string) $sheet->getCell('A' . $row)->getValue());
                    $planName      = trim((string) $sheet->getCell('B' . $row)->getValue());
                    $grupoNum      = trim((string) $sheet->getCell('C' . $row)->getValue());
                    $grupoDesc     = trim((string) $sheet->getCell('D' . $row)->getValue());
                    $subgrupoNum   = trim((string) $sheet->getCell('E' . $row)->getValue());
                    $subgrupoDesc  = trim((string) $sheet->getCell('F' . $row)->getValue());
                    $covNum        = trim((string) $sheet->getCell('G' . $row)->getValue());
                    $covDesc       = trim((string) $sheet->getCell('H' . $row)->getValue());
                    $cupsCode      = trim((string) $sheet->getCell('I' . $row)->getValue());
                    $covTypeNum    = trim((string) $sheet->getCell('J' . $row)->getValue());
                    $covTypeDesc   = trim((string) $sheet->getCell('K' . $row)->getValue());
                    $nivel1        = strtoupper(trim((string) $sheet->getCell('L' . $row)->getValue()));
                    $nivel2        = strtoupper(trim((string) $sheet->getCell('M' . $row)->getValue()));
                    $nivel3        = strtoupper(trim((string) $sheet->getCell('N' . $row)->getValue()));
                    $cuotaTipoNum  = trim((string) $sheet->getCell('Q' . $row)->getValue());
                    $cuotaTipoDesc = trim((string) $sheet->getCell('R' . $row)->getValue());
                    $coberturaTipoDesc = trim((string) $sheet->getCell('S' . $row)->getValue());
                    $cuotaArsPct   = trim((string) $sheet->getCell('T' . $row)->getValue());
                    $cuotaAfilPct  = trim((string) $sheet->getCell('U' . $row)->getValue());
                    $coberturaTope = trim((string) $sheet->getCell('V' . $row)->getValue());
                    $cuotaAfilTope = trim((string) $sheet->getCell('W' . $row)->getValue());

                    // Validar datos mínimos
                    if (empty($grupoNum) || empty($grupoDesc)) {
                        $skippedRows++;
                        continue;
                    }

                    // 1. Crear/Actualizar Grupo
                    $groupKey = $plan->id . ':' . $grupoNum;
                    if (!isset($groupCache[$groupKey])) {
                        $group = PdssGroup::updateOrCreate(
                            ['pdss_plan_id' => $plan->id, 'code' => $grupoNum],
                            [
                                'name' => $grupoDesc,
                                'description' => $grupoDesc,
                                'sort_order' => (int) $grupoNum,
                                'is_active' => true,
                            ]
                        );
                        $groupCache[$groupKey] = $group->id;
                        $groupsCount++;
                    }
                    $groupId = $groupCache[$groupKey];

                    // 2. Crear/Actualizar Subgrupo
                    if (!empty($subgrupoNum) && !empty($subgrupoDesc)) {
                        $subgroupKey = $groupId . ':' . $subgrupoNum;
                        if (!isset($subgroupCache[$subgroupKey])) {
                            $subgroup = PdssSubgroup::updateOrCreate(
                                ['pdss_group_id' => $groupId, 'code' => $subgrupoNum],
                                [
                                    'name' => $subgrupoDesc,
                                    'amount_coverage' => $coberturaTope !== '' && $coberturaTope !== 'NULL' ? $coberturaTipoDesc : 'Ilimitada',
                                    'copay_type' => $cuotaTipoDesc !== '' && $cuotaTipoDesc !== 'NULL' ? $cuotaTipoDesc : 'No',
                                    'description' => $subgrupoDesc,
                                    'sort_order' => (int) $subgrupoNum,
                                    'is_active' => true,
                                ]
                            );
                            $subgroupCache[$subgroupKey] = $subgroup->id;
                            $subgroupsCount++;
                        }
                        $subgroupId = $subgroupCache[$subgroupKey];
                    } else {
                        continue; // Sin subgrupo válido, saltar
                    }

                    // 3. Determinar flags de negocio basado en el grupo
                    $rules = $businessRules[$grupoNum] ?? $businessRules['default'];

                    $isHighCost          = $rules['is_high_cost'];
                    $isEmergency         = $rules['is_emergency'];
                    $isHospitalization   = $rules['is_hospitalization'];
                    $isSurgery           = $rules['is_surgery'];
                    $isDiagnosticSupport = $rules['is_diagnostic_support'];
                    $isMedicine          = $rules['is_medicine'];
                    $requiresAudit       = $rules['requires_audit'];
                    $requiresAuth        = $rules['requires_authorization'];

                    // Ajustes especiales por descripción
                    $descLower = strtolower($covDesc . ' ' . $subgrupoDesc);
                    if (str_contains($descLower, 'quimioterapia') || str_contains($descLower, 'radioterapia')) {
                        $requiresAudit = true;
                        $isHighCost = true;
                    }
                    if (str_contains($descLower, 'resonancia') || str_contains($descLower, 'tomografía') || str_contains($descLower, 'tac')) {
                        $requiresAudit = true;
                    }

                    // 4. Crear/Actualizar Servicio (usando covDesc + cupsCode como unique identifier)
                    $simonCode = $covNum !== '' && $covNum !== 'NULL' ? $covNum : $row;
                    $cupsCodeFinal = $cupsCode !== '' && $cupsCode !== '0' && $cupsCode !== 'NULL' ? $cupsCode : null;

                    PdssService::updateOrCreate(
                        [
                            'pdss_plan_id'      => $plan->id,
                            'simon_code'        => (string) $simonCode,
                            'pdss_subgroup_id'  => $subgroupId,
                            'cups_code'         => $cupsCodeFinal ?? '',
                        ],
                        [
                            'pdss_group_id'         => $groupId,
                            'coverage_type'         => $covTypeDesc !== '' && $covTypeDesc !== 'NULL' ? $covTypeDesc : $covDesc,
                            'coverage_description'  => $covDesc,
                            'level_1_covered'       => ($nivel1 === 'S') ? 'S' : 'N',
                            'level_2_covered'       => ($nivel2 === 'S') ? 'S' : 'N',
                            'level_3_covered'       => ($nivel3 === 'S') ? 'S' : 'N',
                            'amount_coverage'       => $coberturaTope !== '' && $coberturaTope !== 'NULL' && $coberturaTope !== '99999999.00' ? 'RD$ ' . number_format((float)$coberturaTope, 2) : 'Ilimitada',
                            'copay_type'            => $cuotaTipoDesc !== '' && $cuotaTipoDesc !== 'NULL' ? $cuotaTipoDesc : 'No',
                            'requires_authorization' => $requiresAuth,
                            'requires_medical_audit' => $requiresAudit,
                            'is_high_cost'          => $isHighCost,
                            'is_emergency'          => $isEmergency,
                            'is_hospitalization'    => $isHospitalization,
                            'is_surgery'            => $isSurgery,
                            'is_diagnostic_support' => $isDiagnosticSupport,
                            'is_medicine'           => $isMedicine,
                            'is_active'             => true,
                            'source_page'           => $row,
                            'raw_text'              => json_encode([
                                'grupo_num'          => $grupoNum,
                                'grupo_desc'         => $grupoDesc,
                                'subgrupo_num'       => $subgrupoNum,
                                'subgrupo_desc'      => $subgrupoDesc,
                                'cuota_ars_pct'      => $cuotaArsPct,
                                'cuota_afil_pct'     => $cuotaAfilPct,
                                'cobertura_tope'     => $coberturaTope,
                                'cuota_afil_tope'    => $cuotaAfilTope,
                                'coverage_type_desc' => $covTypeDesc,
                            ]),
                        ]
                    );
                    $servicesCount++;
                }
            });

            // Actualizar log
            $log->update([
                'total_groups'    => PdssGroup::count(),
                'total_subgroups' => PdssSubgroup::count(),
                'total_services'  => PdssService::count(),
                'status'          => 'Completado',
                'finished_at'     => now(),
                'errors'          => $skippedRows > 0 ? "Filas omitidas (sin grupo válido): $skippedRows" : null,
            ]);

            $this->info("✅ Importación completada con éxito!");
            $this->info("   Grupos: " . PdssGroup::count());
            $this->info("   Subgrupos: " . PdssSubgroup::count());
            $this->info("   Servicios: " . PdssService::count());
            $this->info("   Filas omitidas: $skippedRows");

            // Resumen por grupo
            $this->info("\n📊 Resumen por grupo:");
            PdssGroup::where('pdss_plan_id', $plan->id)
                ->withCount('services')
                ->orderBy('code')
                ->get()
                ->each(fn($g) => $this->info("   Grupo {$g->code}: {$g->name} ({$g->services_count} servicios)"));

            return 0;

        } catch (\Exception $e) {
            $log->update([
                'status'      => 'Error',
                'errors'      => $e->getMessage() . "\n" . $e->getTraceAsString(),
                'finished_at' => now(),
            ]);
            $this->error("❌ Error durante la importación: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Reglas de negocio por grupo del PDSS 10.0
     */
    private function getBusinessRules(): array
    {
        return [
            // Grupo 1: Prevención y Promoción
            '1' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => false, // Prevención generalmente no requiere autorización
            ],
            // Grupo 2: Atención Ambulatoria (Consultas)
            '2' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true,
            ],
            // Grupo 3: Servicios Odontológicos
            '3' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true,
            ],
            // Grupo 4: Emergencia
            '4' => [
                'is_high_cost' => false,
                'is_emergency' => true,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true, // Se evalúa post-atención
            ],
            // Grupo 5: Hospitalización
            '5' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => true,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true, // Hospitalización siempre requiere auditoría
                'requires_authorization' => true,
            ],
            // Grupo 6: Partos
            '6' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => true, // Se maneja como hospitalización
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true,
                'requires_authorization' => true,
            ],
            // Grupo 7: Cirugía
            '7' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => true,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true, // Cirugía siempre requiere auditoría
                'requires_authorization' => true,
            ],
            // Grupo 8: Apoyo Diagnóstico
            '8' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => true,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true,
            ],
            // Grupo 9: Alto Costo
            '9' => [
                'is_high_cost' => true,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true, // Alto costo siempre requiere auditoría médica
                'requires_authorization' => true,
            ],
            // Grupo 10: Rehabilitación
            '10' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true,
            ],
            // Grupo 11: Hemoterapia
            '11' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true,
                'requires_authorization' => true,
            ],
            // Grupo 12: Medicamentos Ambulatorios
            '12' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => true,
                'requires_audit' => false,
                'requires_authorization' => true, // Medicamentos requieren autorización + disponibilidad
            ],
            // Grupo 13: Trasplante Renal
            '13' => [
                'is_high_cost' => true,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => true, // Trasplante requiere auditoría
                'requires_authorization' => true,
            ],
            // Grupo 16: Coberturas Capitadas
            '16' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => false, // Capitadas se manejan diferente
            ],
            // Default
            'default' => [
                'is_high_cost' => false,
                'is_emergency' => false,
                'is_hospitalization' => false,
                'is_surgery' => false,
                'is_diagnostic_support' => false,
                'is_medicine' => false,
                'requires_audit' => false,
                'requires_authorization' => true,
            ],
        ];
    }
}
