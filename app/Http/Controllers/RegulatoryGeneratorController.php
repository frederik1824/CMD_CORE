<?php

namespace App\Http\Controllers;

use App\Models\RegulatorySchema;
use App\Models\RegulatoryPeriod;
use App\Models\RegulatorySchemaRun;
use App\Models\SimonMockSubmission;
use App\Models\SimonMockSubmissionLog;
use App\Services\RegulatorySchemaGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegulatoryGeneratorController extends Controller
{
    public function generarIndex()
    {
        $esquemas = RegulatorySchema::all();
        $periodos = RegulatoryPeriod::orderBy('start_date', 'desc')->get();
        $corridas = RegulatorySchemaRun::with(['schema', 'period'])->orderBy('generated_at', 'desc')->take(10)->get();

        return view('ars.sisalril.generar', compact('esquemas', 'periodos', 'corridas'));
    }

    public function generarProcesar(Request $request)
    {
        $request->validate([
            'regulatory_schema_id' => 'required|exists:regulatory_schemas,id',
            'period_id' => 'required|exists:regulatory_periods,id'
        ]);

        $schema = RegulatorySchema::findOrFail($request->regulatory_schema_id);
        $period = RegulatoryPeriod::findOrFail($request->period_id);

        $run = RegulatorySchemaGeneratorService::generate($schema, $period, Auth::id() ?? 1);

        return redirect()->route('sisalril.show', $schema->schema_code)->with('success', "Corrida {$run->run_number} completada con estatus: {$run->status}.");
    }

    public function simulador()
    {
        $submissions = SimonMockSubmission::with(['schema', 'period', 'run'])->orderBy('submitted_at', 'desc')->get();
        $corridasDisponibles = RegulatorySchemaRun::whereNotIn('status', ['aprobado'])
            ->whereDoesntHave('submission')
            ->with(['schema', 'period'])
            ->get();

        return view('ars.sisalril.simulador', compact('submissions', 'corridasDisponibles'));
    }

    public function submissionDetalle($id)
    {
        $submission = SimonMockSubmission::with(['schema', 'period', 'run.details', 'logs'])->findOrFail($id);
        return view('ars.sisalril.detalle_envio', compact('submission'));
    }

    public function submissionAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:aprobar,rechazar',
            'rejection_reason' => 'required_if:action,rechazar|string|nullable'
        ]);

        $sub = SimonMockSubmission::findOrFail($id);
        $oldStatus = $sub->status;

        if ($request->action === 'aprobar') {
            $sub->update([
                'status' => 'aprobado',
                'approved_at' => now(),
                'response_summary' => 'Aprobado satisfactoriamente. Todos los registros cumplen con la estructura SIMON.'
            ]);

            $sub->run->update(['status' => 'aprobado']);
            $msg = 'El supervisor aprobó la presentación regulatoria.';
        } else {
            $sub->update([
                'status' => 'rechazado',
                'rejected_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'response_summary' => 'Rechazado por el supervisor. Motivo: ' . $request->rejection_reason
            ]);

            $sub->run->update(['status' => 'con_errores']);
            $msg = 'El supervisor rechazó la presentación: ' . $request->rejection_reason;
        }

        SimonMockSubmissionLog::create([
            'simon_mock_submission_id' => $sub->id,
            'event_type' => 'status_change',
            'old_status' => $oldStatus,
            'new_status' => $sub->status,
            'message' => $msg
        ]);

        return redirect()->route('sisalril.simulador')->with('success', "Envío {$sub->submission_number} actualizado a {$sub->status}.");
    }

    /**
     * Crear simulación de envío a SIMON desde la corrida
     */
    public function enviarSimon(Request $request, $runId)
    {
        $run = RegulatorySchemaRun::findOrFail($runId);
        
        $subNumber = 'SUB-SIMON-' . now()->year . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        $status = $run->invalid_records > 0 ? 'validando_estructura' : 'aprobado';
        
        $sub = SimonMockSubmission::create([
            'submission_number' => $subNumber,
            'regulatory_schema_run_id' => $run->id,
            'regulatory_schema_id' => $run->regulatory_schema_id,
            'period_id' => $run->period_id,
            'submitted_by' => Auth::id() ?? 1,
            'submitted_at' => now(),
            'status' => $status,
            'response_summary' => $status === 'aprobado' 
                ? 'Validación estructural automática completada con éxito.' 
                : 'Se encontraron inconsistencias en la validación inicial de estructura de ancho de columna.'
        ]);

        SimonMockSubmissionLog::create([
            'simon_mock_submission_id' => $sub->id,
            'event_type' => 'creation',
            'old_status' => null,
            'new_status' => $status,
            'message' => 'Envío inicial recibido en la plataforma SIMON.'
        ]);

        return redirect()->route('sisalril.simulador')->with('success', "Corrida enviada a SIMON (Submission: {$subNumber}).");
    }
}
