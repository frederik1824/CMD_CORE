<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AuthorizationEngineRule;

class DemoCoreOperationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cargar catálogo PDSS y reglas de cobertura
        $this->call([
            PdssDemoCatalogSeeder::class,
            PdssCoverageRuleSeeder::class,
        ]);

        // 2. Crear reglas por defecto para el motor de reglas de autorizaciones
        $reglas = [
            [
                'rule_code' => 'R-AFIL-ACT-ENG',
                'name' => 'Validación de Afiliado Activo en Motor',
                'description' => 'Verifica de forma estricta que el afiliado no se encuentre en estado inactivo o suspendido.',
                'process' => 'Core',
                'severity' => 'blocking',
                'priority' => 1,
                'condition_json' => ['field' => 'afiliado_estado', 'operator' => '!=', 'value' => 'OK'],
                'action_json' => ['type' => 'set_status', 'params' => ['severity' => 'blocking', 'message' => 'El afiliado no posee estatus activo en el padrón de la seguridad social.']],
                'status' => 'Activa',
            ],
            [
                'rule_code' => 'R-MONTO-LIMITE-ENG',
                'name' => 'Validación de Monto Máximo Automático',
                'description' => 'Deriva a auditoría médica cualquier solicitud que supere el monto de DOP 15,000.',
                'process' => 'Core',
                'severity' => 'audit_required',
                'priority' => 2,
                'condition_json' => ['field' => 'monto_solicitado', 'operator' => '>', 'value' => '15000'],
                'action_json' => ['type' => 'set_status', 'params' => ['severity' => 'audit_required', 'message' => 'El monto solicitado supera el límite de aprobación directa (DOP 15,000) y requiere auditoría clínica.']],
                'status' => 'Activa',
            ],
            [
                'rule_code' => 'R-ALTO-COST-ENG',
                'name' => 'Auditoría Médica para Servicios de Alto Costo',
                'description' => 'Bloquea o envía a auditoría cualquier servicio marcado como de alto costo en el catálogo PDSS.',
                'process' => 'Core',
                'severity' => 'audit_required',
                'priority' => 3,
                'condition_json' => ['field' => 'servicio_alto_costo', 'operator' => '==', 'value' => '1'],
                'action_json' => ['type' => 'set_status', 'params' => ['severity' => 'audit_required', 'message' => 'La prestación corresponde a un servicio calificado como de alto costo.']],
                'status' => 'Activa',
            ],
        ];

        foreach ($reglas as $r) {
            AuthorizationEngineRule::updateOrCreate(
                ['rule_code' => $r['rule_code']],
                $r
            );
        }
    }
}
