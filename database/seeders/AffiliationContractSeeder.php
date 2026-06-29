<?php

namespace Database\Seeders;

use App\Models\AffiliationContractRange;
use App\Models\AffiliationContractNumber;
use App\Models\AffiliationContractMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliationContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Rango Agotado
        $rangoAgotado = AffiliationContractRange::create([
            'range_code' => 'UNIPAGO-RANGO-AGOTADO',
            'description' => 'Rango anterior de formularios de afiliación Unipago',
            'start_number' => 400000,
            'end_number' => 400050,
            'total_numbers' => 51,
            'available_count' => 0,
            'used_count' => 51,
            'status' => 'agotado',
            'source' => 'unipago',
            'approval_reference' => 'Oficio No. 010-2025',
            'approved_by' => 'Administración',
            'approved_at' => now()->subYear(),
            'valid_from' => now()->subYear()->toDateString(),
            'valid_until' => now()->subMonths(6)->toDateString(),
        ]);

        // Insertar números para rango agotado (usados)
        $numbersAgotado = [];
        for ($i = 400000; $i <= 400050; $i++) {
            $numbersAgotado[] = [
                'affiliation_contract_range_id' => $rangoAgotado->id,
                'contract_number' => strval($i),
                'status' => 'usado',
                'created_at' => now()->subYear(),
                'updated_at' => now()->subYear()
            ];
        }
        AffiliationContractNumber::insert($numbersAgotado);

        // 2. Rango Activo
        $rangoActivo = AffiliationContractRange::create([
            'range_code' => 'UNIPAGO-RANGO-ACTIVO',
            'description' => 'Rango vigente de formularios de afiliación Unipago',
            'start_number' => 450000,
            'end_number' => 450200,
            'total_numbers' => 201,
            'available_count' => 201,
            'status' => 'activo',
            'source' => 'unipago',
            'approval_reference' => 'Oficio No. 042-2026',
            'approved_by' => 'Administración',
            'approved_at' => now(),
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addYear()->toDateString(),
        ]);

        // Insertar números para rango activo (mezcla de disponible, bloqueado, reservado, usado)
        $numbersActivo = [];
        for ($i = 450000; $i <= 450200; $i++) {
            $status = 'disponible';
            if ($i === 450005 || $i === 450012) {
                $status = 'bloqueado';
            } elseif ($i === 450010 || $i === 450015) {
                $status = 'reservado';
            }

            $numbersActivo[] = [
                'affiliation_contract_range_id' => $rangoActivo->id,
                'contract_number' => strval($i),
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        AffiliationContractNumber::insert($numbersActivo);

        // Registrar movimientos históricos demo
        $numBloqueado = AffiliationContractNumber::where('contract_number', '450005')->first();
        if ($numBloqueado) {
            $numBloqueado->update([
                'blocked_at' => now(),
                'blocked_by' => 1,
                'block_reason' => 'Formulario físico dañado en recepción'
            ]);
            AffiliationContractMovement::create([
                'contract_number_id' => $numBloqueado->id,
                'movement_type' => 'bloqueo',
                'old_status' => 'disponible',
                'new_status' => 'bloqueado',
                'user_id' => 1,
                'description' => 'Formulario físico dañado en recepción',
                'created_at' => now()
            ]);
        }

        // Recalcular los contadores del rango activo
        $rangoActivo->recalculateCounts();
    }
}
