<?php

namespace App\Http\Controllers;

use App\Models\AffiliationContractRange;
use App\Models\AffiliationContractNumber;
use App\Models\AffiliationContractMovement;
use App\Models\Bitacora;
use App\Services\AffiliationContractNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliationContractController extends Controller
{
    /**
     * Dashboard del control de formularios y contratos.
     */
    public function dashboard()
    {
        $kpis = [
            'rangos_activos' => AffiliationContractRange::where('status', 'activo')->count(),
            'disponibles' => AffiliationContractNumber::where('status', 'disponible')->count(),
            'reservados' => AffiliationContractNumber::where('status', 'reservado')->count(),
            'usados' => AffiliationContractNumber::where('status', 'usado')->count(),
            'enviados' => AffiliationContractNumber::where('status', 'enviado_unipago')->count(),
            'ok' => AffiliationContractNumber::where('status', 'ok')->count(),
            'pe' => AffiliationContractNumber::where('status', 'pe')->count(),
            're' => AffiliationContractNumber::where('status', 're')->count(),
            'bloqueados' => AffiliationContractNumber::where('status', 'bloqueado')->count()
        ];

        // Último contrato usado
        $ultimoUsado = AffiliationContractNumber::whereIn('status', ['usado', 'enviado_unipago', 'ok', 'pe', 're'])
            ->orderBy('updated_at', 'desc')
            ->first();

        // Próximo disponible
        $proximoDisponible = AffiliationContractNumberService::getNextAvailableNumber();

        // Rangos próximos a agotarse (menos del 20% disponible)
        $rangosCriticos = AffiliationContractRange::where('status', 'activo')
            ->get()
            ->filter(function ($r) {
                if ($r->total_numbers === 0) return false;
                return ($r->available_count / $r->total_numbers) < 0.20;
            });

        return view('ars.afiliaciones.contratos.dashboard', compact('kpis', 'ultimoUsado', 'proximoDisponible', 'rangosCriticos'));
    }

    /**
     * Listado de Rangos de Contratos.
     */
    public function indexRanges(Request $request)
    {
        $search = $request->get('search');
        $query = AffiliationContractRange::query();

        if ($search) {
            $query->where('range_code', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
        }

        $rangos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('ars.afiliaciones.contratos.index_ranges', compact('rangos', 'search'));
    }

    /**
     * Muestra el formulario para crear un rango.
     */
    public function createRange()
    {
        return view('ars.afiliaciones.contratos.create_range');
    }

    /**
     * Almacena y genera automáticamente el rango de números de contrato de forma transaccional.
     */
    public function storeRange(Request $request)
    {
        $request->validate([
            'range_code' => 'required|string|max:50|unique:affiliation_contract_ranges,range_code',
            'description' => 'required|string|max:255',
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|min:1|gte:start_number',
            'source' => 'required|string',
            'approval_reference' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $start = intval($request->start_number);
        $end = intval($request->end_number);
        $total = ($end - $start) + 1;

        if ($total > 10000) {
            return redirect()->back()
                ->withInput()
                ->with('error', "El rango es demasiado grande ({$total} números). El límite para generación directa es 10,000 para evitar desbordamiento de memoria.");
        }

        $userId = Auth::id() ?: 1;

        try {
            $rangoCreado = DB::transaction(function () use ($request, $start, $end, $total, $userId) {
                // 1. Validar no solapamiento
                $solapado = AffiliationContractRange::where(function ($q) use ($start, $end) {
                    $q->where(function ($sub) use ($start) {
                        $sub->where('start_number', '<=', $start)->where('end_number', '>=', $start);
                    })->orWhere(function ($sub) use ($end) {
                        $sub->where('start_number', '<=', $end)->where('end_number', '>=', $end);
                    })->orWhere(function ($sub) use ($start, $end) {
                        $sub->where('start_number', '>=', $start)->where('end_number', '<=', $end);
                    });
                })->exists();

                if ($solapado) {
                    throw new \Exception("El rango propuesto [{$start} - {$end}] se solapa con un rango de formularios preexistente.");
                }

                // 2. Crear rango
                $range = AffiliationContractRange::create([
                    'range_code' => $request->range_code,
                    'description' => $request->description,
                    'start_number' => $start,
                    'end_number' => $end,
                    'total_numbers' => $total,
                    'available_count' => $total,
                    'source' => $request->source,
                    'approval_reference' => $request->approval_reference,
                    'approved_by' => 'Administrador',
                    'approved_at' => now(),
                    'valid_from' => $request->valid_from,
                    'valid_until' => $request->valid_until,
                    'status' => 'activo',
                    'created_by' => $userId
                ]);

                // 3. Generar registros individuales atómicamente
                $insertData = [];
                for ($i = $start; $i <= $end; $i++) {
                    // Comprobar duplicidad del número antes de insertar
                    if (AffiliationContractNumber::where('contract_number', $i)->exists()) {
                        throw new \Exception("El número de contrato {$i} ya existe en el sistema.");
                    }

                    $insertData[] = [
                        'affiliation_contract_range_id' => $range->id,
                        'contract_number' => strval($i),
                        'status' => 'disponible',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // Hacer inserciones en chunks de 500 para mayor seguridad en SQLite
                    if (count($insertData) === 500) {
                        AffiliationContractNumber::insert($insertData);
                        $insertData = [];
                    }
                }

                if (count($insertData) > 0) {
                    AffiliationContractNumber::insert($insertData);
                }

                Bitacora::registrar('Mantenimiento Contratos', "Creado rango de formularios Unipago {$range->range_code} [{$start} - {$end}] con {$total} números.");

                return $range;
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('ars.contract_control.ranges.index')
            ->with('success', "Se creó el rango '{$rangoCreado->range_code}' y se generaron {$total} números individuales de forma exitosa.");
    }

    /**
     * Detalle del Rango de Contratos.
     */
    public function showRange(Request $request, $id)
    {
        $rango = AffiliationContractRange::findOrFail($id);
        $tab = $request->get('tab', 'resumen');

        // Pestaña de Números con filtros
        $searchNumber = $request->get('search_number');
        $statusFilter = $request->get('status_filter');

        $numbersQuery = $rango->numbers();
        if ($searchNumber) {
            $numbersQuery->where('contract_number', 'like', "%{$searchNumber}%");
        }
        if ($statusFilter) {
            $numbersQuery->where('status', $statusFilter);
        }
        $numeros = $numbersQuery->orderBy('contract_number')->paginate(12, ['*'], 'number_page');

        // Pestaña de Auditoría/Movimientos
        $movimientos = AffiliationContractMovement::whereHas('number', function ($q) use ($rango) {
            $q->where('affiliation_contract_range_id', $rango->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(12, ['*'], 'move_page');

        return view('ars.afiliaciones.contratos.show_range', compact('rango', 'tab', 'numeros', 'movimientos', 'searchNumber', 'statusFilter'));
    }

    /**
     * Cambiar el estado administrativo del rango.
     */
    public function updateRangeStatus(Request $request, $id)
    {
        $rango = AffiliationContractRange::findOrFail($id);
        $status = $request->input('status');

        if (!in_array($status, ['activo', 'suspendido', 'cerrado', 'anulado'])) {
            return redirect()->back()->with('error', 'Estado de rango no válido.');
        }

        $rango->update(['status' => $status]);

        Bitacora::registrar('Mantenimiento Contratos', "Cambio de estado del rango {$rango->range_code} a: {$status}");

        return redirect()->back()->with('success', "Se cambió el estado del rango '{$rango->range_code}' a: {$status}.");
    }

    /**
     * Bloquear un número de contrato individual.
     */
    public function blockNumber(Request $request, $id)
    {
        $request->validate(['block_reason' => 'required|string|min:4']);

        $number = AffiliationContractNumber::findOrFail($id);
        $userId = Auth::id() ?: 1;

        AffiliationContractNumberService::blockNumber($number->id, $request->block_reason, $userId);

        return redirect()->back()->with('success', "El contrato #{$number->contract_number} ha sido bloqueado exitosamente.");
    }

    /**
     * Liberar un número de contrato individual reservado.
     */
    public function releaseNumber(Request $request, $id)
    {
        $number = AffiliationContractNumber::findOrFail($id);
        $userId = Auth::id() ?: 1;

        AffiliationContractNumberService::releaseNumber($number->id, 'Liberado manualmente por administración', $userId);

        return redirect()->back()->with('success', "El contrato #{$number->contract_number} ha sido liberado exitosamente.");
    }

    /**
     * Muestra la pantalla de configuración general.
     */
    public function configuracion()
    {
        $config = session('contract_config', [
            'require_contract_for_holder' => true,
            'require_contract_for_dependent' => false,
            'default_reservation_minutes' => 15,
            'allow_reuse_rejected' => false,
            'alert_threshold_available' => 500
        ]);

        return view('ars.afiliaciones.contratos.configuracion', compact('config'));
    }

    /**
     * Guarda la configuración general en la sesión.
     */
    public function saveConfiguracion(Request $request)
    {
        $config = [
            'require_contract_for_holder' => $request->has('require_contract_for_holder'),
            'require_contract_for_dependent' => $request->has('require_contract_for_dependent'),
            'default_reservation_minutes' => intval($request->input('default_reservation_minutes', 15)),
            'allow_reuse_rejected' => $request->has('allow_reuse_rejected'),
            'alert_threshold_available' => intval($request->input('alert_threshold_available', 500))
        ];

        session(['contract_config' => $config]);

        Bitacora::registrar('Mantenimiento Contratos', "Actualizada configuración general de formularios.");

        return redirect()->route('ars.contract_control.dashboard')
            ->with('success', 'Configuración de formularios y contratos guardada exitosamente.');
    }
}
