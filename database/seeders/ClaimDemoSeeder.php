<?php

namespace Database\Seeders;

use App\Models\AuthorizationClaim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use App\Models\ClaimGlosa;
use App\Models\AccountPayable;
use App\Models\PaymentBatch;
use App\Models\PaymentBatchItem;
use App\Models\Autorizacion;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\User;
use App\Models\AuthorizationTimelineEvent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaimDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Validar que tengamos PSS, Afiliados y Usuarios
        $pssList = Pss::all();
        if ($pssList->isEmpty()) {
            // Crear algunas PSS por defecto
            $nombresPss = ['Clínica Abreu', 'Plaza de la Salud', 'Centro de Diagnóstico Medicina Avanzada (CEDIMAT)', 'Clínica Unión Médica', 'Homs Santiago', 'Centro Médico Bournigal', 'Clínica Corominas', 'Centro Médico Cibao', 'Clínica Independencia', 'Hospiten Santo Domingo'];
            foreach ($nombresPss as $index => $nombre) {
                $pssList->push(Pss::create([
                    'rnc' => '101' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'nombre' => $nombre,
                    'tipo_entidad' => $index % 3 === 0 ? 'Hospital' : ($index % 3 === 1 ? 'Clínica' : 'Centro Médico'),
                    'estado' => 'Activa'
                ]));
            }
        }

        $afiliados = Afiliado::all();
        if ($afiliados->isEmpty()) {
            return;
        }

        $userArs = User::where('role', 'Auditor Médico')->first() ?? User::first();
        $auditorId = $userArs ? $userArs->id : 1;

        // 2. Buscar o crear Autorizaciones Aprobadas
        $autorizaciones = Autorizacion::where('estado', 'Aprobada')->get();
        if ($autorizaciones->count() < 120) {
            // Generar autorizaciones aprobadas adicionales para poder reclamar
            for ($i = 0; $i < 150; $i++) {
                $afiliado = $afiliados->random();
                $pss = $pssList->random();
                $monto = rand(500, 15000);
                
                $fecha = Carbon::now()->subDays(rand(5, 120));
                $numAut = 'AUT-' . $fecha->format('Ymd') . '-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

                $autorizaciones->push(Autorizacion::create([
                    'numero_autorizacion' => $numAut,
                    'afiliado_type' => 'titular',
                    'afiliado_id' => $afiliado->id,
                    'pss_id' => $pss->id,
                    'medico_solicitante' => 'Dr. Prescriptor Ficticio ' . rand(1, 100),
                    'diagnostico' => 'I10 - HTA Esencial',
                    'monto_solicitado' => $monto,
                    'monto_contratado' => $monto,
                    'monto_ars' => $monto * 0.8,
                    'monto_afiliado' => $monto * 0.2,
                    'copago' => $monto * 0.2,
                    'exceso' => 0,
                    'prioridad' => 'Media',
                    'estado' => 'Aprobada',
                    'procedimiento' => 'Procedimiento Médico Ficticio ' . rand(1, 10),
                    'created_at' => $fecha,
                    'updated_at' => $fecha
                ]));
            }
        }

        // Limpiar reclamaciones previas para evitar duplicidad (Sintaxis SQLite)
        DB::statement('PRAGMA foreign_keys = OFF;');
        ClaimGlosa::truncate();
        ClaimAudit::truncate();
        ClaimDocument::truncate();
        AccountPayable::truncate();
        PaymentBatchItem::truncate();
        PaymentBatch::truncate();
        AuthorizationClaim::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // 3. Crear Reclamaciones de Demostración distribuidas por antigüedad
        $estados = [
            'Reclamación recibida',
            'En auditoría de reclamación',
            'Reclamación aprobada',
            'Reclamación objetada',
            'Cuenta por pagar generada',
            'En lote de pago',
            'Pagada',
            'Devuelta por documentos',
            'Pendiente de documento'
        ];

        $reclamacionesCreadas = 0;
        
        // Vamos a recorrer nuestras autorizaciones aprobadas y convertirlas en reclamaciones
        foreach ($autorizaciones as $indexAut => $aut) {
            if ($reclamacionesCreadas >= 105) break;

            // Determinar antigüedad deseada
            if ($reclamacionesCreadas < 30) {
                // 1 a 30 días
                $diasAtras = rand(2, 28);
            } elseif ($reclamacionesCreadas < 55) {
                // 31 a 60 días
                $diasAtras = rand(32, 58);
            } elseif ($reclamacionesCreadas < 70) {
                // 61 a 90 días
                $diasAtras = rand(62, 88);
            } elseif ($reclamacionesCreadas < 80) {
                // +90 días
                $diasAtras = rand(92, 120);
            } else {
                // Aleatorio restante
                $diasAtras = rand(2, 60);
            }

            $fechaRec = Carbon::now()->subDays($diasAtras);
            $claimNum = 'REC-' . $fechaRec->format('Y') . '-' . str_pad($indexAut + 1, 6, '0', STR_PAD_LEFT);
            $invNum = 'FACT-' . rand(100000, 999999);
            $ncf = 'B01' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);

            // Determinar estado basado en el índice
            $status = 'Reclamación recibida';
            if ($reclamacionesCreadas < 15) {
                $status = 'Reclamación recibida';
            } elseif ($reclamacionesCreadas < 30) {
                $status = 'En auditoría de reclamación';
            } elseif ($reclamacionesCreadas < 45) {
                $status = 'Reclamación aprobada';
            } elseif ($reclamacionesCreadas < 60) {
                $status = 'Reclamación objetada';
            } elseif ($reclamacionesCreadas < 75) {
                $status = 'Cuenta por pagar generada';
            } elseif ($reclamacionesCreadas < 90) {
                $status = 'En lote de pago';
            } elseif ($reclamacionesCreadas < 100) {
                $status = 'Pagada';
            } else {
                $status = 'Pendiente de documento';
            }

            $montoReclamado = $aut->monto_solicitado;
            
            // Si es reclamada por un valor mayor a veces (simulación de diferencias)
            if (rand(1, 10) > 8) {
                $montoReclamado = $montoReclamado * 1.15; // 15% más
            }

            $claim = AuthorizationClaim::create([
                'claim_number' => $claimNum,
                'authorization_id' => $aut->id,
                'pss_id' => $aut->pss_id,
                'afiliado_id' => $aut->afiliado_id,
                'invoice_number' => $invNum,
                'ncf' => $ncf,
                'service_date' => $aut->created_at->toDateString(),
                'received_at' => $fechaRec,
                'claimed_amount' => $montoReclamado,
                'authorized_amount' => $aut->monto_solicitado,
                'approved_amount' => 0.0,
                'objected_amount' => 0.0,
                'status' => $status,
                'submitted_by' => 1,
                'received_by' => $auditorId,
                'observations' => 'Cargada mediante importación masiva demo.',
                'created_at' => $fechaRec,
                'updated_at' => $fechaRec
            ]);

            // Crear documentos soporte
            ClaimDocument::create([
                'claim_id' => $claim->id,
                'document_type' => 'Factura Médica',
                'file_path' => 'facturas/demo_factura_' . $claim->id . '.pdf',
                'uploaded_by' => 1,
                'uploaded_at' => $fechaRec,
                'status' => 'Activo'
            ]);
            ClaimDocument::create([
                'claim_id' => $claim->id,
                'document_type' => 'Detalle de Autorización',
                'file_path' => 'autorizaciones/demo_aut_' . $claim->id . '.pdf',
                'uploaded_by' => 1,
                'uploaded_at' => $fechaRec,
                'status' => 'Activo'
            ]);

            // Flujo posterior: Auditoría, Glosas y CXP
            if (in_array($status, ['Reclamación aprobada', 'Reclamación objetada', 'Cuenta por pagar generada', 'En lote de pago', 'Pagada'])) {
                $montoAprobado = $aut->monto_solicitado;
                $montoObjetado = max(0, $montoReclamado - $montoAprobado);

                // Si fue objetada total/parcial
                if ($status === 'Reclamación objetada') {
                    $montoAprobado = $montoReclamado * rand(60, 90) / 100;
                    $montoObjetado = $montoReclamado - $montoAprobado;
                }

                $audit = ClaimAudit::create([
                    'claim_id' => $claim->id,
                    'audit_type' => rand(1, 2) === 1 ? 'Médica' : 'Administrativa',
                    'auditor_id' => $auditorId,
                    'status' => 'Aprobada',
                    'claimed_amount' => $montoReclamado,
                    'approved_amount' => $montoAprobado,
                    'objected_amount' => $montoObjetado,
                    'objection_reason' => $montoObjetado > 0 ? 'Diferencia de tarifas contra contrato vigente' : null,
                    'internal_observation' => 'Auditoría automática aprobada sin objeción médica mayor.',
                    'reviewed_at' => $fechaRec->copy()->addDays(rand(2, 5)),
                ]);

                $claim->update([
                    'approved_amount' => $montoAprobado,
                    'objected_amount' => $montoObjetado,
                ]);

                // Registrar Glosas si hay monto objetado
                if ($montoObjetado > 0) {
                    ClaimGlosa::create([
                        'claim_id' => $claim->id,
                        'audit_id' => $audit->id,
                        'glosa_code' => 'GLO-' . $fechaRec->format('Y') . '-' . str_pad($claim->id, 5, '0', STR_PAD_LEFT),
                        'glosa_type' => 'Administrativa',
                        'objected_service' => $aut->procedimiento ?? 'Servicios Médicos',
                        'objection_reason' => 'Diferencia en tarifas pactadas.',
                        'evidence_reference' => 'Contrato tarifario anexo 3',
                        'original_amount' => $montoReclamado,
                        'objected_amount' => $montoObjetado,
                        'recognized_amount' => $montoAprobado,
                        'status' => 'Notificada a PSS',
                        'created_by' => $auditorId
                    ]);
                }

                // Generar Cuentas por Pagar (CXP)
                if (in_array($status, ['Cuenta por pagar generada', 'En lote de pago', 'Pagada'])) {
                    $apNum = 'CXP-' . $fechaRec->format('Y') . '-' . str_pad($claim->id, 6, '0', STR_PAD_LEFT);
                    $retencion = $claim->pss->tipo_entidad === 'Profesional' ? ($montoAprobado * 0.10) : 0;
                    
                    $ap = AccountPayable::create([
                        'payable_number' => $apNum,
                        'account_payable_number' => $apNum,
                        'claim_id' => $claim->id,
                        'authorization_id' => $aut->id,
                        'pss_id' => $claim->pss_id,
                        'amount' => $montoReclamado,
                        'retained_amount' => $montoObjetado,
                        'gross_amount' => $montoAprobado,
                        'objected_amount' => $montoObjetado,
                        'approved_amount' => $montoAprobado,
                        'tax_withholding_amount' => $retencion,
                        'other_deductions' => 0.0,
                        'net_amount' => $montoAprobado - $retencion,
                        'vendor_type' => 'PSS',
                        'vendor_id' => $claim->pss_id,
                        'status' => 'Contabilizada',
                        'generated_by' => $auditorId,
                        'generated_at' => $fechaRec->copy()->addDays(6),
                        'due_date' => $fechaRec->copy()->addDays(36)
                    ]);
                }
            }

            // Registrar Hito en Timeline
            AuthorizationTimelineEvent::create([
                'authorization_id' => $aut->id,
                'event_type' => 'CLAIM_SUBMITTED',
                'title' => 'Reclamación Digital Recibida',
                'description' => "Se ha recibido la reclamación formal {$claimNum} para esta autorización por valor de DOP " . number_format($montoReclamado, 2),
                'old_status' => 'Aprobada',
                'new_status' => $status,
                'user_id' => 1
            ]);

            $reclamacionesCreadas++;
        }

        // 4. Crear Lotes de Pago
        $payables = AccountPayable::where('status', 'Contabilizada')->get();
        if ($payables->count() >= 15) {
            // Dividir las CXP en 5 lotes de pago
            $chunks = $payables->chunk(3);
            $loteNumIndex = 1;
            foreach ($chunks as $chunk) {
                if ($loteNumIndex > 5) break;

                $batchNum = 'LOTE-PAY-' . Carbon::now()->format('Y') . '-' . str_pad($loteNumIndex, 4, '0', STR_PAD_LEFT);
                $isPaid = $loteNumIndex <= 3; // Lotes del 1 al 3 ya pagados

                $totalLote = $chunk->sum('net_amount');

                $batch = PaymentBatch::create([
                    'batch_number' => $batchNum,
                    'status' => $isPaid ? 'Pagado' : 'Aprobado',
                    'total_amount' => $totalLote,
                    'total_items' => $chunk->count(),
                    'scheduled_payment_date' => Carbon::now()->addDays(rand(1, 10))->toDateString(),
                    'created_by' => $auditorId,
                    'approved_by' => $auditorId,
                    'paid_at' => $isPaid ? Carbon::now()->subDays(rand(1, 5)) : null,
                ]);

                foreach ($chunk as $ap) {
                    PaymentBatchItem::create([
                        'payment_batch_id' => $batch->id,
                        'account_payable_id' => $ap->id,
                        'amount' => $ap->net_amount,
                        'status' => $isPaid ? 'Pagado' : 'En lote de pago'
                    ]);

                    $ap->update([
                        'status' => $isPaid ? 'Pagada' : 'En lote de pago',
                        'payment_entry_id' => $batch->id
                    ]);

                    // Actualizar reclamaciones asociadas
                    $ap->claim->update([
                        'status' => $isPaid ? 'Pagada' : 'En lote de pago'
                    ]);
                    
                    $ap->claim->authorization->update([
                        'estado' => $isPaid ? 'Pagada' : 'En lote de pago'
                    ]);
                }

                $loteNumIndex++;
            }
        }
    }
}
