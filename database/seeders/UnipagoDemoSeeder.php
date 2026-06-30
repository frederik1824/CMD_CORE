<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\AffiliationBatch;
use App\Models\AffiliationBatchDetail;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\DispersionCutDetail;
use App\Models\UnipagoMockRequest;
use App\Models\UnipagoMockNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UnipagoDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tablas para evitar duplicados
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        DB::table('unipago_mock_notifications')->truncate();
        DB::table('dispersion_cut_details')->truncate();
        DB::table('dispersion_cuts')->truncate();
        DB::table('capitation_notifications')->truncate();
        DB::table('affiliation_batch_details')->truncate();
        DB::table('affiliation_batches')->truncate();
        DB::table('unipago_mock_requests')->truncate();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $faker = \Faker\Factory::create('es_ES');

        // Obtener IDs de catálogos necesarios o usar valores por defecto
        $tipoIdCed = \App\Models\Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CEDULA')->first()->id ?? 1;
        $parentescoConyuge = \App\Models\Catalogo::where('grupo', 'parentesco')->where('codigo', 'CONYUGE')->first()->id ?? 2;
        $parentescoHijo = \App\Models\Catalogo::where('grupo', 'parentesco')->where('codigo', 'HIJO')->first()->id ?? 3;

        echo "Generando 200 titulares y 300 dependientes...\n";
        // Asegurarnos de que tenemos suficientes titulares y dependientes
        $titularesCount = Afiliado::count();
        if ($titularesCount < 200) {
            $needed = 200 - $titularesCount;
            for ($i = 0; $i < $needed; $i++) {
                Afiliado::create([
                    'tipo_identificacion_id' => $tipoIdCed,
                    'nombres' => strtoupper($faker->firstName),
                    'primer_apellido' => strtoupper($faker->lastName),
                    'segundo_apellido' => strtoupper($faker->lastName),
                    'cedula' => '225-' . str_pad(rand(100000, 999999), 7, '0', STR_PAD_LEFT) . '-' . rand(0, 9),
                    'nss' => 'NSS-' . rand(100000000, 999999999),
                    'nui' => '3' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(20, 60))->toDateString(),
                    'sexo' => rand(0, 1) ? 'M' : 'F',
                    'regimen_actual' => 'Contributivo',
                    'estado_afiliacion' => 'OK',
                    'numero_contrato' => 'POL-2026-' . str_pad($i + $titularesCount + 1, 6, '0', STR_PAD_LEFT)
                ]);
            }
        }

        $titulares = Afiliado::select('id', 'primer_apellido')->inRandomOrder()->limit(500)->get();
        $dependientesCount = Dependiente::count();
        if ($dependientesCount < 300) {
            $needed = 300 - $dependientesCount;
            for ($i = 0; $i < $needed; $i++) {
                $titular = $titulares->random();
                Dependiente::create([
                    'titular_id' => $titular->id,
                    'tipo_identificacion_id' => $tipoIdCed,
                    'nombres' => strtoupper($faker->firstName),
                    'apellidos' => $titular->primer_apellido . ' ' . $faker->lastName,
                    'cedula' => '225-' . str_pad(rand(100000, 999999), 7, '0', STR_PAD_LEFT) . '-' . rand(0, 9),
                    'nss' => 'NSS-DEP-' . rand(10000000, 99999999),
                    'parentesco_id' => rand(0, 1) ? $parentescoHijo : $parentescoConyuge,
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(1, 18))->toDateString(),
                    'sexo' => rand(0, 1) ? 'M' : 'F',
                    'tipo_dependiente' => 'Directo',
                    'nacionalidad' => 'Dominicana',
                    'estudiante' => false,
                    'discapacitado' => false,
                    'requiere_documento' => true,
                    'estado_afiliacion' => 'OK',
                ]);
            }
        }

        $allTitulares = Afiliado::pluck('id');
        $allDependientes = Dependiente::pluck('id');

        echo "Generando 30 lotes de afiliación...\n";
        // 30 lotes de afiliación:
        // 20 procesados correctamente (PC)
        // 5 procesados con errores (PE)
        // 5 rechazados (RE)
        for ($i = 1; $i <= 30; $i++) {
            $status = 'PC';
            if ($i > 20 && $i <= 25) {
                $status = 'PE';
            } elseif ($i > 25) {
                $status = 'RE';
            }

            $type = rand(0, 1) ? 'titulares' : 'dependientes';
            $batchNum = 'LOT-SEEDER-2026-' . str_pad($i, 6, '0', STR_PAD_LEFT);

            $batch = AffiliationBatch::create([
                'batch_number' => $batchNum,
                'batch_type' => $type,
                'unipago_lote_id' => 'LOTE-MOCK-' . rand(10000, 99999),
                'status' => $status,
                'total_records' => 10,
                'total_ok' => $status === 'PC' ? 10 : ($status === 'PE' ? 7 : 0),
                'total_pending' => $status === 'PE' ? 2 : 0,
                'total_rejected' => $status === 'RE' ? 10 : ($status === 'PE' ? 1 : 0),
                'submitted_by' => 1,
                'submitted_at' => Carbon::now()->subDays(31 - $i),
                'processed_at' => Carbon::now()->subDays(31 - $i)->addHours(2),
            ]);

            // Generar detalles para el lote
            for ($j = 1; $j <= 10; $j++) {
                $recStatus = 'OK';
                $code = 'OK';
                $desc = 'Procesado correctamente';

                if ($status === 'PE') {
                    if ($j === 8) {
                        $recStatus = 'PE64';
                        $code = 'PE64';
                        $desc = 'Pendiente verificar aporte TSS';
                    } elseif ($j === 9) {
                        $recStatus = 'PE75';
                        $code = 'PE75';
                        $desc = 'Ciudadano no existe en Maestro';
                    } elseif ($j === 10) {
                        $recStatus = 'RE';
                        $code = 'RE';
                        $desc = 'Rechazo: Ciudadano fallecido';
                    }
                } elseif ($status === 'RE') {
                    $recStatus = 'RE';
                    $code = 'RE';
                    $desc = 'Rechazo estructural';
                }

                AffiliationBatchDetail::create([
                    'affiliation_batch_id' => $batch->id,
                    'afiliado_id' => $type === 'titulares' ? $allTitulares->random() : null,
                    'dependiente_id' => $type === 'dependientes' ? $allDependientes->random() : null,
                    'request_number' => 'REQ-' . rand(1000, 9999),
                    'contract_number' => 'CON-' . rand(1000, 9999),
                    'status' => $recStatus,
                    'reason_code' => $code,
                    'reason_description' => $desc,
                ]);
            }
        }

        echo "Generando notificaciones de solicitudes...\n";
        // Generar 100 solicitudes OK, 50 PE64, 30 PE75 y 20 RE en total
        // Esto se cubre con la distribución de los detalles de los lotes sembrados arriba.

        echo "Generando 300 cápitas y 6 cortes de dispersión...\n";
        // 6 cortes de dispersión (períodos 202601 a 202606)
        // 300 cápitas en total
        // 250 confirmadas (IC)
        // 40 dispersadas (DI)
        // 10 rechazadas (IR)
        $capitaIndex = 1;
        for ($corteNum = 1; $corteNum <= 6; $corteNum++) {
            $period = '20260' . $corteNum;
            $cutNum = 'DISP-SEEDER-' . $period . '-' . str_pad($corteNum, 4, '0', STR_PAD_LEFT);

            // Crear corte de dispersión
            $cut = DispersionCut::create([
                'cut_number' => $cutNum,
                'period' => $period,
                'cut_type' => 'operativo',
                'status' => 'Dispersado',
                'total_affiliates' => 40,
                'total_holders' => 20,
                'total_dependents' => 20,
                'total_capitations' => 40,
                'total_amount' => 40 * 1450.50,
                'generated_at' => Carbon::now()->subMonths(6 - $corteNum),
                'certified_at' => Carbon::now()->subMonths(6 - $corteNum)->addDays(1),
                'dispersed_at' => Carbon::now()->subMonths(6 - $corteNum)->addDays(2),
                'closed_at' => Carbon::now()->subMonths(6 - $corteNum)->addDays(2),
            ]);

            // Generar 40 cápitas dispersadas (DI) por corte
            for ($k = 1; $k <= 40; $k++) {
                $afiliadoId = $allTitulares->random();
                $capitaNum = 'CAP-SEEDER-' . $period . '-' . str_pad($capitaIndex, 6, '0', STR_PAD_LEFT);

                $cn = CapitationNotification::create([
                    'notification_number' => $capitaNum,
                    'afiliado_id' => $afiliadoId,
                    'period' => $period,
                    'capitation_amount' => 1450.50,
                    'individualization_type' => 'Capita Normal',
                    'status' => 'DI', // Dispersada
                    'notified_at' => Carbon::now()->subMonths(6 - $corteNum),
                    'confirmed_at' => Carbon::now()->subMonths(6 - $corteNum)->addDays(1),
                ]);

                // Asignar al corte
                DispersionCutDetail::create([
                    'dispersion_cut_id' => $cut->id,
                    'capitation_notification_id' => $cn->id,
                    'afiliado_id' => $afiliadoId,
                    'amount' => 1450.50,
                    'status' => 'DI'
                ]);

                $capitaIndex++;
            }
        }

        // Generar 210 cápitas adicionales (para completar las 250 confirmadas (IC) y 10 rechazadas (IR))
        // Estas estarán en periodos sueltos y no metidas en cortes
        for ($k = 1; $k <= 50; $k++) {
            $afiliadoId = $allTitulares->random();
            $period = '202606';
            $capitaNum = 'CAP-EXTRA-IC-' . str_pad($k, 6, '0', STR_PAD_LEFT);
            CapitationNotification::create([
                'notification_number' => $capitaNum,
                'afiliado_id' => $afiliadoId,
                'period' => $period,
                'capitation_amount' => 1450.50,
                'individualization_type' => 'Capita Normal',
                'status' => 'IC', // Confirmada pero no dispersada
                'notified_at' => Carbon::now(),
                'confirmed_at' => Carbon::now()->addHours(1),
            ]);
        }

        for ($k = 1; $k <= 10; $k++) {
            $afiliadoId = $allTitulares->random();
            $period = '202606';
            $capitaNum = 'CAP-EXTRA-IR-' . str_pad($k, 6, '0', STR_PAD_LEFT);
            CapitationNotification::create([
                'notification_number' => $capitaNum,
                'afiliado_id' => $afiliadoId,
                'period' => $period,
                'capitation_amount' => 1450.50,
                'individualization_type' => 'Capita Normal',
                'status' => 'IR', // Rechazada
                'notified_at' => Carbon::now(),
                'rejected_at' => Carbon::now()->addHours(1),
                'rejection_reason' => 'Retroactivo no certificado en nómina pública',
            ]);
        }

        echo "Generando logs de peticiones y notificaciones mock...\n";
        // Logs de peticiones API
        for ($k = 1; $k <= 50; $k++) {
            UnipagoMockRequest::create([
                'service_code' => 'CARG-LOTE',
                'service_name' => 'Cargar Lote Afiliaciones',
                'endpoint_mock' => '/api/unipago/lote/cargar',
                'request_payload' => ['batch_id' => $k, 'records' => 10],
                'response_payload' => ['status' => 'success', 'code' => 200],
                'status' => 'Processed',
                'created_by' => 1,
                'processed_at' => Carbon::now()->subMinutes($k * 10),
            ]);
        }

        // Notificaciones internas de Unipago
        for ($k = 1; $k <= 20; $k++) {
            UnipagoMockNotification::create([
                'notification_type' => 'Lote recibido',
                'reference_type' => 'batch',
                'reference_id' => rand(1, 30),
                'title' => 'Lote de Afiliación Procesado',
                'message' => 'Lote consolidado de forma exitosa ante el padrón Unipago.',
                'read_at' => rand(0, 1) ? Carbon::now() : null,
            ]);
        }

        echo "Poblado Unipago Demo finalizado con éxito.\n";
    }
}
