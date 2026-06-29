<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Pss;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\ServicioMedico;
use App\Models\Autorizacion;
use App\Models\Documento;
use App\Services\UnipagoMockService;
use App\Services\AuthorizationEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed catalog codes since our models/services depend on them.
        $this->seedCatalogos();
    }

    private function seedCatalogos(): void
    {
        $catalogs = [
            ['grupo' => 'estado_lote', 'codigo' => 'VE', 'descripcion' => 'Validado en Espera'],
            ['grupo' => 'estado_lote', 'codigo' => 'PC', 'descripcion' => 'Procesando'],
            ['grupo' => 'estado_lote', 'codigo' => 'PE', 'descripcion' => 'Procesado con Errores'],
            ['grupo' => 'estado_lote', 'codigo' => 'RE', 'descripcion' => 'Rechazado'],
            ['grupo' => 'estado_lote', 'codigo' => 'EV', 'descripcion' => 'Enviado a Validar / Procesado OK'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'OK', 'descripcion' => 'Aprobada'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'PE', 'descripcion' => 'Pendiente'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'RE', 'descripcion' => 'Rechazada'],
            ['grupo' => 'parentesco', 'codigo' => 'HIJO', 'descripcion' => 'Hijo / Hija'],
            ['grupo' => 'parentesco', 'codigo' => 'CONYUGE', 'descripcion' => 'Cónyuge / Compañero'],
            ['grupo' => 'parentesco', 'codigo' => 'OTROS', 'descripcion' => 'Otros Dependientes'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'CED', 'descripcion' => 'Cédula'],
        ];

        foreach ($catalogs as $cat) {
            \App\Models\Catalogo::create($cat);
        }
    }

    /**
     * Test Unipago Mock validation rules by last digit of Cedula.
     */
    public function test_unipago_cedula_validation_rules(): void
    {
        // 0 and 1 -> Apto
        $res0 = UnipagoMockService::consultarDisponibilidadAfiliacion('00100000000');
        $this->assertTrue($res0['apto']);
        $this->assertEquals('OK', $res0['motivo_codigo']);

        $res1 = UnipagoMockService::consultarDisponibilidadAfiliacion('00100000001');
        $this->assertTrue($res1['apto']);

        // 2 -> Ya afiliado (PE75)
        $res2 = UnipagoMockService::consultarDisponibilidadAfiliacion('00100000002');
        $this->assertFalse($res2['apto']);
        $this->assertEquals('PE75', $res2['motivo_codigo']);

        // 3 -> Otra ARS (RE)
        $res3 = UnipagoMockService::consultarDisponibilidadAfiliacion('00100000003');
        $this->assertFalse($res3['apto']);
        $this->assertEquals('RE', $res3['motivo_codigo']);
        $this->assertStringContainsString('otra ARS', $res3['motivo_descripcion']);

        // 6 -> Sin nomina (RE)
        $res6 = UnipagoMockService::consultarDisponibilidadAfiliacion('00100000006');
        $this->assertFalse($res6['apto']);
        $this->assertEquals('RE', $res6['motivo_codigo']);
        $this->assertStringContainsString('Sin nómina activa', $res6['motivo_descripcion']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 1 - Inactive Affiliate.
     */
    public function test_auth_rule_inactive_affiliate(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'RE', // Inactivo/Rechazado
            'activo_nomina' => false,
            'tiene_aporte' => false,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Test',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta General',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Rechazada', $eval['estado']);
        $this->assertStringContainsString('no se encuentra activo', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 2 - Inactive PSS.
     */
    public function test_auth_rule_inactive_pss(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Inactiva',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Inactiva',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta General',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Rechazada', $eval['estado']);
        $this->assertStringContainsString('se encuentra inactiva', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 3 - PSS No Active Contract.
     */
    public function test_auth_rule_no_contract(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Sin Contrato',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta General',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Auditoría', $eval['estado']);
        $this->assertStringContainsString('no posee un contrato vigente', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 4 - Missing Required Document.
     */
    public function test_auth_rule_missing_document(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'IMA-001',
            'descripcion' => 'Radiografía de Tórax',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => true, // Requiere documento!
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio->id,
            'monto_tarifa' => 2000.00,
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Pendiente Documento', $eval['estado']);
        $this->assertStringContainsString('requiere adjuntar documento', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 5 - Alto Costo.
     */
    public function test_auth_rule_alto_costo(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'ATC-001',
            'descripcion' => 'Quimioterapia',
            'cobertura_base' => 90.00,
            'es_alto_costo' => true, // Alto Costo!
            'requiere_documento' => true,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio->id,
            'monto_tarifa' => 100000.00,
        ]);

        // Simular que el documento soporte ya fue cargado
        $autorizacion = Autorizacion::create([
            'numero_autorizacion' => 'AUT-TEST-001',
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'medico_solicitante' => 'Dr. Lopez',
            'diagnostico' => 'C50 - Tumor',
            'servicio_medico_id' => $servicio->id,
            'procedimiento' => $servicio->descripcion,
            'monto_solicitado' => 90000.00,
            'estado' => 'Pendiente',
            'prioridad' => 'Media',
            'fecha_solicitud' => now(),
        ]);

        Documento::create([
            'entidad_type' => 'autorizacion',
            'entidad_id' => $autorizacion->id,
            'nombre_archivo' => 'indicacion.pdf',
            'ruta_archivo' => 'doc/indicacion.pdf',
            'tipo_documento' => 'Soporte Médico',
            'fecha_carga' => now(),
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Auditoría', $eval['estado']);
        $this->assertEquals('Alta', $eval['prioridad']);
        $this->assertStringContainsString('catalogado como de Alto Costo', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 6 - Exceeds contracted rate.
     */
    public function test_auth_rule_rate_exceeded(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio->id,
            'monto_tarifa' => 1500.00, // Tarifa es 1500
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 2000.00, // Solicitado es 2000!
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Auditoría', $eval['estado']);
        $this->assertStringContainsString('supera la tarifa contratada', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Rule 7 - Frequency Exceeded.
     */
    public function test_auth_rule_frequency_exceeded(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio->id,
            'monto_tarifa' => 1500.00,
        ]);

        // Crear una autorización previa aprobada hace 10 días para el mismo servicio
        Autorizacion::create([
            'numero_autorizacion' => 'AUT-TEST-100',
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'medico_solicitante' => 'Dr. Perez',
            'diagnostico' => 'I10',
            'servicio_medico_id' => $servicio->id,
            'procedimiento' => $servicio->descripcion,
            'monto_solicitado' => 1500.00,
            'estado' => 'Aprobada',
            'prioridad' => 'Baja',
            'fecha_solicitud' => now()->subDays(10),
        ]);

        $nuevaAutorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
            'fecha_solicitud' => now(),
        ]);

        $eval = AuthorizationEvaluator::evaluar($nuevaAutorizacion);
        $this->assertEquals('Auditoría', $eval['estado']);
        $this->assertStringContainsString('Frecuencia de servicio excedida', $eval['motivo_estado']);
    }

    /**
     * Test AuthorizationEvaluator: Auto Approval.
     */
    public function test_auth_rule_auto_approval(): void
    {
        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta General',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio->id,
            'monto_tarifa' => 1500.00,
        ]);

        $autorizacion = new Autorizacion([
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'servicio_medico_id' => $servicio->id,
            'monto_solicitado' => 1500.00,
            'fecha_solicitud' => now(),
        ]);

        $eval = AuthorizationEvaluator::evaluar($autorizacion);
        $this->assertEquals('Aprobada', $eval['estado']);
        $this->assertEquals(1500.00, $eval['monto_contratado']);
        $this->assertStringContainsString('Aprobación automática', $eval['motivo_estado']);
    }

    /**
     * Test PSS AJAX validation endpoint.
     */
    public function test_pss_ajax_validation(): void
    {
        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $user = User::create([
            'name' => 'Usuario PSS',
            'email' => 'pss@ars.com',
            'password' => bcrypt('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $pss->id,
        ]);

        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/portal-autorizaciones/afiliados/validar-json?identificacion=00100000000&tipo_busqueda=cedula");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('afiliado.nombres', 'JUAN');
    }

    /**
     * Test PSS Batch Authorization submission.
     */
    public function test_pss_batch_authorization_submission(): void
    {
        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $user = User::create([
            'name' => 'Usuario PSS',
            'email' => 'pss@ars.com',
            'password' => bcrypt('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $pss->id,
        ]);

        $contrato = ContratoPss::create([
            'pss_id' => $pss->id,
            'numero_contrato' => 'CONTR-TEST-001',
            'fecha_inicio' => now()->subMonths(1),
            'fecha_fin' => now()->addMonths(12),
            'estado' => 'Activo',
        ]);

        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $servicio1 = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        $servicio2 = ServicioMedico::create([
            'codigo' => 'LAB-001',
            'descripcion' => 'Hemograma',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio1->id,
            'monto_tarifa' => 1500.00,
        ]);

        TarifaPss::create([
            'contrato_pss_id' => $contrato->id,
            'servicio_medico_id' => $servicio2->id,
            'monto_tarifa' => 500.00,
        ]);

        $postData = [
            'afiliado_id' => $afiliado->id,
            'afiliado_type' => 'titular',
            'diagnostico' => 'I10 - HTA Esencial',
            'telefono' => '829-555-1234',
            'servicios' => [$servicio1->id, $servicio2->id],
            'valores' => [1500.00, 500.00],
        ];

        $response = $this->actingAs($user)
            ->post("/portal-autorizaciones/autorizaciones/nueva", $postData);

        $response->assertRedirect(route('pss.solicitudes'));
        $this->assertEquals(1, Autorizacion::count());
        $this->assertEquals(2, \App\Models\AutorizacionDetalle::count());
    }

    /**
     * Test authorization cancellation search and process.
     */
    public function test_pss_authorization_cancellation_flow(): void
    {
        $pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
        ]);

        $user = User::create([
            'name' => 'Usuario PSS',
            'email' => 'pss@ars.com',
            'password' => bcrypt('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $pss->id,
        ]);

        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
        ]);

        $servicio = ServicioMedico::create([
            'codigo' => 'CON-001',
            'descripcion' => 'Consulta',
            'cobertura_base' => 80.00,
            'es_alto_costo' => false,
            'requiere_documento' => false,
        ]);

        $autorizacion = Autorizacion::create([
            'numero_autorizacion' => 'AUT-20260626-00099',
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'medico_solicitante' => 'Dr. Test',
            'diagnostico' => 'I10',
            'servicio_medico_id' => $servicio->id,
            'procedimiento' => $servicio->descripcion,
            'monto_solicitado' => 1000.00,
            'estado' => 'Aprobada',
            'fecha_solicitud' => now(),
        ]);

        $detalle = \App\Models\AutorizacionDetalle::create([
            'autorizacion_id' => $autorizacion->id,
            'codigo' => $servicio->codigo,
            'descripcion' => $servicio->descripcion,
            'cantidad' => 1,
            'monto' => 1000.00,
            'estado' => 'Aprobado',
        ]);

        // 1. Visitar vista de cancelación
        $response = $this->actingAs($user)->get("/portal-autorizaciones/autorizaciones/cancelar");
        $response->assertStatus(200);

        // 2. Buscar por número de autorización
        $response = $this->actingAs($user)->post("/portal-autorizaciones/autorizaciones/cancelar/buscar", [
            'numero_autorizacion' => 'AUT-20260626-00099'
        ]);
        $response->assertStatus(200);
        $response->assertSee('AUT-20260626-00099');
        $response->assertSee('Juan Perez Gomez');

        // 3. Procesar cancelación
        $response = $this->actingAs($user)->post("/portal-autorizaciones/autorizaciones/cancelar/{$autorizacion->id}", [
            'motivo_cancelacion' => 'Paciente decidió no realizarse el estudio hoy.'
        ]);
        $response->assertRedirect(route('pss.autorizaciones.cancelar'));

        // 4. Verificar estados en la base de datos
        $autorizacion->refresh();
        $this->assertEquals('Cancelada', $autorizacion->estado);
        $this->assertStringContainsString('Cancelada por PSS', $autorizacion->motivo_estado);

        $detalle->refresh();
        $this->assertEquals('Cancelado', $detalle->estado);
    }
}
