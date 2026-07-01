<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReimbursementCase;
use App\Models\ReimbursementDocument;
use App\Models\ReimbursementAction;
use App\Models\Afiliado;
use App\Models\Pss;
use App\Models\User;
use App\Services\AccountingPostingService;
use Carbon\Carbon;

class ReimbursementDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Limpiar registros previos
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        ReimbursementAction::truncate();
        ReimbursementDocument::truncate();
        ReimbursementCase::truncate();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 2. Obtener Afiliados y PSS para relacionar
        $afiliadoIds = Afiliado::where('estado_afiliacion', 'OK')->pluck('id');
        $pssList = Pss::where('estado', 'Activa')->get();
        $userArs = User::where('role', 'Auditor Médico')->first() ?? User::first();
        $userId = $userArs ? $userArs->id : 1;

        if ($afiliadoIds->isEmpty() || $pssList->isEmpty()) {
            $this->command->error('No hay afiliados o PSS activos para asociar con reembolsos.');
            return;
        }

        // 3. Casos y datos realistas para la simulación
        $canales = ['presencial', 'app', 'correo', 'otro'];
        $origenes = ['ars', 'dida', 'sisalril', 'idoppril'];
        
        $motivosCobroIndebido = [
            'Cobro de copago por encima del 20% regulado en cirugía laparoscópica.',
            'Cobro de material gastable médico cubierto por el plan básico de salud.',
            'Cobro indebido de honorarios médicos de anestesiología fuera de tarifario contratado.',
            'Diferencia cobrada en exceso por internamiento en habitación individual estándar.',
            'Cobro de insumos de osteosíntesis pre-aprobados en autorización médica.',
            'Cobro no autorizado de pruebas de laboratorio de rutina bajo cobertura SFS.'
        ];

        $motivosNegacionCobertura = [
            'Negación de cobertura en medicamento oncológico de alto costo pre-aprobado.',
            'Negación de reembolso por emergencia médica atendida en centro de salud fuera de la red.',
            'Rechazo de cobertura de tomografía computarizada por presunto error de sistema en la PSS.',
            'Cobro total de parto por cesárea debido a supuesta falta de documentación del recién nacido.',
            'Falta de acreditación de cobertura en prótesis ortopédica aprobada por auditoría.',
            'Exclusión injustificada de internamiento clínico de emergencia por sospecha de preexistencia.'
        ];

        // Distribución de estados deseados
        $estados = [
            'Recibido', 'Recibido',
            'Pendiente de documentos',
            'Expediente completo', 'Expediente completo',
            'En revisión', 'En revisión',
            'Aprobado', 'Aprobado', 'Aprobado', 'Aprobado',
            'Rechazado', 'Rechazado',
            'Aprobado parcial', 'Aprobado parcial',
            'Cerrado', 'Cerrado'
        ];

        $count = 1;
        $totalCases = 26;

        for ($i = 0; $i < $totalCases; $i++) {
            $status = $estados[$i % count($estados)];
            $afiliadoId = $afiliadoIds->random();
            $pss = $pssList->random();

            $requestType = rand(0, 1) === 0 ? 'cobro_indebido' : 'negacion_cobertura';
            if ($i % 8 === 0) {
                $requestType = 'ambas';
            }

            // Generar montos realistas
            $requestedAmount = round(rand(20000, 850000) / 10, 2); // De RD$ 2,000 a RD$ 85,000
            $approvedAmount = 0.00;
            $rejectedAmount = 0.00;

            if ($status === 'Aprobado') {
                $approvedAmount = $requestedAmount;
            } elseif ($status === 'Aprobado parcial' || $status === 'Cerrado') {
                $approvedAmount = round($requestedAmount * rand(60, 90) / 100, 2);
                $rejectedAmount = round($requestedAmount - $approvedAmount, 2);
            } elseif ($status === 'Rechazado') {
                $rejectedAmount = $requestedAmount;
            }

            // Generar fechas cronológicas coherentes
            $diasAtras = rand(5, 110);
            $serviceDate = Carbon::now()->subDays($diasAtras);
            $paymentDate = $serviceDate->copy()->addDays(rand(1, 10)); // Afiliado pagó poco después
            $receivedDate = $paymentDate->copy()->addDays(rand(5, 30)); // Solicitó reembolso semanas después

            // Si está muy cercano al presente, asegurar que no supere los 120 días
            if ($paymentDate->diffInDays(Carbon::now()) > 120) {
                $paymentDate = Carbon::now()->subDays(30);
                $serviceDate = $paymentDate->copy()->subDays(5);
                $receivedDate = Carbon::now()->subDays(10);
            }

            $caseNumber = 'REEM-' . Carbon::now()->year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);

            // Plazo de respuesta: 10 días hábiles
            $responseDueDate = $receivedDate->copy()->addDays(14); 

            $finalDecision = null;
            $respondedAt = null;

            if (in_array($status, ['Aprobado', 'Aprobado parcial', 'Rechazado', 'Cerrado'])) {
                $respondedAt = $receivedDate->copy()->addDays(rand(2, 9));
                if ($status === 'Aprobado') {
                    $finalDecision = 'Se aprueba el reembolso total al haberse comprobado la procedencia de la cobertura según normativa SFS.';
                } elseif ($status === 'Aprobado parcial') {
                    $finalDecision = 'Se aprueba de forma parcial. Algunos de los insumos médicos reclamados no forman parte de la cobertura del PDSS regulado.';
                } else {
                    $finalDecision = 'Se rechaza la solicitud de reembolso al verificarse que el servicio médico recibido se encuentra excluido del catálogo de prestaciones de la SISALRIL.';
                }
            }

            // Crear el caso
            $case = ReimbursementCase::create([
                'case_number' => $caseNumber,
                'afiliado_id' => $afiliadoId,
                'origin' => $origenes[$i % count($origenes)],
                'request_channel' => $canales[$i % count($canales)],
                'request_type' => $requestType,
                'pss_id' => $pss->id,
                'service_date' => $serviceDate->toDateString(),
                'payment_date' => $paymentDate->toDateString(),
                'requested_amount' => $requestedAmount,
                'approved_amount' => $approvedAmount,
                'rejected_amount' => $rejectedAmount,
                'status' => $status,
                'received_at' => $receivedDate,
                'completed_documents_at' => in_array($status, ['Expediente completo', 'En revisión', 'Aprobado', 'Rechazado', 'Aprobado parcial', 'Cerrado']) ? $receivedDate->copy()->addDays(1) : null,
                'response_due_date' => $responseDueDate->toDateString(),
                'responded_at' => $respondedAt,
                'final_decision' => $finalDecision,
                'pss_debit_required' => false, // Se actualizará al postear contabilidad
                'created_by' => $userId,
            ]);

            // Cargar documentos del expediente
            $this->seedDocumentsForCase($case, $receivedDate, $userId);

            // Cargar acciones de trazabilidad
            $this->seedActionsForCase($case, $receivedDate, $status, $userId);

            // 4. Integrar contabilidad para casos aprobados
            if (($status === 'Aprobado' || $status === 'Aprobado parcial' || $status === 'Cerrado') && $approvedAmount > 0) {
                // Marcar requerimiento de débito a PSS si es cobro indebido
                if ($requestType === 'cobro_indebido' || $requestType === 'ambas') {
                    $case->update(['pss_debit_required' => true]);
                }

                // Generar los asientos contables utilizando el servicio del core administrativo
                try {
                    // 1. Asiento de desembolso al afiliado
                    AccountingPostingService::registrarReembolsoCaso($case);

                    // 2. Si es cobro indebido, generar compensación automática contra CXP de la PSS (débito a PSS)
                    if ($case->pss_debit_required) {
                        AccountingPostingService::registrarCompensacionPss($case);
                    }
                } catch (\Exception $e) {
                    $this->command->error("Error contabilizando caso {$caseNumber}: " . $e->getMessage());
                }
            }

            $count++;
        }

        $this->command->info("Se sembraron {$totalCases} casos de reembolsos excepcionales con sus correspondientes flujos contables y de auditoría.");
    }

    /**
     * Genera los documentos soporte del reembolso.
     */
    private function seedDocumentsForCase(ReimbursementCase $case, Carbon $baseDate, int $userId): void
    {
        $docs = [
            ['type' => 'Factura Original', 'file' => 'factura_reembolso_' . $case->case_number . '.pdf'],
            ['type' => 'Recibo de Pago', 'file' => 'recibo_pago_' . $case->case_number . '.pdf'],
            ['type' => 'Indicación Médica', 'file' => 'indicacion_medica_' . $case->case_number . '.pdf'],
        ];

        // Si es pendiente de documentos, no subimos la indicación médica (faltante)
        $limit = $case->status === 'Pendiente de documentos' ? 1 : count($docs);

        for ($idx = 0; $idx < $limit; $idx++) {
            $doc = $docs[$idx];
            ReimbursementDocument::create([
                'reimbursement_case_id' => $case->id,
                'document_type' => $doc['type'],
                'file_path' => 'documentos/reembolsos/' . $doc['file'],
                'status' => 'Válido',
                'uploaded_at' => $baseDate->copy()->addMinutes(15 * ($idx + 1)),
                'uploaded_by' => $userId,
            ]);
        }
    }

    /**
     * Genera los eventos de trazabilidad (acciones) del reembolso.
     */
    private function seedActionsForCase(ReimbursementCase $case, Carbon $baseDate, string $status, int $userId): void
    {
        // 1. Recepción siempre existe
        ReimbursementAction::create([
            'reimbursement_case_id' => $case->id,
            'action_type' => 'Recepción',
            'description' => "Caso de reembolso {$case->case_number} registrado formalmente en el sistema.",
            'user_id' => $userId,
            'created_at' => $baseDate,
        ]);

        // 2. Carga de Documentos
        ReimbursementAction::create([
            'reimbursement_case_id' => $case->id,
            'action_type' => 'Carga Documento',
            'description' => "Documentos soporte cargados para validación inicial (Factura Original y Recibo de Pago).",
            'user_id' => $userId,
            'created_at' => $baseDate->copy()->addMinutes(30),
        ]);

        // 3. Expediente completo (si no está recibido o pendiente)
        if (!in_array($status, ['Recibido', 'Pendiente de documentos'])) {
            ReimbursementAction::create([
                'reimbursement_case_id' => $case->id,
                'action_type' => 'Expediente Completo',
                'description' => "Se ha verificado el expediente con los documentos completos. Inicia el plazo legal de respuesta.",
                'user_id' => $userId,
                'created_at' => $baseDate->copy()->addDays(1),
            ]);
        }

        // 4. En revisión
        if (in_array($status, ['En revisión', 'Aprobado', 'Rechazado', 'Aprobado parcial', 'Cerrado'])) {
            ReimbursementAction::create([
                'reimbursement_case_id' => $case->id,
                'action_type' => 'Cambio Estado',
                'description' => "Caso asignado a analista de auditoría médica para revisión de cobertura.",
                'user_id' => $userId,
                'created_at' => $baseDate->copy()->addDays(2),
            ]);
        }

        // 5. Resolución
        if (in_array($status, ['Aprobado', 'Rechazado', 'Aprobado parcial', 'Cerrado'])) {
            $actionType = $status === 'Rechazado' ? 'Rechazo' : 'Aprobación';
            $desc = $status === 'Rechazado' 
                ? "Solicitud de reembolso rechazada. Decisión: {$case->final_decision}" 
                : "Solicitud de reembolso autorizada por DOP " . number_format($case->approved_amount, 2) . ".";

            ReimbursementAction::create([
                'reimbursement_case_id' => $case->id,
                'action_type' => $actionType,
                'description' => $desc,
                'user_id' => $userId,
                'created_at' => $case->responded_at,
            ]);

            // Si es cobro indebido aprobado, se generó un débito a PSS
            if ($case->pss_debit_required) {
                ReimbursementAction::create([
                    'reimbursement_case_id' => $case->id,
                    'action_type' => 'Cambio Estado',
                    'description' => "Generación de débito automático de DOP " . number_format($case->approved_amount, 2) . " aplicado contra la PSS por cobro indebido.",
                    'user_id' => $userId,
                    'created_at' => $case->responded_at->copy()->addMinutes(5),
                ]);
            }
        }
    }
}
