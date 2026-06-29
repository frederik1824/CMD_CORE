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
use App\Models\AuthorizationClaim;
use App\Models\AccountPayable;
use App\Models\PaymentBatch;
use App\Models\AffiliationBatch;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class OperationalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $pssUser;
    protected $afiliado;
    protected $pss;
    protected $servicio;
    protected $contrato;
    protected $tarifa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCatalogos();
        $this->setUpEntities();
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
            ['grupo' => 'tipo_identificacion', 'codigo' => 'CEDULA', 'descripcion' => 'Cédula'],
        ];

        foreach ($catalogs as $cat) {
            \App\Models\Catalogo::create($cat);
        }
    }

    private function setUpEntities(): void
    {
        // 1. Create PSS
        $this->pss = Pss::create([
            'rnc' => '101002034',
            'nombre' => 'Clínica de Prueba Abreu',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'direccion' => 'Av. Independencia',
            'telefono' => '809-555-1234',
            'correo' => 'abreu@correo.com',
        ]);

        // 2. Create Users
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@ars.com',
            'password' => bcrypt('password'),
            'role' => 'Administrador ARS',
        ]);

        $this->pssUser = User::create([
            'name' => 'Usuario PSS Abreu',
            'email' => 'pss.abreu@ars.com',
            'password' => bcrypt('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $this->pss->id,
        ]);

        // 3. Create Afiliado
        $tipoIdCed = \App\Models\Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CEDULA')->first()->id ?? 1;
        $this->afiliado = Afiliado::create([
            'tipo_identificacion_id' => $tipoIdCed,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'JUAN',
            'primer_apellido' => 'PEREZ',
            'segundo_apellido' => 'GOMEZ',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
            'regimen_actual' => 'Contributivo',
            'numero_contrato' => 'POL-99999',
        ]);

        // 4. Create Medical Service, Contract, and Rate
        $this->servicio = ServicioMedico::create([
            'codigo' => '801001',
            'descripcion' => 'Consulta Médica General',
            'cobertura_base' => 80.00,
        ]);

        $this->contrato = ContratoPss::create([
            'pss_id' => $this->pss->id,
            'numero_contrato' => 'CONTRATO-TEST-123',
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2027-12-31',
            'estado' => 'Activo',
        ]);

        $this->tarifa = TarifaPss::create([
            'contrato_pss_id' => $this->contrato->id,
            'servicio_medico_id' => $this->servicio->id,
            'monto_tarifa' => 1500.00,
        ]);
    }

    /**
     * Test major flow 1: Authorization -> Claim -> Audit -> Payable -> Lote -> Payment -> Conciliation
     */
    public function test_complete_authorization_billing_and_payment_flow(): void
    {
        // 1. Create an approved authorization
        $autorizacion = Autorizacion::create([
            'numero_autorizacion' => 'AUT-' . rand(100000, 999999),
            'afiliado_id' => $this->afiliado->id,
            'afiliado_type' => 'titular',
            'pss_id' => $this->pss->id,
            'servicio_medico_id' => $this->servicio->id,
            'procedimiento' => 'Consulta Médica General',
            'diagnostico' => 'Gripe común (J00)',
            'medico_solicitante' => 'Dr. Pedro Ramirez',
            'monto_solicitado' => 1500.00,
            'monto_contratado' => 1200.00, // Coinsurance / coverage applied
            'estado' => 'Aprobada',
            'motivo_estado' => 'Aprobado automáticamente por motor de reglas',
            'fecha_solicitud' => Carbon::now(),
            'fecha_respuesta' => Carbon::now(),
        ]);

        // 2. Submit a Claim from the PSS Portal
        $responseClaim = $this->actingAs($this->pssUser)->post("/portal-autorizaciones/autorizaciones/{$autorizacion->id}/reclamar", [
            'invoice_number' => 'FAC-TEST-001',
            'ncf' => 'B0100000001',
            'service_date' => Carbon::now()->toDateString(),
            'claimed_amount' => 1200.00,
        ]);
        
        $responseClaim->assertRedirect();
        
        // Assert claim was created
        $claim = AuthorizationClaim::where('invoice_number', 'FAC-TEST-001')->first();
        $this->assertNotNull($claim);
        $this->assertEquals(1200.00, $claim->claimed_amount);
        $this->assertEquals('Reclamación recibida', $claim->status);

        // 3. Audit the claim from the Core Admin Portal
        $responseAudit = $this->actingAs($this->adminUser)->post("/core/reclamaciones/{$claim->id}/auditar", [
            'audit_type' => 'Administrativa',
            'decision' => 'Aprobada',
            'approved_amount' => 1200.00,
            'internal_observation' => 'Factura coincide perfectamente con el monto autorizado.',
            'pss_observation' => 'Aprobado para pago completo.',
        ]);

        $responseAudit->assertRedirect();
        $claim->refresh();
        // After approval + CXP generation, status becomes 'Cuenta por pagar generada'
        $this->assertEquals('Cuenta por pagar generada', $claim->status);
        $this->assertEquals(1200.00, $claim->approved_amount);

        // Check that AccountPayable (CXP) was generated
        $payable = AccountPayable::where('claim_id', $claim->id)->first();
        $this->assertNotNull($payable);
        $this->assertEquals(1200.00, $payable->net_amount);
        $this->assertEquals('Contabilizada', $payable->status);

        // 4. Group the CXP into a Payment Lote
        $responseBatch = $this->actingAs($this->adminUser)->post("/core/pagos/lotes/crear", [
            'cxp_ids' => [$payable->id],
            'scheduled_payment_date' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $responseBatch->assertRedirect();
        $payable->refresh();
        $this->assertEquals('En lote de pago', $payable->status);

        $batch = PaymentBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals('Borrador', $batch->status);
        $this->assertEquals(1200.00, $batch->total_amount);

        // Approve and Program the batch
        $responseApprove = $this->actingAs($this->adminUser)->post("/core/pagos/lotes/{$batch->id}/aprobar");
        $responseApprove->assertRedirect();
        $batch->refresh();
        $this->assertEquals('Programado', $batch->status);

        // 5. Execute Bank Disbursement / Pagar Lote
        $responsePay = $this->actingAs($this->adminUser)->post("/core/pagos/lotes/{$batch->id}/pagar");
        $responsePay->assertRedirect();
        $batch->refresh();
        $this->assertEquals('Pagado', $batch->status);
        $payable->refresh();
        $this->assertEquals('Pagada', $payable->status);

        // 6. Conciliar Lote
        $responseReconcile = $this->actingAs($this->adminUser)->post("/core/pagos/lotes/{$batch->id}/conciliar", [
            'bank_reference' => 'TX-999888777',
        ]);
        $responseReconcile->assertRedirect();
        $batch->refresh();
        $this->assertEquals('Conciliado', $batch->status);
        $payable->refresh();
        // Payable stays in 'Conciliada' while claim/authorization move to 'Cerrada'
        $this->assertEquals('Conciliada', $payable->status);
    }

    /**
     * Test major flow 2: Unipago Simulation (Prevalidar, Lotes, Capitas, Cortes)
     */
    public function test_unipago_simulator_flows(): void
    {
        // 1. Prevalidar
        $responsePreval = $this->actingAs($this->adminUser)->post("/core/unipago/prevalidar", [
            'raw_data' => "22500756150,PEDRO,JIMENEZ\n22500756159,RAMON,GARCIA"
        ]);
        $responsePreval->assertStatus(200);
        $responsePreval->assertViewHas('prevalidated');

        // 2. Transmit Lote (Subir)
        $responseUpload = $this->actingAs($this->adminUser)->post("/core/unipago/lotes/subir", [
            'batch_type' => 'titulares',
            'raw_records' => "22500756150,PEDRO,JIMENEZ\n22500756159,RAMON,GARCIA"
        ]);
        $responseUpload->assertRedirect();

        $batch = AffiliationBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals('VE', $batch->status); // Validado en Espera
        $this->assertEquals(2, $batch->total_records);

        // 3. Process Lote (Procesar Respuestas Mock)
        $responseProcess = $this->actingAs($this->adminUser)->post("/core/unipago/lotes/{$batch->id}/procesar");
        $responseProcess->assertRedirect();
        $batch->refresh();
        $this->assertContains($batch->status, ['PC', 'PE', 'RE']);

        // 4. Capita Confirmation
        $cn = CapitationNotification::create([
            'notification_number' => 'CAP-TEST-999',
            'afiliado_id' => $this->afiliado->id,
            'period' => '202606',
            'capitation_amount' => 1450.50,
            'individualization_type' => 'Capita Normal',
            'status' => 'NT',
            'notified_at' => Carbon::now(),
        ]);

        $responseConfirm = $this->actingAs($this->adminUser)->post("/core/unipago/capitas/{$cn->id}/confirmar");
        $responseConfirm->assertRedirect();
        $cn->refresh();
        $this->assertEquals('IC', $cn->status); // Individualización Confirmada

        // 5. Generate Dispersion Cut
        $responseCut = $this->actingAs($this->adminUser)->post("/core/unipago/cortes/generar", [
            'period' => '202606',
            'cut_type' => 'primer corte',
        ]);
        $responseCut->assertRedirect();

        $cut = DispersionCut::where('period', '202606')->first();
        $this->assertNotNull($cut);
        $this->assertEquals('Dispersado', $cut->status);

        $cn->refresh();
        $this->assertEquals('DI', $cn->status); // Dispersada
    }
}
