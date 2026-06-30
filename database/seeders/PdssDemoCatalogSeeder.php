<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PdssPlan;
use App\Models\PdssGroup;
use App\Models\PdssSubgroup;
use App\Models\PdssService;
use Illuminate\Support\Facades\DB;

class PdssDemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Plan PDSS único
        $plan = PdssPlan::updateOrCreate(
            ['plan_number' => 'PDSS-11.0'],
            [
                'name' => 'Plan de Servicios de Salud PDSS 11.0',
                'resolution' => 'Res. SISALRIL 245-25',
                'version' => '11.0',
                'source_file' => 'Catalogo_PDSS_Demo.xlsx',
                'imported_at' => now(),
                'is_active' => true
            ]
        );

        // 2. Crear Grupos
        $gruposData = [
            ['code' => 'G1', 'name' => 'Prevención y Promoción', 'description' => 'Servicios preventivos y vacunas.'],
            ['code' => 'G2', 'name' => 'Atención Ambulatoria', 'description' => 'Consultas y atenciones que no requieren internamiento.'],
            ['code' => 'G3', 'name' => 'Apoyo Diagnóstico (Laboratorios)', 'description' => 'Analíticas y laboratorios clínicos.'],
            ['code' => 'G4', 'name' => 'Apoyo Diagnóstico (Imágenes)', 'description' => 'Radiografías, resonancias, ecografías.'],
            ['code' => 'G5', 'name' => 'Hospitalización y Cirugía', 'description' => 'Internamientos clínicos y procedimientos de quirófano.'],
            ['code' => 'G6', 'name' => 'Tratamientos Especiales y Alto Costo', 'description' => 'Oncología, trasplantes y enfermedades catastróficas.']
        ];

        $groups = [];
        foreach ($gruposData as $g) {
            $groups[$g['code']] = PdssGroup::updateOrCreate(
                ['pdss_plan_id' => $plan->id, 'code' => $g['code']],
                ['name' => $g['name'], 'description' => $g['description'], 'sort_order' => 1]
            );
        }

        // 3. Crear Subgrupos
        $subgruposData = [
            ['group' => 'G1', 'code' => 'S1.1', 'name' => 'Vacunas y Chequeos Preventivos'],
            ['group' => 'G2', 'code' => 'S2.1', 'name' => 'Consultas de Especialidades'],
            ['group' => 'G3', 'code' => 'S3.1', 'name' => 'Analíticas de Sangre y Orina'],
            ['group' => 'G4', 'code' => 'S4.1', 'name' => 'Radiología e Imagenología'],
            ['group' => 'G5', 'code' => 'S5.1', 'name' => 'Cirugías Generales y Obstetricia'],
            ['group' => 'G6', 'code' => 'S6.1', 'name' => 'Quimioterapia y Oncología Especializada']
        ];

        $subgroups = [];
        foreach ($subgruposData as $s) {
            $groupId = $groups[$s['group']]->id;
            $subgroups[$s['code']] = PdssSubgroup::updateOrCreate(
                ['pdss_group_id' => $groupId, 'code' => $s['code']],
                ['name' => $s['name'], 'sort_order' => 1]
            );
        }

        // 4. Crear Servicios PDSS representativos
        $servicios = [
            // Prevención
            [
                'group' => 'G1', 'subgroup' => 'S1.1', 'simon_code' => '101001',
                'coverage_type' => 'Prevención', 'coverage_description' => 'Consulta Médica de Control de Niño Sano',
                'cups_code' => '890201', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            // Consultas
            [
                'group' => 'G2', 'subgroup' => 'S2.1', 'simon_code' => '201001',
                'coverage_type' => 'Consulta', 'coverage_description' => 'Consulta de Pediatría General',
                'cups_code' => '890202', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            [
                'group' => 'G2', 'subgroup' => 'S2.1', 'simon_code' => '201002',
                'coverage_type' => 'Consulta', 'coverage_description' => 'Consulta de Cardiología Especializada',
                'cups_code' => '890203', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            [
                'group' => 'G2', 'subgroup' => 'S2.1', 'simon_code' => '201003',
                'coverage_type' => 'Consulta', 'coverage_description' => 'Consulta de Ginecología y Obstetricia',
                'cups_code' => '890204', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            // Analíticas (Laboratorios)
            [
                'group' => 'G3', 'subgroup' => 'S3.1', 'simon_code' => '301001',
                'coverage_type' => 'Laboratorio', 'coverage_description' => 'Hemograma Completo Automatizado (CBC)',
                'cups_code' => '902204', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G3', 'subgroup' => 'S3.1', 'simon_code' => '301002',
                'coverage_type' => 'Laboratorio', 'coverage_description' => 'Glicemia en Ayunas (Glucosa)',
                'cups_code' => '903839', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G3', 'subgroup' => 'S3.1', 'simon_code' => '301003',
                'coverage_type' => 'Laboratorio', 'coverage_description' => 'Examen Completo de Orina (Uroanálisis)',
                'cups_code' => '907106', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G3', 'subgroup' => 'S3.1', 'simon_code' => '301004',
                'coverage_type' => 'Laboratorio', 'coverage_description' => 'Perfil de Lípidos Completo (Colesterol, Triglicéridos)',
                'cups_code' => '903815', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G3', 'subgroup' => 'S3.1', 'simon_code' => '301005',
                'coverage_type' => 'Laboratorio', 'coverage_description' => 'Creatinina en Sangre (Función Renal)',
                'cups_code' => '903825', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            // Imágenes
            [
                'group' => 'G4', 'subgroup' => 'S4.1', 'simon_code' => '401001',
                'coverage_type' => 'Radiología', 'coverage_description' => 'Radiografía de Tórax (P.A. o Lateral)',
                'cups_code' => '871121', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G4', 'subgroup' => 'S4.1', 'simon_code' => '401002',
                'coverage_type' => 'Radiología', 'coverage_description' => 'Sonografía Abdominal Completa',
                'cups_code' => '881301', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G4', 'subgroup' => 'S4.1', 'simon_code' => '401003',
                'coverage_type' => 'Radiología', 'coverage_description' => 'Mamografía Bilateral con Proyección',
                'cups_code' => '876801', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            [
                'group' => 'G4', 'subgroup' => 'S4.1', 'simon_code' => '401004',
                'coverage_type' => 'Estudio', 'coverage_description' => 'Electrocardiograma (EKG) de 12 Derivaciones',
                'cups_code' => '895100', 'level_1' => 'S', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => false, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => true, 'medicine' => false
            ],
            // Quirúrgicos
            [
                'group' => 'G5', 'subgroup' => 'S5.1', 'simon_code' => '501001',
                'coverage_type' => 'Cirugía', 'coverage_description' => 'Apendicectomía Quirúrgica Convencional',
                'cups_code' => '470100', 'level_1' => 'N', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => true, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            [
                'group' => 'G5', 'subgroup' => 'S5.1', 'simon_code' => '501002',
                'coverage_type' => 'Cirugía', 'coverage_description' => 'Herniorrafía Inguinal Unilateral',
                'cups_code' => '530001', 'level_1' => 'N', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            [
                'group' => 'G5', 'subgroup' => 'S5.1', 'simon_code' => '501003',
                'coverage_type' => 'Cirugía', 'coverage_description' => 'Parto Vaginal Normal / Asistencia Obstétrica',
                'cups_code' => '735300', 'level_1' => 'N', 'level_2' => 'S', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => false, 'high_cost' => false, 'diagnostic' => false, 'medicine' => false
            ],
            // Alto Costo
            [
                'group' => 'G6', 'subgroup' => 'S6.1', 'simon_code' => '601001',
                'coverage_type' => 'Alto Costo', 'coverage_description' => 'Tratamiento de Quimioterapia Intratecal u Oncológica',
                'cups_code' => '992501', 'level_1' => 'N', 'level_2' => 'N', 'level_3' => 'S',
                'requires_auth' => true, 'requires_audit' => true, 'is_high_cost' => true, 'diagnostic' => false, 'medicine' => false
            ]
        ];

        foreach ($servicios as $s) {
            $groupId = $groups[$s['group']]->id;
            $subgroupId = $subgroups[$s['subgroup']]->id;

            // Construir estructura JSON simulada para raw_text (coberturas y porcentajes)
            $rawJson = json_encode([
                'cuota_ars_pct' => 80.00,
                'cuota_afil_pct' => 20.00,
                'cobertura_tope' => ($s['high_cost'] ?? false) ? 1000000.00 : 50000.00,
                'cuota_afil_tope' => 10000.00
            ]);

            PdssService::updateOrCreate(
                ['pdss_plan_id' => $plan->id, 'simon_code' => $s['simon_code']],
                [
                    'pdss_group_id' => $groupId,
                    'pdss_subgroup_id' => $subgroupId,
                    'coverage_type' => $s['coverage_type'],
                    'coverage_description' => $s['coverage_description'],
                    'cups_code' => $s['cups_code'],
                    'level_1_covered' => $s['level_1'],
                    'level_2_covered' => $s['level_2'],
                    'level_3_covered' => $s['level_3'],
                    'amount_coverage' => '80%',
                    'copay_type' => '20%',
                    'requires_authorization' => $s['requires_auth'],
                    'requires_medical_audit' => $s['requires_audit'],
                    'is_high_cost' => $s['high_cost'] ?? false,
                    'is_emergency' => false,
                    'is_hospitalization' => false,
                    'is_surgery' => $s['coverage_type'] === 'Cirugía',
                    'is_diagnostic_support' => $s['diagnostic'],
                    'is_medicine' => $s['medicine'],
                    'is_active' => true,
                    'raw_text' => $rawJson
                ]
            );
        }
    }
}
