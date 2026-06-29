<?php

namespace App\Http\Controllers;

use App\Models\Pss;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use App\Models\PssTariffImport;
use App\Models\PssContractLog;
use App\Models\PdssService;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContratoTarifarioController extends Controller
{
    /**
     * Listado general de contratos con métricas de vencimiento.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $pssId = $request->get('pss_id');

        $query = PssContract::with(['pss', 'versions']);

        if ($status) $query->where('status', $status);
        if ($pssId) $query->where('pss_id', $pssId);

        $contratos = $query->orderBy('end_date', 'asc')->paginate(15);
        $pssList = Pss::orderBy('nombre')->get();

        // Alertas y conteos
        $proximosVencer = PssContract::where('status', 'vigente')
            ->where('end_date', '<=', Carbon::now()->addDays(30))
            ->count();

        $vencidos = PssContract::where('status', 'vencido')
            ->orWhere(function($q) {
                $q->where('status', 'vigente')->where('end_date', '<', Carbon::now());
            })->count();

        // PSS sin contrato
        $pssConContratoIds = PssContract::where('status', 'vigente')->pluck('pss_id')->toArray();
        $pssSinContrato = Pss::whereNotIn('id', $pssConContratoIds)->count();

        return view('ars.pss.contratos_tarifarios', compact(
            'contratos', 'pssList', 'status', 'pssId', 'proximosVencer', 'vencidos', 'pssSinContrato'
        ));
    }

    /**
     * Detalle interactivo de un contrato.
     */
    public function show($id)
    {
        $contrato = PssContract::with(['pss', 'versions', 'tariffSchedules'])->findOrFail($id);
        
        $activeVersion = $contrato->versions()->where('status', 'vigente')->first() ?? $contrato->versions()->first();
        
        $tariffItems = [];
        $schedule = null;

        if ($activeVersion) {
            $schedule = PssTariffSchedule::where('pss_contract_version_id', $activeVersion->id)->first();
            if ($schedule) {
                $tariffItems = PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
                    ->orderBy('simon_code_snapshot')
                    ->paginate(20);
            }
        }

        // Historial de cambios
        $logs = PssContractLog::where('pss_contract_id', $contrato->id)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $imports = PssTariffImport::where('pss_contract_id', $contrato->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdssServices = PdssService::orderBy('simon_code')->get();

        return view('ars.pss.contratos_tarifarios_show', compact(
            'contrato', 'activeVersion', 'schedule', 'tariffItems', 'logs', 'pdssServices', 'imports'
        ));
    }

    /**
     * Crea un contrato nuevo.
     */
    public function crearContrato(Request $request)
    {
        $request->validate([
            'pss_id' => 'required|exists:pss,id',
            'contract_number' => 'required|string|unique:pss_contracts,contract_number',
            'contract_name' => 'required|string',
            'contract_type' => 'required|in:general,especialidad,capitado,evento,mixto',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'observations' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            $contract = PssContract::create([
                'pss_id' => $request->pss_id,
                'contract_number' => $request->contract_number,
                'contract_name' => $request->contract_name,
                'contract_type' => $request->contract_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'vigente',
                'signed_at' => now(),
                'signed_by' => Auth::id() ?? 1,
                'observations' => $request->observations
            ]);

            $version = PssContractVersion::create([
                'pss_contract_id' => $contract->id,
                'version_number' => '1.0.0',
                'effective_from' => $request->start_date,
                'effective_to' => $request->end_date,
                'status' => 'vigente',
                'approved_by' => Auth::id() ?? 1,
                'approved_at' => now(),
                'change_reason' => 'Creación de contrato base.'
            ]);

            PssTariffSchedule::create([
                'pss_contract_id' => $contract->id,
                'pss_contract_version_id' => $version->id,
                'name' => "Tarifario Inicial {$contract->contract_number}",
                'effective_from' => $request->start_date,
                'effective_to' => $request->end_date,
                'status' => 'vigente'
            ]);

            PssContractLog::create([
                'pss_contract_id' => $contract->id,
                'user_id' => Auth::id() ?? 1,
                'action' => 'crear_contrato',
                'new_values' => $contract->toArray(),
                'observation' => 'Registro inicial en sistema.'
            ]);

            Bitacora::registrar('Contratos PSS', "Creado contrato {$contract->contract_number} para PSS ID {$contract->pss_id}.");
        });

        return redirect()->route('ars.pss.contratos_tarifarios')->with('success', 'Contrato registrado exitosamente con versión inicial 1.0.0.');
    }

    /**
     * Versiona el contrato para cambios mayores.
     */
    public function crearVersion(Request $request, $id)
    {
        $contract = PssContract::findOrFail($id);
        $request->validate([
            'version_number' => 'required|string',
            'change_reason' => 'required|string|min:5'
        ]);

        DB::transaction(function() use ($contract, $request) {
            // Reemplazar versión anterior
            $contract->versions()->where('status', 'vigente')->update([
                'status' => 'reemplazada',
                'effective_to' => now()->toDateString()
            ]);

            $version = PssContractVersion::create([
                'pss_contract_id' => $contract->id,
                'version_number' => $request->version_number,
                'effective_from' => now()->toDateString(),
                'effective_to' => $contract->end_date,
                'status' => 'vigente',
                'approved_by' => Auth::id() ?? 1,
                'approved_at' => now(),
                'change_reason' => $request->change_reason
            ]);

            // Copiar tarifario
            $oldSchedule = PssTariffSchedule::where('pss_contract_id', $contract->id)->where('status', 'vigente')->first();
            
            $newSchedule = PssTariffSchedule::create([
                'pss_contract_id' => $contract->id,
                'pss_contract_version_id' => $version->id,
                'name' => "Tarifario Versión {$request->version_number}",
                'effective_from' => now(),
                'effective_to' => $contract->end_date,
                'status' => 'vigente'
            ]);

            if ($oldSchedule) {
                $oldSchedule->update(['status' => 'reemplazado', 'effective_to' => now()]);
                
                $items = PssTariffItem::where('pss_tariff_schedule_id', $oldSchedule->id)->get();
                foreach ($items as $item) {
                    $newItem = $item->replicate();
                    $newItem->pss_tariff_schedule_id = $newSchedule->id;
                    $newItem->save();
                }
            }

            PssContractLog::create([
                'pss_contract_id' => $contract->id,
                'user_id' => Auth::id() ?? 1,
                'action' => 'crear_version',
                'new_values' => ['version_number' => $request->version_number],
                'observation' => $request->change_reason
            ]);
        });

        return redirect()->back()->with('success', "Se ha creado la versión {$request->version_number} y copiado el tarifario correspondiente.");
    }

    /**
     * Agrega o edita una tarifa individual.
     */
    public function guardarTarifa(Request $request, $id)
    {
        $contract = PssContract::findOrFail($id);
        $request->validate([
            'pdss_service_id' => 'required|exists:pdss_services,id',
            'contracted_amount' => 'required|numeric|min:0',
            'copay_percent' => 'required|numeric|between:0,100',
            'requires_authorization' => 'nullable|boolean',
            'requires_medical_audit' => 'nullable|boolean',
            'frequency_limit' => 'nullable|integer',
            'frequency_period' => 'nullable|string'
        ]);

        $activeVersion = $contract->versions()->where('status', 'vigente')->first();
        if (!$activeVersion) return redirect()->back()->with('error', 'No hay versión de contrato activa para editar tarifas.');

        $schedule = PssTariffSchedule::where('pss_contract_version_id', $activeVersion->id)->where('status', 'vigente')->first();
        if (!$schedule) return redirect()->back()->with('error', 'No hay tarifario activo.');

        $pdss = PdssService::findOrFail($request->pdss_service_id);

        DB::transaction(function() use ($request, $schedule, $contract, $pdss) {
            // Verificar si ya existe en este tarifario
            $item = PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
                ->where('pdss_service_id', $pdss->id)
                ->first();

            $oldValues = $item ? $item->toArray() : [];

            $data = [
                'pss_tariff_schedule_id' => $schedule->id,
                'pss_id' => $contract->pss_id,
                'pdss_service_id' => $pdss->id,
                'simon_code_snapshot' => $pdss->simon_code,
                'cups_code_snapshot' => $pdss->cups_code,
                'service_description_snapshot' => $pdss->coverage_description,
                'service_group_snapshot' => $pdss->group?->name ?? 'General',
                'contracted_amount' => $request->contracted_amount,
                'copay_percent' => $request->copay_percent,
                'ars_covered_percent' => 100 - $request->copay_percent,
                'requires_authorization' => $request->has('requires_authorization'),
                'requires_medical_audit' => $request->has('requires_medical_audit'),
                'frequency_limit' => $request->frequency_limit,
                'frequency_period' => $request->frequency_period,
                'level_1_allowed' => true,
                'level_2_allowed' => true,
                'level_3_allowed' => true,
                'is_high_cost' => $pdss->is_high_cost,
                'is_surgery' => $pdss->is_surgery,
                'is_hospitalization' => $pdss->is_hospitalization,
                'is_medicine' => $pdss->is_medicine,
                'status' => 'activo'
            ];

            if ($item) {
                $item->update($data);
                $action = 'modificar_tarifa';
            } else {
                $item = PssTariffItem::create($data);
                $action = 'agregar_tarifa';
            }

            PssContractLog::create([
                'pss_contract_id' => $contract->id,
                'user_id' => Auth::id() ?? 1,
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $item->toArray(),
                'observation' => "Tarifa individual configurada para el código Simon {$pdss->simon_code}."
            ]);
        });

        return redirect()->back()->with('success', 'Tarifa guardada con éxito.');
    }

    /**
     * Importador masivo de tarifarios por CSV.
     */
    public function importarTarifario(Request $request, $id)
    {
        $contract = PssContract::findOrFail($id);
        $request->validate([
            'csv_file' => 'required|file|mimes:txt,csv|max:4096'
        ]);

        $activeVersion = $contract->versions()->where('status', 'vigente')->first();
        if (!$activeVersion) return redirect()->back()->with('error', 'Contrato sin versión activa.');

        $schedule = PssTariffSchedule::where('pss_contract_version_id', $activeVersion->id)->first();
        if (!$schedule) return redirect()->back()->with('error', 'Tarifario no encontrado.');

        $file = $request->file('csv_file');
        $filePath = $file->store('imports');
        
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        
        if (count($csvData) < 2) {
            return redirect()->back()->with('error', 'El archivo CSV está vacío.');
        }

        $headers = array_shift($csvData); // Cabeceras

        $imported = 0;
        $rejected = 0;
        $errors = [];

        DB::transaction(function() use ($csvData, $schedule, $contract, $filePath, &$imported, &$rejected, &$errors) {
            foreach ($csvData as $index => $row) {
                if (count($row) < 5) {
                    $rejected++;
                    $errors[] = "Fila " . ($index + 2) . ": Columnas insuficientes.";
                    continue;
                }

                $simonCode = trim($row[0]);
                $cupsCode = trim($row[1]);
                $monto = floatval($row[5] ?? 0);
                $copagoPct = floatval($row[6] ?? 20);

                // Buscar en el catálogo PDSS
                $pdss = PdssService::where('simon_code', $simonCode)->first();
                
                if (!$pdss) {
                    $rejected++;
                    $errors[] = "Fila " . ($index + 2) . ": Código Simon {$simonCode} no existe en Catálogo PDSS.";
                    continue;
                }

                // Evitar duplicados
                $item = PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
                    ->where('pdss_service_id', $pdss->id)
                    ->first();

                $data = [
                    'pss_tariff_schedule_id' => $schedule->id,
                    'pss_id' => $contract->pss_id,
                    'pdss_service_id' => $pdss->id,
                    'simon_code_snapshot' => $pdss->simon_code,
                    'cups_code_snapshot' => $cupsCode ?: $pdss->cups_code,
                    'service_description_snapshot' => $pdss->coverage_description,
                    'service_group_snapshot' => $pdss->group?->name ?? 'General',
                    'contracted_amount' => $monto,
                    'copay_percent' => $copagoPct,
                    'ars_covered_percent' => 100 - $copagoPct,
                    'requires_authorization' => true,
                    'status' => 'activo'
                ];

                if ($item) {
                    $item->update($data);
                } else {
                    PssTariffItem::create($data);
                }

                $imported++;
            }

            PssTariffImport::create([
                'pss_id' => $contract->pss_id,
                'pss_contract_id' => $contract->id,
                'file_path' => $filePath,
                'total_rows' => count($csvData),
                'imported_rows' => $imported,
                'rejected_rows' => $rejected,
                'status' => $rejected > 0 ? 'con_errores' : 'completado',
                'errors' => $errors,
                'imported_by' => Auth::id() ?? 1,
                'imported_at' => now()
            ]);
        });

        return redirect()->back()->with('success', "Importación completada: {$imported} importados, {$rejected} rechazados.");
    }

    /**
     * Descarga plantilla CSV.
     */
    public function descargarPlantilla()
    {
        $headers = ['codigo_simon', 'codigo_cups', 'descripcion_servicio', 'grupo', 'subgrupo', 'monto_contratado', 'copago_porcentaje', 'nivel_1', 'nivel_2', 'nivel_3', 'estado'];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            // Fila de ejemplo
            fputcsv($file, ['900010', '90.0.1.0', 'Consulta Médica General', 'Consultas', 'Consultas comunes', '1200.00', '20.00', 'S', 'S', 'S', 'activo']);
            fputcsv($file, ['901010', '90.1.0.1', 'Hemograma Completo', 'Apoyo Diagnóstico', 'Laboratorio', '450.00', '20.00', 'S', 'S', 'S', 'activo']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_tarifario_pss.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
