<?php

namespace App\Http\Controllers;

use App\Models\UnipagoMockService;
use App\Models\UnipagoMockRequest;
use App\Models\UnipagoResponseCode;
use App\Models\UnipagoMockScenario;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\Lote;
use App\Models\AffiliationBatch;
use App\Services\UnipagoMockService as SimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnipagoSimuladorController extends Controller
{
    /**
     * Dashboard del simulador Unipago
     */
    public function dashboard()
    {
        $metricas = [
            'total_requests' => UnipagoMockRequest::count(),
            'lotes_core' => Lote::count(),
            'lotes_simulador' => AffiliationBatch::count(),
            'capitas_notificadas' => CapitationNotification::where('status', 'NT')->count(),
            'capitas_confirmadas' => CapitationNotification::where('status', 'IC')->count(),
            'capitas_dispersadas' => CapitationNotification::where('status', 'DI')->count(),
            'cortes' => DispersionCut::count(),
        ];

        // Lista de Web Services del Catálogo
        $servicios = UnipagoMockService::all();
        
        // Historial de Logs de llamadas
        $logs = UnipagoMockRequest::orderBy('created_at', 'desc')->take(10)->get();

        return view('ars.unipago.simulador-dashboard', compact('metricas', 'servicios', 'logs'));
    }

    /**
     * Consola interactiva de pruebas
     */
    public function consola(Request $request)
    {
        $servicios = UnipagoMockService::where('is_active', true)->get();
        $codigos = UnipagoResponseCode::where('is_active', true)->get();
        $escenarios = UnipagoMockScenario::where('is_active', true)->get();

        $selectedService = $request->get('service_code');
        $service = null;
        if ($selectedService) {
            $service = UnipagoMockService::where('service_code', $selectedService)->first();
        }

        return view('ars.unipago.simulador-consola', compact('servicios', 'codigos', 'escenarios', 'service', 'selectedService'));
    }

    /**
     * Ejecuta una llamada de prueba desde la consola interactiva
     */
    public function ejecutarWS(Request $request)
    {
        $request->validate([
            'service_code' => 'required|exists:unipago_mock_services,service_code',
            'payload' => 'nullable|string',
        ]);

        $serviceCode = $request->service_code;
        $service = UnipagoMockService::where('service_code', $serviceCode)->first();
        
        // Decodificar payload enviado
        $payloadData = [];
        if ($request->payload) {
            $payloadData = json_decode($request->payload, true) ?: [];
        }

        // Aplicar latencia y probabilidades de error
        if ($service->simulated_latency_ms > 0) {
            usleep($service->simulated_latency_ms * 1000);
        }

        // Simular error técnico según probabilidad configurada
        if (rand(1, 100) <= $service->error_probability) {
            $errorResponse = [
                'status' => 'error',
                'code' => 'ER500',
                'message' => 'Fallo interno de comunicación o timeout con el nodo de Unipago.',
                'timestamp' => now()->toDateTimeString(),
            ];
            
            UnipagoMockRequest::create([
                'service_code' => $serviceCode,
                'service_name' => $service->service_name,
                'endpoint_mock' => $service->endpoint_mock,
                'request_payload' => $payloadData,
                'response_payload' => $errorResponse,
                'status' => 'Error',
                'processed_at' => now(),
            ]);

            return response()->json($errorResponse, 500);
        }

        // 1. Verificar si hay un escenario que coincida
        $scenario = UnipagoMockScenario::where('service_code', $serviceCode)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get()
            ->first(function($sc) use ($payloadData) {
                if (empty($sc->conditions)) return false;
                foreach ($sc->conditions as $k => $v) {
                    if (!isset($payloadData[$k]) || $payloadData[$k] !== $v) {
                        return false;
                    }
                }
                return true;
            });

        if ($scenario) {
            $response = $scenario->response_payload_template;
            $status = $scenario->response_code === 'OK' ? 'Processed' : 'Failed';
        } else {
            // 2. Respuesta por defecto
            $response = $this->generarRespuestaDefecto($serviceCode, $payloadData);
            $status = 'Processed';
        }

        // Registrar Log Técnico
        UnipagoMockRequest::create([
            'service_code' => $serviceCode,
            'service_name' => $service->service_name,
            'endpoint_mock' => $service->endpoint_mock,
            'request_payload' => $payloadData,
            'response_payload' => $response,
            'status' => $status,
            'processed_at' => now(),
        ]);

        return response()->json($response);
    }

    /**
     * Guarda la configuración de un servicio (latencia, error rate)
     */
    public function guardarConfigWS(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:unipago_mock_services,id',
            'simulated_latency_ms' => 'required|integer|min:0|max:10000',
            'error_probability' => 'required|integer|min:0|max:100',
            'default_response_type' => 'required|string',
        ]);

        $service = UnipagoMockService::findOrFail($request->service_id);
        $service->update([
            'simulated_latency_ms' => $request->simulated_latency_ms,
            'error_probability' => $request->error_probability,
            'default_response_type' => $request->default_response_type,
        ]);

        return redirect()->back()->with('success', "Configuración de {$service->service_name} actualizada correctamente.");
    }

    /**
     * Generar respuestas lógicas por defecto para la consola
     */
    private function generarRespuestaDefecto(string $code, array $payload): array
    {
        $timestamp = now()->toDateTimeString();

        switch ($code) {
            case 'CONSULTA_CIUDADANO':
                $cedula = $payload['cedula'] ?? '';
                return SimService::consultarCiudadanoDB($cedula);

            case 'VALIDA_CONTRATO_FORMULARIO':
                $num = $payload['contract_number'] ?? '';
                $record = AffiliationContractNumber::where('contract_number', $num)->first();
                if (!$record) {
                    return [
                        'valido' => false,
                        'codigo_respuesta' => 'RE002',
                        'mensaje' => 'El número de contrato/formulario no existe en el registro de series autorizadas.'
                    ];
                }
                return [
                    'valido' => $record->status === 'disponible',
                    'contract_number' => $num,
                    'status' => $record->status,
                    'codigo_respuesta' => $record->status === 'disponible' ? 'OK' : 'RE003',
                    'mensaje' => $record->status === 'disponible' ? 'Contrato libre para afiliación.' : 'Contrato ya consumido o bloqueado.'
                ];

            case 'NOTIFICACION_CAPITA':
                $period = $payload['period'] ?? date('Ym');
                $count = CapitationNotification::where('period', $period)->count();
                return [
                    'success' => true,
                    'period' => $period,
                    'notificadas_count' => $count,
                    'timestamp' => $timestamp,
                ];

            case 'CONFIRMACION_INDIVIDUALIZACION':
                $id = $payload['notification_id'] ?? 0;
                $decision = $payload['decision'] ?? 'confirmar';
                SimService::procesarCapita($id, $decision);
                return [
                    'success' => true,
                    'notification_id' => $id,
                    'status' => $decision === 'confirmar' ? 'IC' : 'IR',
                    'message' => 'Confirmación de cápita registrada de forma exitosa.'
                ];

            default:
                return [
                    'status' => 'success',
                    'service_code' => $code,
                    'message' => 'Web Service ejecutado exitosamente con respuesta mock genérica.',
                    'received_payload' => $payload,
                    'timestamp' => $timestamp
                ];
        }
    }
}
