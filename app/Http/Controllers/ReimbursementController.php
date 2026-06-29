<?php

namespace App\Http\Controllers;

use App\Models\ReimbursementCase;
use App\Models\ReimbursementDocument;
use App\Models\ReimbursementAction;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\Documento;
use App\Services\AccountingPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ReimbursementController extends Controller
{
    /**
     * Bandeja de Reembolsos del Core Administrativo.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = ReimbursementCase::with(['afiliado', 'pss']);

        if ($status) {
            $query->where('status', $status);
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(15);
        $estados = ['Recibido', 'Pendiente de documentos', 'Expediente completo', 'En revisión', 'Aprobado', 'Rechazado', 'Aprobado parcial', 'Reembolsado', 'Escalado a DIDA', 'Escalado a SISALRIL', 'Cerrado'];

        return view('ars.reembolsos.index', compact('cases', 'status', 'estados'));
    }

    /**
     * Formulario de nuevo reembolso.
     */
    public function create()
    {
        $afiliados = Afiliado::orderBy('nombres')->limit(50)->get(); // Limitar para performance
        $pssList = Pss::orderBy('nombre')->get();
        $canales = ['presencial', 'app', 'correo', 'otro'];
        $origenes = ['ars', 'dida', 'sisalril', 'idoppril'];
        $tipos = ['cobro_indebido', 'negacion_cobertura', 'ambas'];

        return view('ars.reembolsos.create', compact('afiliados', 'pssList', 'canales', 'origenes', 'tipos'));
    }

    /**
     * Guarda la solicitud de reembolso y genera el número de caso.
     */
    public function store(Request $request)
    {
        $request->validate([
            'afiliado_id'      => 'required|exists:afiliados,id',
            'pss_id'           => 'required|exists:pss,id',
            'service_date'     => 'required|date',
            'payment_date'     => 'required|date',
            'requested_amount' => 'required|numeric|min:0',
            'request_type'     => 'required|in:cobro_indebido,negacion_cobertura,ambas',
            'request_channel'  => 'required|string',
            'origin'           => 'required|string',
        ]);

        // Regla: El afiliado tiene hasta 120 días calendario desde la emisión del pago para solicitar el reembolso
        $paymentDate = Carbon::parse($request->payment_date);
        $daysDiff = $paymentDate->diffInDays(now());

        if ($daysDiff > 120) {
            return redirect()->back()
                ->withInput()
                ->with('error', "La fecha de pago supera el plazo máximo legal de 120 días calendario para solicitar reembolsos (Transcurridos: {$daysDiff} días).");
        }

        // Generar número de caso
        $year = now()->year;
        $count = ReimbursementCase::whereYear('created_at', $year)->count() + 1;
        $caseNum = 'REEM-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);

        DB::transaction(function() use ($request, $caseNum) {
            $due = now()->addBusinessDays(10); // 10 días hábiles de plazo de respuesta

            $case = ReimbursementCase::create([
                'case_number' => $caseNum,
                'afiliado_id' => $request->afiliado_id,
                'origin' => $request->origin,
                'request_channel' => $request->request_channel,
                'request_type' => $request->request_type,
                'pss_id' => $request->pss_id,
                'service_date' => $request->service_date,
                'payment_date' => $request->payment_date,
                'requested_amount' => $request->requested_amount,
                'status' => 'Recibido',
                'received_at' => now(),
                'response_due_date' => $due,
                'created_by' => Auth::id() ?? 1,
            ]);

            // Registrar acción inicial
            ReimbursementAction::create([
                'reimbursement_case_id' => $case->id,
                'action_type' => 'Recepción',
                'description' => "Caso de reembolso {$caseNum} aperturado y asignado para evaluación.",
                'user_id' => Auth::id() ?? 1,
            ]);
        });

        return redirect()->route('ars.reembolsos.index')->with('success', "Caso de reembolso {$caseNum} registrado exitosamente.");
    }

    /**
     * Detalle del caso.
     */
    public function show($id)
    {
        $case = ReimbursementCase::with(['afiliado', 'pss', 'documents.uploader', 'actions.user'])->findOrFail($id);
        $estados = ['Recibido', 'Pendiente de documentos', 'Expediente completo', 'En revisión', 'Aprobado', 'Rechazado', 'Aprobado parcial', 'Reembolsado', 'Escalado a DIDA', 'Escalado a SISALRIL', 'Cerrado'];

        return view('ars.reembolsos.show', compact('case', 'estados'));
    }

    /**
     * Procesa y cambia el estado del reembolso.
     * Si se aprueba, genera asientos contables y débitos automáticos.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'description' => 'required|string',
            'approved_amount' => 'nullable|numeric|min:0',
        ]);

        $case = ReimbursementCase::findOrFail($id);
        $oldStatus = $case->status;
        $newStatus = $request->status;

        DB::transaction(function() use ($case, $newStatus, $request, $oldStatus) {
            $case->update([
                'status' => $newStatus,
                'approved_amount' => $request->approved_amount ?? $case->approved_amount,
                'rejected_amount' => $newStatus === 'Rechazado' ? $case->requested_amount : ($case->requested_amount - ($request->approved_amount ?? 0)),
                'final_decision' => $request->description,
                'responded_at' => in_array($newStatus, ['Aprobado', 'Rechazado', 'Aprobado parcial']) ? now() : $case->responded_at
            ]);

            // Registrar acción de trazabilidad
            ReimbursementAction::create([
                'reimbursement_case_id' => $case->id,
                'action_type' => 'Cambio Estado',
                'description' => "Caso modificado de {$oldStatus} a {$newStatus}. Comentario: {$request->description}",
                'user_id' => Auth::id() ?? 1,
            ]);

            // Si pasa a Aprobado o Aprobado parcial, generar el Asiento Contable
            if (($newStatus === 'Aprobado' || $newStatus === 'Aprobado parcial') && $case->approved_amount > 0) {
                // Si es por cobro indebido, se marca que se requiere débito a PSS
                if ($case->request_type === 'cobro_indebido' || $case->request_type === 'ambas') {
                    $case->update(['pss_debit_required' => true]);
                }

                // 1. Asiento contable de desembolso al afiliado
                $entry = AccountingPostingService::registrarReembolsoCaso($case);

                // 2. Si es Cobro Indebido, generar compensación automática contra Cuentas por Pagar de la PSS (débito)
                if ($case->pss_debit_required && $case->pss_id) {
                    AccountingPostingService::registrarCompensacionPss($case);
                    
                    // Notificar a la bitácora
                    \App\Models\Bitacora::registrar('Débito PSS', "Aplicado descuento de DOP {$case->approved_amount} a la PSS ID {$case->pss_id} por reembolso de cobro indebido del caso {$case->case_number}.");
                }
            }
        });

        return redirect()->route('ars.reembolsos.show', $case->id)->with('success', "Estado del reembolso actualizado correctamente.");
    }

    /**
     * Sube documentos soporte.
     */
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document_type' => 'required|string',
            'file'          => 'required|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $case = ReimbursementCase::findOrFail($id);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_reemb_' . $file->getClientOriginalName();
            $path = $file->storeAs('documentos/reembolsos', $fileName, 'public');

            DB::transaction(function() use ($case, $request, $path) {
                ReimbursementDocument::create([
                    'reimbursement_case_id' => $case->id,
                    'document_type' => $request->document_type,
                    'file_path' => $path,
                    'status' => 'Válido',
                    'uploaded_at' => now(),
                    'uploaded_by' => Auth::id() ?? 1,
                ]);

                // Registrar en acciones
                ReimbursementAction::create([
                    'reimbursement_case_id' => $case->id,
                    'action_type' => 'Carga Documento',
                    'description' => "Cargado documento de tipo: {$request->document_type}.",
                    'user_id' => Auth::id() ?? 1,
                ]);

                // Regla: Si el expediente ya cuenta con los documentos requeridos principales
                // (Factura y Recibo de Pago), se marca como Expediente Completo.
                $documentTypes = $case->documents()->pluck('document_type')->toArray();
                if (in_array('Factura Original', $documentTypes) && in_array('Recibo de Pago', $documentTypes)) {
                    $case->update([
                        'status' => 'Expediente completo',
                        'completed_documents_at' => now(),
                        'response_due_date' => now()->addBusinessDays(10) // Reiniciar plazo de 10 días a partir de expediente completo
                    ]);

                    ReimbursementAction::create([
                        'reimbursement_case_id' => $case->id,
                        'action_type' => 'Expediente Completo',
                        'description' => "Se ha verificado el expediente con los documentos completos. Inicia el plazo de 10 días hábiles de respuesta.",
                        'user_id' => Auth::id() ?? 1,
                    ]);
                }
            });
        }

        return redirect()->route('ars.reembolsos.show', $case->id)->with('success', "Documento cargado exitosamente.");
    }
}
