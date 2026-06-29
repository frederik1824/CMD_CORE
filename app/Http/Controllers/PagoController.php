<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Models\PaymentBatch;
use App\Models\PaymentBatchItem;
use App\Models\PaymentReconciliation;
use App\Models\AuthorizationTimelineEvent;
use App\Models\Pss;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    /**
     * Bandeja de Cuentas por Pagar (CXP).
     */
    public function cxpIndex(Request $request)
    {
        $status = $request->get('status');
        $pssId = $request->get('pss_id');

        $query = AccountPayable::with(['claim', 'authorization', 'pss']);

        if ($status) $query->where('status', $status);
        if ($pssId) $query->where('pss_id', $pssId);

        $payables = $query->orderBy('created_at', 'desc')->paginate(15);
        $pssList = Pss::orderBy('nombre')->get();
        
        $estados = ['Generada', 'Validada', 'En lote de pago', 'Pagada', 'Retenida', 'Anulada', 'Conciliada'];

        // CXP disponibles para lotes de pago (estado Generada, Validada o Contabilizada)
        $cxpDisponibles = AccountPayable::whereIn('status', ['Generada', 'Validada', 'Contabilizada'])
            ->with(['pss', 'authorization'])
            ->get();

        return view('ars.pagos.cxp', compact('payables', 'pssList', 'status', 'pssId', 'estados', 'cxpDisponibles'));
    }

    /**
     * Bandeja de Lotes de Pago.
     */
    public function lotesIndex(Request $request)
    {
        $lotes = PaymentBatch::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('ars.pagos.lotes_index', compact('lotes'));
    }

    /**
     * Detalle de un Lote de Pago.
     */
    public function loteShow($id)
    {
        $lote = PaymentBatch::with(['items.payable.pss', 'items.payable.claim', 'items.payable.authorization', 'reconciliations.pss'])->findOrFail($id);
        return view('ars.pagos.lotes_show', compact('lote'));
    }

    /**
     * Crea un lote de pago en estado Borrador agrupando CXP seleccionadas.
     */
    public function crearLote(Request $request)
    {
        $request->validate([
            'cxp_ids' => 'required|array',
            'cxp_ids.*' => 'exists:accounts_payable,id',
            'scheduled_payment_date' => 'required|date'
        ]);

        $cxpIds = $request->input('cxp_ids');

        DB::transaction(function() use ($cxpIds, $request) {
            $payables = AccountPayable::whereIn('id', $cxpIds)->get();
            $totalAmount = $payables->sum('net_amount');
            $totalItems = $payables->count();

            $year = now()->year;
            $countLotes = PaymentBatch::whereYear('created_at', $year)->count();
            $batchNum = 'PAG-LOTE-' . $year . '-' . str_pad($countLotes + 1, 6, '0', STR_PAD_LEFT);

            // 1. Crear lote
            $batch = PaymentBatch::create([
                'batch_number' => $batchNum,
                'status' => 'Borrador',
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'scheduled_payment_date' => $request->scheduled_payment_date,
                'created_by' => Auth::id()
            ]);

            // 2. Agregar ítems y actualizar CXP
            foreach ($payables as $payable) {
                PaymentBatchItem::create([
                    'payment_batch_id' => $batch->id,
                    'account_payable_id' => $payable->id,
                    'amount' => $payable->net_amount,
                    'status' => 'En lote de pago'
                ]);

                $payable->update(['status' => 'En lote de pago']);
                
                // Actualizar reclamación y autorización
                $payable->claim->update(['status' => 'En lote de pago']);
                $payable->authorization->update(['estado' => 'En lote de pago']);

                // Registrar en timeline
                AuthorizationTimelineEvent::registrar(
                    $payable->authorization_id,
                    'PAYMENT_BATCHED',
                    'Agregado a lote de pago',
                    "La cuenta por pagar {$payable->payable_number} se consolidó dentro del lote {$batchNum}.",
                    'Cuenta por pagar generada',
                    'En lote de pago',
                    ['batch_id' => $batch->id]
                );
            }

            Bitacora::registrar('Pagos PSS', "Creado lote de pago {$batchNum} con {$totalItems} facturas por un monto de DOP {$totalAmount}.");
        });

        return redirect()->route('ars.lotes.index')->with('success', 'Lote de pago creado con éxito en estado Borrador.');
    }

    /**
     * Aprueba un lote de pago programándolo.
     */
    public function aprobarLote($id)
    {
        $lote = PaymentBatch::findOrFail($id);
        $lote->update([
            'status' => 'Programado',
            'approved_by' => Auth::id()
        ]);

        Bitacora::registrar('Pagos PSS', "Lote de pago {$lote->batch_number} aprobado y programado.");
        return redirect()->route('ars.lotes.show', $lote->id)->with('success', 'Lote de pago aprobado y programado.');
    }

    /**
     * Procesa físicamente el desembolso y marca el lote como pagado.
     */
    public function pagarLote($id)
    {
        $lote = PaymentBatch::findOrFail($id);

        DB::transaction(function() use ($lote) {
            $lote->update([
                'status' => 'Pagado',
                'paid_at' => now()
            ]);

            // 1. Contabilizar asiento de pago en banco (Débito CXP vs Crédito Banco)
            $entPago = \App\Services\AccountingPostingService::registrarPagoLote($lote);

            foreach ($lote->items as $item) {
                $payable = $item->payable;
                $payable->update([
                    'status' => 'Pagada',
                    'payment_entry_id' => $entPago ? $entPago->id : null
                ]);
                $payable->claim->update(['status' => 'Pagada']);
                $payable->authorization->update(['estado' => 'Pagada']);

                // 2. Contabilizar reconocimiento de gasto de reclamaciones pagadas
                \App\Services\AccountingPostingService::registrarGastoReclamacion($payable);

                // Registrar en timeline
                AuthorizationTimelineEvent::registrar(
                    $payable->authorization_id,
                    'PAYMENT_EXECUTED',
                    'Pago realizado',
                    "Transferencia bancaria efectuada para la cuenta {$payable->payable_number} por valor de DOP " . number_format($payable->net_amount, 2) . ".",
                    'En lote de pago',
                    'Pagada',
                    ['batch_id' => $lote->id]
                );
            }

            Bitacora::registrar('Pagos PSS', "Desembolsado lote de pago {$lote->batch_number} por DOP {$lote->total_amount}.");
        });

        return redirect()->route('ars.lotes.show', $lote->id)->with('success', 'Desembolso procesado con éxito. Lote marcado como Pagado.');
    }

    /**
     * Realiza la conciliación bancaria de las facturas en el lote.
     */
    public function conciliarLote(Request $request, $id)
    {
        $lote = PaymentBatch::with('items.payable')->findOrFail($id);
        $reference = $request->input('bank_reference', 'REF-' . rand(100000, 999999));

        DB::transaction(function() use ($lote, $reference) {
            foreach ($lote->items as $item) {
                $payable = $item->payable;

                // Crear conciliación
                PaymentReconciliation::create([
                    'payment_batch_id' => $lote->id,
                    'account_payable_id' => $payable->id,
                    'pss_id' => $payable->pss_id,
                    'expected_amount' => $payable->net_amount,
                    'paid_amount' => $payable->net_amount,
                    'difference' => 0.00,
                    'bank_reference' => $reference,
                    'payment_date' => now(),
                    'status' => 'Conciliado'
                ]);

                // Actualizar a Conciliada / Cerrada
                $payable->update(['status' => 'Conciliada']);
                $payable->claim->update(['status' => 'Conciliada']);
                $payable->authorization->update(['estado' => 'Conciliada']);

                // Registrar hito final en timeline
                AuthorizationTimelineEvent::registrar(
                    $payable->authorization_id,
                    'PAYMENT_RECONCILED',
                    'Conciliación completada',
                    "Pago conciliado contra extracto bancario con referencia {$reference}.",
                    'Pagada',
                    'Conciliada'
                );

                // Cierre formal
                $payable->claim->update(['status' => 'Cerrada']);
                $payable->authorization->update(['estado' => 'Cerrada']);
                
                AuthorizationTimelineEvent::registrar(
                    $payable->authorization_id,
                    'CYCLE_CLOSED',
                    'Ciclo de pago cerrado',
                    "Finalizado y cerrado el ciclo operativo completo para esta transacción médica.",
                    'Conciliada',
                    'Cerrada'
                );
            }

            $lote->update(['status' => 'Conciliado']);
            Bitacora::registrar('Pagos PSS', "Lote de pago {$lote->batch_number} conciliado exitosamente.");
        });

        return redirect()->route('ars.lotes.show', $lote->id)->with('success', 'Lote de pago conciliado y cerrado.');
    }
}
