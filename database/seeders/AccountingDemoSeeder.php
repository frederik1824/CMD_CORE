<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChartAccount;
use App\Models\PeriodoContable;
use App\Models\AccountingJournal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Pss;
use App\Models\Afiliado;
use Carbon\Carbon;

class AccountingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function() {
            // 1. Limpiar tablas contables
            JournalEntryLine::query()->delete();
            JournalEntry::query()->delete();
            PeriodoContable::query()->delete();
            ChartAccount::query()->delete();
            AccountingJournal::query()->delete();

            // 2. Crear Diarios Contables Básicos
            $diarios = [
                ['code' => 'ING', 'name' => 'Diario de Ingresos', 'description' => 'Registro de cápitas y transferencias recibidas.'],
                ['code' => 'EGR', 'name' => 'Diario de Egresos / Pagos', 'description' => 'Registro de transferencias a PSS y reembolsos.'],
                ['code' => 'PRO', 'name' => 'Diario de Provisiones', 'description' => 'Provisiones de reclamaciones médicas.'],
                ['code' => 'GEN', 'name' => 'Diario General', 'description' => 'Ajustes, amortizaciones y reclasificaciones contables.'],
            ];
            foreach ($diarios as $d) {
                AccountingJournal::create($d);
            }
            $diarioIng = AccountingJournal::where('code', 'ING')->first();
            $diarioEgr = AccountingJournal::where('code', 'EGR')->first();
            $diarioPro = AccountingJournal::where('code', 'PRO')->first();
            $diarioGen = AccountingJournal::where('code', 'GEN')->first();

