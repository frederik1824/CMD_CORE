<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Catalogo;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Pss;
use App\Models\ServicioMedico;
use App\Models\ContratoPss;
use App\Models\TarifaPss;
use App\Models\Lote;
use App\Models\LoteDetalle;
use App\Models\Novedad;
use App\Models\Autorizacion;
use App\Models\AutorizacionDetalle;
use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\AuthorizationClaim;
use App\Models\AccountPayable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding Catalogs...');
        $this->seedCatalogos();

        $this->command->info('Seeding Users and Roles...');
        $this->seedUsers();

        $this->command->info('Seeding PSS and Services...');
        $this->seedPssAndServices();

        try {
            $this->command->info('Importing PDSS 10.0 Catalog from Excel...');
            Artisan::call('pdss:import-excel');
            $this->command->info(Artisan::output());
        } catch (\Throwable $e) {
            $this->command->warn('Warning: PDSS Excel import skipped or failed: ' . $e->getMessage());
        }

        $this->command->info('Seeding PDSS Contracts...');
        $this->call(PdssSeeder::class);

        $this->command->info('Seeding PDSS Coverage Rules...');
        $this->call(PdssCoverageRuleSeeder::class);

        $this->command->info('Seeding Afiliados and Dependientes...');
        $this->seedAfiliadosAndDependientes();

        $this->command->info('Seeding Lotes and Novedades...');
        $this->seedLotesAndNovedades();

        $this->command->info('Seeding Autorizaciones Médicas...');
        $this->seedAutorizaciones();

        $this->command->info('Seeding Aula Virtual...');
        $this->seedAulaVirtual();

        $this->command->info('Seeding Contabilidad y Finanzas ARS...');
        $this->call(AccountingDemoSeeder::class);

        $this->command->info('Creating massive affiliate data + dispersion + notifications...');
        $this->call(MassiveDataSeeder::class);

        $this->command->info('Seeding Reclamaciones y Lotes de Pago PSS...');
        $this->call(ClaimDemoSeeder::class);

        $this->command->info('Seeding Unipago Cápitas and Dispersiones...');
        $this->call(UnipagoDemoSeeder::class);

        $this->command->info('Seeding Reembolsos Excepcionales de Afiliados...');
        $this->call(ReimbursementDemoSeeder::class);

        $this->command->info('Seeding Contratos y Tarifarios V2 PSS (incluyendo Prestador Farmacia)...');
        $this->call(TariffV2DemoSeeder::class);

        $this->command->info('Seeding dynamic multi-prestador demo records...');
        $this->seedMultiPssDemoData();
    }

    private function seedCatalogos(): void
    {
        $catalogs = [
            // Estados de lote
            ['grupo' => 'estado_lote', 'codigo' => 'VE', 'descripcion' => 'Validado en Espera'],
            ['grupo' => 'estado_lote', 'codigo' => 'PC', 'descripcion' => 'Procesando'],
            ['grupo' => 'estado_lote', 'codigo' => 'PE', 'descripcion' => 'Procesado con Errores'],
            ['grupo' => 'estado_lote', 'codigo' => 'RE', 'descripcion' => 'Rechazado'],
            ['grupo' => 'estado_lote', 'codigo' => 'EV', 'descripcion' => 'Enviado a Validar / Procesado OK'],

            // Estados de solicitud
            ['grupo' => 'estado_solicitud', 'codigo' => 'OK', 'descripcion' => 'Aprobada'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'PE', 'descripcion' => 'Pendiente'],
            ['grupo' => 'estado_solicitud', 'codigo' => 'RE', 'descripcion' => 'Rechazada'],

            // Estados de novedades
            ['grupo' => 'estado_novedad', 'codigo' => 'OK', 'descripcion' => 'Aplicada'],
            ['grupo' => 'estado_novedad', 'codigo' => 'PE', 'descripcion' => 'Pendiente'],
            ['grupo' => 'estado_novedad', 'codigo' => 'RE', 'descripcion' => 'Rechazada'],
            ['grupo' => 'estado_novedad', 'codigo' => 'AC', 'descripcion' => 'Activa'],
            ['grupo' => 'estado_novedad', 'codigo' => 'CA', 'descripcion' => 'Cancelada'],
            ['grupo' => 'estado_novedad', 'codigo' => 'DE', 'descripcion' => 'Devuelta'],

            // Tipos de identificación
            ['grupo' => 'tipo_identificacion', 'codigo' => 'CED', 'descripcion' => 'Cédula de Identidad'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'RNC', 'descripcion' => 'Registro Nacional de Contribuyentes'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'NSS', 'descripcion' => 'Número de Seguridad Social'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'NUI', 'descripcion' => 'Número Único de Identificación'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'ACT', 'descripcion' => 'Acta de Nacimiento'],
            ['grupo' => 'tipo_identificacion', 'codigo' => 'EXT', 'descripcion' => 'Acta de Nacimiento Extranjero'],

            // Tipos de régimen
            ['grupo' => 'tipo_regimen', 'codigo' => 'Contributivo', 'descripcion' => 'Régimen Contributivo'],
            ['grupo' => 'tipo_regimen', 'codigo' => 'Subsidiado', 'descripcion' => 'Régimen Subsidiado'],
            ['grupo' => 'tipo_regimen', 'codigo' => 'Pensionados', 'descripcion' => 'Régimen de Pensionados'],

            // Tipos de administración
            ['grupo' => 'tipo_administracion', 'codigo' => 'Privada', 'descripcion' => 'Privada'],
            ['grupo' => 'tipo_administracion', 'codigo' => 'Publica', 'descripcion' => 'Pública'],
            ['grupo' => 'tipo_administracion', 'codigo' => 'Autogestion', 'descripcion' => 'Autogestión'],

            // Tipos de afiliación
            ['grupo' => 'tipo_afiliacion', 'codigo' => 'Normal', 'descripcion' => 'Normal'],
            ['grupo' => 'tipo_afiliacion', 'codigo' => 'Automatica', 'descripcion' => 'Automática'],
            ['grupo' => 'tipo_afiliacion', 'codigo' => 'Traspaso', 'descripcion' => 'Traspaso'],
            ['grupo' => 'tipo_afiliacion', 'codigo' => 'Novedad', 'descripcion' => 'Novedad'],

            // Tipos de entidad
            ['grupo' => 'tipo_entidad', 'codigo' => 'ARS', 'descripcion' => 'Administradora de Riesgos de Salud'],
            ['grupo' => 'tipo_entidad', 'codigo' => 'PSS', 'descripcion' => 'Prestador de Servicios de Salud'],

            // Parentescos
            ['grupo' => 'parentesco', 'codigo' => 'CONYUGE', 'descripcion' => 'Cónyuge / Compañero'],
            ['grupo' => 'parentesco', 'codigo' => 'HIJO', 'descripcion' => 'Hijo / Hija'],
            ['grupo' => 'parentesco', 'codigo' => 'PADRE', 'descripcion' => 'Padre / Madre'],
            ['grupo' => 'parentesco', 'codigo' => 'OTROS', 'descripcion' => 'Otros Dependientes Adicionales'],

            // Tipos de novedad
            ['grupo' => 'tipo_novedad', 'codigo' => 'DATO', 'descripcion' => 'Cambio de datos de afiliación'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'CONTRATO', 'descripcion' => 'Cambio de contrato'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'UBICACION', 'descripcion' => 'Cambio de ubicación geográfica'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'FALLECE', 'descripcion' => 'Notificación de fallecimiento'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'DIVORCIO', 'descripcion' => 'Divorcio o separación'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'TITULARIDAD', 'descripcion' => 'Cambio de titularidad'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'BAJA', 'descripcion' => 'Baja de afiliado'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'EMPLEO', 'descripcion' => 'Pérdida de empleo'],
            ['grupo' => 'tipo_novedad', 'codigo' => 'PROVISIONAL', 'descripcion' => 'Baja por antigüedad en afiliación provisional'],

            // Diagnósticos CIE-10
            ['grupo' => 'diagnostico', 'codigo' => 'I10', 'descripcion' => 'HTA Esencial (Primaria)'],
            ['grupo' => 'diagnostico', 'codigo' => 'E11', 'descripcion' => 'Diabetes Mellitus No Insulinodependiente'],
            ['grupo' => 'diagnostico', 'codigo' => 'K80', 'descripcion' => 'Colelitiasis'],
            ['grupo' => 'diagnostico', 'codigo' => 'C50', 'descripcion' => 'Tumor Maligno de la Mama'],
            ['grupo' => 'diagnostico', 'codigo' => 'N18', 'descripcion' => 'Insuficiencia Renal Crónica'],
            ['grupo' => 'diagnostico', 'codigo' => 'M54', 'descripcion' => 'Dorsalgia'],
            ['grupo' => 'diagnostico', 'codigo' => 'Z00', 'descripcion' => 'Examen Médico General'],
            ['grupo' => 'diagnostico', 'codigo' => 'R51', 'descripcion' => 'Cefalea'],
            ['grupo' => 'diagnostico', 'codigo' => 'K35', 'descripcion' => 'Apendicitis Aguda'],
            ['grupo' => 'diagnostico', 'codigo' => 'O82', 'descripcion' => 'Parto por Cesárea'],
            ['grupo' => 'diagnostico', 'codigo' => 'O034', 'descripcion' => 'Aborto Espontáneo, Incompleto, Sin Complicación']
        ];

        foreach ($catalogs as $cat) {
            Catalogo::updateOrCreate(
                ['codigo' => $cat['codigo']],
                $cat
            );
        }
    }

    private function seedUsers(): void
    {
        $roles = [
            'Administrador ARS' => 'admin@ars.com',
            'Supervisor Afiliación' => 'supervisor@ars.com',
            'Analista Afiliación' => 'analista@ars.com',
            'Auditor Médico' => 'auditor@ars.com',
            'Autorizaciones Médicas' => 'autorizaciones@ars.com',
            'Usuario PSS' => 'pss@ars.com',
            'Consulta' => 'consulta@ars.com'
        ];

        foreach ($roles as $role => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $role,
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'pss_id' => ($role === 'Usuario PSS') ? 1 : null
                ]
            );
        }

        // 3 Estudiantes Demo
        for ($s = 1; $s <= 3; $s++) {
            User::updateOrCreate(
                ['email' => "estudiante$s@ars.com"],
                [
                    'name' => "Estudiante Demo $s",
                    'password' => Hash::make('password'),
                    'role' => 'Estudiante',
                    'pss_id' => null
                ]
            );
        }
    }

    private function seedPssAndServices(): void
    {
        // 12 PSS
        $pssList = [
            ['rnc' => '101002034', 'nombre' => 'Clínica Central Demo', 'tipo_entidad' => 'Clínica', 'telefono' => '809-688-4411', 'correo' => 'info@clinicacentral.com.do', 'direccion' => 'Calle Beller No. 42, Santo Domingo', 'estado' => 'Activa', 'tipo_pss' => 'medical_center', 'pss_type' => 'medical_center', 'pss_category' => 'centro_medico', 'nivel_atencion' => 3, 'red_contratada' => true, 'contrato_vigente' => true],
            ['rnc' => '101083421', 'nombre' => 'CEDIMAT', 'tipo_entidad' => 'Centro Médico', 'telefono' => '809-565-9989', 'correo' => 'contacto@cedimat.com', 'direccion' => 'Plaza de la Salud, Ensanche Ortega y Gasset, SD', 'estado' => 'Activa'],
            ['rnc' => '102034567', 'nombre' => 'Clínica Unión Médica', 'tipo_entidad' => 'Clínica', 'telefono' => '809-226-8686', 'correo' => 'info@unionmedica.com', 'direccion' => 'Av. 27 de Febrero No. 23, Santiago', 'estado' => 'Activa'],
            ['rnc' => '101123456', 'nombre' => 'Hospital General Plaza de la Salud', 'tipo_entidad' => 'Hospital', 'telefono' => '809-565-7477', 'correo' => 'hgps@hgps.org.do', 'direccion' => 'Av. Ortega y Gasset, Santo Domingo', 'estado' => 'Activa'],
            ['rnc' => '101234765', 'nombre' => 'Centro Médico Dominicano', 'tipo_entidad' => 'Centro Médico', 'telefono' => '809-535-6677', 'correo' => 'cmd@cmd.com.do', 'direccion' => 'Av. Winston Churchill, Santo Domingo', 'estado' => 'Activa'],
            ['rnc' => '101456321', 'nombre' => 'Clínica Corazones Unidos', 'tipo_entidad' => 'Clínica', 'telefono' => '809-567-4421', 'correo' => 'info@corazonesunidos.com.do', 'direccion' => 'Calle Fantino Falco, Santo Domingo', 'estado' => 'Activa'],
            ['rnc' => '103098765', 'nombre' => 'Clínica Santa Cruz', 'tipo_entidad' => 'Clínica', 'telefono' => '809-525-3344', 'correo' => 'info@clinicasantacruz.com', 'direccion' => 'Av. Libertad, San Francisco de Macorís', 'estado' => 'Activa'],
            ['rnc' => '104056789', 'nombre' => 'Centro Médico Bournigal', 'tipo_entidad' => 'Centro Médico', 'telefono' => '809-586-2342', 'correo' => 'bournigal@cmbournigal.com', 'direccion' => 'Calle Antera Mota, Puerto Plata', 'estado' => 'Activa'],
            ['rnc' => '102123456', 'nombre' => 'HOMS (Hospital Metropolitano)', 'tipo_entidad' => 'Hospital', 'telefono' => '829-947-2222', 'correo' => 'info@homs.com.do', 'direccion' => 'Autopista Duarte Km 2.8, Santiago', 'estado' => 'Activa'],
            ['rnc' => '101987654', 'nombre' => 'Centro Médico Inactivo Demo', 'tipo_entidad' => 'Clínica', 'telefono' => '809-555-1234', 'correo' => 'inactivo@demo.com', 'direccion' => 'Zona Oriental, Santo Domingo', 'estado' => 'Inactiva'],
            
            // New multi-prestadoras
            ['rnc' => '202003045', 'nombre' => 'Amadita Laboratorio Clínico Demo', 'tipo_entidad' => 'Laboratorio', 'telefono' => '809-555-2233', 'correo' => 'info@amaditalab.com', 'direccion' => 'Av. Lincoln No. 100, Santo Domingo', 'estado' => 'Activa', 'tipo_pss' => 'laboratory', 'pss_type' => 'laboratory', 'pss_category' => 'laboratorio', 'nivel_atencion' => 1, 'red_contratada' => true, 'contrato_vigente' => true],
            ['rnc' => '303004056', 'nombre' => 'Farmacias GBC Demo', 'tipo_entidad' => 'Farmacia', 'telefono' => '809-555-4455', 'correo' => 'info@farmaciasgbc.com', 'direccion' => 'Av. Churchill No. 200, Santo Domingo', 'estado' => 'Activa', 'tipo_pss' => 'pharmacy', 'pss_type' => 'pharmacy', 'pss_category' => 'farmacia', 'nivel_atencion' => 1, 'red_contratada' => true, 'contrato_vigente' => true]
        ];

        foreach ($pssList as $pss) {
            Pss::create($pss);
        }

        // 30 Servicios Médicos
        $servicios = [
            // Consultas
            ['codigo' => 'CON-001', 'descripcion' => 'Consulta Médica General', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'CON-002', 'descripcion' => 'Consulta Médica Especializada (Pediatría/Gineco-Obstetricia)', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'CON-003', 'descripcion' => 'Consulta de Cardiología', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            
            // Laboratorio
            ['codigo' => 'LAB-001', 'descripcion' => 'Hemograma Completo', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'LAB-002', 'descripcion' => 'Perfil Lipídico', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'LAB-003', 'descripcion' => 'Glucosa en Ayunas', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'LAB-004', 'descripcion' => 'Urianálisis Completo', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'LAB-005', 'descripcion' => 'Prueba de Función Renal (Creatinina/Urea)', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            
            // Imágenes
            ['codigo' => 'IMA-001', 'descripcion' => 'Radiografía de Tórax AP', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'IMA-002', 'descripcion' => 'Sonografía Abdominal', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'IMA-003', 'descripcion' => 'Mamografía Bilateral', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'IMA-004', 'descripcion' => 'Tomografía Computarizada (TAC) de Cráneo', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'IMA-005', 'descripcion' => 'Resonancia Magnética (RMN) de Columna', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            
            // Procedimientos menores
            ['codigo' => 'PRO-001', 'descripcion' => 'Electrocardiograma (EKG)', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'PRO-002', 'descripcion' => 'Endoscopía Digestiva Alta', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'PRO-003', 'descripcion' => 'Colonoscopía Diagnóstica', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            
            // Cirugías
            ['codigo' => 'CIR-001', 'descripcion' => 'Apendicectomía Laparoscópica', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'CIR-002', 'descripcion' => 'Colecistectomía (Vesícula)', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'CIR-003', 'descripcion' => 'Hernioplastía Inguinal', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'CIR-004', 'descripcion' => 'Cesárea Segmentaria', 'cobertura_base' => 90.00, 'es_alto_costo' => false, 'requiere_documento' => true],

            // Alto Costo
            ['codigo' => 'ATC-001', 'descripcion' => 'Tratamiento Quimioterapia (Ciclo Completo)', 'cobertura_base' => 90.00, 'es_alto_costo' => true, 'requiere_documento' => true],
            ['codigo' => 'ATC-002', 'descripcion' => 'Hemodiálisis por Insuficiencia Renal', 'cobertura_base' => 90.00, 'es_alto_costo' => true, 'requiere_documento' => true],
            ['codigo' => 'ATC-003', 'descripcion' => 'Cirugía de Bypass Coronario (Cardiovascular)', 'cobertura_base' => 90.00, 'es_alto_costo' => true, 'requiere_documento' => true],
            ['codigo' => 'ATC-004', 'descripcion' => 'Reemplazo Total de Cadera (Prótesis)', 'cobertura_base' => 90.00, 'es_alto_costo' => true, 'requiere_documento' => true],
            ['codigo' => 'ATC-005', 'descripcion' => 'Radioterapia Oncológica (3D Conformada)', 'cobertura_base' => 90.00, 'es_alto_costo' => true, 'requiere_documento' => true],
            
            // Medicamentos e Insumos
            ['codigo' => 'MED-001', 'descripcion' => 'Medicamentos Ambulatorios Básicos', 'cobertura_base' => 70.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'MED-002', 'descripcion' => 'Insulina y Suministros para Diabéticos', 'cobertura_base' => 70.00, 'es_alto_costo' => false, 'requiere_documento' => true],
            ['codigo' => 'MED-003', 'descripcion' => 'Material Gastable Quirúrgico', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            
            // Emergencias
            ['codigo' => 'EME-001', 'descripcion' => 'Atención Médica de Emergencia', 'cobertura_base' => 85.00, 'es_alto_costo' => false, 'requiere_documento' => false],
            ['codigo' => 'EME-002', 'descripcion' => 'Sutura de Herida Menor', 'cobertura_base' => 80.00, 'es_alto_costo' => false, 'requiere_documento' => false],
        ];

        foreach ($servicios as $srv) {
            ServicioMedico::create($srv);
        }

        // Crear 5 Contratos PSS con tarifas para las 4 principales PSS
        $contratoPssIds = [1, 2, 4, 9]; // Clínica Abreu, CEDIMAT, Plaza de la Salud, HOMS
        $servs = ServicioMedico::all();

        foreach ($contratoPssIds as $pssId) {
            $contrato = ContratoPss::create([
                'pss_id' => $pssId,
                'numero_contrato' => 'CONTR-' . now()->format('Y') . '-' . str_pad($pssId, 3, '0', STR_PAD_LEFT),
                'fecha_inicio' => now()->subMonths(6)->toDateString(),
                'fecha_fin' => now()->addMonths(18)->toDateString(),
                'estado' => 'Activo'
            ]);

            // Tarifas
            foreach ($servs as $srv) {
                // Monto base simulado según el código del servicio
                $montoBase = 1500.00;
                if (str_starts_with($srv->codigo, 'CON')) {
                    $montoBase = 2000.00;
                } elseif (str_starts_with($srv->codigo, 'LAB')) {
                    $montoBase = 450.00;
                } elseif (str_starts_with($srv->codigo, 'IMA')) {
                    $montoBase = 2500.00;
                    if ($srv->codigo === 'IMA-005') $montoBase = 8500.00;
                } elseif (str_starts_with($srv->codigo, 'CIR')) {
                    $montoBase = 45000.00;
                } elseif ($srv->es_alto_costo) {
                    $montoBase = 120000.00;
                }

                // Pequeña variación de tarifa por PSS
                $montoTarifa = $montoBase * (1 + (($pssId % 5) * 0.05));

                TarifaPss::create([
                    'contrato_pss_id' => $contrato->id,
                    'servicio_medico_id' => $srv->id,
                    'monto_tarifa' => round($montoTarifa, 2)
                ]);
            }
        }
    }

    private function seedAfiliadosAndDependientes(): void
    {
        $tipoIdCed = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CED')->first()->id;
        $tipoIdNss = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'NSS')->first()->id;
        
        $provincias = ['Distrito Nacional', 'Santo Domingo', 'Santiago', 'San Cristóbal', 'La Altagracia', 'Duarte', 'Puerto Plata', 'La Vega', 'San Pedro de Macorís', 'La Romana'];
        $municipios = [
            'Distrito Nacional' => 'Santo Domingo de Guzmán',
            'Santo Domingo' => 'Santo Domingo Este',
            'Santiago' => 'Santiago de los Caballeros',
            'San Cristóbal' => 'San Cristóbal',
            'La Altagracia' => 'Salvaleón de Higüey',
            'Duarte' => 'San Francisco de Macorís',
            'Puerto Plata' => 'San Felipe de Puerto Plata',
            'La Vega' => 'Concepción de La Vega',
            'San Pedro de Macorís' => 'San Pedro de Macorís',
            'La Romana' => 'La Romana'
        ];

        $nombresM = ['Luis', 'Carlos', 'José', 'Juan', 'Miguel', 'Manuel', 'Ramón', 'Francisco', 'Rafael', 'Pedro', 'David', 'Jorge', 'Julio', 'Ángel', 'Roberto', 'Eduardo', 'Héctor', 'Mario', 'Rubén', 'Alejandro', 'Daniel', 'Fernando', 'Ricardo', 'Alberto', 'Raúl'];
        $nombresF = ['María', 'Ana', 'Carmen', 'Juana', 'Rosa', 'Francisca', 'Yolanda', 'Margarita', 'Luz', 'Juana', 'Sandra', 'Patricia', 'Elizabeth', 'Laura', 'Carolina', 'Diana', 'Marta', 'Teresa', 'Clara', 'Elena', 'Sofía', 'Beatriz', 'Raquel', 'Julia', 'Isabel'];
        $apellidos = ['Martínez', 'Rodríguez', 'Gómez', 'Pérez', 'Sánchez', 'Díaz', 'Fernández', 'Álvarez', 'Cruz', 'Ramírez', 'Flores', 'Vázquez', 'Guzmán', 'Ortiz', 'Castillo', 'Reyes', 'Ramos', 'Espinal', 'Vargas', 'Jiménez', 'Mejía', 'Castro', 'Arias', 'Santos', 'Brito'];

        // 50 Titulares
        for ($i = 1; $i <= 50; $i++) {
            $sexo = ($i % 2 === 0) ? 'F' : 'M';
            $nombre = ($sexo === 'F') ? $nombresF[($i) % count($nombresF)] : $nombresM[($i) % count($nombresM)];
            $ape1 = $apellidos[($i * 2) % count($apellidos)];
            $ape2 = $apellidos[($i * 3) % count($apellidos)];
            
            // Determinamos el último dígito para probar diversos casos en el simulador
            // Cedula finalizada en 0 o 1 es Apta (y la marcaremos como OK ya procesada)
            // Otras serán RE (rechazadas TSS), en otra ARS, etc.
            $lastDigit = $i % 10;
            $cedula = '001' . str_pad($i * 12345, 7, '0', STR_PAD_LEFT) . $lastDigit;
            $nss = '1' . str_pad($i * 8765, 8, '0', STR_PAD_LEFT);
            $nui = '3' . str_pad($i * 9999, 8, '0', STR_PAD_LEFT);
            
            $prov = $provincias[$i % count($provincias)];
            $mun = $municipios[$prov];

            $estado = 'OK'; // Por defecto los creamos activos para que el demo tenga datos iniciales
            $motivo = 'Afiliado aprobado por Unipago.';
            
            // Algunos los creamos en otros estados para enriquecer la visualización
            if ($i > 40) {
                if ($lastDigit === 2) {
                    $estado = 'RE';
                    $motivo = 'Ciudadano ya se encuentra afiliado a esta ARS (Duplicado).';
                } elseif ($lastDigit === 3) {
                    $estado = 'RE';
                    $motivo = 'Ciudadano está afiliado en otra ARS (Régimen Contributivo).';
                } elseif ($lastDigit === 6) {
                    $estado = 'RE';
                    $motivo = 'Sin nómina activa reportada en la Tesorería de la Seguridad Social (TSS).';
                } else {
                    $estado = 'Pendiente';
                    $motivo = 'Pendiente de generación de lote y validación Unipago.';
                }
            }

            Afiliado::create([
                'tipo_identificacion_id' => $tipoIdCed,
                'cedula' => $cedula,
                'nss' => $nss,
                'nui' => $nui,
                'nombres' => $nombre,
                'primer_apellido' => $ape1,
                'segundo_apellido' => $ape2,
                'fecha_nacimiento' => now()->subYears(22 + ($i % 38))->subDays($i % 28)->toDateString(),
                'sexo' => $sexo,
                'provincia' => $prov,
                'municipio' => $mun,
                'telefono' => '809-555-' . str_pad($i * 11, 4, '0', STR_PAD_LEFT),
                'correo' => strtolower($nombre . '.' . $ape1 . '@correo.com'),
                'numero_contrato' => 'CONTR-ARS-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'fecha_suscripcion' => now()->subYears(2)->toDateString(),
                'estado_afiliacion' => $estado,
                'motivo_estado' => $motivo,
                'activo_nomina' => ($estado === 'OK'),
                'tiene_aporte' => ($estado === 'OK'),
                'regimen_actual' => 'Contributivo',
                'entidad_actual' => 'ARS Core Demo',
                'tipo_afiliacion' => ($estado === 'OK') ? 'Normal' : null,
                'fecha_afiliacion' => ($estado === 'OK') ? now()->subMonths(18)->toDateString() : null,
                'ultimo_periodo_pagado' => ($estado === 'OK') ? now()->subMonth()->format('Ym') : null,
            ]);
        }

        // Afiliado de prueba solicitado por el usuario: FREDERIK LOPEZ ALCANTARA
        Afiliado::create([
            'tipo_identificacion_id' => $tipoIdCed,
            'cedula' => '07900175907',
            'nss' => '10790017590',
            'nui' => '30790017590',
            'nombres' => 'FREDERIK',
            'primer_apellido' => 'LOPEZ',
            'segundo_apellido' => 'ALCANTARA',
            'fecha_nacimiento' => '1992-09-18',
            'sexo' => 'M',
            'provincia' => 'Distrito Nacional',
            'municipio' => 'Santo Domingo de Guzmán',
            'telefono' => '829-555-1234',
            'correo' => 'frederik.lopez@correo.com',
            'numero_contrato' => '008961897901', // Coincide con el screenshot (00896-18979-01)
            'fecha_suscripcion' => '2024-01-01',
            'estado_afiliacion' => 'OK',
            'esta_carnetizado' => true,
            'tiene_formulario' => true,
            'ubicacion_formulario' => 'Archivo F-04, Estante 3, Caja 12',
            'sector' => 'Piantini',
            'direccion' => 'Av. Winston Churchill #105, Torre Blue, Apto 5B',
            'motivo_estado' => 'Afiliado de prueba solicitado por el usuario.',
            'activo_nomina' => true,
            'tiene_aporte' => true,
            'regimen_actual' => 'Contributivo',
            'entidad_actual' => 'ARS CMD',
            'tipo_afiliacion' => 'Normal',
            'fecha_afiliacion' => '2024-01-01',
            'ultimo_periodo_pagado' => now()->subMonth()->format('Ym')
        ]);

        // 80 Dependientes
        $titulares = Afiliado::where('estado_afiliacion', 'OK')->get();
        $parentescoConyuge = Catalogo::where('grupo', 'parentesco')->where('codigo', 'CONYUGE')->first()->id;
        $parentescoHijo = Catalogo::where('grupo', 'parentesco')->where('codigo', 'HIJO')->first()->id;
        $parentescoPadre = Catalogo::where('grupo', 'parentesco')->where('codigo', 'PADRE')->first()->id;
        $parentescoOtros = Catalogo::where('grupo', 'parentesco')->where('codigo', 'OTROS')->first()->id;

        $depCount = 0;
        foreach ($titulares as $idx => $titular) {
            if ($depCount >= 80) break;

            // Cada titular con ID par tiene Cónyuge
            if ($titular->id % 2 === 0) {
                $sexoDep = ($titular->sexo === 'M') ? 'F' : 'M';
                $nombreDep = ($sexoDep === 'F') ? $nombresF[($idx * 3) % count($nombresF)] : $nombresM[($idx * 3) % count($nombresM)];
                $cedulaDep = '001' . str_pad($titular->id * 54321 + 50000, 7, '0', STR_PAD_LEFT) . '1';

                Dependiente::create([
                    'titular_id' => $titular->id,
                    'tipo_identificacion_id' => $tipoIdCed,
                    'cedula' => $cedulaDep,
                    'nss' => '2' . str_pad($titular->id * 222 + 10000, 8, '0', STR_PAD_LEFT),
                    'nui' => '3' . str_pad($titular->id * 333 + 10000, 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombreDep,
                    'apellidos' => $titular->primer_apellido . ' ' . $apellidos[($idx) % count($apellidos)],
                    'fecha_nacimiento' => $titular->fecha_nacimiento->addYears(abs($titular->edad - 30) % 5)->subMonths($idx % 12)->toDateString(),
                    'sexo' => $sexoDep,
                    'parentesco_id' => $parentescoConyuge,
                    'tipo_dependiente' => 'Directo',
                    'estudiante' => false,
                    'discapacitado' => false,
                    'nacionalidad' => 'Dominicana',
                    'requiere_documento' => true,
                    'estado_afiliacion' => 'OK',
                    'motivo_estado' => 'Dependiente aprobado por Unipago.'
                ]);
                $depCount++;
            }

            // Hijos (1 o 2 por titular)
            $cantHijos = ($titular->id % 3 === 0) ? 2 : 1;
            for ($h = 1; $h <= $cantHijos; $h++) {
                if ($depCount >= 80) break;
                
                $sexoDep = ($h % 2 === 0) ? 'F' : 'M';
                $nombreDep = ($sexoDep === 'F') ? $nombresF[($idx + $h) % count($nombresF)] : $nombresM[($idx + $h) % count($nombresM)];
                
                // Algunos hijos mayores de edad para probar las reglas de validación en el demo
                $edadHijo = ($idx % 7 === 0) ? (20 + $h) : (2 + ($idx % 15));
                $esEstudiante = ($edadHijo > 18 && $idx % 2 === 0);
                
                $estadoDep = 'OK';
                $motivoDep = 'Dependiente aprobado por Unipago.';
                if ($edadHijo > 21 && !$esEstudiante) {
                    $estadoDep = 'RE';
                    $motivoDep = 'Hijo mayor de 21 años que no es estudiante ni discapacitado.';
                }

                Dependiente::create([
                    'titular_id' => $titular->id,
                    'tipo_identificacion_id' => ($edadHijo < 10) ? $parentescoHijo : $tipoIdCed, // Acta de nacimiento o Cédula
                    'cedula' => ($edadHijo >= 16) ? '001' . str_pad($titular->id * 4444 + $h * 1000, 7, '0', STR_PAD_LEFT) . '1' : null,
                    'nss' => '2' . str_pad($titular->id * 555 + $h * 10000, 8, '0', STR_PAD_LEFT),
                    'nui' => '3' . str_pad($titular->id * 666 + $h * 10000, 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombreDep,
                    'apellidos' => $titular->primer_apellido . ' ' . $titular->segundo_apellido,
                    'fecha_nacimiento' => now()->subYears($edadHijo)->subMonths($idx % 12)->toDateString(),
                    'sexo' => $sexoDep,
                    'parentesco_id' => $parentescoHijo,
                    'tipo_dependiente' => 'Directo',
                    'estudiante' => $esEstudiante,
                    'discapacitado' => ($idx % 15 === 0), // Algunos discapacitados
                    'nacionalidad' => 'Dominicana',
                    'requiere_documento' => true,
                    'estado_afiliacion' => $estadoDep,
                    'motivo_estado' => $motivoDep
                ]);
                $depCount++;
            }
        }
    }

    private function seedLotesAndNovedades(): void
    {
        $userId = 1; // Administrador ARS
        
        // 10 lotes históricos
        for ($i = 1; $i <= 10; $i++) {
            $tipo = ($i % 3 === 0) ? 'novedades' : (($i % 2 === 0) ? 'afiliacion_dependientes' : 'afiliacion_titulares');
            $estado = ($i === 10) ? 'VE' : (($i % 4 === 0) ? 'PE' : 'EV');
            
            $fecha = now()->subDays(11 - $i);
            $numLote = 'LOTE-' . $fecha->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);

            $lote = Lote::create([
                'numero_lote' => $numLote,
                'tipo_lote' => $tipo,
                'estado_lote' => $estado,
                'total_registros' => 5,
                'registros_ok' => ($estado === 'EV') ? 5 : (($estado === 'PE') ? 4 : 0),
                'registros_re' => ($estado === 'PE') ? 1 : 0,
                'creado_por' => $userId,
                'fecha_creacion' => $fecha,
                'fecha_procesamiento' => ($estado !== 'VE') ? $fecha->addMinutes(30) : null
            ]);

            // Seeding LoteDetalles para los lotes
            if ($tipo === 'afiliacion_titulares') {
                $afiliados = Afiliado::orderBy('id', 'asc')->skip(($i - 1) * 3)->take(5)->get();
                foreach ($afiliados as $idx => $afil) {
                    LoteDetalle::create([
                        'lote_id' => $lote->id,
                        'entidad_type' => 'titular',
                        'entidad_id' => $afil->id,
                        'estado' => ($estado === 'EV') ? 'OK' : (($estado === 'PE' && $idx === 4) ? 'PE64' : 'PE'),
                        'motivo_rechazo' => ($estado === 'PE' && $idx === 4) ? 'Error de NSS o datos del empleador.' : null
                    ]);
                }
            } else if ($tipo === 'afiliacion_dependientes') {
                $deps = Dependiente::orderBy('id', 'asc')->skip(($i - 1) * 3)->take(5)->get();
                foreach ($deps as $idx => $dep) {
                    LoteDetalle::create([
                        'lote_id' => $lote->id,
                        'entidad_type' => 'dependiente',
                        'entidad_id' => $dep->id,
                        'estado' => ($estado === 'EV') ? 'OK' : (($estado === 'PE' && $idx === 4) ? 'PE10036' : 'PE'),
                        'motivo_rechazo' => ($estado === 'PE' && $idx === 4) ? 'Parentesco no válido para el núcleo familiar.' : null
                    ]);
                }
            }
        }

        // 20 novedades
        $afiliados = Afiliado::where('estado_afiliacion', 'OK')->take(10)->get();
        $tipoNovedadDato = Catalogo::where('grupo', 'tipo_novedad')->where('codigo', 'DATO')->first()->id;
        $tipoNovedadUbicacion = Catalogo::where('grupo', 'tipo_novedad')->where('codigo', 'UBICACION')->first()->id;

        foreach ($afiliados as $idx => $afil) {
            // Novedad 1: Datos
            Novedad::create([
                'afiliado_type' => 'titular',
                'afiliado_id' => $afil->id,
                'tipo_novedad_id' => $tipoNovedadDato,
                'campos_modificados' => ['telefono' => '809-555-' . str_pad($idx * 9, 4, '0', STR_PAD_LEFT)],
                'estado' => ($idx % 3 == 0) ? 'Pendiente' : 'OK',
                'motivo_estado' => ($idx % 3 == 0) ? 'Pendiente de lote Unipago' : 'Novedad aplicada y procesada exitosamente en Unipago.',
                'lote_id' => ($idx % 3 != 0) ? 3 : null,
                'creado_por' => $userId,
                'fecha_novedad' => now()->subDays($idx)
            ]);

            // Novedad 2: Ubicacion
            Novedad::create([
                'afiliado_type' => 'titular',
                'afiliado_id' => $afil->id,
                'tipo_novedad_id' => $tipoNovedadUbicacion,
                'campos_modificados' => ['provincia' => 'Santiago', 'municipio' => 'Santiago de los Caballeros'],
                'estado' => 'OK',
                'motivo_estado' => 'Novedad aplicada y procesada exitosamente en Unipago.',
                'lote_id' => 6,
                'creado_por' => $userId,
                'fecha_novedad' => now()->subDays($idx + 5)
            ]);
        }

        // Registrar logs de bitácora iniciales
        Bitacora::registrar('Seguridad', 'Inicio de sesión del usuario administrador.');
        Bitacora::registrar('Afiliados', 'Carga inicial de 50 afiliados titulares demo.');
        Bitacora::registrar('Afiliados', 'Carga inicial de 80 afiliados dependientes demo.');
        Bitacora::registrar('PSS', 'Configuración de 10 PSS con sus contratos y tarifarios base.');
    }

    private function seedAutorizaciones(): void
    {
        $afiliados = Afiliado::where('estado_afiliacion', 'OK')->take(20)->get();
        $dependientes = Dependiente::where('estado_afiliacion', 'OK')->take(20)->get();
        $pss = Pss::whereHas('contratos')->where('estado', 'Activa')->get(); // PSS with active contracts
        $servicios = ServicioMedico::all();
        
        $nombresM = ['Luis', 'Carlos', 'José', 'Juan', 'Miguel', 'Manuel', 'Ramón', 'Francisco', 'Rafael', 'Pedro', 'David', 'Jorge', 'Julio', 'Ángel', 'Roberto', 'Eduardo', 'Héctor', 'Mario', 'Rubén', 'Alejandro', 'Daniel', 'Fernando', 'Ricardo', 'Alberto', 'Raúl'];
        $apellidos = ['Martínez', 'Rodríguez', 'Gómez', 'Pérez', 'Sánchez', 'Díaz', 'Fernández', 'Álvarez', 'Cruz', 'Ramírez', 'Flores', 'Vázquez', 'Guzmán', 'Ortiz', 'Castillo', 'Reyes', 'Ramos', 'Espinal', 'Vargas', 'Jiménez', 'Mejía', 'Castro', 'Arias', 'Santos', 'Brito'];

        $estados = ['Aprobada', 'Rechazada', 'Auditoría', 'Pendiente Documento'];
        $diagnosticos = [
            'I10 - HTA Esencial (Primaria)',
            'E11 - Diabetes Mellitus No Insulinodependiente',
            'K80 - Colelitiasis',
            'C50 - Tumor Maligno de la Mama',
            'N18 - Insuficiencia Renal Crónica',
            'M54 - Dorsalgia',
            'Z00 - Examen Médico General',
            'R51 - Cefalea',
            'K35 - Apendicitis Aguda',
            'O82 - Parto por Cesárea'
        ];

        $totalAutorizaciones = 100;
        $created = 0;

        for ($day = 30; $day >= 0; $day--) {
            if ($created >= $totalAutorizaciones) break;

            // Cantidad de autorizaciones por día (de 2 a 5)
            $cantDia = ($day % 3) + 2;

            for ($a = 1; $a <= $cantDia; $a++) {
                if ($created >= $totalAutorizaciones) break;

                $fecha = now()->subDays($day)->subHours($a * 2)->subMinutes($a * 5);
                
                // Determinar afiliado
                $esTitular = ($a % 2 === 0);
                $afiliado = $esTitular ? $afiliados[$created % $afiliados->count()] : $dependientes[$created % $dependientes->count()];
                $afiliadoType = $esTitular ? 'titular' : 'dependiente';

                // Determinar PSS y servicio
                $pssSelected = $pss[$created % $pss->count()];
                $servicio = $servicios[$created % $servicios->count()];
                
                // Obtener tarifa
                $contrato = ContratoPss::where('pss_id', $pssSelected->id)->where('estado', 'Activo')->first();
                $tarifa = TarifaPss::where('contrato_pss_id', $contrato->id)->where('servicio_medico_id', $servicio->id)->first();
                
                $montoTarifa = $tarifa ? $tarifa->monto_tarifa : 1500.00;
                // Simular monto solicitado
                $desviacion = ($created % 5 === 0) ? 1.2 : 1.0; // 20% de las veces excede la tarifa
                $montoSolicitado = $montoTarifa * $desviacion;

                // Determinar estado de la autorización
                $prioridad = 'Media';
                $motivo = '';
                
                if ($servicio->es_alto_costo) {
                    $estado = 'Auditoría';
                    $motivo = 'Servicio catalogado como de Alto Costo. Requiere auditoría médica especializada.';
                    $prioridad = 'Alta';
                } elseif ($desviacion > 1.0) {
                    $estado = 'Auditoría';
                    $motivo = 'Monto solicitado ($' . number_format($montoSolicitado, 2) . ') supera la tarifa contratada ($' . number_format($montoTarifa, 2) . ').';
                } elseif ($servicio->requiere_documento && ($created % 4 === 0)) {
                    $estado = 'Pendiente Documento';
                    $motivo = 'El servicio solicitado requiere adjuntar documento de soporte clínico (Receta/Indicación).';
                } elseif ($created % 10 === 0) {
                    $estado = 'Rechazada';
                    $motivo = 'Solicitud denegada por no cumplir criterios de cobertura del plan básico.';
                } else {
                    $estado = 'Aprobada';
                    $motivo = 'Aprobación automática por motor de reglas. Cobertura del ' . $servicio->cobertura_base . '%.';
                    $prioridad = 'Baja';
                }

                // Si fue aprobada hace días, ya tiene fecha de respuesta y usuario
                $fechaResp = null;
                $usuarioResp = null;
                if ($estado === 'Aprobada' || $estado === 'Rechazada') {
                    $fechaResp = $fecha->copy()->addMinutes(10 + ($created % 50));
                    $usuarioResp = ($estado === 'Aprobada') ? 5 : 4; // Usuario de autorizaciones o Auditor Médico
                }

                $autorizacion = Autorizacion::create([
                    'numero_autorizacion' => 'AUT-' . $fecha->format('Ymd') . '-' . str_pad($created + 1, 5, '0', STR_PAD_LEFT),
                    'afiliado_type' => $afiliadoType,
                    'afiliado_id' => $afiliado->id,
                    'pss_id' => $pssSelected->id,
                    'medico_solicitante' => 'Dr. ' . $apellidos[$created % count($apellidos)] . ' ' . $nombresM[($created * 2) % count($nombresM)],
                    'diagnostico' => $diagnosticos[$created % count($diagnosticos)],
                    'servicio_medico_id' => $servicio->id,
                    'procedimiento' => $servicio->descripcion,
                    'monto_solicitado' => $montoSolicitado,
                    'monto_contratado' => ($estado === 'Aprobada') ? $montoTarifa : 0.00,
                    'prioridad' => $prioridad,
                    'estado' => $estado,
                    'motivo_estado' => $motivo,
                    'fecha_solicitud' => $fecha,
                    'fecha_respuesta' => $fechaResp,
                    'usuario_responsable_id' => $usuarioResp
                ]);

                // Agregar detalle
                AutorizacionDetalle::create([
                    'autorizacion_id' => $autorizacion->id,
                    'codigo' => $servicio->codigo,
                    'descripcion' => $servicio->descripcion,
                    'cantidad' => 1,
                    'monto' => $montoSolicitado,
                    'estado' => ($estado === 'Aprobada') ? 'Aprobado' : (($estado === 'Rechazada') ? 'Rechazado' : 'Pendiente')
                ]);

                // Si requiere documento y no está pendiente, crear registro de documento simulado
                if ($servicio->requiere_documento && $estado !== 'Pendiente Documento') {
                    Documento::create([
                        'entidad_type' => 'autorizacion',
                        'entidad_id' => $autorizacion->id,
                        'nombre_archivo' => 'receta_medica_' . $autorizacion->numero_autorizacion . '.pdf',
                        'ruta_archivo' => 'documentos/autorizaciones/receta_medica_' . $autorizacion->numero_autorizacion . '.pdf',
                        'tipo_documento' => 'Soporte Médico',
                        'fecha_carga' => $fecha
                    ]);
                }

                $created++;
            }
        }
    }

    private function seedAulaVirtual(): void
    {
        // 6 cursos
        $cursosData = [
            [
                'title' => 'Inducción al uso de la Plataforma Virtual',
                'description' => 'Aprende a navegar por la plataforma virtual de afiliados, consultar tu carnet digital, verificar tu núcleo familiar y realizar solicitudes de servicios en pocos pasos.',
                'category' => 'Afiliados',
                'hours' => 2,
                'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Cómo solicitar autorizaciones médicas',
                'description' => 'Guía paso a paso para la solicitud y seguimiento de autorizaciones para cirugías, estudios especializados y medicamentos de alto costo.',
                'category' => 'Autorizaciones',
                'hours' => 3,
                'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Buenas prácticas para Prestadores (PSS)',
                'description' => 'Capacitación orientada a clínicas y centros de salud sobre el uso del portal de autorizaciones, copagos, tarifas contratadas y carga de soporte clínico.',
                'category' => 'Prestadores',
                'hours' => 4,
                'image' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Promoción y prevención en salud integral',
                'description' => 'Conoce nuestros programas preventivos de salud cardiovascular, nutrición y control de enfermedades crónicas para afiliados.',
                'category' => 'Salud',
                'hours' => 5,
                'image' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Atención al afiliado y calidad de servicio',
                'description' => 'Módulo de formación interna sobre los estándares de atención, empatía, tiempos de respuesta y canales de comunicación institucionales.',
                'category' => 'Interno',
                'hours' => 3,
                'image' => 'https://images.unsplash.com/photo-1521791136368-1a46827d53b6?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Gestión de reclamaciones y reembolsos',
                'description' => 'Detalle de los requisitos legales, plazos y documentos necesarios para el trámite exitoso de reembolsos de gastos médicos fuera de red.',
                'category' => 'Operaciones',
                'hours' => 2,
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&auto=format&fit=crop&q=60'
            ]
        ];

        $cursos = [];
        foreach ($cursosData as $c) {
            $cursos[] = \App\Models\VirtualCourse::create($c);
        }

        // Lecciones y materiales
        foreach ($cursos as $course) {
            for ($l = 1; $l <= 5; $l++) {
                $lesson = \App\Models\VirtualLesson::create([
                    'course_id' => $course->id,
                    'title' => "Tema $l: " . $this->getLessonTitle($course->title, $l),
                    'duration_minutes' => 10 + ($l * 5),
                    'content' => "Contenido detallado y material explicativo para la lección número $l del curso: {$course->title}. Aquí se simula el material de estudio, diagramas interactivos y videos de capacitación.",
                    'order_index' => $l
                ]);

                // Agregar material descargable a la primera lección
                if ($l === 1) {
                    \App\Models\VirtualMaterial::create([
                        'lesson_id' => $lesson->id,
                        'name' => "Guia_Completa_Curso_{$course->id}.pdf",
                        'file_path' => "/documentos/aula-virtual/guia_curso_{$course->id}.pdf",
                        'file_type' => 'pdf',
                        'size_bytes' => 1024 * 1024 * 1.5 // 1.5 MB
                    ]);
                }
            }

            // Examen/Evaluación por curso
            $preguntas = [
                [
                    'pregunta' => '¿Cuál es el objetivo principal de este curso?',
                    'opciones' => ['Aprender a realizar trámites', 'Conocer la historia de la medicina', 'Ninguna de las anteriores'],
                    'correcta' => 0
                ],
                [
                    'pregunta' => '¿De quién es responsabilidad cumplir las directrices explicadas?',
                    'opciones' => ['Del paciente', 'De la ARS y el personal involucrado', 'De la JCE'],
                    'correcta' => 1
                ],
                [
                    'pregunta' => '¿Qué documento sirve de soporte principal según lo aprendido?',
                    'opciones' => ['Receta/Indicación Médica', 'Cédula de Identidad', 'Ambas'],
                    'correcta' => 2
                ]
            ];

            \App\Models\VirtualAssessment::create([
                'course_id' => $course->id,
                'title' => "Evaluación Final: {$course->title}",
                'questions_json' => $preguntas,
                'min_score' => 70
            ]);
        }

        // Obtener los estudiantes creados
        $students = User::where('role', 'Estudiante')->get();

        foreach ($students as $sIndex => $student) {
            // Inscribir en los 2 primeros cursos
            for ($cIndex = 0; $cIndex < 2; $cIndex++) {
                $course = $cursos[$cIndex];
                
                // Estudiante 1 completa el primer curso y obtiene certificado
                if ($sIndex === 0 && $cIndex === 0) {
                    $enrollment = \App\Models\VirtualEnrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'status' => 'Completado',
                        'enrolled_at' => now()->subDays(5),
                        'completed_at' => now()->subDays(1)
                    ]);

                    // Completar todas las lecciones en el progreso
                    foreach ($course->lessons as $lesson) {
                        \App\Models\VirtualProgress::create([
                            'enrollment_id' => $enrollment->id,
                            'lesson_id' => $lesson->id,
                            'completed' => true,
                            'completed_at' => now()->subDays(3)
                        ]);
                    }

                    // Emitir certificado
                    \App\Models\VirtualCertificate::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'certificate_code' => 'CERT-' . strtoupper(uniqid()),
                        'issued_at' => now()->subDays(1)
                    ]);

                } else {
                    // Otros están "En Curso" con progreso parcial (lecciones 1 y 2 completadas)
                    $enrollment = \App\Models\VirtualEnrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'status' => 'En Curso',
                        'enrolled_at' => now()->subDays(2)
                    ]);

                    $lessons = $course->lessons->take(2);
                    foreach ($lessons as $lesson) {
                        \App\Models\VirtualProgress::create([
                            'enrollment_id' => $enrollment->id,
                            'lesson_id' => $lesson->id,
                            'completed' => true,
                            'completed_at' => now()->subDays(1)
                        ]);
                    }
                }
            }
        }
    }

    private function getLessonTitle(string $courseTitle, int $lessonNum): string
    {
        $titles = [
            'Inducción al uso de la Plataforma Virtual' => [
                1 => 'Introducción y bienvenida a la plataforma',
                2 => 'Consultando mi carnet digital de afiliado',
                3 => 'Cómo gestionar y agregar dependientes',
                4 => 'Bandeja de historial de solicitudes',
                5 => 'Preguntas frecuentes y soporte técnico'
            ],
            'Cómo solicitar autorizaciones médicas' => [
                1 => 'Conceptos básicos de cobertura y copagos',
                2 => 'Requisitos para autorizaciones de cirugías',
                3 => 'El flujo de medicamentos de alto costo',
                4 => 'Carga de recetas e indicaciones clínicas',
                5 => 'Interpretación de estados de respuesta'
            ]
        ];

        return $titles[$courseTitle][$lessonNum] ?? "Introducción general parte $lessonNum";
    }

    private function seedMultiPssDemoData(): void
    {
        $clinica = Pss::where('rnc', '101002034')->first();
        $farmacia = Pss::where('rnc', '303004056')->first();
        $lab = Pss::where('rnc', '202003045')->first();
        
        // Create demo users first
        $centroUser = User::create([
            'name' => 'Centro Médico Demo',
            'email' => 'centro@demo.com',
            'password' => Hash::make('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $clinica ? $clinica->id : 1
        ]);

        $farmaciaUser = User::create([
            'name' => 'Farmacia GBC Demo',
            'email' => 'farmacia@demo.com',
            'password' => Hash::make('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $farmacia ? $farmacia->id : 1
        ]);

        $labUser = User::create([
            'name' => 'Laboratorio Amadita Demo',
            'email' => 'laboratorio@demo.com',
            'password' => Hash::make('password'),
            'role' => 'Usuario PSS',
            'pss_id' => $lab ? $lab->id : 1
        ]);

        $defaultPssUser = User::where('email', 'pss@ars.com')->first();
        $afiliado = Afiliado::first() ?? Dependiente::first();

        if (!$clinica || !$farmacia || !$lab || !$afiliado || !$defaultPssUser) {
            return;
        }

        // Link default pss@ars.com to all three profiles for demo switching
        \App\Models\PssUser::create(['user_id' => $defaultPssUser->id, 'pss_id' => $clinica->id, 'access_type' => 'medical_center', 'role' => 'medical_center_operator', 'is_default' => true]);
        \App\Models\PssUser::create(['user_id' => $defaultPssUser->id, 'pss_id' => $farmacia->id, 'access_type' => 'pharmacy', 'role' => 'pharmacy_operator']);
        \App\Models\PssUser::create(['user_id' => $defaultPssUser->id, 'pss_id' => $lab->id, 'access_type' => 'laboratory', 'role' => 'lab_operator']);

        // Link demo users to their respective profiles
        \App\Models\PssUser::create(['user_id' => $centroUser->id, 'pss_id' => $clinica->id, 'access_type' => 'medical_center', 'role' => 'medical_center_operator', 'is_default' => true]);
        \App\Models\PssUser::create(['user_id' => $farmaciaUser->id, 'pss_id' => $farmacia->id, 'access_type' => 'pharmacy', 'role' => 'pharmacy_operator', 'is_default' => true]);
        \App\Models\PssUser::create(['user_id' => $labUser->id, 'pss_id' => $lab->id, 'access_type' => 'laboratory', 'role' => 'lab_operator', 'is_default' => true]);

        $diagnosticos = [
            'I10 - HTA Esencial (Primaria)',
            'E11 - Diabetes Mellitus No Insulinodependiente',
            'K80 - Colelitiasis',
            'C50 - Tumor Maligno de la Mama',
            'N18 - Insuficiencia Renal Crónica',
            'M54 - Dorsalgia',
            'Z00 - Examen Médico General',
            'R51 - Cefalea',
            'K35 - Apendicitis Aguda',
            'O82 - Parto por Cesárea'
        ];

        // 1. Seed 50 pharmacy dispensations & prescriptions
        for ($i = 1; $i <= 50; $i++) {
            $prescription = \App\Models\PharmacyPrescription::create([
                'pss_id' => $farmacia->id,
                'afiliado_id' => $afiliado->id,
                'prescription_number' => 'RX-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'doctor_name' => 'Dr. Prescriptor GBC ' . $i,
                'doctor_exequatur' => '100' . $i . '-20',
                'specialty' => 'Médico General',
                'diagnosis' => $diagnosticos[$i % count($diagnosticos)],
                'prescription_date' => now()->subDays($i),
                'status' => 'Validada',
                'created_by' => $defaultPssUser->id,
            ]);

            $disp = \App\Models\PharmacyDispensation::create([
                'prescription_id' => $prescription->id,
                'pss_id' => $farmacia->id,
                'afiliado_id' => $afiliado->id,
                'dispensation_number' => 'DISP-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'dispensed_at' => now()->subDays($i),
                'total_amount' => 1000.00 + ($i * 50),
                'ars_amount' => (1000.00 + ($i * 50)) * 0.70,
                'affiliate_copay_amount' => (1000.00 + ($i * 50)) * 0.30,
                'non_covered_amount' => 0.00,
                'status' => 'Dispensada',
                'created_by' => $defaultPssUser->id,
            ]);

            \App\Models\PharmacyDispensationItem::create([
                'dispensation_id' => $disp->id,
                'medicine_code' => 'MED-00' . $i,
                'medicine_name' => 'Medicamento Demo ' . $i,
                'quantity' => 1,
                'unit_price' => $disp->total_amount,
                'total_price' => $disp->total_amount,
                'ars_covered_amount' => $disp->ars_amount,
                'copay_amount' => $disp->affiliate_copay_amount,
                'status' => 'Activo',
            ]);
        }

        // 2. Seed 50 lab orders
        for ($i = 1; $i <= 50; $i++) {
            $order = \App\Models\LabOrder::create([
                'pss_id' => $lab->id,
                'afiliado_id' => $afiliado->id,
                'order_number' => 'ORD-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'doctor_name' => 'Dr. Solicitante Amadita ' . $i,
                'doctor_exequatur' => '200' . $i . '-30',
                'specialty' => 'Médico Internista',
                'diagnosis' => $diagnosticos[$i % count($diagnosticos)],
                'order_date' => now()->subDays($i),
                'status' => 'Realizada',
                'created_by' => $defaultPssUser->id,
            ]);

            \App\Models\LabOrderItem::create([
                'lab_order_id' => $order->id,
                'simon_code_snapshot' => 'LAB-00' . $i,
                'test_name' => 'Examen Lab Demo ' . $i,
                'contracted_amount' => 1500.00,
                'requested_amount' => 1500.00,
                'authorized_amount' => 1200.00,
                'status' => 'Realizada',
            ]);
        }

        // 3. Seed 30 claims of each type (medical_center, pharmacy, laboratory)
        // Pharmacy Claims
        for ($i = 1; $i <= 30; $i++) {
            $disp = \App\Models\PharmacyDispensation::where('pss_id', $farmacia->id)->skip($i-1)->first();
            $claim = AuthorizationClaim::create([
                'claim_number' => 'REC-RX-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'authorization_id' => 1,
                'pss_id' => $farmacia->id,
                'afiliado_id' => $afiliado->id,
                'invoice_number' => 'INV-RX-' . $i,
                'ncf' => 'B01000000' . $i,
                'service_date' => now()->subDays($i + 5),
                'received_at' => now()->subDays($i),
                'claimed_amount' => $disp ? $disp->total_amount : 1000,
                'authorized_amount' => $disp ? $disp->ars_amount : 700,
                'approved_amount' => $disp ? $disp->ars_amount : 700,
                'status' => $i <= 20 ? 'Pagada' : ($i <= 25 ? 'Glosada' : 'Reclamación recibida'),
                'claim_origin_type' => 'pharmacy',
                'submitted_by' => $defaultPssUser->id,
            ]);

            $payable = AccountPayable::create([
                'payable_number' => 'CXP-RX-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'claim_id' => $claim->id,
                'authorization_id' => $claim->authorization_id,
                'pss_id' => $farmacia->id,
                'amount' => $claim->approved_amount,
                'net_amount' => $claim->approved_amount,
                'status' => $claim->status === 'Pagada' ? 'Pagada' : 'Generada',
            ]);

            if ($claim->status === 'Glosada') {
                \App\Models\ClaimGlosa::create([
                    'claim_id' => $claim->id,
                    'glosa_code' => 'GLO-RX-' . $i,
                    'glosa_type' => 'Técnica',
                    'objected_service' => 'Medicamentos',
                    'original_amount' => $claim->claimed_amount,
                    'objected_amount' => $claim->claimed_amount * 0.10,
                    'status' => 'Pendiente',
                    'objection_reason' => 'Diferencia en tarifas contratadas / Medicamento no autorizado.',
                ]);
            }

            if ($claim->status === 'Pagada') {
                $batch = \App\Models\PaymentBatch::firstOrCreate(
                    ['batch_number' => 'LOTE-PAY-RX'],
                    ['status' => 'Pagado', 'total_amount' => 0, 'paid_at' => now()]
                );
                $batch->increment('total_amount', $payable->amount);
                $batch->increment('total_items');
                
                \App\Models\PaymentBatchItem::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'amount' => $payable->amount,
                ]);

                \App\Models\PaymentReconciliation::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'pss_id' => $farmacia->id,
                    'expected_amount' => $payable->amount,
                    'paid_amount' => $payable->amount,
                    'status' => 'Conciliado',
                ]);
            }
        }

        // Laboratory Claims
        for ($i = 1; $i <= 30; $i++) {
            $claim = AuthorizationClaim::create([
                'claim_number' => 'REC-LAB-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'authorization_id' => 1,
                'pss_id' => $lab->id,
                'afiliado_id' => $afiliado->id,
                'invoice_number' => 'INV-LAB-' . $i,
                'ncf' => 'B01000000' . ($i + 50),
                'service_date' => now()->subDays($i + 5),
                'received_at' => now()->subDays($i),
                'claimed_amount' => 1500.00,
                'authorized_amount' => 1200.00,
                'approved_amount' => 1200.00,
                'status' => $i <= 20 ? 'Pagada' : ($i <= 25 ? 'Glosada' : 'Reclamación recibida'),
                'claim_origin_type' => 'laboratory',
                'submitted_by' => $defaultPssUser->id,
            ]);

            $payable = AccountPayable::create([
                'payable_number' => 'CXP-LAB-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'claim_id' => $claim->id,
                'authorization_id' => $claim->authorization_id,
                'pss_id' => $lab->id,
                'amount' => $claim->approved_amount,
                'net_amount' => $claim->approved_amount,
                'status' => $claim->status === 'Pagada' ? 'Pagada' : 'Generada',
            ]);

            if ($claim->status === 'Glosada') {
                \App\Models\ClaimGlosa::create([
                    'claim_id' => $claim->id,
                    'glosa_code' => 'GLO-LAB-' . $i,
                    'glosa_type' => 'Médica',
                    'objected_service' => 'Pruebas de Laboratorio',
                    'original_amount' => $claim->claimed_amount,
                    'objected_amount' => $claim->claimed_amount * 0.15,
                    'status' => 'Pendiente',
                    'objection_reason' => 'Soporte de resultados clínicos no legibles o ausentes.',
                ]);
            }

            if ($claim->status === 'Pagada') {
                $batch = \App\Models\PaymentBatch::firstOrCreate(
                    ['batch_number' => 'LOTE-PAY-LAB'],
                    ['status' => 'Pagado', 'total_amount' => 0, 'paid_at' => now()]
                );
                $batch->increment('total_amount', $payable->amount);
                $batch->increment('total_items');
                
                \App\Models\PaymentBatchItem::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'amount' => $payable->amount,
                ]);

                \App\Models\PaymentReconciliation::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'pss_id' => $lab->id,
                    'expected_amount' => $payable->amount,
                    'paid_amount' => $payable->amount,
                    'status' => 'Conciliado',
                ]);
            }
        }

        // Clinic Claims
        for ($i = 1; $i <= 30; $i++) {
            $claim = AuthorizationClaim::create([
                'claim_number' => 'REC-CLI-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'authorization_id' => 1,
                'pss_id' => $clinica->id,
                'afiliado_id' => $afiliado->id,
                'invoice_number' => 'INV-CLI-' . $i,
                'ncf' => 'B01000000' . ($i + 100),
                'service_date' => now()->subDays($i + 5),
                'received_at' => now()->subDays($i),
                'claimed_amount' => 2500.00,
                'authorized_amount' => 2000.00,
                'approved_amount' => 2000.00,
                'status' => $i <= 20 ? 'Pagada' : ($i <= 25 ? 'Glosada' : 'Reclamación recibida'),
                'claim_origin_type' => 'medical_center',
                'submitted_by' => $defaultPssUser->id,
            ]);

            $payable = AccountPayable::create([
                'payable_number' => 'CXP-CLI-' . now()->year . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'claim_id' => $claim->id,
                'authorization_id' => $claim->authorization_id,
                'pss_id' => $clinica->id,
                'amount' => $claim->approved_amount,
                'net_amount' => $claim->approved_amount,
                'status' => $claim->status === 'Pagada' ? 'Pagada' : 'Generada',
            ]);

            if ($claim->status === 'Glosada') {
                \App\Models\ClaimGlosa::create([
                    'claim_id' => $claim->id,
                    'glosa_code' => 'GLO-CLI-' . $i,
                    'glosa_type' => 'Médica',
                    'objected_service' => 'Servicios Médicos',
                    'original_amount' => $claim->claimed_amount,
                    'objected_amount' => $claim->claimed_amount * 0.20,
                    'status' => 'Pendiente',
                    'objection_reason' => 'Falta de firma de afiliado en constancia de servicio prestado.',
                ]);
            }

            if ($claim->status === 'Pagada') {
                $batch = \App\Models\PaymentBatch::firstOrCreate(
                    ['batch_number' => 'LOTE-PAY-CLI'],
                    ['status' => 'Pagado', 'total_amount' => 0, 'paid_at' => now()]
                );
                $batch->increment('total_amount', $payable->amount);
                $batch->increment('total_items');
                
                \App\Models\PaymentBatchItem::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'amount' => $payable->amount,
                ]);

                \App\Models\PaymentReconciliation::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'pss_id' => $clinica->id,
                    'expected_amount' => $payable->amount,
                    'paid_amount' => $payable->amount,
                    'status' => 'Conciliado',
                ]);
            }
        }
    }
}
