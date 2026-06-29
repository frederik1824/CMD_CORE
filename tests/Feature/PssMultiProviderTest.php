<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\ServicioMedico;
use App\Models\Autorizacion;
use App\Models\AuthorizationClaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PssMultiProviderTest extends TestCase
{
    use RefreshDatabase;

    protected $clinicUser;
    protected $pharmacyUser;
    protected $laboratoryUser;
    protected $affiliate;
    protected $clinicPss;
    protected $pharmacyPss;
    protected $laboratoryPss;
    protected $medService;
    protected $labService;
    protected $medicineService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCatalogos();
        $this->setUpEntities();
    }

    private function seedCatalogos(): void
    {
        $catalogs = [
            ['grupo' => 'tipo_identificacion', 'codigo' => 'CEDULA', 'descripcion' => 'Cédula'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'OK', 'descripcion' => 'Aprobada'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'PE', 'descripcion' => 'Pendiente'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'RE', 'descripcion' => 'Rechazada'],
        ];

        foreach ($catalogs as $cat) {
            \App\Models\Catalogo::create($cat);
        }
    }

    private function setUpEntities(): void
    {
        // 1. Create Clinic, Pharmacy, and Lab PSS
        $this->clinicPss = Pss::create([
            'rnc' => '101000001',
            'nombre' => 'Clínica Central Demo',
            'tipo_entidad' => 'Clínica',
            'estado' => 'Activa',
            'tipo_pss' => 'clinic',
            'pss_type' => 'clinic',
            'pss_category' => 'clinica',
            'red_contratada' => true,
            'contrato_vigente' => true,
        ]);

        $this->pharmacyPss = Pss::create([
            'rnc' => '101000002',
            'nombre' => 'Farmacias GBC Demo',
            'tipo_entidad' => 'Farmacia',
            'estado' => 'Activa',
            'tipo_pss' => 'pharmacy',
            'pss_type' => 'pharmacy',
            'pss_category' => 'farmacia',
            'red_contratada' => true,
            'contrato_vigente' => true,
        ]);

        $this->laboratoryPss = Pss::create([
            'rnc' => '101000003',
            'nombre' => 'Amadita Laboratorio Clínico Demo',
            'tipo_entidad' => 'Laboratorio',
            'estado' => 'Activa',
            'tipo_pss' => 'laboratory',
            'pss_type' => 'laboratory',
            'pss_category' => 'laboratorio',
            'red_contratada' => true,
            'contrato_vigente' => true,
        ]);

        // 2. Create Users associated with each PSS (setting fields directly to bypass #[Fillable] restrictions)
        $this->clinicUser = new User();
        $this->clinicUser->name = 'Usuario Clínica';
        $this->clinicUser->email = 'clinica@pss.com';
        $this->clinicUser->password = bcrypt('password');
        $this->clinicUser->role = 'Usuario PSS';
        $this->clinicUser->pss_id = $this->clinicPss->id;
        $this->clinicUser->save();

        $this->pharmacyUser = new User();
        $this->pharmacyUser->name = 'Usuario Farmacia';
        $this->pharmacyUser->email = 'farmacia@pss.com';
        $this->pharmacyUser->password = bcrypt('password');
        $this->pharmacyUser->role = 'Usuario PSS';
        $this->pharmacyUser->pss_id = $this->pharmacyPss->id;
        $this->pharmacyUser->save();

        $this->laboratoryUser = new User();
        $this->laboratoryUser->name = 'Usuario Laboratorio';
        $this->laboratoryUser->email = 'laboratorio@pss.com';
        $this->laboratoryUser->password = bcrypt('password');
        $this->laboratoryUser->role = 'Usuario PSS';
        $this->laboratoryUser->pss_id = $this->laboratoryPss->id;
        $this->laboratoryUser->save();

        // Connect Pss to Users via pivot with roles and active status
        \App\Models\PssUser::create([
            'user_id' => $this->clinicUser->id,
            'pss_id' => $this->clinicPss->id,
            'access_type' => 'medical_center',
            'status' => 'activo'
        ]);

        \App\Models\PssUser::create([
            'user_id' => $this->pharmacyUser->id,
            'pss_id' => $this->pharmacyPss->id,
            'access_type' => 'pharmacy',
            'status' => 'activo'
        ]);

        \App\Models\PssUser::create([
            'user_id' => $this->laboratoryUser->id,
            'pss_id' => $this->laboratoryPss->id,
            'access_type' => 'laboratory',
            'status' => 'activo'
        ]);

        // 3. Create active Affiliate
        $tipoIdCed = \App\Models\Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CEDULA')->first()->id ?? 1;
        $this->affiliate = Afiliado::create([
            'tipo_identificacion_id' => $tipoIdCed,
            'cedula' => '00199999999',
            'nss' => '99999999',
            'nui' => '88888888',
            'nombres' => 'MARIA',
            'primer_apellido' => 'RODRIGUEZ',
            'segundo_apellido' => 'SOSA',
            'fecha_nacimiento' => '1985-05-15',
            'sexo' => 'F',
            'provincia' => 'Santiago',
            'municipio' => 'Santiago de los Caballeros',
            'estado_afiliacion' => 'OK',
            'activo_nomina' => true,
            'tiene_aporte' => true,
            'regimen_actual' => 'Contributivo',
            'numero_contrato' => 'CONTR-999-01',
        ]);

        // 4. Create Medical Services (Clinic Consultation, Lab Test, Medicine)
        $this->medService = ServicioMedico::create([
            'codigo' => '801001',
            'descripcion' => 'Consulta Médica General',
            'cobertura_base' => 80.00,
        ]);

        $this->labService = ServicioMedico::create([
            'codigo' => '802001',
            'descripcion' => 'Hemograma Completo',
            'cobertura_base' => 80.00,
        ]);

        $this->medicineService = ServicioMedico::create([
            'codigo' => '803001',
            'descripcion' => 'Ibuprofeno 400mg',
            'cobertura_base' => 70.00,
        ]);

        // Setup Contracts
        $cClinic = ContratoPss::create(['pss_id' => $this->clinicPss->id, 'numero_contrato' => 'CONTR-CLI-01', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2027-12-31', 'estado' => 'Activo']);
        TarifaPss::create(['contrato_pss_id' => $cClinic->id, 'servicio_medico_id' => $this->medService->id, 'monto_tarifa' => 1500.00]);

        $cPharmacy = ContratoPss::create(['pss_id' => $this->pharmacyPss->id, 'numero_contrato' => 'CONTR-PHAR-01', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2027-12-31', 'estado' => 'Activo']);
        TarifaPss::create(['contrato_pss_id' => $cPharmacy->id, 'servicio_medico_id' => $this->medicineService->id, 'monto_tarifa' => 500.00]);

        $cLab = ContratoPss::create(['pss_id' => $this->laboratoryPss->id, 'numero_contrato' => 'CONTR-LAB-01', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2027-12-31', 'estado' => 'Activo']);
        TarifaPss::create(['contrato_pss_id' => $cLab->id, 'servicio_medico_id' => $this->labService->id, 'monto_tarifa' => 450.00]);
    }

    /**
     * Test clinic dashboard redirection.
     */
    public function test_login_redirection_clinic(): void
    {
        $response = $this->actingAs($this->clinicUser)
            ->withSession(['active_access_type' => 'medical_center', 'active_pss_id' => $this->clinicPss->id])
            ->get('/portal-autorizaciones/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Portal de Transmisión Clínica'); // Clinic view
    }

    /**
     * Test pharmacy dashboard redirection.
     */
    public function test_login_redirection_pharmacy(): void
    {
        $response = $this->actingAs($this->pharmacyUser)
            ->withSession(['active_access_type' => 'pharmacy', 'active_pss_id' => $this->pharmacyPss->id])
            ->get('/portal-autorizaciones/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Farmacia'); // Pharmacy view
    }

    /**
     * Test laboratory dashboard redirection.
     */
    public function test_login_redirection_laboratory(): void
    {
        $response = $this->actingAs($this->laboratoryUser)
            ->withSession(['active_access_type' => 'laboratory', 'active_pss_id' => $this->laboratoryPss->id])
            ->get('/portal-autorizaciones/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Laboratorio'); // Laboratory view
    }

    /**
     * Test Pharmacy Outpatient Medicine Flow & Limit check.
     */
    public function test_pharmacy_dispensation_creation_and_limit_check(): void
    {
        Storage::fake('local');
        $recipeFile = UploadedFile::fake()->create('receta_medica.pdf', 100);

        // Submit new dispensation
        $response = $this->actingAs($this->pharmacyUser)
            ->withSession(['active_access_type' => 'pharmacy', 'active_pss_id' => $this->pharmacyPss->id])
            ->post('/portal-autorizaciones/farmacia/nueva-dispensacion', [
                'afiliado_id' => $this->affiliate->id,
                'afiliado_type' => 'titular',
                'doctor_name' => 'Dr. Carlos Mendoza',
                'doctor_exequatur' => '12345-67',
                'prescription_date' => Carbon::now()->toDateString(),
                'diagnostico' => 'J00 - Gripe común',
                'medicamentos' => [$this->medicineService->id],
                'cantidades' => [2],
                'precios' => [500.00],
                'documento_receta' => $recipeFile,
            ]);

        $response->assertRedirect('/portal-autorizaciones/autorizaciones/mis-solicitudes');

        // Verify dispensation + claims created
        $dispensations = \App\Models\PharmacyDispensation::all();
        $this->assertCount(1, $dispensations);

        $claim = AuthorizationClaim::where('claim_origin_type', 'pharmacy')->first();
        $this->assertNotNull($claim);
        // Ibuprofeno rate is 500, count is 2. Total = 1000. Coinsurance is 70% ARS, 30% Affiliate.
        // ARS amount is 700, Affiliate copay is 300.
        $this->assertEquals(1000.00, $claim->claimed_amount);
        $this->assertEquals(700.00, $claim->approved_amount);
        $this->assertEquals('Pagada', $claim->status);
    }

    /**
     * Test Laboratory Order Flow & mock results.
     */
    public function test_laboratory_order_creation_and_results_upload(): void
    {
        Storage::fake('local');
        $orderFile = UploadedFile::fake()->create('orden_medica.pdf', 120);

        // Register new lab order
        $response = $this->actingAs($this->laboratoryUser)
            ->withSession(['active_access_type' => 'laboratory', 'active_pss_id' => $this->laboratoryPss->id])
            ->post('/portal-autorizaciones/laboratorio/nueva-orden', [
                'afiliado_id' => $this->affiliate->id,
                'afiliado_type' => 'titular',
                'doctor_name' => 'Dr. Jose Perez',
                'doctor_exequatur' => '76543-21',
                'order_date' => Carbon::now()->toDateString(),
                'diagnostico' => 'J00 - Gripe común',
                'pruebas' => [$this->labService->id],
                'precios' => [450.00],
                'documento_orden' => $orderFile,
            ]);

        $response->assertRedirect('/portal-autorizaciones/autorizaciones/mis-solicitudes');

        $orders = \App\Models\LabOrder::all();
        $this->assertCount(1, $orders);

        $claim = AuthorizationClaim::where('claim_origin_type', 'laboratory')->first();
        $this->assertNotNull($claim);
        // Lab rate is 450, coverage is 80% ARS, 20% Affiliate.
        // ARS amount is 360, Affiliate copay is 90.
        $this->assertEquals(450.00, $claim->claimed_amount);
        $this->assertEquals(360.00, $claim->approved_amount);

        // Upload results to complete order
        $resultFile = UploadedFile::fake()->create('resultado_hemograma.pdf', 150);
        $responseResult = $this->actingAs($this->laboratoryUser)
            ->withSession(['active_access_type' => 'laboratory', 'active_pss_id' => $this->laboratoryPss->id])
            ->post("/portal-autorizaciones/laboratorio/resultados/subir", [
                'lab_order_id' => $orders->first()->id,
                'lab_order_item_id' => $orders->first()->items->first()->id,
                'resultado_archivo' => $resultFile,
                'observaciones' => 'Resultados normales',
            ]);

        $responseResult->assertRedirect();
        
        $orders->first()->items->first()->refresh();
        $this->assertEquals('Realizada', $orders->first()->items->first()->status);
    }
}