            // 3. Crear Catálogo de Cuentas Decimal (SISALRIL Adaptado)
            $catalogo = [
                // 1 - ACTIVO
                ['code' => '1', 'name' => 'ACTIVO', 'account_class' => 1, 'level' => 1, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '11', 'name' => 'ACTIVOS CORRIENTES', 'account_class' => 1, 'level' => 2, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '1102', 'name' => 'EFECTIVO Y EQUIVALENTES DE EFECTIVO', 'account_class' => 1, 'level' => 3, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '110204', 'name' => 'Banco de Reservas de la República Dominicana', 'account_class' => 1, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '110205', 'name' => 'Banco BHD León', 'account_class' => 1, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '1103', 'name' => 'CUENTAS POR COBRAR OPERATIVAS', 'account_class' => 1, 'level' => 3, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '110301', 'name' => 'Cápitas por Cobrar SNS (Unipago)', 'account_class' => 1, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '1104', 'name' => 'OTRAS CUENTAS POR COBRAR', 'account_class' => 1, 'level' => 3, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '110406', 'name' => 'Cuentas por Cobrar a Prestadoras (PSS) por Cobros Indebidos', 'account_class' => 1, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],

                // 2 - PASIVO
                ['code' => '2', 'name' => 'PASIVO', 'account_class' => 2, 'level' => 1, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '21', 'name' => 'PASIVOS CORRIENTES', 'account_class' => 2, 'level' => 2, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                
                // Reservas Técnicas
                ['code' => '2101', 'name' => 'RESERVAS TECNICAS CONSTITUIDAS', 'account_class' => 2, 'level' => 3, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '210101', 'name' => 'Reserva de Cápitas No Devengadas (SNS)', 'account_class' => 2, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '210102', 'name' => 'Reserva de Siniestros Liquidados Pendientes de Pago (CXP PSS)', 'account_class' => 2, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '210103', 'name' => 'Reserva de Siniestros en Trámite Pendientes de Liquidación', 'account_class' => 2, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                
                // Cuentas por pagar
                ['code' => '2105', 'name' => 'CUENTAS POR PAGAR OPERATIVAS', 'account_class' => 2, 'level' => 3, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '210501', 'name' => 'Cuentas por Pagar a PSS Red de Servicios', 'account_class' => 2, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '210502', 'name' => 'Cuentas por Pagar Reembolsos de Afiliados', 'account_class' => 2, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],

                // 3 - PATRIMONIO / CAPITAL
                ['code' => '3', 'name' => 'PATRIMONIO', 'account_class' => 3, 'level' => 1, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '31', 'name' => 'CAPITAL SOCIAL', 'account_class' => 3, 'level' => 2, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '3101', 'name' => 'Capital Social Autorizado', 'account_class' => 3, 'level' => 3, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '3102', 'name' => 'Patrimonio Técnico Legal (SISALRIL)', 'account_class' => 3, 'level' => 3, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '3301', 'name' => 'Utilidades Acumuladas de Ejercicios Anteriores', 'account_class' => 3, 'level' => 3, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],

                // 4 - INGRESOS
                ['code' => '4', 'name' => 'INGRESOS', 'account_class' => 4, 'level' => 1, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '41', 'name' => 'INGRESOS OPERACIONALES POR SEGUROS', 'account_class' => 4, 'level' => 2, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '4101', 'name' => 'Ingresos por Cápita de Seguro Familiar de Salud (SFS)', 'account_class' => 4, 'level' => 3, 'nature' => 'credito', 'is_postable' => false, 'is_system' => true],
                ['code' => '410101', 'name' => 'Ingresos Cápita SFS Régimen Contributivo (Unipago)', 'account_class' => 4, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],
                ['code' => '410102', 'name' => 'Ingresos Cápita SFS Régimen Subsidiado (SNS)', 'account_class' => 4, 'level' => 4, 'nature' => 'credito', 'is_postable' => true, 'is_system' => true],

                // 5 - GASTOS
                ['code' => '5', 'name' => 'GASTOS', 'account_class' => 5, 'level' => 1, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '51', 'name' => 'GASTOS DE OPERACIÓN DE SALUD', 'account_class' => 5, 'level' => 2, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                
                // Gastos por Siniestros
                ['code' => '5101', 'name' => 'Gastos por Prestación de Servicios de Salud (Siniestros)', 'account_class' => 5, 'level' => 3, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '510101', 'name' => 'Gasto de Hospitalización y Clínicas', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '510102', 'name' => 'Gasto de Servicios de Farmacia / Medicamentos', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '510103', 'name' => 'Gasto de Consultas Médicas y Especialistas', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '510104', 'name' => 'Gasto de Reembolsos Autorizados a Afiliados', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],

                // Gastos Administrativos
                ['code' => '5102', 'name' => 'Gastos Administrativos y de Comercialización', 'account_class' => 5, 'level' => 3, 'nature' => 'debito', 'is_postable' => false, 'is_system' => true],
                ['code' => '510201', 'name' => 'Gastos de Comisión a Promotores de Salud', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
                ['code' => '510202', 'name' => 'Gasto de Administración de Unipago / Unisigma', 'account_class' => 5, 'level' => 4, 'nature' => 'debito', 'is_postable' => true, 'is_system' => true],
            ];
            foreach ($catalogo as $cat) {
                ChartAccount::create($cat);
            }

            // 4. Crear 12 Períodos Contables Históricos (Cerrando los primeros 11)
            $mesActual = Carbon::now()->startOfMonth();
            $periodosCreados = [];
            for ($i = 11; $i >= 0; $i--) {
                $monthDate = (clone $mesActual)->subMonths($i);
                $pCode = $monthDate->format('Y-m');
                $isClosed = $i > 0; // Solo el mes actual queda abierto

                $periodo = PeriodoContable::create([
                    'period_code' => $pCode,
                    'start_date' => $monthDate->clone()->startOfMonth(),
                    'end_date' => $monthDate->clone()->endOfMonth(),
                    'is_closed' => $isClosed,
                    'closed_at' => $isClosed ? $monthDate->clone()->endOfMonth() : null,
                ]);

                $periodosCreados[] = $periodo;
            }

            // 5. Asientos Históricos Demo (Generar movimientos realistas para cada mes cerrado)
            $ctaBancos = ChartAccount::where('code', '110204')->first();
            $ctaCapital = ChartAccount::where('code', '3101')->first();
            $ctaPatrimonioTec = ChartAccount::where('code', '3102')->first();
            
            $ctaCápitasSNS = ChartAccount::where('code', '110301')->first();
            $ctaIngresosCapita = ChartAccount::where('code', '410101')->first();
            
            $ctaReservaTransito = ChartAccount::where('code', '210103')->first();
            $ctaGastoHosp = ChartAccount::where('code', '510101')->first();
            $ctaGastoUnipago = ChartAccount::where('code', '510202')->first();
            $ctaCxpPss = ChartAccount::where('code', '210501')->first();

            // 5.1 Aporte de Capital Inicial (En el primer período contable creado)
            $primerPer = $periodosCreados[0];
            $primerMesStart = $primerPer->start_date;

            $entryCap = JournalEntry::create([
                'entry_number' => 'AS-CAP-INIT-' . $primerPer->period_code,
                'entry_date' => $primerMesStart,
                'period' => $primerPer->period_code,
                'journal_id' => $diarioGen->id,
                'description' => 'Aporte inicial de capital social y reserva técnica de patrimonio técnico legal para constitución de la ARS.',
                'source_module' => 'sistema',
                'total_debit' => 50000000.00,
                'total_credit' => 50000000.00,
                'status' => 'posteado',
            ]);

            // Débito a Banco de Reservas
            JournalEntryLine::create([
                'journal_entry_id' => $entryCap->id,
                'account_id' => $ctaBancos->id,
                'debit' => 50000000.00,
                'credit' => 0.00,
                'description' => 'Ingreso de aporte inicial a Banco de Reservas',
            ]);

            // Crédito a Capital Social (35,000,000)
            JournalEntryLine::create([
                'journal_entry_id' => $entryCap->id,
                'account_id' => $ctaCapital->id,
                'debit' => 0.00,
                'credit' => 35000000.00,
                'description' => 'Capital Social Suscrito y Pagado',
            ]);

            // Crédito a Patrimonio Técnico (15,000,000)
            JournalEntryLine::create([
                'journal_entry_id' => $entryCap->id,
                'account_id' => $ctaPatrimonioTec->id,
                'debit' => 0.00,
                'credit' => 15000000.00,
                'description' => 'Reserva Técnica Patrimonio Técnico de Solvencia',
            ]);

            // 5.2 Generar dispersiones de cápitas mensuales y provisiones de siniestralidad
            $pssList = Pss::take(5)->get();
            $afiliadosList = Afiliado::take(10)->get();

            foreach ($periodosCreados as $idx => $per) {
                // Para cada mes, registramos:
                // A) Ingreso de cápitas por RD$ 4,800,000
                // B) Provisión de reclamaciones médicas por RD$ 3,850,000 (80.2% de siniestralidad promedio)
                // C) Pagos a PSS por RD$ 3,200,000

                $fechaAsiento = $per->start_date->clone()->addDays(10); // Día 10 de cada mes

                // A) Asiento de Ingreso Cápita (Devengo de Cápitas Unipago)
                $montoCapita = 4800000.00;
                $montoComisionUnipago = 48000.00; // 1% comisión
                $montoNetoRecibido = $montoCapita - $montoComisionUnipago;

                $entryCapita = JournalEntry::create([
                    'entry_number' => 'AS-CAPITA-' . $per->period_code . '-001',
                    'entry_date' => $fechaAsiento,
                    'period' => $per->period_code,
                    'journal_id' => $diarioIng->id,
                    'description' => "Devengamiento de cápitas del Seguro Familiar de Salud SFS correspondientes al Régimen Contributivo, período {$per->period_code}.",
                    'source_module' => 'unipago',
                    'total_debit' => $montoCapita,
                    'total_credit' => $montoCapita,
                    'status' => 'posteado',
                ]);

                // Débito a Banco de Reservas (Monto Neto recibido)
                JournalEntryLine::create([
                    'journal_entry_id' => $entryCapita->id,
                    'account_id' => $ctaBancos->id,
                    'debit' => $montoNetoRecibido,
                    'credit' => 0.00,
                    'description' => 'Recibo de transferencia cápita neta dispersada por Unipago',
                ]);

                // Débito a Gasto de Administración Unipago (Comisión)
                JournalEntryLine::create([
                    'journal_entry_id' => $entryCapita->id,
                    'account_id' => $ctaGastoUnipago->id,
                    'debit' => $montoComisionUnipago,
                    'credit' => 0.00,
                    'description' => 'Gasto de comisión operativa por recaudo Unipago',
                ]);

                // Crédito a Ingresos Cápita SFS
                JournalEntryLine::create([
                    'journal_entry_id' => $entryCapita->id,
                    'account_id' => $ctaIngresosCapita->id,
                    'debit' => 0.00,
                    'credit' => $montoCapita,
                    'description' => 'Ingreso devengado de cápitas mensuales SFS',
                ]);

                // B) Provisión de Siniestros (Reclamaciones médicas presentadas y en trámite)
                $montoSiniestros = 3850000.00;
                $entrySiniestro = JournalEntry::create([
                    'entry_number' => 'AS-PROV-' . $per->period_code . '-001',
                    'entry_date' => $per->start_date->clone()->addDays(25), // Fin de mes
                    'period' => $per->period_code,
                    'journal_id' => $diarioPro->id,
                    'description' => "Provisión mensual de siniestros acumulados por reclamaciones médicas del período {$per->period_code}.",
                    'source_module' => 'reclamaciones',
                    'total_debit' => $montoSiniestros,
                    'total_credit' => $montoSiniestros,
                    'status' => 'posteado',
                ]);

                // Débito al Gasto Clínicas y Hospitalización
                JournalEntryLine::create([
                    'journal_entry_id' => $entrySiniestro->id,
                    'account_id' => $ctaGastoHosp->id,
                    'debit' => $montoSiniestros,
                    'credit' => 0.00,
                    'description' => 'Reconocimiento de gastos por servicios de salud prestados',
                ]);

                // Crédito a la Reserva Técnica de Siniestros en Trámite
                JournalEntryLine::create([
                    'journal_entry_id' => $entrySiniestro->id,
                    'account_id' => $ctaReservaTransito->id,
                    'debit' => 0.00,
                    'credit' => $montoSiniestros,
                    'description' => 'Constitución de pasivo de reserva para liquidación de siniestros en trámite',
                ]);

                // C) Pago de Siniestros (Disminución de reservas y salida de bancos)
                // En el primer mes no hay pagos tan grandes, pero a partir del segundo mes pagamos parte del anterior
                $montoPagos = 3200000.00;
                $entryPago = JournalEntry::create([
                    'entry_number' => 'AS-PAGO-' . $per->period_code . '-001',
                    'entry_date' => $per->start_date->clone()->addDays(28),
                    'period' => $per->period_code,
                    'journal_id' => $diarioEgr->id,
                    'description' => "Desembolso masivo para pago de lotes conciliados a prestadoras de la red, mes {$per->period_code}.",
                    'source_module' => 'pagos',
                    'total_debit' => $montoPagos,
                    'total_credit' => $montoPagos,
                    'status' => 'posteado',
                ]);

                // Débito a Reserva Técnica (Liberación de pasivo provisión)
                JournalEntryLine::create([
                    'journal_entry_id' => $entryPago->id,
                    'account_id' => $ctaReservaTransito->id,
                    'debit' => $montoPagos,
                    'credit' => 0.00,
                    'description' => 'Liberación de pasivos de siniestros liquidados y pagados a PSS',
                ]);

                // Crédito a Banco de Reservas
                JournalEntryLine::create([
                    'journal_entry_id' => $entryPago->id,
                    'account_id' => $ctaBancos->id,
                    'debit' => 0.00,
                    'credit' => $montoPagos,
                    'description' => 'Egreso de transferencia de fondos a prestadoras PSS',
                ]);
            }
        });
    }
}
