<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\AffiliationBatch;
use App\Models\AffiliationBatchDetail;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\UnipagoMockRequest;
use App\Models\UnipagoMockNotification;
use App\Services\UnipagoMockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UnipagoController extends Controller
{
    /**
     * Dashboard y Estadísticas del simulador.
     */
    public function dashboard()
    {
        $stats = [
            'titulares_count' => Afiliado::count(),
            'dependientes_count' => Dependiente::count(),
            'lotes_enviados' => AffiliationBatch::count(),
            'lotes_procesados' => AffiliationBatch::whereIn('status', ['PC', 'PE', 'RE'])->count(),
            'solicitudes_ok' => AffiliationBatchDetail::where('status', 'OK')->count(),
            'solicitudes_pe64' => AffiliationBatchDetail::where('status', 'PE64')->count(),
            'solicitudes_pe75' => AffiliationBatchDetail::where('status', 'PE75')->count(),
            'solicitudes_re' => AffiliationBatchDetail::where('status', 'RE')->count(),
            'capitas_notificadas' => CapitationNotification::count(),
            'capitas_dispersadas' => CapitationNotification::where('status', 'DI')->count(),
            'monto_dispersado' => CapitationNotification::where('status', 'DI')->sum('capitation_amount'),
            'proximo_corte' => now()->endOfMonth()->toDateString(),
        ];

        // Reportes complementarios para gráficos
        $lotesResultado = AffiliationBatch::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $corteMonto = DispersionCut::orderBy('period')->pluck('total_amount', 'period')->toArray();

        return view('ars.unipago.dashboard', compact('stats', 'lotesResultado', 'corteMonto'));
    }

    /**
     * Prevalidación Masiva (Formulario y Vista previa).
     */
    public function prevalidar(Request $request)
    {
        $inputData = $request->input('raw_data', '');
        $records = [];
        $prevalidated = [];
        $summary = ['total' => 0, 'aptos' => 0, 'pendientes' => 0, 'rechazados' => 0, 'en_bd' => 0];

        if ($request->isMethod('post') && !empty($inputData)) {
            $lines = array_filter(explode("\n", $inputData), fn($l) => trim($l) !== '');
            foreach ($lines as $line) {
                $cols = explode(",", $line);
                if (count($cols) >= 1) {
                    $records[] = [
                        'cedula'    => trim($cols[0]),
                        'nombres'   => trim($cols[1] ?? ''),
                        'apellidos' => trim($cols[2] ?? '')
                    ];
                }
            }

            $prevalidated = UnipagoMockService::prevalidarLote($records);

            // Calcular resumen
            $summary['total'] = count($prevalidated);
            foreach ($prevalidated as $p) {
                if ($p['found_in_db'] ?? false) $summary['en_bd']++;
                $code = $p['codigo_respuesta'] ?? '';
                if ($code === 'OK') $summary['aptos']++;
                elseif ($code === 'RE') $summary['rechazados']++;
                else $summary['pendientes']++;
            }
        }

        return view('ars.unipago.prevalidar', compact('prevalidated', 'inputData', 'summary'));
    }

    /**
     * AJAX: Consulta individual de cédula contra Maestro Unipago (BD real + simulación).
     */
    public function consultarCedula(Request $request)
    {
        $cedula = $request->input('cedula', '');
        if (empty(trim($cedula))) {
            return response()->json(['error' => 'Cédula requerida'], 422);
        }
        $data = UnipagoMockService::consultarCiudadanoDB($cedula);
        return response()->json($data);
    }

    /**
     * Listado de Lotes de Afiliación.
     */
    public function lotesIndex()
    {
        $lotes = AffiliationBatch::orderBy('created_at', 'desc')->paginate(15);
        return view('ars.unipago.lotes_index', compact('lotes'));
    }

    /**
     * Detalle de Lote.
     */
    public function loteShow($id)
    {
        $lote = AffiliationBatch::with(['details.afiliado', 'details.dependiente'])->findOrFail($id);
        return view('ars.unipago.lotes_show', compact('lote'));
    }

    /**
     * Sube un lote de prueba e inicia el flujo.
     */
    public function subirLote(Request $request)
    {
        $request->validate([
            'batch_type' => 'required|in:titulares,dependientes',
            'raw_records' => 'required|string'
        ]);

        DB::transaction(function() use ($request) {
            $type = $request->batch_type;
            $rawRecords = $request->raw_records;

            // Generar número de lote
            $year = now()->year;
            $countLotes = AffiliationBatch::whereYear('created_at', $year)->count();
            $batchNum = 'LOT-UNIPAGO-' . $year . '-' . str_pad($countLotes + 1, 6, '0', STR_PAD_LEFT);

            // Crear el lote
            $batch = AffiliationBatch::create([
                'batch_number' => $batchNum,
                'batch_type' => $type,
                'unipago_lote_id' => 'LOTE-' . rand(100000, 999999),
                'status' => 'VE', // Validado Estructuralmente
                'total_records' => 0,
                'submitted_by' => Auth::id(),
                'submitted_at' => now()
            ]);

            $lines = explode("\n", $rawRecords);
            $total = 0;

            foreach ($lines as $line) {
                $cols = explode(",", $line);
                if (count($cols) >= 2) {
                    $cedula = trim($cols[0]);
                    $nombre = trim($cols[1]);
                    $apellido = trim($cols[2] ?? 'GÓMEZ');

                    // Crear el afiliado correspondiente temporal (en estado PE de confirmación)
                    $tipoIdCed = \App\Models\Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CEDULA')->first()?->id ?? 1;
                    $parentescoHijo = \App\Models\Catalogo::where('grupo', 'parentesco')->where('codigo', 'HIJO')->first()?->id ?? 3;
                    if ($type === 'titulares') {
                        $afiliado = Afiliado::create([
                            'tipo_identificacion_id' => $tipoIdCed,
                            'nombres' => $nombre,
                            'primer_apellido' => $apellido,
                            'segundo_apellido' => 'SÁNCHEZ',
                            'cedula' => $cedula,
                            'nss' => 'NSS-' . rand(100000, 999999),
                            'fecha_nacimiento' => '1985-04-12',
                            'sexo' => 'M',
                            'regimen_actual' => 'Contributivo',
                            'estado_afiliacion' => 'PE', // Pendiente Unipago
                        ]);

                        AffiliationBatchDetail::create([
                            'affiliation_batch_id' => $batch->id,
                            'afiliado_id' => $afiliado->id,
                            'status' => 'PE64', // Estado inicial
                        ]);
                    } else {
                        // Crear dependiente (vincular al primer afiliado titular en la BD)
                        $titular = Afiliado::first();
                        $dependiente = Dependiente::create([
                            'titular_id' => $titular ? $titular->id : 1,
                            'tipo_identificacion_id' => $tipoIdCed,
                            'nombres' => $nombre,
                            'apellidos' => $apellido,
                            'cedula' => $cedula,
                            'nss' => 'NSS-DEP-' . rand(100000, 999999),
                            'parentesco_id' => $parentescoHijo,
                            'tipo_dependiente' => 'Directo',
                            'nacionalidad' => 'Dominicana',
                            'estudiante' => false,
                            'discapacitado' => false,
                            'requiere_documento' => true,
                            'fecha_nacimiento' => '2015-08-20',
                            'sexo' => 'F',
                            'estado_afiliacion' => 'PE',
                        ]);

                        AffiliationBatchDetail::create([
                            'affiliation_batch_id' => $batch->id,
                            'dependiente_id' => $dependiente->id,
                            'status' => 'PE64',
                        ]);
                    }
                    $total++;
                }
            }

            $batch->update(['total_records' => $total]);

            // Registrar alerta mock
            UnipagoMockNotification::enviar(
                'Lote recibido',
                'batch',
                $batch->id,
                'Lote de Afiliación Recibido',
                "El lote estructuralmente validado {$batchNum} conteniendo {$total} registros fue cargado y se encuentra en cola de procesamiento."
            );
        });

        return redirect()->route('ars.unipago.lotes')->with('success', 'Lote cargado correctamente en estado VE. Listo para procesar.');
    }

    /**
     * Simula la resolución asíncrona del lote.
     */
    public function procesarLote($id)
    {
        $batch = AffiliationBatch::findOrFail($id);
        UnipagoMockService::procesarLote($batch);

        return redirect()->route('ars.unipago.lotes.show', $batch->id)
            ->with('success', 'El lote fue procesado y su resultado fue actualizado.');
    }

    /**
     * Listado de Notificaciones de Cápitas.
     */
    public function capitasIndex(Request $request)
    {
        $status = $request->get('status');
        $query = CapitationNotification::with('afiliado');

        if ($status) $query->where('status', $status);

        $capitas = $query->orderBy('created_at', 'desc')->paginate(15);
        $estados = ['IN', 'NT', 'IC', 'IR', 'DI', 'BL', 'PE', 'RE'];

        return view('ars.unipago.capitas_index', compact('capitas', 'status', 'estados'));
    }

    /**
     * Acción sobre Cápita (Confirmar o Rechazar).
     */
    public function capitaAccion($id, $accion)
    {
        $motivo = request()->get('rejection_reason');
        UnipagoMockService::procesarCapita($id, $accion, $motivo);

        return redirect()->back()->with('success', 'Cápita procesada con éxito.');
    }

    /**
     * Cortes de Dispersión.
     */
    public function cortesIndex()
    {
        $cortes = DispersionCut::orderBy('created_at', 'desc')->paginate(15);
        return view('ars.unipago.cortes_index', compact('cortes'));
    }

    /**
     * Generar Corte de Dispersión.
     */
    public function generarCorte(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'cut_type' => 'required|in:primer corte,segundo corte,operativo'
        ]);

        $period = $request->period;
        $type = $request->cut_type;

        $cut = UnipagoMockService::generarCorteDispersion($period, $type);

        if (!$cut) {
            return redirect()->back()->with('error', 'No se encontraron cápitas confirmadas (IC) pendientes de dispersión para el período seleccionado.');
        }

        return redirect()->route('ars.unipago.cortes')->with('success', "Corte {$cut->cut_number} generado exitosamente.");
    }

    /**
     * Registro de logs de llamadas mock de Unipago.
     */
    public function mockLogs()
    {
        $logs = UnipagoMockRequest::orderBy('created_at', 'desc')->paginate(20);
        return view('ars.unipago.mock_logs', compact('logs'));
    }

    /**
     * Bandeja de Notificaciones Internas de Unipago.
     */
    public function notificaciones()
    {
        $notificaciones = UnipagoMockNotification::orderBy('created_at', 'desc')->paginate(20);
        
        // Marcar todas como leídas
        UnipagoMockNotification::whereNull('read_at')->update(['read_at' => now()]);

        return view('ars.unipago.notificaciones', compact('notificaciones'));
    }
}
