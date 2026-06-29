<?php

namespace App\Http\Controllers;

use App\Models\AuthorizationClaim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use App\Models\AccountPayable;
use App\Models\Autorizacion;
use App\Models\AuthorizationTimelineEvent;
use App\Models\Pss;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReclamacionController extends Controller
{
    /**
     * Lista de Reclamaciones (Core ARS).
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $pssId = $request->get('pss_id');

        $query = AuthorizationClaim::with(['authorization', 'pss']);

        if ($status) $query->where('status', $status);
        if ($pssId) $query->where('pss_id', $pssId);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('ncf', 'like', "%{$search}%");
            });
        }

        $reclamaciones = $query->orderBy('created_at', 'desc')->paginate(15);
        $pssList = Pss::orderBy('nombre')->get();
        
        $estados = [
            'Reclamación recibida',
            'En auditoría de reclamación',
            'Reclamación aprobada',
            'Reclamación objetada',
            'Cuenta por pagar generada',
            'En lote de pago',
            'Pagada',
            'Conciliada'
        ];

        return view('ars.reclamaciones.index', compact('reclamaciones', 'pssList', 'status', 'search', 'pssId', 'estados'));
    }

    /**
     * Detalle de la Reclamación.
     */
    public function show($id)
    {
        $reclamacion = AuthorizationClaim::with(['authorization.servicioPdss', 'pss', 'documents', 'audits.auditor', 'payables'])->findOrFail($id);
        $afiliado = $reclamacion->afiliado;

        // Historial de timeline de la autorización
        $timeline = AuthorizationTimelineEvent::where('authorization_id', $reclamacion->authorization_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('ars.reclamaciones.show', compact('reclamacion', 'afiliado', 'timeline'));
    }

    /**
     * Procesa la auditoría clínica/administrativa de una reclamación.
     */
    public function auditar(Request $request, $id)
    {
        $claim = AuthorizationClaim::findOrFail($id);

        $request->validate([
            'decision' => 'required|in:Aprobada,Objetada parcial,Objetada total,Pendiente documento,Rechazada',
            'audit_type' => 'required|in:Administrativa,Médica,Tarifa,Documental',
            'approved_amount' => 'nullable|numeric|min:0',
            'objection_reason' => 'nullable|string',
            'internal_observation' => 'nullable|string',
            'pss_observation' => 'nullable|string',
        ]);

        $approvedAmount = floatval($request->input('approved_amount', 0));
        $claimedAmount = floatval($claim->claimed_amount);
        $objectedAmount = max(0, $claimedAmount - $approvedAmount);

        if ($request->decision === 'Objetada total' || $request->decision === 'Rechazada') {
            $approvedAmount = 0;
            $objectedAmount = $claimedAmount;
        }

        DB::transaction(function() use ($claim, $request, $approvedAmount, $objectedAmount) {
            // Obtener auditor médico registrado
            $medAuditor = \App\Models\MedicalAuditor::where('user_id', Auth::id())->first();
            $auditorCode = $medAuditor ? $medAuditor->auditor_code : 'AUD-' . str_pad(Auth::id(), 4, '0', STR_PAD_LEFT);

            // 1. Crear registro de auditoría
            $audit = ClaimAudit::create([
                'claim_id' => $claim->id,
                'audit_type' => $request->audit_type,
                'auditor_id' => Auth::id(),
                'status' => $request->decision === 'Aprobada' ? 'Aprobada' : ($request->decision === 'Rechazada' ? 'Rechazada' : 'Objetada'),
                'claimed_amount' => $claim->claimed_amount,
                'approved_amount' => $approvedAmount,
                'objected_amount' => $objectedAmount,
                'objection_reason' => $request->objection_reason,
                'internal_observation' => $request->internal_observation . ($medAuditor ? " [Auditor Exequatur: {$medAuditor->exequatur}]" : ""),
                'pss_observation' => $request->pss_observation,
                'reviewed_at' => now(),
            ]);

            // 2. Si hay objeción parcial o total, registrar la glosa en claim_glosses
            if ($objectedAmount > 0) {
                $glosaNum = 'GLO-' . now()->format('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $glosa = \App\Models\ClaimGlosa::create([
                    'claim_id' => $claim->id,
                    'audit_id' => $audit->id,
                    'glosa_code' => $glosaNum,
                    'glosa_type' => $request->audit_type,
                    'objected_service' => $claim->authorization->procedimiento ?? 'Servicios médicos',
                    'objection_reason' => $request->objection_reason ?: 'Monto excede lo autorizado.',
                    'evidence_reference' => 'Historial de autorizaciones y tarifas vigentes PSS',
                    'original_amount' => $claim->claimed_amount,
                    'objected_amount' => $objectedAmount,
                    'recognized_amount' => $approvedAmount,
                    'status' => 'Notificada a PSS', // Pasa a notificada para iniciar el ciclo
                    'created_by' => Auth::id() ?? 1,
                ]);

                // Registrar plazo de 15 días hábiles para glosas de pertinencia en la descripción
                if ($request->audit_type === 'Médica') {
                    $glosa->update([
                        'objection_reason' => $glosa->objection_reason . " [Plazo de descargo: 15 días hábiles]."
                    ]);
                }
            }

            // 3. Actualizar reclamación
            $newStatus = match($request->decision) {
                'Aprobada' => 'Reclamación aprobada',
                'Objetada parcial' => 'Reclamación aprobada',
                'Objetada total' => 'Reclamación objetada',
                'Pendiente documento' => 'Pendiente de documento',
                'Rechazada' => 'Reclamación objetada',
            };

            $claim->update([
                'status' => $newStatus,
                'approved_amount' => $approvedAmount,
                'objected_amount' => $objectedAmount,
                'observations' => $request->pss_observation
            ]);

            // 4. Contabilidad: Registrar asiento de auditoría (liberar reserva e imputar a liquidadas)
            \App\Services\AccountingPostingService::registrarReclamacionAuditada($claim);

            // 5. Registrar hito en la línea de tiempo
            AuthorizationTimelineEvent::registrar(
                $claim->authorization_id,
                'CLAIM_AUDITED',
                'Reclamación auditada: ' . $request->decision,
                "Auditoría {$request->audit_type} completada por el auditor {$auditorCode}. Aprobado: DOP {$approvedAmount}. Objetado: DOP {$objectedAmount}.",
                'Reclamación recibida',
                $newStatus,
                ['audit_id' => $audit->id]
            );

            // 6. Si se aprobó (total o parcial), generar Cuenta por Pagar (CXP) y registrar su asiento contable
            if (in_array($request->decision, ['Aprobada', 'Objetada parcial'])) {
                $year = now()->year;
                $countPayables = AccountPayable::whereYear('created_at', $year)->count();
                $payableNum = 'CXP-' . $year . '-' . str_pad($countPayables + 1, 6, '0', STR_PAD_LEFT);

                // Calcular retención de impuestos ficticia (e.g. 10% de ISR si es profesional / PSS física)
                $retencion = 0.0;
                if ($claim->pss->tipo === 'Física') {
                    $retencion = $approvedAmount * 0.10; // Retención de 10%
                }

                $ap = AccountPayable::create([
                    'payable_number' => $payableNum,
                    'account_payable_number' => $payableNum, // Copiar a la columna nueva
                    'claim_id' => $claim->id,
                    'authorization_id' => $claim->authorization_id,
                    'pss_id' => $claim->pss_id,
                    'amount' => $claim->claimed_amount,
                    'retained_amount' => $objectedAmount,
                    'gross_amount' => $approvedAmount,
                    'objected_amount' => $objectedAmount,
                    'approved_amount' => $approvedAmount,
                    'tax_withholding_amount' => $retencion,
                    'other_deductions' => 0.0,
                    'net_amount' => $approvedAmount - $retencion,
                    'vendor_type' => 'PSS',
                    'vendor_id' => $claim->pss_id,
                    'status' => 'Generada',
                    'generated_by' => Auth::id(),
                    'generated_at' => now(),
                    'due_date' => now()->addDays(30)
                ]);

                // Generar asiento contable de Cuentas por Pagar (CXP)
                $entContable = \App\Services\AccountingPostingService::registrarCuentaPorPagar($ap);
                if ($entContable) {
                    $ap->update(['accounting_entry_id' => $entContable->id, 'status' => 'Contabilizada']);
                }

                // Registrar generación de CXP en timeline
                AuthorizationTimelineEvent::registrar(
                    $claim->authorization_id,
                    'CXP_GENERATED',
                    'Cuenta por Pagar Generada',
                    "Creado registro de cuenta por pagar {$payableNum} por valor neto de DOP " . number_format($ap->net_amount, 2) . " (Retención ISR: DOP " . number_format($retencion, 2) . ").",
                    $newStatus,
                    'Cuenta por pagar generada',
                    ['payable_id' => $ap->id]
                );

                // Actualizar estado de autorización y reclamación
                $claim->update(['status' => 'Cuenta por pagar generada']);
                $claim->authorization->update(['estado' => 'Cuenta por pagar generada']);
            } else {
                $claim->authorization->update(['estado' => $newStatus]);
            }

            Bitacora::registrar('Auditoría Reclamaciones', "Procesada auditoría para reclamación {$claim->claim_number}. Decisión: {$request->decision}.");
        });

        return redirect()->route('ars.reclamaciones.show', $claim->id)
            ->with('success', 'Auditoría registrada y estado de reclamación actualizado.');
    }

    /**
     * Tramita una conciliación de glosa médica.
     */
    public function conciliacionStore(Request $request, $claimId)
    {
        $claim = AuthorizationClaim::findOrFail($claimId);

        $request->validate([
            'glosa_id'         => 'required|exists:claim_glosses,id',
            'instance'         => 'required|in:primera_instancia,segunda_instancia,arbitraje',
            'result_status'    => 'required|in:Aprobada,Rechazada,Aprobada Parcial',
            'agreement_amount' => 'required|numeric|min:0',
            'ars_observation'  => 'required|string',
            'pss_observation'  => 'nullable|string',
            'final_decision'   => 'required|in:Ratificada,Levantada,Parcialmente Aceptada',
        ]);

        $glosa = \App\Models\ClaimGlosa::findOrFail($request->glosa_id);

        DB::transaction(function() use ($claim, $glosa, $request) {
            // 1. Crear conciliación
            $conciliation = \App\Models\ClaimConciliation::create([
                'claim_id' => $claim->id,
                'glosa_id' => $glosa->id,
                'instance' => $request->instance,
                'requested_by' => 'PSS',
                'requested_at' => now(),
                'resolved_at' => now(),
                'result_status' => $request->result_status,
                'agreement_amount' => $request->agreement_amount,
                'ars_observation' => $request->ars_observation,
                'pss_observation' => $request->pss_observation,
                'final_decision' => $request->final_decision,
            ]);

            // 2. Actualizar estado de la glosa según decisión
            $newGlosaStatus = match($request->final_decision) {
                'Ratificada' => 'Ratificada',
                'Levantada' => 'Levantada',
                'Parcialmente Aceptada' => 'Parcialmente aceptada',
            };

            $glosa->update([
                'status' => $newGlosaStatus,
                'recognized_amount' => $request->agreement_amount
            ]);

            // 3. Si se reconoce algún monto (Levantada o Parcialmente Aceptada), generar CXP complementaria
            if ($request->agreement_amount > 0) {
                $year = now()->year;
                $countPayables = AccountPayable::whereYear('created_at', $year)->count();
                $payableNum = 'CXP-COMP-' . $year . '-' . str_pad($countPayables + 1, 6, '0', STR_PAD_LEFT);

                $retencion = 0.0;
                if ($claim->pss->tipo === 'Física') {
                    $retencion = $request->agreement_amount * 0.10;
                }

                $ap = AccountPayable::create([
                    'payable_number' => $payableNum,
                    'account_payable_number' => $payableNum,
                    'claim_id' => $claim->id,
                    'authorization_id' => $claim->authorization_id,
                    'pss_id' => $claim->pss_id,
                    'amount' => $request->agreement_amount,
                    'retained_amount' => 0.0,
                    'gross_amount' => $request->agreement_amount,
                    'objected_amount' => 0.0,
                    'approved_amount' => $request->agreement_amount,
                    'tax_withholding_amount' => $retencion,
                    'other_deductions' => 0.0,
                    'net_amount' => $request->agreement_amount - $retencion,
                    'vendor_type' => 'PSS',
                    'vendor_id' => $claim->pss_id,
                    'status' => 'Generada',
                    'generated_by' => Auth::id(),
                    'generated_at' => now(),
                    'due_date' => now()->addDays(30)
                ]);

                // Registrar asiento contable complementario (CXP de conciliación)
                $entContable = \App\Services\AccountingPostingService::registrarCuentaPorPagar($ap);
                if ($entContable) {
                    $ap->update(['accounting_entry_id' => $entContable->id, 'status' => 'Contabilizada']);
                }

                // Registrar en timeline
                AuthorizationTimelineEvent::registrar(
                    $claim->authorization_id,
                    'GLOSA_RESOLVED',
                    "Glosa Conciliada ({$request->final_decision})",
                    "Conciliación de glosa {$glosa->glosa_code} completada. Generada CXP complementaria {$payableNum} por DOP " . number_format($ap->net_amount, 2) . ".",
                    $claim->status,
                    $claim->status,
                    ['conciliation_id' => $conciliation->id]
                );
            } else {
                // Glosa ratificada (no pagable)
                AuthorizationTimelineEvent::registrar(
                    $claim->authorization_id,
                    'GLOSA_RATIFIED',
                    'Glosa Ratificada',
                    "Conciliación completada en instancia: {$request->instance}. La glosa de DOP {$glosa->objected_amount} fue ratificada (monto no pagable).",
                    $claim->status,
                    $claim->status,
                    ['conciliation_id' => $conciliation->id]
                );
            }

            // Cambiar estado de la reclamación a Conciliada si todas las glosas están resueltas
            $glosasPendientes = \App\Models\ClaimGlosa::where('claim_id', $claim->id)
                ->whereIn('status', ['Registrada', 'Notificada a PSS', 'En conciliación'])
                ->count();

            if ($glosasPendientes === 0) {
                $claim->update(['status' => 'Conciliada']);
            }

            Bitacora::registrar('Conciliación Glosas', "Conciliación de glosa {$glosa->glosa_code} registrada. Resultado: {$request->final_decision}.");
        });

        return redirect()->route('ars.reclamaciones.show', $claim->id)
            ->with('success', 'Conciliación de glosa médica guardada y registrada exitosamente.');
    }

    /**
     * Bandeja de mesa de entrada de reclamaciones recibidas.
     */
    public function recepcionIndex(Request $request)
    {
        $search = $request->get('search');
        
        $query = AuthorizationClaim::where(function($q) {
            $q->where('status', 'Reclamación recibida')
              ->orWhere('status', 'Pendiente de documento')
              ->orWhere('status', 'Devuelta por documentos');
        })->with(['authorization', 'pss']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('claim_number', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('ncf', 'like', "%{$search}%")
                  ->orWhereHas('pss', function($qp) use ($search) {
                      $qp->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('authorization', function($qa) use ($search) {
                      $qa->where('numero_autorizacion', 'like', "%{$search}%");
                  });
            });
        }

        $reclamaciones = $query->orderBy('created_at', 'asc')->paginate(15);

        return view('ars.reclamaciones.recepcion', compact('reclamaciones', 'search'));
    }

    /**
     * Da entrada formal / radicación a una reclamación PSS.
     */
    public function darEntrada(Request $request, $id)
    {
        $claim = AuthorizationClaim::findOrFail($id);

        DB::transaction(function() use ($claim) {
            $claim->update([
                'status' => 'En auditoría de reclamación',
                'received_by' => Auth::id() ?? 1,
                'received_at' => now()
            ]);

            // Registrar evento en timeline
            AuthorizationTimelineEvent::registrar(
                $claim->authorization_id,
                'CLAIM_ACCEPTED',
                'Reclamación Radicada Oficialmente',
                "La ARS ha dado entrada formal a la reclamación {$claim->claim_number}. Se inicia el conteo de antigüedad del expediente.",
                'Reclamación recibida',
                'En auditoría de reclamación',
                ['entry_number' => 'ENT-' . now()->format('Y') . '-' . str_pad($claim->id, 6, '0', STR_PAD_LEFT)]
            );

            Bitacora::registrar('Recepción Reclamaciones', "Dada entrada oficial a reclamación {$claim->claim_number} de la PSS {$claim->pss->nombre}.");
        });

        return redirect()->route('ars.reclamaciones.recepcion')
            ->with('success', 'Se ha radicado formalmente la reclamación y ha sido enrutada a la bandeja de Auditoría.');
    }

    /**
     * Devuelve una reclamación por falta de documentos soporte mínimos.
     */
    public function devolverDocumental(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5'
        ]);

        $claim = AuthorizationClaim::findOrFail($id);

        DB::transaction(function() use ($claim, $request) {
            $claim->update([
                'status' => 'Devuelta por documentos',
                'observations' => $request->input('reason')
            ]);

            AuthorizationTimelineEvent::registrar(
                $claim->authorization_id,
                'CLAIM_RETURNED',
                'Reclamación Devuelta',
                "Expediente devuelto a la PSS por documentación incompleta: " . $request->input('reason'),
                'Reclamación recibida',
                'Devuelta por documentos'
            );

            Bitacora::registrar('Recepción Reclamaciones', "Devuelta reclamación {$claim->claim_number} a PSS. Motivo: " . $request->input('reason'));
        });

        return redirect()->route('ars.reclamaciones.recepcion')
            ->with('success', 'La reclamación ha sido devuelta a la PSS para corrección documental.');
    }

    /**
     * Tablero de control de plazos y antigüedad (Aging) de reclamaciones.
     */
    public function controlPlazos(Request $request)
    {
        $claims = AuthorizationClaim::with(['authorization', 'pss'])->get();
        
        $claimsWithAging = [];
        foreach ($claims as $claim) {
            $aging = \App\Services\ClaimAgingService::getAgingData($claim);
            $claim->aging = (object)$aging;
            $claimsWithAging[] = $claim;
        }

        $stats = \App\Services\ClaimAgingService::getAgingStats();

        return view('ars.reclamaciones.plazos', compact('claimsWithAging', 'stats'));
    }

    /**
     * Módulo de reportes e indicadores de reclamaciones.
     */
    public function reportes(Request $request)
    {
        $stats = \App\Services\ClaimAgingService::getAgingStats();
        $claims = AuthorizationClaim::with(['pss', 'payables'])->get();

        // Agrupación por PSS para top 5 montos y glosas
        $pssData = DB::table('authorization_claims')
            ->join('pss', 'authorization_claims.pss_id', '=', 'pss.id')
            ->select('pss.nombre', 
                DB::raw('SUM(claimed_amount) as total_claimed'),
                DB::raw('SUM(approved_amount) as total_approved'),
                DB::raw('SUM(objected_amount) as total_objected'))
            ->groupBy('pss.nombre')
            ->orderBy('total_claimed', 'desc')
            ->take(5)
            ->get();

        return view('ars.reclamaciones.reportes', compact('stats', 'pssData'));
    }
}
