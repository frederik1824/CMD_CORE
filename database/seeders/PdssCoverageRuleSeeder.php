<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PdssCoverageRule;
use App\Models\PdssGroup;
use App\Models\PdssService;
use Illuminate\Support\Facades\DB;

class PdssCoverageRuleSeeder extends Seeder
{
    public function run(): void
    {
        $groups = PdssGroup::orderBy('code')->get();

        foreach ($groups as $group) {
            $this->createRuleForGroup($group);
        }
    }

    private function createRuleForGroup(PdssGroup $group): void
    {
        // Obtener datos promedio de los servicios del grupo desde raw_text
        $stats = DB::table('pdss_services')
            ->where('pdss_group_id', $group->id)
            ->selectRaw('
                COUNT(*) as total,
                AVG(CAST(JSON_EXTRACT(raw_text, "$.cuota_ars_pct") AS DECIMAL(10,2))) as avg_ars_pct,
                AVG(CAST(JSON_EXTRACT(raw_text, "$.cuota_afil_pct") AS DECIMAL(10,2))) as avg_afil_pct,
                MAX(CAST(JSON_EXTRACT(raw_text, "$.cobertura_tope") AS DECIMAL(15,2))) as max_tope,
                MIN(CAST(JSON_EXTRACT(raw_text, "$.cobertura_tope") AS DECIMAL(15,2))) as min_tope,
                AVG(CAST(JSON_EXTRACT(raw_text, "$.cuota_afil_tope") AS DECIMAL(15,2))) as avg_afil_tope
            ')
            ->first();

        $arsPct = round((float) ($stats->avg_ars_pct ?? 80), 2);
        $afilPct = round((float) ($stats->avg_afil_pct ?? 20), 2);
        $maxTope = (float) ($stats->max_tope ?? 0);
        $minTope = (float) ($stats->min_tope ?? 0);
        $avgAfilTope = round((float) ($stats->avg_afil_tope ?? 0), 2);

        // Determinar tipo de cobertura
        $hasLimit = $maxTope > 0 && $maxTope < 99999999;
        $limitType = $hasLimit ? 'tope' : 'ilimitada';

        // Determinar si requiere validación de continuidad/antigüedad
        $requiresContinuity = in_array((int)$group->code, [9, 13]); // Alto Costo y Trasplante
        $requiresSeniority = in_array((int)$group->code, [13]); // Solo Trasplante

        // Obtener flags de requerimiento del grupo
        $sampleService = PdssService::where('pdss_group_id', $group->id)->first();
        $requiresAuth = $sampleService?->requires_authorization ?? true;
        $requiresAudit = $sampleService?->requires_medical_audit ?? false;

        // Crear la regla
        PdssCoverageRule::updateOrCreate(
            [
                'plan_code' => '00000014',
                'service_group' => $group->name,
            ],
            [
                'plan_name' => 'PDSS 10.0 RÉGIMEN CONTRIBUTIVO',
                'effective_date' => '2024-01-01',
                'service_subgroup' => null,
                'coverage_limit_type' => $limitType,
                'coverage_limit_amount' => $hasLimit ? $maxTope : 0,
                'coverage_percent_ars' => $arsPct,
                'copay_percent_affiliate' => $afilPct,
                'copay_fixed_amount' => 0,
                'copay_cap_amount' => $avgAfilTope,
                'annual_limit' => $hasLimit ? $maxTope : 0,
                'event_limit' => 0,
                'daily_limit' => 0,
                'requires_continuity_validation' => $requiresContinuity,
                'requires_seniority_validation' => $requiresSeniority,
                'requires_authorization' => $requiresAuth,
                'requires_medical_audit' => $requiresAudit,
                'notes' => "Regla generada del catálogo PDSS 10.0. Servicios: {$stats->total}. ARS: {$arsPct}%, Afiliado: {$afilPct}%",
                'is_active' => true,
            ]
        );

        $this->command->info("  Grupo {$group->code} - {$group->name}: ARS {$arsPct}% / Afil {$afilPct}% ({$stats->total} servicios)");
    }
}
