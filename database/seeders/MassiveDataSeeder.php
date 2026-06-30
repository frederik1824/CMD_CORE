<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Catalogo;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\DispersionCutDetail;
use App\Models\UnipagoMockNotification;
use App\Models\PypRiskGroup;
use App\Models\PypRiskFactor;
use App\Models\PypProgram;
use App\Models\PypProgramCandidate;
use App\Models\PypProgramEnrollment;
use App\Models\PypProgramCalendar;
use App\Models\HealthPlan;
use App\Models\HealthPlanCoverage;
use App\Models\CoverageDerivationRule;
use App\Models\CoverageLimit;
use App\Models\ProviderGroup;
use App\Models\ProviderNetwork;
use App\Models\ProviderContractedService;
use App\Models\ProviderPriceAgreement;
use App\Models\ProviderGeoLocation;
use App\Models\CapitatedServiceContract;
use App\Models\CapitatedServicePayment;
use App\Models\AffiliateGroup;
use App\Models\AffiliateContract;
use App\Models\BusinessUnit;
use App\Models\GeographicCode;
use App\Models\AffiliateTransaction;
use App\Models\PrintingCenter;
use App\Models\PrintingSupply;
use App\Models\PrintingSupplyMovement;
use App\Models\CarnetRequest;
use App\Models\CarnetDelivery;
use App\Models\CarnetTransfer;
use App\Models\CarnetAdjustment;
use App\Models\Promoter;
use App\Models\PromoterContract;
use App\Models\PromoterCampaign;
use App\Models\PromoterCommission;
use App\Models\BillingInvoice;
use App\Models\CustomerCase;
use App\Models\Pss;
use App\Models\ServicioMedico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MassiveDataSeeder extends Seeder
{
    private array $nombresM = ['Luis','Carlos','José','Juan','Miguel','Manuel','Ramón','Francisco','Rafael','Pedro','David','Jorge','Julio','Ángel','Roberto'];
    private array $nombresF = ['María','Ana','Carmen','Juana','Rosa','Francisca','Yolanda','Margarita','Luz','Sandra','Patricia','Elizabeth','Laura','Carolina'];
    private array $apellidos = ['Martínez','Rodríguez','Gómez','Pérez','Sánchez','Díaz','Fernández','Álvarez','Cruz','Ramírez','Flores','Vázquez','Guzmán','Ortiz'];
    private array $provincias = ['Distrito Nacional', 'Santo Domingo', 'Santiago', 'San Cristóbal', 'La Altagracia'];
    private array $sectores = ['Piantini', 'Naco', 'Bella Vista', 'Evaristo Morales', 'Los Prados'];

    public function run(): void
    {
        DB::statement('PRAGMA journal_mode=WAL');
        DB::statement('PRAGMA busy_timeout=5000');

        $tipoIdCed = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CED')->first()->id ?? 1;
        $parentescoConyuge = Catalogo::where('grupo', 'parentesco')->where('codigo', 'CONYUGE')->first()->id ?? 2;
        $parentescoHijo = Catalogo::where('grupo', 'parentesco')->where('codigo', 'HIJO')->first()->id ?? 3;

        // 1. CREACIÓN EXPLICITA DE AFILIADO DE PRUEBAS OBLIGATORIO
        $this->command->info("Creando afiliado de pruebas obligatorios...");
        $frederik = Afiliado::updateOrCreate(
            ['nss' => '10790017590'],
            [
                'tipo_identificacion_id' => $tipoIdCed,
                'cedula' => '07900175907',
                'nui' => '30790017590',
                'nombres' => 'JUAN',
                'primer_apellido' => 'PEREZ',
                'segundo_apellido' => 'ALCANTARA',
                'fecha_nacimiento' => '1990-05-15',
                'sexo' => 'M',
                'provincia' => 'Distrito Nacional',
                'municipio' => 'Santo Domingo de Guzmán',
                'telefono' => '809-555-0192',
                'correo' => 'juan.perez@correo.com',
                'numero_contrato' => '008961897901',
                'fecha_suscripcion' => '2020-01-01',
                'estado_afiliacion' => 'OK',
                'motivo_estado' => 'Afiliado principal de pruebas.',
                'activo_nomina' => true,
                'tiene_aporte' => true,
                'regimen_actual' => 'Contributivo',
                'entidad_actual' => 'ARS Core Demo',
                'esta_carnetizado' => true,
                'tiene_formulario' => true,
                'sector' => 'Piantini',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 2. POBLAR 40,000 AFILIADOS
        $this->command->info("Poblando 40,000 afiliados...");
        $batchSize = 35;
        $totalAffiliates = 40000;
        
        for ($start = 1; $start <= $totalAffiliates; $start += $batchSize) {
            $end = min($start + $batchSize - 1, $totalAffiliates);
            $batch = [];

            for ($i = $start; $i <= $end; $i++) {
                $sexo = (mt_rand(0, 1) === 0) ? 'M' : 'F';
                $nombre = ($sexo === 'F') ? $this->nombresF[array_rand($this->nombresF)] : $this->nombresM[array_rand($this->nombresM)];
                $ape1 = $this->apellidos[array_rand($this->apellidos)];
                $ape2 = $this->apellidos[array_rand($this->apellidos)];
                $prov = $this->provincias[array_rand($this->provincias)];

                $batch[] = [
                    'tipo_identificacion_id' => $tipoIdCed,
                    'cedula' => '402' . str_pad($i + 5000, 7, '0', STR_PAD_LEFT) . mt_rand(0, 9),
                    'nss' => '1' . str_pad($i + 5000, 9, '0', STR_PAD_LEFT),
                    'nui' => '3' . str_pad(10000000 + $i + 5000, 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombre,
                    'primer_apellido' => $ape1,
                    'segundo_apellido' => $ape2,
                    'fecha_nacimiento' => now()->subYears(mt_rand(18, 65))->subDays(mt_rand(0, 360))->toDateString(),
                    'sexo' => $sexo,
                    'provincia' => $prov,
                    'municipio' => $prov . ' Municipio',
                    'telefono' => '809-' . mt_rand(200, 999) . '-' . mt_rand(1000, 9999),
                    'correo' => strtolower($nombre . '.' . $ape1 . $i . '@correo.com'),
                    'numero_contrato' => 'CONTR-ARS-' . str_pad($i + 5000, 8, '0', STR_PAD_LEFT),
                    'fecha_suscripcion' => now()->subYears(mt_rand(1, 4))->toDateString(),
                    'estado_afiliacion' => 'OK',
                    'motivo_estado' => 'Aprobado Unipago.',
                    'activo_nomina' => true,
                    'tiene_aporte' => true,
                    'regimen_actual' => 'Contributivo',
                    'entidad_actual' => 'ARS Core Demo',
                    'esta_carnetizado' => (mt_rand(1, 10) > 2),
                    'tiene_formulario' => true,
                    'sector' => $this->sectores[array_rand($this->sectores)],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            DB::transaction(function() use ($batch) {
                Afiliado::insert($batch);
            });
        }
        $this->command->info("Total Afiliados en DB: " . Afiliado::count());

        // 3. POBLAR 40,000 DEPENDIENTES
        $this->command->info("Poblando 40,000 dependientes...");
        $titularIds = Afiliado::pluck('id')->toArray();
        $totalDeps = 40000;
        $batch = [];
        $batchSize = 35;

        for ($i = 1; $i <= $totalDeps; $i++) {
            $titularId = $titularIds[array_rand($titularIds)];
            $sexo = (mt_rand(0, 1) === 0) ? 'M' : 'F';
            $nombre = ($sexo === 'F') ? $this->nombresF[array_rand($this->nombresF)] : $this->nombresM[array_rand($this->nombresM)];
            $ape = $this->apellidos[array_rand($this->apellidos)] . ' ' . $this->apellidos[array_rand($this->apellidos)];
            $parentesco = (mt_rand(0, 1) === 0) ? $parentescoConyuge : $parentescoHijo;

            $batch[] = [
                'titular_id' => $titularId,
                'tipo_identificacion_id' => $tipoIdCed,
                'cedula' => '001' . str_pad($i + 5000, 7, '0', STR_PAD_LEFT) . mt_rand(0, 9),
                'nss' => '2' . str_pad($i + 5000, 9, '0', STR_PAD_LEFT),
                'nui' => '3' . str_pad(20000000 + $i + 5000, 8, '0', STR_PAD_LEFT),
                'nombres' => $nombre,
                'apellidos' => $ape,
                'fecha_nacimiento' => now()->subYears(mt_rand(1, 25))->toDateString(),
                'sexo' => $sexo,
                'parentesco_id' => $parentesco,
                'tipo_dependiente' => 'Directo',
                'estudiante' => (mt_rand(1, 10) > 8),
                'discapacitado' => (mt_rand(1, 100) < 3),
                'nacionalidad' => 'Dominicana',
                'requiere_documento' => true,
                'estado_afiliacion' => 'OK',
                'motivo_estado' => 'Aprobado Unipago.',
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($batch) >= $batchSize) {
                DB::transaction(function() use ($batch) {
                    Dependiente::insert($batch);
                });
                $batch = [];
            }
        }
        if (!empty($batch)) {
            DB::transaction(function() use ($batch) {
                Dependiente::insert($batch);
            });
        }
        $this->command->info("Total Dependientes en DB: " . Dependiente::count());

        // 4. SEED PYP (PROMOCION Y PREVENCION)
        $this->command->info("Seeding PyP Tables...");
        $riskGroup = PypRiskGroup::create(['name' => 'Cardiopatías', 'description' => 'Pacientes propensos a riesgo cardiovascular.', 'criteria' => 'Mayor a 45 años']);
        $riskFactor = PypRiskFactor::create(['name' => 'Tabaquismo', 'description' => 'Consumo diario de cigarrillos.']);
        
        $program = PypProgram::create([
            'name' => 'Vida Sana Corazón',
            'program_type' => 'Cardiovascular',
            'risk_group_id' => $riskGroup->id,
            'start_date' => '2026-01-01',
            'status' => 'Activo'
        ]);

        PypProgramCandidate::create([
            'program_id' => $program->id,
            'affiliate_id' => $frederik->id,
            'risk_group_id' => $riskGroup->id,
            'detected_at' => '2026-06-25',
            'status' => 'Detectado'
        ]);

        PypProgramEnrollment::create([
            'program_id' => $program->id,
            'affiliate_id' => $frederik->id,
            'enrollment_date' => '2026-06-28',
            'status' => 'Activo'
        ]);

        PypProgramCalendar::create([
            'program_id' => $program->id,
            'service_name' => 'Charla Nutricional Preventiva',
            'scheduled_date' => '2026-07-15',
            'location' => 'Salón Multiusos Core',
            'capacity' => 50,
            'status' => 'Programado'
        ]);

        // 5. SEED PLANES DE SALUD
        $this->command->info("Seeding Planes de Salud...");
        $planComplementario = HealthPlan::create([
            'code' => 'PLAN-COMP',
            'name' => 'Plan Especial Complementario',
            'plan_type' => 'Complementario',
            'description' => 'Plan de cobertura complementario para ejecutivos.',
            'status' => 'Activo',
            'effective_from' => '2026-01-01'
        ]);

        $servicio = ServicioMedico::first();
        if ($servicio) {
            HealthPlanCoverage::create([
                'health_plan_id' => $planComplementario->id,
                'pdss_service_id' => $servicio->id,
                'coverage_percent' => 80.00,
                'copay_percent' => 20.00,
                'fixed_copay' => 0.00,
                'limit_amount' => 150000.00,
                'limit_period' => 'Anual',
                'waiting_period_days' => 30,
                'requires_authorization' => true
            ]);
        }

        CoverageDerivationRule::create([
            'health_plan_id' => $planComplementario->id,
            'derivation_type' => 'Edad',
            'condition_json' => ['edad_min' => 60],
            'result_json' => ['copay_additional' => 5],
            'priority' => 1,
            'status' => 'Activo'
        ]);

        CoverageLimit::create([
            'health_plan_id' => $planComplementario->id,
            'limit_type' => 'Monto',
            'amount' => 500000.00,
            'period' => 'Anual',
            'status' => 'Activo'
        ]);

        // 6. SEED PRESTADORES
        $this->command->info("Seeding Prestadores Fortalecimiento...");
        $groupPss = ProviderGroup::create(['name' => 'Grupo Hospiten', 'description' => 'Red Hospiten a nivel nacional.']);
        $network = ProviderNetwork::create(['name' => 'Red Premium Gold', 'status' => 'Activo']);
        $network->plans()->attach([$planComplementario->id]);

        $pssFisica = Pss::create([
            'nombre' => 'DR. ALBERTO CABRERA',
            'rnc' => '00199281729',
            'tipo_entidad' => 'Médico Especialista',
            'telefono' => '809-555-0199',
            'direccion' => 'Av. Lincoln #444',
            'estado' => 'Activa',
            'pss_nature' => 'Física'
        ]);

        $pssClinica = Pss::create([
            'nombre' => 'CLINICA ABREU',
            'rnc' => '101009988',
            'tipo_entidad' => 'Clínica',
            'telefono' => '809-688-4411',
            'direccion' => 'Calle Beller #42',
            'estado' => 'Activa',
            'pss_nature' => 'Jurídica'
        ]);

        if ($servicio) {
            ProviderContractedService::create([
                'pss_id' => $pssClinica->id,
                'servicio_medico_id' => $servicio->id,
                'status' => 'Activo'
            ]);

            ProviderPriceAgreement::create([
                'pss_id' => $pssClinica->id,
                'health_plan_id' => $planComplementario->id,
                'servicio_medico_id' => $servicio->id,
                'price' => 1200.00,
                'status' => 'Activo'
            ]);
        }

        ProviderGeoLocation::create([
            'pss_id' => $pssClinica->id,
            'province' => 'Distrito Nacional',
            'municipality' => 'Santo Domingo de Guzmán',
            'sector' => 'Piantini',
            'latitude' => 18.47186,
            'longitude' => -69.90712,
            'address_details' => 'Frente al Parque Independencia'
        ]);

        $capContract = CapitatedServiceContract::create([
            'pss_id' => $pssClinica->id,
            'contract_number' => 'CAP-CONTR-009',
            'coverage_population_count' => 1500,
            'monthly_capitation_rate' => 150.00,
            'total_monthly_amount' => 225000.00,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'Activo'
        ]);

        CapitatedServicePayment::create([
            'capitated_contract_id' => $capContract->id,
            'period' => '202606',
            'amount_paid' => 225000.00,
            'paid_at' => '2026-06-30',
            'status' => 'Pagado'
        ]);

        // 7. SEED AFILIACIONES GRUPOS
        $this->command->info("Seeding Afiliaciones Grupos...");
        $groupAfil = AffiliateGroup::create(['name' => 'Corporación Banco Popular', 'rnc' => '101002384']);
        $contractAfil = AffiliateContract::create(['code' => 'EMP-P1', 'name' => 'Contrato Popular Premium', 'contract_type' => 'Corporativo']);
        $businessUnit = BusinessUnit::create(['name' => 'Unidad Zona Metropolitana', 'description' => 'Servicio a Santo Domingo']);
        
        $geoCode = GeographicCode::create([
            'region' => 'Metropolitana',
            'province' => 'Distrito Nacional',
            'municipality' => 'Santo Domingo de Guzmán',
            'sector' => 'Piantini'
        ]);

        AffiliateTransaction::create([
            'affiliate_id' => $frederik->id,
            'affiliate_type' => 'titular',
            'transaction_type' => 'Traspaso',
            'concept' => 'Traspaso de Entrada',
            'payload_before' => json_encode([]),
            'payload_after' => json_encode($frederik->toArray()),
            'user_id' => 1
        ]);

        // 8. SEED CARNETIZACIÓN STOCK & CENTERS
        $this->command->info("Seeding Carnetización Stock...");
        $center = PrintingCenter::create(['name' => 'Centro Principal Metropolitana', 'location' => 'Edificio Sede Central']);
        
        $supplyPlastico = PrintingSupply::create([
            'name' => 'Tarjetas Plásticas PVC',
            'supply_family' => 'plastico',
            'unit' => 'Unidad',
            'initial_stock' => 5000,
            'current_stock' => 4820
        ]);

        PrintingSupplyMovement::create([
            'supply_id' => $supplyPlastico->id,
            'printing_center_id' => $center->id,
            'movement_type' => 'salida',
            'quantity' => 1,
            'reason' => 'Carnet impreso de Juan Perez',
            'user_id' => 1
        ]);

        $carnetReq = CarnetRequest::create([
            'affiliate_id' => $frederik->id,
            'affiliate_type' => 'titular',
            'request_type' => 'Renovación',
            'printing_center_id' => $center->id,
            'request_date' => '2026-06-29',
            'status' => 'Impreso',
            'print_date' => '2026-06-29',
            'batch_number' => 'BATCH-202606'
        ]);

        CarnetDelivery::create([
            'carnet_request_id' => $carnetReq->id,
            'recipient_name' => 'JUAN PEREZ',
            'delivery_date' => '2026-06-30',
            'status' => 'Entregado'
        ]);

        CarnetTransfer::create([
            'carnet_request_id' => $carnetReq->id,
            'origin_location' => 'Sede Central',
            'destination_location' => 'Oficina Piantini',
            'sent_date' => '2026-06-29',
            'status' => 'Completada'
        ]);

        CarnetAdjustment::create([
            'supply_id' => $supplyPlastico->id,
            'printing_center_id' => $center->id,
            'adjustment_type' => 'merma',
            'quantity' => -5,
            'reason' => 'Plásticos dañados en calibración',
            'user_id' => 1
        ]);

        // 9. SEED PROMOTORES
        $this->command->info("Seeding Promotores...");
        $promoter = Promoter::create([
            'name' => 'PEDRO JAVIER SANTOS',
            'promoter_type' => 'persona_fisica',
            'identification_number' => '00109283742',
            'status' => 'Activo'
        ]);

        PromoterContract::create([
            'promoter_id' => $promoter->id,
            'contract_number' => 'PROM-CONTR-992',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'commission_percent' => 5.00,
            'status' => 'Activo'
        ]);

        $campaign = PromoterCampaign::create([
            'name' => 'Campaña Verano Premium',
            'description' => 'Ventas de Planes Complementarios del 1 de Junio al 31 de Agosto.',
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
            'commission_amount' => 500.00,
            'status' => 'Activa'
        ]);

        PromoterCommission::create([
            'promoter_id' => $promoter->id,
            'campaign_id' => $campaign->id,
            'affiliate_id' => $frederik->id,
            'amount' => 500.00,
            'payout_period' => '202606',
            'status' => 'Aprobada'
        ]);

        // 10. SEED BILLING & TICKETS
        $this->command->info("Seeding Billing & Tickets...");
        BillingInvoice::create([
            'invoice_number' => 'FAC-2026-0001',
            'health_plan_id' => $planComplementario->id,
            'affiliate_group_id' => $groupAfil->id,
            'amount' => 450000.00,
            'ncf' => 'B0100000122',
            'status' => 'Emitida',
            'issued_at' => '2026-06-30',
            'due_date' => '2026-07-30'
        ]);

        CustomerCase::create([
            'affiliate_id' => $frederik->id,
            'case_type' => 'Reclamación Cobertura',
            'description' => 'Afiliado Juan Perez reporta cobro indebido en farmacia para cobertura.',
            'status' => 'Abierto',
            'priority' => 'Media',
            'sla_hours' => 72
        ]);
        
        $this->command->info("Done Seeding operational data and 80k affiliates!");
    }
}
