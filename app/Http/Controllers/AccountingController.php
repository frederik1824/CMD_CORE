<?php

namespace App\Http\Controllers;

use App\Models\ChartAccount;
use App\Models\AccountingJournal;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PeriodoContable;
use App\Models\CapitationNotification;
use App\Models\AuthorizationClaim;
use App\Models\AccountPayable;
use App\Services\AccountingPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AccountingController extends Controller
{
    /**
     * Dashboard contable y financiero.
     */
    public function dashboard()
    {
        $periodoActual = PeriodoContable::orderBy('period_code', 'desc')->first();
        $periodCode = $periodoActual ? $periodoActual->period_code : date('Ym');

        // Métricas rápidas
        $ingresosCapita = CapitationNotification::where('period', $periodCode)
            ->whereIn('status', ['IC', 'DI'])
            ->sum('capitation_amount');

        $reclamacionesRecibidas = AuthorizationClaim::where('status', '!=', 'Anulada')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('claimed_amount');

        $reclamacionesAprobadas = AuthorizationClaim::whereIn('status', ['Aprobada', 'Cuenta por pagar generada', 'Pagada', 'Conciliada', 'Cerrada'])
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('approved_amount');

        $cuentasPorPagar = AccountPayable::whereIn('status', ['Generada', 'Validada', 'En lote de pago'])->sum('net_amount');
        
        // Sumar cuentas por cobrar en base al balance de la cuenta 1103 (Aportaciones por cobrar)
        $cuentasPorCobrar = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '1103%')
              ->orWhere('code', 'like', '1104%');
        })->select(DB::raw('SUM(debit) - SUM(credit) as balance'))->value('balance') ?? 0.0;

        // Siniestralidad: Reclamaciones Aprobadas / Ingresos Cápita
        $siniestralidad = $ingresosCapita > 0 ? ($reclamacionesAprobadas / $ingresosCapita) * 100 : 0.0;

        // Reservas técnicas
        $reservasNoDevengadas = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '210101%');
        })->select(DB::raw('SUM(credit) - SUM(debit) as balance'))->value('balance') ?? 0.0;

        $reservasPendientePago = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '210102%');
        })->select(DB::raw('SUM(credit) - SUM(debit) as balance'))->value('balance') ?? 0.0;

        $reservasPendienteLiquidacion = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '210103%');
        })->select(DB::raw('SUM(credit) - SUM(debit) as balance'))->value('balance') ?? 0.0;

        $totalReservas = $reservasNoDevengadas + $reservasPendientePago + $reservasPendienteLiquidacion;

        // Margen de Solvencia Requerido (Simulado al 10% de los ingresos de los últimos 12 meses)
        $patrimonioTecnico = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '3%');
        })->select(DB::raw('SUM(credit) - SUM(debit) as balance'))->value('balance') ?? 0.0;

        $margenSolvencia = $ingresosCapita * 0.15; // 15% de ingresos del mes como margen mínimo

        // Activos Líquidos (Bancos - Cuenta 1102)
        $efectivoBancos = JournalEntryLine::whereHas('account', function($q) {
            $q->where('code', 'like', '1102%');
        })->select(DB::raw('SUM(debit) - SUM(credit) as balance'))->value('balance') ?? 0.0;

        return view('ars.contabilidad.dashboard', compact(
            'periodCode',
            'ingresosCapita',
            'reclamacionesRecibidas',
            'reclamacionesAprobadas',
            'cuentasPorPagar',
            'cuentasPorCobrar',
            'siniestralidad',
            'totalReservas',
            'reservasNoDevengadas',
            'reservasPendientePago',
            'reservasPendienteLiquidacion',
            'patrimonioTecnico',
            'margenSolvencia',
            'efectivoBancos',
            'periodoActual'
        ));
    }

    /**
     * Catálogo de cuentas contables.
     */
    public function catalogo()
    {
        $cuentas = ChartAccount::orderBy('code')->get();
        return view('ars.contabilidad.catalogo', compact('cuentas'));
    }

    /**
     * Libro Diario y Asientos Contables.
     */
    public function asientos(Request $request)
    {
        $period = $request->get('period');
        $module = $request->get('module');

        $query = JournalEntry::with(['journal', 'poster']);

        if ($period) $query->where('period', $period);
        if ($module) $query->where('source_module', $module);

        $entries = $query->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        $periodos = PeriodoContable::orderBy('period_code', 'desc')->get();
        $modulos = ['dispersion', 'reclamaciones', 'pagos', 'reembolsos', 'promotores', 'manual'];

        return view('ars.contabilidad.asientos', compact('entries', 'periodos', 'modulos', 'period', 'module'));
    }

    /**
     * Detalle de un Asiento Contable.
     */
    public function asientoShow($id)
    {
        $entry = JournalEntry::with(['lines.account', 'journal', 'poster'])->findOrFail($id);
        return view('ars.contabilidad.asiento_show', compact('entry'));
    }

    /**
     * Consulta del Mayor General para una cuenta contable.
     */
    public function mayor(Request $request)
    {
        $accountId = $request->get('account_id');
        $period = $request->get('period', date('Ym'));

        $accounts = ChartAccount::where('is_postable', true)->orderBy('code')->get();
        $selectedAccount = $accountId ? ChartAccount::find($accountId) : null;
        $periodos = PeriodoContable::orderBy('period_code', 'desc')->get();

        $lines = [];
        $saldoInicial = 0.0;
        $saldoFinal = 0.0;

        if ($selectedAccount) {
            // Calcular saldo anterior (acumulado de débitos y créditos antes de la fecha de inicio del período)
            $periodoObj = PeriodoContable::where('period_code', $period)->first();
            $startDate = $periodoObj ? $periodoObj->start_date : now()->startOfMonth()->toDateString();

            $movimientosPrevios = JournalEntryLine::where('account_id', $selectedAccount->id)
                ->whereHas('entry', function($q) use ($startDate) {
                    $q->where('entry_date', '<', $startDate)
                      ->where('status', 'posteado');
                })
                ->select(DB::raw('SUM(debit) as debito, SUM(credit) as credito'))
                ->first();

            $prevDebit = $movimientosPrevios->debito ?? 0.0;
            $prevCredit = $movimientosPrevios->credito ?? 0.0;
            $saldoInicial = $selectedAccount->nature === 'debito' ? ($prevDebit - $prevCredit) : ($prevCredit - $prevDebit);

            // Obtener movimientos del período
            $lines = JournalEntryLine::where('account_id', $selectedAccount->id)
                ->whereHas('entry', function($q) use ($period) {
                    $q->where('period', $period)
                      ->where('status', 'posteado');
                })
                ->with('entry')
                ->get()
                ->sortBy(function($line) {
                    return $line->entry->entry_date->toDateString() . $line->created_at;
                });

            // Calcular saldo final
            $totalDebit = $lines->sum('debit');
            $totalCredit = $lines->sum('credit');
            if ($selectedAccount->nature === 'debito') {
                $saldoFinal = $saldoInicial + ($totalDebit - $totalCredit);
            } else {
                $saldoFinal = $saldoInicial + ($totalCredit - $totalDebit);
            }
        }

        return view('ars.contabilidad.mayor', compact(
            'accounts', 'selectedAccount', 'lines', 'saldoInicial', 'saldoFinal', 'periodos', 'period', 'accountId'
        ));
    }

    /**
     * Visualización de Balances y Estados Financieros.
     */
    public function balances(Request $request)
    {
        $period = $request->get('period', date('Ym'));
        $periodos = PeriodoContable::orderBy('period_code', 'desc')->get();

        // 1. Balance de Comprobación
        $comprobacion = DB::table('chart_accounts as c')
            ->leftJoin('journal_entry_lines as l', 'c.id', '=', 'l.account_id')
            ->leftJoin('journal_entries as e', function($join) use ($period) {
                $join->on('l.journal_entry_id', '=', 'e.id')
                     ->where('e.period', '=', $period)
                     ->where('e.status', '=', 'posteado');
            })
            ->select(
                'c.code',
                'c.name',
                'c.nature',
                'c.account_type',
                DB::raw('SUM(l.debit) as debito'),
                DB::raw('SUM(l.credit) as credito')
            )
            ->groupBy('c.code', 'c.name', 'c.nature', 'c.account_type')
            ->orderBy('c.code')
            ->get();

        // 2. Estado de Situación Financiera (Balance General)
        // Activos (Clase 1)
        $activos = $this->obtenerSaldosPorClase(1, $period);
        // Pasivos (Clase 2)
        $pasivos = $this->obtenerSaldosPorClase(2, $period);
        // Capital (Clase 3)
        $capital = $this->obtenerSaldosPorClase(3, $period);

        // 3. Estado de Resultados / Beneficios
        // Ingresos (Clase 4)
        $ingresos = $this->obtenerSaldosPorClase(4, $period);
        // Gastos (Clase 5)
        $gastos = $this->obtenerSaldosPorClase(5, $period);

        $totalIngresos = collect($ingresos)->sum('saldo');
        $totalGastos = collect($gastos)->sum('saldo');
        $utilidadPeriodo = $totalIngresos - $totalGastos;

        return view('ars.contabilidad.balances', compact(
            'periodos', 'period', 'comprobacion', 'activos', 'pasivos', 'capital', 'ingresos', 'gastos', 'totalIngresos', 'totalGastos', 'utilidadPeriodo'
        ));
    }

    /**
     * Cierre de Período Contable.
     */
    public function cierreIndex()
    {
        $periodos = PeriodoContable::orderBy('period_code', 'desc')->get();
        return view('ars.contabilidad.cierre', compact('periodos'));
    }

    public function ejecutarCierre(Request $request)
    {
        $request->validate(['period_code' => 'required|string']);
        $periodCode = $request->period_code;

        $periodo = PeriodoContable::where('period_code', $periodCode)->first();
        if (!$periodo) {
            return redirect()->back()->with('error', "No se encontró el período contable {$periodCode}.");
        }

        if ($periodo->is_closed) {
            return redirect()->back()->with('error', "El período contable {$periodCode} ya se encuentra cerrado.");
        }

        // Validar si existen asientos en borrador
        $borradores = JournalEntry::where('period', $periodCode)->where('status', 'borrador')->count();
        if ($borradores > 0) {
            return redirect()->back()->with('error', "No se puede cerrar el período. Existen {$borradores} asientos en estado Borrador que deben postearse o anularse.");
        }

        // Simular validaciones adicionales de cierre
        DB::transaction(function() use ($periodo) {
            $periodo->update([
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => Auth::id() ?? 1
            ]);

            // Registrar en bitácora
            \App\Models\Bitacora::registrar('Cierre Contable', "Cierre formal contable del período {$periodo->period_code} completado de forma exitosa.");
        });

        return redirect()->route('ars.contabilidad.cierre')->with('success', "Período contable {$periodCode} cerrado exitosamente.");
    }

    /**
     * Helper para obtener los saldos acumulados de cuentas por clase (Activo, Pasivo, etc.).
     */
    private function obtenerSaldosPorClase(int $clase, string $period)
    {
        $cuentas = ChartAccount::where('account_class', $clase)
            ->where('is_postable', true)
            ->orderBy('code')
            ->get();

        $resultados = [];
        foreach ($cuentas as $cta) {
            $mov = JournalEntryLine::where('account_id', $cta->id)
                ->whereHas('entry', function($q) use ($period) {
                    $q->where('period', '<=', $period)
                      ->where('status', 'posteado');
                })
                ->select(DB::raw('SUM(debit) as debito, SUM(credit) as credito'))
                ->first();

            $deb = $mov->debito ?? 0.0;
            $cred = $mov->credito ?? 0.0;

            $saldo = $cta->nature === 'debito' ? ($deb - $cred) : ($cred - $deb);

            if ($saldo != 0) {
                $resultados[] = [
                    'code' => $cta->code,
                    'name' => $cta->name,
                    'saldo' => $saldo
                ];
            }
        }

        return $resultados;
    }
}
