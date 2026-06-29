<?php

namespace Tests\Feature;

use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\PdssService;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use App\Models\Autorizacion;
use App\Models\AuthorizationOverride;
use App\Services\AuthorizationEvaluator;
use App\Models\Catalogo;
use App\Models\ServicioMedico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractAuthorizationRulesTest extends TestCase
{
    use RefreshDatabase;

    protected $afiliado;
    protected $pss;
    protected $pdssService;
    protected $contract;
    protected $version;
    protected $schedule;
    protected $tariffItem;
    protected $user;
    protected $servicioMedico;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear tipo de identificación catalogo
        $tipoId = Catalogo::create([
            'grupo' => 'tipo_identificacion',
            'codigo' => 'CED',
            'descripcion' => 'Cédula de Identidad',
            'activo' => true
        ]);

        // 2. Crear Servicio Médico Fallback para FK
        $this->servicioMedico = ServicioMedico::create([
            'id' => 1,
            'codigo' => 'SM-999',
            'descripcion' => 'Servicio Fallback de Test',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false
        ]);

        // 3. Crear Afiliado
        $this->afiliado = Afiliado::create([
            'tipo_identificacion_id' => $tipoId->id,
            'cedula' => '001-2839281-9',
            'nss' => '998273627',
            'nombres' => 'JUAN EMILIO',
            'primer_apellido' => 'PEREZ',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'M',
            'estado_afiliacion' => 'OK'
        ]);

        // 4. Crear PSS
        $this->pss = Pss::create([
            'rnc' => '101882736',
            'nombre' => 'Centro Médico de Prueba',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
            'nivel_atencion' => 2,
            'tipo_pss' => 'Clínica',
            'red_contratada' => true,
            'contrato_vigente' => true
        ]);

        // 5. Crear planes, grupos y servicio PDSS
        $planId = \Illuminate\Support\Facades\DB::table('pdss_plans')->insertGetId([
            'plan_number' => 'PDSS-11.0',
            'name' => 'Plan de Servicios 11.0',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $groupId = \Illuminate\Support\Facades\DB::table('pdss_groups')->insertGetId([
            'pdss_plan_id' => $planId,
            'code' => 'G1',
            'name' => 'Atención Ambulatoria', // Nombre de grupo compatible con tipo_servicio 'consulta'
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $subgroupId = \Illuminate\Support\Facades\DB::table('pdss_subgroups')->insertGetId([
            'pdss_group_id' => $groupId,
            'code' => 'SG1',
            'name' => 'Consultas generales',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $pdssServiceId = \Illuminate\Support\Facades\DB::table('pdss_services')->insertGetId([
            'pdss_plan_id' => $planId,
            'pdss_group_id' => $groupId,
            'pdss_subgroup_id' => $subgroupId,
            'simon_code' => '900010',
            'coverage_description' => 'Consulta Médica General de Test',
            'requires_authorization' => true,
            'level_3_covered' => 'N',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $this->pdssService = PdssService::find($pdssServiceId);

        // 6. Crear Contrato, Versión y Esquema Tarifario
        $this->contract = PssContract::create([
            'pss_id' => $this->pss->id,
            'contract_number' => 'CONV-TEST-998',
            'contract_name' => 'Convenio Test Red PSS',
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(11),
            'status' => 'vigente'
        ]);

        $this->version = PssContractVersion::create([
            'pss_contract_id' => $this->contract->id,
            'version_number' => '1.0.0',
            'effective_from' => Carbon::now()->subMonths(1),
            'status' => 'vigente'
        ]);

        $this->schedule = PssTariffSchedule::create([
            'pss_contract_id' => $this->contract->id,
            'pss_contract_version_id' => $this->version->id,
            'name' => 'Tarifario Test',
            'effective_from' => Carbon::now()->subMonths(1),
            'status' => 'vigente'
        ]);

        // 7. Crear Ítem de Tarifa
        $this->tariffItem = PssTariffItem::create([
            'pss_tariff_schedule_id' => $this->schedule->id,
            'pss_id' => $this->pss->id,
            'pdss_service_id' => $this->pdssService->id,
            'simon_code_snapshot' => $this->pdssService->simon_code,
            'service_description_snapshot' => $this->pdssService->coverage_description,
            'contracted_amount' => 1500.00,
            'copay_percent' => 20.00,
            'ars_covered_percent' => 80.00,
            'requires_authorization' => true,
            'level_1_allowed' => true,
            'level_2_allowed' => true,
            'level_3_allowed' => true,
            'status' => 'activo'
        ]);

        // 8. Crear Usuario Responsable
        $this->user = User::create([
            'name' => 'Supervisor Contratos',
            'email' => 'supervisor@cmd.gob.do',
            'password' => bcrypt('password'),
            'role' => 'Administrador ARS'
        ]);
        $this->actingAs($this->user);
    }

    /**
     * Prueba que se rechace la autorización si no hay contrato vigente.
     */
    public function test_rejects_authorization_when_pss_has_no_active_contract()
    {
        // Suspender contrato
        $this->contract->update(['status' => 'suspendido']);

        $aut = new Autorizacion([
            'numero_autorizacion' => 'AUT-TEST-001',
            'afiliado_type' => 'titular',
            'afiliado_id' => $this->afiliado->id,
            'pss_id' => $this->pss->id,
            'medico_solicitante' => 'Dr. Interno',
            'diagnostico' => 'Diagnostico general',
            'servicio_medico_id' => $this->servicioMedico->id,
            'pdss_service_id' => $this->pdssService->id,
            'monto_solicitado' => 1200.00,
            'fecha_solicitud' => now()
        ]);

        $eval = AuthorizationEvaluator::evaluar($aut);

        $this->assertEquals('Rechazada', $eval['estado']);
        $this->assertTrue(isset($eval['contract_error']));
    }

    /**
     * Prueba el cálculo correcto de copago y parte cubierta por la ARS.
     */
    public function test_calculates_correct_copay_and_ars_shares()
    {
        $aut = new Autorizacion([
            'numero_autorizacion' => 'AUT-TEST-002',
            'afiliado_type' => 'titular',
            'afiliado_id' => $this->afiliado->id,
            'pss_id' => $this->pss->id,
            'medico_solicitante' => 'Dr. Ginecólogo',
            'diagnostico' => 'Diagnóstico rutinario',
            'servicio_medico_id' => $this->servicioMedico->id,
            'pdss_service_id' => $this->pdssService->id,
            'monto_solicitado' => 1500.00,
            'fecha_solicitud' => now()
        ]);

        $eval = AuthorizationEvaluator::evaluar($aut);

        $this->assertEquals('Aprobada', $eval['estado']);
        // Cobertura pactada al 80% de 1500 = 1200 ARS y 300 Copago afiliado
        $this->assertEquals(1200.00, $eval['monto_ars']);
        $this->assertEquals(300.00, $eval['copago']);
        $this->assertEquals(0.00, $eval['exceso']);
    }

    /**
     * Prueba que el exceso de monto solicitado respecto a la tarifa se desvíe a Auditoría.
     */
    public function test_deflects_to_audit_when_amount_requested_exceeds_tariff()
    {
        $aut = new Autorizacion([
            'numero_autorizacion' => 'AUT-TEST-003',
            'afiliado_type' => 'titular',
            'afiliado_id' => $this->afiliado->id,
            'pss_id' => $this->pss->id,
            'medico_solicitante' => 'Dr. Cardiólogo',
            'diagnostico' => 'Insuficiencia Cardiaca',
            'servicio_medico_id' => $this->servicioMedico->id,
            'pdss_service_id' => $this->pdssService->id,
            'monto_solicitado' => 2000.00, // Excede los 1500.00 contratados
            'fecha_solicitud' => now()
        ]);

        $eval = AuthorizationEvaluator::evaluar($aut);

        $this->assertEquals('Auditoría', $eval['estado']);
        $this->assertTrue(isset($eval['monto_excedido']));
    }

    /**
     * Prueba que el cambio futuro de tarifas no altere los snapshots de autorizaciones pasadas.
     */
    public function test_preserves_past_snapshots_after_tariff_upgrade()
    {
        // Crear autorización histórica
        $aut = Autorizacion::create([
            'numero_autorizacion' => 'AUT-HISTORICA-999',
            'afiliado_type' => 'titular',
            'afiliado_id' => $this->afiliado->id,
            'pss_id' => $this->pss->id,
            'medico_solicitante' => 'Dr. Histórico',
            'diagnostico' => 'Gripe común',
            'servicio_medico_id' => $this->servicioMedico->id,
            'procedimiento' => $this->pdssService->coverage_description,
            'monto_solicitado' => 1500.00,
            'monto_contratado' => 1500.00,
            'copago' => 300.00,
            'exceso' => 0.00,
            'estado' => 'Aprobada',
            'fecha_solicitud' => now()->subMonths(2),
            // Snapshots V2
            'pss_contract_id' => $this->contract->id,
            'pss_contract_version_id' => $this->version->id,
            'pss_tariff_schedule_id' => $this->schedule->id,
            'pss_tariff_item_id' => $this->tariffItem->id,
            'contracted_amount_snapshot' => 1500.00,
            'affiliate_copay_amount' => 300.00,
            'ars_amount' => 1200.00,
            'non_covered_amount' => 0.00
        ]);

        // Modificar tarifa del contrato en el presente
        $this->tariffItem->update(['contracted_amount' => 1800.00]);

        // Consultar autorización histórica de la base de datos
        $reloaded = Autorizacion::findOrFail($aut->id);

        // Comprobar que sigue valiendo 1500.00 en base al snapshot
        $this->assertEquals(1500.00, $reloaded->contracted_amount_snapshot);
        $this->assertEquals(300.00, $reloaded->affiliate_copay_amount);
    }

    /**
     * Prueba que el endpoint AJAX de consulta de tarifas devuelva los datos correctos del convenio.
     */
    public function test_can_fetch_tariff_dynamically_via_ajax()
    {
        $response = $this->get(route('ars.autorizaciones_medicas.obtener_tarifa_ajax', [
            'pss_id' => $this->pss->id,
            'pdss_service_id' => $this->pdssService->id
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'contracted_amount' => 1500.00,
            'copay_percent' => 20.00,
            'requires_authorization' => true
        ]);
    }

    /**
     * Prueba que el endpoint de búsqueda AJAX del catálogo filtre los servicios en base al convenio V2 de la PSS.
     */
    public function test_can_filter_pdss_catalog_services_by_pss_contract_v2()
    {
        // Crear un servicio alterno que NO está contratado para esta PSS
        $planId = \Illuminate\Support\Facades\DB::table('pdss_plans')->first()->id;
        $groupId = \Illuminate\Support\Facades\DB::table('pdss_groups')->first()->id;
        $subgroupId = \Illuminate\Support\Facades\DB::table('pdss_subgroups')->first()->id;

        $uncontractedServiceId = \Illuminate\Support\Facades\DB::table('pdss_services')->insertGetId([
            'pdss_plan_id' => $planId,
            'pdss_group_id' => $groupId,
            'pdss_subgroup_id' => $subgroupId,
            'simon_code' => '999999',
            'coverage_description' => 'Servicio No Contratado Especial',
            'requires_authorization' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 1. Buscar a nivel global: debe retornar ambos
        $responseGlobal = $this->get(route('ars.pdss.buscar_servicio', [
            'q' => 'Test'
        ]));
        $responseGlobal->assertStatus(200);
        $globalResults = $responseGlobal->json();
        $this->assertTrue(collect($globalResults)->contains('simon_code', '900010'));

        // 2. Buscar filtrando por PSS: solo debe retornar el contratado (900010) y omitir el alterno (999999)
        $responseFiltered = $this->get(route('ars.pdss.buscar_servicio', [
            'q' => 'Servicio',
            'pss_id' => $this->pss->id
        ]));
        $responseFiltered->assertStatus(200);
        $filteredResults = $responseFiltered->json();
        
        $this->assertFalse(collect($filteredResults)->contains('simon_code', '999999'));
    }
}
