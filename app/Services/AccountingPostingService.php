<?php

namespace App\Services;

use App\Models\ChartAccount;
use App\Models\AccountingJournal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingPostingService
{
    /**
     * Genera un número de asiento contable único.
     */
    public static function generarNumeroAsiento(string $journalCode): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $count = JournalEntry::whereYear('created_at', $year)->count() + 1;
        
        return "ASI-{$journalCode}-{$year}{$month}-" . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Registra un asiento contable validando el balance (Débito == Crédito).
     */
    public static function crearAsiento(array $entryData, array $lines): ?JournalEntry
    {
        // Validar si el período contable está cerrado
        $period = $entryData['period'] ?? now()->format('Ym');
        $periodo = PeriodoContable::where('period_code', $period)->first();
        if ($periodo && $periodo->is_closed) {
            throw new \Exception("No se pueden registrar transacciones en el período contable cerrado: {$period}.");
        }

        return DB::transaction(function() use ($entryData, $lines, $period) {
            // Obtener o crear diario
            $journalCode = $entryData['journal_code'] ?? 'GEN';
            $journal = AccountingJournal::firstOrCreate(
                ['code' => $journalCode],
                ['name' => "Diario de {$journalCode}", 'is_active' => true]
            );

            // Crear el asiento
            $entry = JournalEntry::create([
                'entry_number' => self::generarNumeroAsiento($journal->code),
                'journal_id' => $journal->id,
                'entry_date' => $entryData['entry_date'] ?? now()->toDateString(),
                'period' => $period,
                'source_module' => $entryData['source_module'],
                'source_type' => $entryData['source_type'] ?? null,
                'source_id' => $entryData['source_id'] ?? null,
                'description' => $entryData['description'],
                'status' => $entryData['status'] ?? 'posteado',
                'posted_by' => Auth::id() ?? 1,
                'posted_at' => now(),
            ]);

            $totalDebit = 0.0;
            $totalCredit = 0.0;

            foreach ($lines as $line) {
                // Resolver cuenta por código
                $account = ChartAccount::where('code', $line['account_code'])->first();
                if (!$account) {
                    // Si no existe la cuenta, crearla automáticamente con su naturaleza por defecto
                    $class = intval(substr($line['account_code'], 0, 1));
                    $nature = in_array($class, [1, 5]) ? 'debito' : 'credito';
                    $account = ChartAccount::create([
                        'code' => $line['account_code'],
                        'name' => $line['account_name'] ?? "Cuenta Autogenerada {$line['account_code']}",
                        'account_class' => $class,
                        'level' => strlen($line['account_code']) <= 4 ? 1 : 2,
                        'account_type' => 'auxiliar',
                        'nature' => $nature,
                        'is_postable' => true
                    ]);
                }

                $debit = floatval($line['debit'] ?? 0);
                $credit = floatval($line['credit'] ?? 0);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $account->id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'description' => $line['description'] ?? $entry->description,
                    'third_party_type' => $line['third_party_type'] ?? null,
                    'third_party_id' => $line['third_party_id'] ?? null,
                ]);

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            // Validar Partida Doble
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new \Exception("Asiento contable descuadrado: Total Débito ($totalDebit) != Total Crédito ($totalCredit).");
            }

            $entry->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit
            ]);

            return $entry;
        });
    }

    /**
     * 1. Registrar dispersión de cápitas (devengado).
     */
    public static function registrarDispersion($corte): ?JournalEntry
    {
        $period = $corte->period;
        
        $lines = [
            [
                'account_code' => '11030101',
                'account_name' => 'Aportaciones y contribuciones por cobrar régimen contributivo',
                'debit' => $corte->total_amount,
                'credit' => 0.0,
            ],
            [
                'account_code' => '41010101',
                'account_name' => 'Aportaciones y contribuciones suscritas régimen contributivo',
                'debit' => 0.0,
                'credit' => $corte->total_amount,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'VTAS',
            'period' => $period,
            'source_module' => 'dispersion',
            'source_type' => get_class($corte),
            'source_id' => $corte->id,
            'description' => "Devengado de cápitas para el período {$period} - Corte {$corte->cut_number}."
        ], $lines);
    }

    /**
     * 1b. Recibir efectivo de dispersión.
     */
    public static function registrarCobroDispersion($corte, float $montoRecibido): ?JournalEntry
    {
        $period = $corte->period;

        $lines = [
            [
                'account_code' => '110204',
                'account_name' => 'Cuentas corrientes en bancos moneda nacional',
                'debit' => $montoRecibido,
                'credit' => 0.0,
            ],
            [
                'account_code' => '11030101',
                'account_name' => 'Aportaciones y contribuciones por cobrar régimen contributivo',
                'debit' => 0.0,
                'credit' => $montoRecibido,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'CGER',
            'period' => $period,
            'source_module' => 'dispersion',
            'source_type' => get_class($corte),
            'source_id' => $corte->id,
            'description' => "Ingreso de caja por cobro de cápitas - Corte {$corte->cut_number}."
        ], $lines);
    }

    /**
     * 3. Reserva de reclamaciones recibidas pendientes de liquidación (cuando entra la reclamación).
     */
    public static function registrarReclamacionRecibida($claim): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '5113',
                'account_name' => 'Reservas de reclamaciones por prestaciones de servicios del presente ejercicio',
                'debit' => $claim->claimed_amount,
                'credit' => 0.0,
            ],
            [
                'account_code' => '210103',
                'account_name' => 'Reservas de reclamaciones pendientes de liquidación',
                'debit' => 0.0,
                'credit' => $claim->claimed_amount,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'COMP',
            'source_module' => 'reclamaciones',
            'source_type' => get_class($claim),
            'source_id' => $claim->id,
            'description' => "Registro de reserva para reclamación recibida {$claim->claim_number} de la PSS ID {$claim->pss_id}."
        ], $lines);
    }

    /**
     * 4. Reclamación liquidada y aprobada (mueve de pendientes de liquidación a liquidadas).
     */
    public static function registrarReclamacionAuditada($claim): ?JournalEntry
    {
        // Se reversa la reserva anterior y se crea la liquidada por el monto aprobado
        $lines = [
            [
                'account_code' => '210103',
                'account_name' => 'Reservas de reclamaciones pendientes de liquidación',
                'debit' => $claim->claimed_amount,
                'credit' => 0.0,
            ],
            [
                'account_code' => '21010202',
                'account_name' => 'Reservas de reclamaciones liquidadas y pendientes de pago',
                'debit' => 0.0,
                'credit' => $claim->approved_amount,
            ]
        ];

        // Si hay una glosa (diferencia no aprobada), se ajusta contra el gasto
        $diferencia = $claim->claimed_amount - $claim->approved_amount;
        if ($diferencia > 0) {
            $lines[] = [
                'account_code' => '5113',
                'account_name' => 'Reservas de reclamaciones por prestaciones de servicios',
                'debit' => 0.0,
                'credit' => $diferencia,
                'description' => 'Ajuste de reserva por glosa aplicada'
            ];
        }

        return self::crearAsiento([
            'journal_code' => 'COMP',
            'source_module' => 'reclamaciones',
            'source_type' => get_class($claim),
            'source_id' => $claim->id,
            'description' => "Liquidación y auditoría de la reclamación {$claim->claim_number}. Monto Aprobado: {$claim->approved_amount}."
        ], $lines);
    }

    /**
     * 5. Crear cuenta por pagar PSS (CXP).
     */
    public static function registrarCuentaPorPagar($payable): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '21010202',
                'account_name' => 'Reservas de reclamaciones liquidadas y pendientes de pago',
                'debit' => $payable->approved_amount,
                'credit' => 0.0,
            ],
            [
                'account_code' => '210501',
                'account_name' => 'Cuentas por pagar suplidores (PSS)',
                'debit' => 0.0,
                'credit' => $payable->net_amount,
                'third_party_type' => 'pss',
                'third_party_id' => $payable->pss_id,
            ]
        ];

        // Si aplica retención de impuestos (ej: 10% ISR a profesionales)
        if ($payable->tax_withholding_amount > 0) {
            $lines[] = [
                'account_code' => '210706',
                'account_name' => 'Retenciones de ISR a prestadores de servicios de salud',
                'debit' => 0.0,
                'credit' => $payable->tax_withholding_amount,
                'third_party_type' => 'pss',
                'third_party_id' => $payable->pss_id,
            ];
        }

        return self::crearAsiento([
            'journal_code' => 'CXP',
            'source_module' => 'pagos',
            'source_type' => get_class($payable),
            'source_id' => $payable->id,
            'description' => "Generación de CXP {$payable->payable_number} para la PSS ID {$payable->pss_id}."
        ], $lines);
    }

    /**
     * 6. Pago físico de lote a PSS (Banco).
     */
    public static function registrarPagoLote($batch): ?JournalEntry
    {
        $lines = [];
        $totalPaid = 0.0;

        foreach ($batch->items as $item) {
            $payable = $item->payable;
            $lines[] = [
                'account_code' => '210501',
                'account_name' => 'Cuentas por pagar suplidores (PSS)',
                'debit' => $payable->net_amount,
                'credit' => 0.0,
                'third_party_type' => 'pss',
                'third_party_id' => $payable->pss_id,
            ];
            $totalPaid += $payable->net_amount;
        }

        $lines[] = [
            'account_code' => '110204',
            'account_name' => 'Cuentas corrientes en bancos moneda nacional',
            'debit' => 0.0,
            'credit' => $totalPaid,
        ];

        return self::crearAsiento([
            'journal_code' => 'PAGS',
            'source_module' => 'pagos',
            'source_type' => get_class($batch),
            'source_id' => $batch->id,
            'description' => "Pago de lote de facturas PSS {$batch->batch_number}. Total: {$totalPaid}."
        ], $lines);
    }

    /**
     * 7. Reconocimiento de Gasto de Reclamaciones Pagadas (Al finalizar el pago).
     */
    public static function registrarGastoReclamacion($payable): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '51010101',
                'account_name' => 'Reclamaciones pagadas por prestaciones de servicios régimen contributivo',
                'debit' => $payable->net_amount,
                'credit' => 0.0,
            ],
            [
                'account_code' => '21010202',
                'account_name' => 'Reservas de reclamaciones liquidadas y pendientes de pago',
                'debit' => 0.0,
                'credit' => $payable->net_amount,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'CGER',
            'source_module' => 'pagos',
            'source_type' => get_class($payable),
            'source_id' => $payable->id,
            'description' => "Reconocimiento de gasto por reclamación pagada para CXP {$payable->payable_number}."
        ], $lines);
    }

    /**
     * 9. Reembolso excepcional de Afiliado (Negación de cobertura o Cobro indebido).
     */
    public static function registrarReembolsoCaso($case): ?JournalEntry
    {
        $lines = [];

        if ($case->request_type === 'negacion_cobertura') {
            // ARS asume el gasto directamente
            $lines[] = [
                'account_code' => '51010101',
                'account_name' => 'Reclamaciones pagadas por prestaciones de servicios régimen contributivo',
                'debit' => $case->approved_amount,
                'credit' => 0.0,
            ];
            $lines[] = [
                'account_code' => '110204',
                'account_name' => 'Cuentas corrientes en bancos moneda nacional',
                'debit' => 0.0,
                'credit' => $case->approved_amount,
                'third_party_type' => 'afiliado',
                'third_party_id' => $case->afiliado_id,
            ];
        } else {
            // Cobro indebido de PSS: se le paga al afiliado y se crea una cuenta por cobrar a la PSS
            $lines[] = [
                'account_code' => '110406',
                'account_name' => 'Otras cuentas por cobrar PSS por reembolsos debitados',
                'debit' => $case->approved_amount,
                'credit' => 0.0,
                'third_party_type' => 'pss',
                'third_party_id' => $case->pss_id,
            ];
            $lines[] = [
                'account_code' => '110204',
                'account_name' => 'Cuentas corrientes en bancos moneda nacional',
                'debit' => 0.0,
                'credit' => $case->approved_amount,
                'third_party_type' => 'afiliado',
                'third_party_id' => $case->afiliado_id,
            ];
        }

        return self::crearAsiento([
            'journal_code' => 'EGRE',
            'source_module' => 'reembolsos',
            'source_type' => get_class($case),
            'source_id' => $case->id,
            'description' => "Pago de reembolso excepcional caso {$case->case_number} al afiliado ID {$case->afiliado_id}."
        ], $lines);
    }

    /**
     * Provisión contable cuando se aprueba una pre-autorización médica.
     */
    public static function registrarPreautorizacionAprobada($autorizacion): ?JournalEntry
    {
        $montoArs = floatval($autorizacion->monto_ars ?? $autorizacion->monto_solicitado);
        if ($montoArs <= 0) return null;

        $lines = [
            [
                'account_code' => '5113',
                'account_name' => 'Reservas de reclamaciones por prestaciones de servicios del presente ejercicio',
                'debit' => $montoArs,
                'credit' => 0.0,
            ],
            [
                'account_code' => '210103',
                'account_name' => 'Reservas de reclamaciones pendientes de liquidación',
                'debit' => 0.0,
                'credit' => $montoArs,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'GEN',
            'source_module' => 'autorizaciones',
            'source_type' => get_class($autorizacion),
            'source_id' => $autorizacion->id,
            'description' => "Provisión contable por pre-autorización médica aprobada {$autorizacion->numero_autorizacion}."
        ], $lines);
    }

    /**
     * 9b. Compensar cuenta por cobrar de PSS contra sus cuentas por pagar (Débito a PSS).
     */
    public static function registrarCompensacionPss($case): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '210501',
                'account_name' => 'Cuentas por pagar suplidores (PSS)',
                'debit' => $case->approved_amount,
                'credit' => 0.0,
                'third_party_type' => 'pss',
                'third_party_id' => $case->pss_id,
            ],
            [
                'account_code' => '110406',
                'account_name' => 'Otras cuentas por cobrar PSS por reembolsos debitados',
                'debit' => 0.0,
                'credit' => $case->approved_amount,
                'third_party_type' => 'pss',
                'third_party_id' => $case->pss_id,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'CGER',
            'source_module' => 'reembolsos',
            'source_type' => get_class($case),
            'source_id' => $case->id,
            'description' => "Compensación y débito automático a la PSS ID {$case->pss_id} por reembolso de cobro indebido caso {$case->case_number}."
        ], $lines);
    }

    /**
     * 10. Comisión de Promotores de Salud.
     */
    public static function registrarComisionPromotor($promotorId, float $montoComision): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '51020101',
                'account_name' => 'Comisiones a promotores Plan Básico Régimen Contributivo',
                'debit' => $montoComision,
                'credit' => 0.0,
            ],
            [
                'account_code' => '2104',
                'account_name' => 'Obligaciones con promotores de salud',
                'debit' => 0.0,
                'credit' => $montoComision,
                'third_party_type' => 'promotor',
                'third_party_id' => $promotorId,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'CXP',
            'source_module' => 'promotores',
            'source_id' => $promotorId,
            'description' => "Devengado de comisión para el promotor ID {$promotorId}."
        ], $lines);
    }

    /**
     * 10b. Pago de comisión a promotor.
     */
    public static function registrarPagoPromotor($promotorId, float $montoPagado): ?JournalEntry
    {
        $lines = [
            [
                'account_code' => '2104',
                'account_name' => 'Obligaciones con promotores de salud',
                'debit' => $montoPagado,
                'credit' => 0.0,
                'third_party_type' => 'promotor',
                'third_party_id' => $promotorId,
            ],
            [
                'account_code' => '110204',
                'account_name' => 'Cuentas corrientes en bancos moneda nacional',
                'debit' => 0.0,
                'credit' => $montoPagado,
            ]
        ];

        return self::crearAsiento([
            'journal_code' => 'PAGS',
            'source_module' => 'promotores',
            'source_id' => $promotorId,
            'description' => "Desembolso de pago de comisiones a promotor ID {$promotorId}."
        ], $lines);
    }
}
