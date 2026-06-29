<?php

namespace Tests\Feature;

use App\Models\AffiliationContractRange;
use App\Models\AffiliationContractNumber;
use App\Models\User;
use App\Services\AffiliationContractNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliationContractControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario demo
        $this->user = User::factory()->create([
            'role' => 'Administrador ARS'
        ]);
    }

    /**
     * Prueba que se pueden crear rangos sin solapamientos.
     */
    public function test_can_create_contract_range_successfully()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('ars.contract_control.ranges.store'), [
            'range_code' => 'UNIPAGO-2026-T1',
            'description' => 'Test de rango',
            'start_number' => 450000,
            'end_number' => 450050,
            'source' => 'unipago',
            'valid_from' => '2026-01-01',
            'valid_until' => '2026-12-31',
        ]);

        $response->assertRedirect(route('ars.contract_control.ranges.index'));
        $this->assertDatabaseHas('affiliation_contract_ranges', [
            'range_code' => 'UNIPAGO-2026-T1',
            'start_number' => 450000,
            'end_number' => 450050,
            'total_numbers' => 51,
            'status' => 'activo'
        ]);

        $this->assertDatabaseHas('affiliation_contract_numbers', [
            'contract_number' => '450000',
            'status' => 'disponible'
        ]);
        
        $this->assertDatabaseHas('affiliation_contract_numbers', [
            'contract_number' => '450050',
            'status' => 'disponible'
        ]);
    }

    /**
     * Prueba que no se permite crear rangos que se solapen.
     */
    public function test_cannot_create_overlapping_contract_ranges()
    {
        $this->actingAs($this->user);

        // Crear primer rango
        AffiliationContractRange::create([
            'range_code' => 'RANGO-1',
            'description' => 'Primer rango',
            'start_number' => 450000,
            'end_number' => 450100,
            'total_numbers' => 101,
            'status' => 'activo'
        ]);

        // Intentar crear rango solapado
        $response = $this->post(route('ars.contract_control.ranges.store'), [
            'range_code' => 'RANGO-2-SOLAPADO',
            'description' => 'Rango solapado',
            'start_number' => 450050,
            'end_number' => 450200,
            'source' => 'unipago',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('affiliation_contract_ranges', [
            'range_code' => 'RANGO-2-SOLAPADO'
        ]);
    }

    /**
     * Prueba el flujo de asignación y consumo del servicio.
     */
    public function test_service_can_reserve_and_consume_contracts()
    {
        $range = AffiliationContractRange::create([
            'range_code' => 'RANGO-TEST',
            'description' => 'Rango de test',
            'start_number' => 450000,
            'end_number' => 450010,
            'total_numbers' => 11,
            'status' => 'activo'
        ]);

        $number = AffiliationContractNumber::create([
            'affiliation_contract_range_id' => $range->id,
            'contract_number' => '450000',
            'status' => 'disponible'
        ]);

        // Recalcular counts
        $range->recalculateCounts();

        // 1. Reservar
        $reserved = AffiliationContractNumberService::reserveNumber($number->id, 15, null, $this->user->id);
        $this->assertEquals('reservado', $reserved->status);
        $this->assertNotNull($reserved->reservation_token);

        // 2. Consumir
        $consumed = AffiliationContractNumberService::consumeNumber($number->id, 999, $this->user->id);
        $this->assertEquals('usado', $consumed->status);
        $this->assertEquals(999, $consumed->assigned_to_affiliate_id);
    }
}
