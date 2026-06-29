<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Autorizacion;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\AuthorizationClaim;
use App\Models\ClaimAudit;
use App\Models\ClaimGlosa;
use App\Models\AccountPayable;
use App\Models\PaymentBatch;
use App\Models\PaymentBatchItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
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

    public function test_ciclo_de_vida_completo_de_reclamacion_glosa_y_pago()
    {
        // 1. Configurar datos semilla básicos
        $userArs = User::factory()->create(['role' => 'Administrador ARS']);
        $userPss = User::factory()->create(['role' => 'Representante PSS', 'pss_id' => 1]);
        
        $pss = Pss::create([
            'id' => 1,
            'rnc' => '101000001',
            'nombre' => 'Clínica de Prueba',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa'
        ]);

        $afiliado = Afiliado::create([
            'tipo_identificacion_id' => 1,
            'cedula' => '00100000000',
            'nss' => '12345678',
            'nui' => '87654321',
            'nombres' => 'JUAN',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Gomez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo',
            'estado_afiliacion' => 'AC',
            'activo_nomina' => true,
            'tiene_aporte' => true,
            'regimen' => 'Contributivo',
            'poliza' => '00896-18979-01',
            'tipo_afiliado' => 'Titular'
        ]);

        $autorizacion = Autorizacion::create([
            'numero_autorizacion' => 'AUT-20260627-00001',
            'afiliado_type' => 'titular',
            'afiliado_id' => $afiliado->id,
            'pss_id' => $pss->id,
            'medico_solicitante' => 'Dr. Test',
            'diagnostico' => 'Diagnóstico de Prueba',
            'monto_solicitado' => 10000.00,
            'monto_contratado' => 10000.00,
            'monto_ars' => 8000.00,
            'monto_afiliado' => 2000.00,
            'copago' => 2000.00,
            'exceso' => 0.00,
            'prioridad' => 'Media',
            'estado' => 'Aprobada',
            'procedimiento' => 'Procedimiento de Prueba'
        ]);

        // 2. PSS somete reclamación
        $this->actingAs($userPss);
        $payloadClaim = [
            'invoice_number' => 'FAC-9988',
            'ncf' => 'B0100000005',
            'service_date' => now()->toDateString(),
            'claimed_amount' => 10000.00
        ];

        $responseClaim = $this->post(route('pss.autorizaciones.reclamar.store', $autorizacion->id), $payloadClaim);
        $responseClaim->assertRedirect();

        $this->assertDatabaseHas('authorization_claims', [
            'invoice_number' => 'FAC-9988',
            'ncf' => 'B0100000005',
            'status' => 'Reclamación recibida'
        ]);

        $claim = AuthorizationClaim::where('invoice_number', 'FAC-9988')->first();

        // 3. ARS Radica / Da entrada oficial a la reclamación
        $this->actingAs($userArs);
        $responseEntrada = $this->post(route('ars.reclamaciones.dar_entrada', $claim->id));
        $responseEntrada->assertRedirect();
        
        $claim->refresh();
        $this->assertEquals('En auditoría de reclamación', $claim->status);

        // 4. ARS Audita Reclamación con una glosa parcial de 2,000 DOP
        $payloadAudit = [
            'decision' => 'Objetada parcial',
            'audit_type' => 'Médica',
            'approved_amount' => 8000.00,
            'objection_reason' => 'Diferencia en tarifas contratadas',
            'internal_observation' => 'Ajuste de tarifario aprobado.',
            'pss_observation' => 'Diferencia tarifaria aplicada según anexo 2.'
        ];

        $responseAudit = $this->post(route('ars.reclamaciones.auditar', $claim->id), $payloadAudit);
        $responseAudit->assertRedirect();

        $claim->refresh();
        $this->assertEquals('Cuenta por pagar generada', $claim->status);
        $this->assertEquals(8000.00, $claim->approved_amount);
        $this->assertEquals(2000.00, $claim->objected_amount);

        // Verificar la creación de la CXP y de la Glosa en base de datos
        $this->assertDatabaseHas('claim_glosses', [
            'claim_id' => $claim->id,
            'objected_amount' => 2000.00,
            'status' => 'Notificada a PSS'
        ]);

        $this->assertDatabaseHas('accounts_payable', [
            'claim_id' => $claim->id,
            'gross_amount' => 8000.00,
            'net_amount' => 8000.00, // Retención profesional es 0 por ser Entidad
            'status' => 'Contabilizada'
        ]);

        $ap = AccountPayable::where('claim_id', $claim->id)->first();

        // 5. PSS Responde a la Glosa
        $this->actingAs($userPss);
        $glosa = ClaimGlosa::where('claim_id', $claim->id)->first();
        $responseGlosa = $this->post(route('pss.reclamaciones.glosa.responder', [$claim->id, $glosa->id]), [
            'pss_response' => 'Justificación médica enviada para anulación de glosa tarifaria.'
        ]);
        $responseGlosa->assertRedirect();

        $glosa->refresh();
        $this->assertEquals('En conciliación', $glosa->status);

        // 6. ARS crea Lote de Pago Borrador con la CXP aprobada
        $this->actingAs($userArs);
        $payloadLote = [
            'cxp_ids' => [$ap->id],
            'scheduled_payment_date' => now()->addDays(5)->toDateString()
        ];
        $responseLote = $this->post(route('ars.lotes.crear'), $payloadLote);
        $responseLote->assertRedirect();

        $batch = PaymentBatch::latest()->first();
        $this->assertEquals('Borrador', $batch->status);
        $this->assertEquals(8000.00, $batch->total_amount);

        // 7. ARS aprueba y programa Lote
        $responseAprobar = $this->post(route('ars.lotes.aprobar', $batch->id));
        $responseAprobar->assertRedirect();
        $batch->refresh();
        $this->assertEquals('Programado', $batch->status);

        // 8. ARS realiza el Pago del Lote
        $responsePagar = $this->post(route('ars.lotes.pagar', $batch->id));
        $responsePagar->assertRedirect();
        $batch->refresh();
        $this->assertEquals('Pagado', $batch->status);
        
        $ap->refresh();
        $this->assertEquals('Pagada', $ap->status);

        // 9. ARS concilia el extracto bancario de este Lote
        $responseConciliar = $this->post(route('ars.lotes.conciliar', $batch->id), [
            'bank_reference' => 'TXN-998822'
        ]);
        $responseConciliar->assertRedirect();

        $batch->refresh();
        $ap->refresh();
        $claim->refresh();

        $this->assertEquals('Conciliado', $batch->status);
        $this->assertEquals('Conciliada', $ap->status);
        $this->assertEquals('Cerrada', $claim->status); // Ciclo operativo finalizado
    }
}
