<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegulatoryPeriod;
use App\Models\RegulatorySchema;
use App\Models\RegulatorySchemaRun;
use App\Models\RegulatorySchemaRunDetail;
use App\Models\SimonMockSubmission;
use App\Models\SimonMockSubmissionLog;

class RegulatoryDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear periodos para 2026
        $periodos = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
            $periodos[] = RegulatoryPeriod::create([
                'period_code' => "2026-{$monthStr}",
                'month' => $m,
                'year' => 2026,
                'start_date' => "2026-{$monthStr}-01",
                'end_date' => Carbon::parse("2026-{$monthStr}-01")->endOfMonth()->toDateString(),
                'status' => $m < 6 ? 'cerrado' : ($m === 6 ? 'abierto' : 'abierto')
            ]);
        }

        // Obtener el esquema 0031
        $schema0031 = RegulatorySchema::where('schema_code', '0031')->first();
        if (!$schema0031) return;

        // 2. Crear una corrida histórica Aprobada en el período 2026-04
        $runAprobado = RegulatorySchemaRun::create([
            'run_number' => 'RUN-0031-202604-001',
            'regulatory_schema_id' => $schema0031->id,
            'period_id' => $periodos[3]->id, // 2026-04
            'generated_by' => 1,
            'generated_at' => '2026-05-02 10:15:00',
            'status' => 'aprobado',
            'total_records' => 50,
            'valid_records' => 50,
            'invalid_records' => 0,
            'file_name' => 'ARS_0031_202604_001.txt',
            'file_path' => 'app/regulatory/ARS_0031_202604_001.txt',
            'checksum' => md5('RUN-0031-202604-001')
        ]);

        RegulatorySchemaRunDetail::create([
            'regulatory_schema_run_id' => $runAprobado->id,
            'record_type' => 'E',
            'line_number' => 1,
            'raw_line' => 'EARS-001   003120260420260502',
            'validation_status' => 'valido'
        ]);

        $subAprobado = SimonMockSubmission::create([
            'submission_number' => 'SUB-SIMON-2026-0001',
            'regulatory_schema_run_id' => $runAprobado->id,
            'regulatory_schema_id' => $schema0031->id,
            'period_id' => $periodos[3]->id,
            'submitted_by' => 1,
            'submitted_at' => '2026-05-02 10:20:00',
            'status' => 'aprobado',
            'response_summary' => 'Archivo procesado y validado con éxito. No se encontraron inconsistencias estructurales o de contenido.',
            'approved_at' => '2026-05-03 14:00:00'
        ]);

        SimonMockSubmissionLog::create([
            'simon_mock_submission_id' => $subAprobado->id,
            'event_type' => 'status_change',
            'old_status' => 'recibido',
            'new_status' => 'aprobado',
            'message' => 'El archivo ha sido aprobado formalmente por el supervisor clínico de SISALRIL.'
        ]);

        // 3. Crear una corrida histórica Rechazada en el período 2026-05
        $runRechazado = RegulatorySchemaRun::create([
            'run_number' => 'RUN-0031-202605-001',
            'regulatory_schema_id' => $schema0031->id,
            'period_id' => $periodos[4]->id, // 2026-05
            'generated_by' => 1,
            'generated_at' => '2026-06-03 09:30:00',
            'status' => 'con_errores',
            'total_records' => 45,
            'valid_records' => 40,
            'invalid_records' => 5,
            'file_name' => 'ARS_0031_202605_001.txt',
            'file_path' => 'app/regulatory/ARS_0031_202605_001.txt',
            'checksum' => md5('RUN-0031-202605-001')
        ]);

        $subRechazado = SimonMockSubmission::create([
            'submission_number' => 'SUB-SIMON-2026-0002',
            'regulatory_schema_run_id' => $runRechazado->id,
            'regulatory_schema_id' => $schema0031->id,
            'period_id' => $periodos[4]->id,
            'submitted_by' => 1,
            'submitted_at' => '2026-06-03 09:40:00',
            'status' => 'rechazado',
            'response_summary' => 'Archivo rechazado por inconsistencia en campos obligatorios y parentesco familiar inválido en la línea 4.',
            'rejected_at' => '2026-06-04 11:15:00',
            'rejection_reason' => 'Estructura o contenido con inconsistencias severas.'
        ]);

        SimonMockSubmissionLog::create([
            'simon_mock_submission_id' => $subRechazado->id,
            'event_type' => 'status_change',
            'old_status' => 'recibido',
            'new_status' => 'rechazado',
            'message' => 'Rechazo automático por fallos severos de validación cruzada en dependientes.'
        ]);
    }
}

// Para usar Carbon de forma estática
class Carbon extends \Carbon\Carbon {}
