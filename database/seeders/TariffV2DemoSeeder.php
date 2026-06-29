<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pss;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use App\Models\PssContractLog;
use App\Models\PdssService;
use Carbon\Carbon;

class TariffV2DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar datos V2 previos
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        PssContractLog::truncate();
        PssTariffItem::truncate();
        PssTariffSchedule::truncate();
        PssContractVersion::truncate();
        PssContract::truncate();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 2. Crear Prestador de tipo Farmacéutico si no existe
        $pharmacy = Pss::updateOrCreate(
            ['rnc' => '131092837'],
            [
                'nombre' => 'Farmacia Carol Principal',
                'tipo_entidad' => 'Farmacia',
                'telefono' => '809-562-6767',
                'correo' => 'convenios@farmaciacarol.com',
                'direccion' => 'Av. Winston Churchill No. 1002, Santo Domingo',
                'estado' => 'Activa',
                'nivel_atencion' => 1,
                'tipo_pss' => 'Farmacia',
                'red_contratada' => true,
                'contrato_vigente' => true
            ]
        );

        // 3. Obtener todas las PSS activas bajo contrato
        $pssList = Pss::where('estado', 'Activa')
            ->where('red_contratada', true)
            ->where('contrato_vigente', true)
            ->get();

        // 4. Cargar servicios del PDSS agrupados
        $medicineServices = PdssService::where('is_active', true)->where('is_medicine', 1)->get();
        
        // Obtener una mezcla representativa de otros servicios para clínicas/hospitales (Consultas, Lab, Imágenes, Cirugía, etc.)
        $clinicalServices = PdssService::where('is_active', true)
            ->where('is_medicine', 0)
            ->whereNotNull('simon_code')
            ->orderBy('id')
            ->get();

        // Tomar una muestra representativa de 150 servicios clínicos para no saturar pero tener volumen
        $clinicalSample = $clinicalServices->count() > 150 ? $clinicalServices->random(150) : $clinicalServices;

        $this->command->info("Seeding V2 Contracts for " . $pssList->count() . " PSS providers...");

        foreach ($pssList as $pss) {
            DB::transaction(function() use ($pss, $medicineServices, $clinicalSample) {
                // Crear contrato V2
                $tipoCod = match ($pss->tipo_entidad) {
                    'Clínica' => 'CLI',
                    'Centro Médico' => 'CME',
                    'Hospital' => 'HOS',
                    'Farmacia' => 'FAR',
                    default => strtoupper(mb_substr($pss->tipo_entidad, 0, 3, 'UTF-8'))
                };
                $contractNumber = 'CONV-' . $tipoCod . '-' . $pss->id . '-' . Carbon::now()->year;
                $contractName = 'Convenio de Tarifas ' . $pss->nombre . ' ' . Carbon::now()->year;
                
                $contract = PssContract::create([
                    'pss_id' => $pss->id,
                    'contract_number' => $contractNumber,
                    'contract_name' => $contractName,
                    'contract_type' => $pss->tipo_entidad === 'Farmacia' ? 'especialidad' : 'general',
                    'start_date' => Carbon::now()->startOfYear()->toDateString(),
                    'end_date' => Carbon::now()->endOfYear()->toDateString(),
                    'status' => 'vigente',
                    'signed_at' => Carbon::now()->startOfYear(),
                    'signed_by' => 1,
                    'observations' => 'Convenio regulado SFS para la red de prestadores.'
                ]);

                // Crear versión 1.0.0
                $version = PssContractVersion::create([
                    'pss_contract_id' => $contract->id,
                    'version_number' => '1.0.0',
                    'effective_from' => $contract->start_date,
                    'effective_to' => $contract->end_date,
                    'status' => 'vigente',
                    'approved_by' => 1,
                    'approved_at' => now(),
                    'change_reason' => 'Registro de versión base contratada.'
                ]);

                // Crear esquema tarifario
                $schedule = PssTariffSchedule::create([
                    'pss_contract_id' => $contract->id,
                    'pss_contract_version_id' => $version->id,
                    'name' => 'Tarifario Regulado V2 - ' . $pss->nombre,
                    'effective_from' => $contract->start_date,
                    'effective_to' => $contract->end_date,
                    'status' => 'vigente'
                ]);

                // Determinar qué servicios contratar según el tipo de PSS
                $servicesToContract = [];
                $isPharmacy = ($pss->tipo_entidad === 'Farmacia');

                if ($isPharmacy) {
                    $servicesToContract = $medicineServices;
                } else {
                    $servicesToContract = $clinicalSample;
                }

                $tariffItemsBatch = [];

                foreach ($servicesToContract as $service) {
                    // Calcular montos realistas
                    $baseAmount = 1200.00;
                    if ($service->is_medicine) {
                        $baseAmount = round(rand(150, 4500), 2);
                    } else {
                        // Clínico
                        $groupName = strtolower($service->group->name ?? '');
                        if (str_contains($groupName, 'alto costo') || $service->is_high_cost) {
                            $baseAmount = 120000.00;
                        } elseif (str_contains($groupName, 'cirugía') || $service->is_surgery) {
                            $baseAmount = 45000.00;
                        } elseif (str_contains($groupName, 'hospitalización') || $service->is_hospitalization) {
                            $baseAmount = 5500.00;
                        } elseif (str_contains($groupName, 'emergencia') || $service->is_emergency) {
                            $baseAmount = 2500.00;
                        } elseif (str_contains($groupName, 'apoyo diagnóstico') || $service->is_diagnostic_support) {
                            $baseAmount = 850.00;
                        }
                    }

                    // Pequeña variación de precio por PSS
                    $contractedAmount = $baseAmount * (1 + (($pss->id % 4) * 0.04));
                    
                    // Copago: 30% para farmacia, 20% para clínica
                    $copayPercent = $isPharmacy ? 30.00 : 20.00;
                    $copayAmount = round($contractedAmount * ($copayPercent / 100), 2);
                    $arsPercent = 100.00 - $copayPercent;

                    $tariffItemsBatch[] = [
                        'pss_tariff_schedule_id' => $schedule->id,
                        'pss_id' => $pss->id,
                        'pdss_service_id' => $service->id,
                        'simon_code_snapshot' => $service->simon_code,
                        'cups_code_snapshot' => $service->cups_code,
                        'service_description_snapshot' => $service->coverage_description,
                        'service_group_snapshot' => $service->group->name ?? 'General',
                        'service_subgroup_snapshot' => $service->subgroup->name ?? null,
                        'coverage_type_snapshot' => $service->coverage_type,
                        'contracted_amount' => round($contractedAmount, 2),
                        'currency' => 'DOP',
                        'copay_percent' => $copayPercent,
                        'affiliate_copay_amount' => $copayAmount,
                        'ars_covered_percent' => $arsPercent,
                        'requires_authorization' => $service->requires_authorization ?? true,
                        'requires_medical_audit' => $service->requires_medical_audit ?? false,
                        'requires_document' => false,
                        'frequency_limit' => null,
                        'frequency_period' => null,
                        'max_amount_per_event' => null,
                        'max_amount_per_year' => null,
                        'level_1_allowed' => true,
                        'level_2_allowed' => true,
                        'level_3_allowed' => true,
                        'is_high_cost' => $service->is_high_cost ?? false,
                        'is_emergency' => $service->is_emergency ?? false,
                        'is_hospitalization' => $service->is_hospitalization ?? false,
                        'is_surgery' => $service->is_surgery ?? false,
                        'is_diagnostic_support' => $service->is_diagnostic_support ?? false,
                        'is_medicine' => $service->is_medicine ?? false,
                        'status' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                // Insertar en lotes de 200 ítems para performance
                $chunks = array_chunk($tariffItemsBatch, 200);
                foreach ($chunks as $chunk) {
                    PssTariffItem::insert($chunk);
                }

                // Loguear cambio contractual
                PssContractLog::create([
                    'pss_contract_id' => $contract->id,
                    'user_id' => 1,
                    'action' => 'crear_contrato',
                    'new_values' => ['contract_number' => $contract->contract_number, 'version' => '1.0.0'],
                    'observation' => 'Convenio contractual inicial e importación del tarifario completo.'
                ]);
            });

            $this->command->info("  Contrato V2 creado para {$pss->nombre} con tarifas configuradas.");
        }

        $this->command->info("Poblamiento de contratos y tarifarios V2 finalizado con éxito.");
    }
}
