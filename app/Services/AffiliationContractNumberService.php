<?php

namespace App\Services;

use App\Models\AffiliationContractRange;
use App\Models\AffiliationContractNumber;
use App\Models\AffiliationContractMovement;
use App\Models\AffiliationContractReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliationContractNumberService
{
    /**
     * Obtiene el próximo número de contrato disponible de los rangos activos.
     */
    public static function getNextAvailableNumber()
    {
        return DB::transaction(function () {
            return AffiliationContractNumber::where('status', 'disponible')
                ->whereHas('range', function ($query) {
                    $query->where('status', 'activo');
                })
                ->lockForUpdate()
                ->first();
        });
    }

    /**
     * Reserva temporalmente un número de contrato.
     */
    public static function reserveNumber($numberId, $minutes = 15, $token = null, $userId = null)
    {
        return DB::transaction(function () use ($numberId, $minutes, $token, $userId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            if ($number->status !== 'disponible') {
                throw new \Exception("El contrato {$number->contract_number} no se encuentra disponible para reserva.");
            }

            $token = $token ?: Str::uuid()->toString();
            $expiresAt = now()->addMinutes($minutes);

            $oldStatus = $number->status;
            $number->update([
                'status' => 'reservado',
                'reservation_token' => $token,
                'reserved_at' => now(),
                'reservation_expires_at' => $expiresAt,
                'assigned_to_user_id' => $userId
            ]);

            // Crear reserva temporal
            AffiliationContractReservation::create([
                'contract_number_id' => $number->id,
                'reserved_by' => $userId,
                'expires_at' => $expiresAt,
                'status' => 'activa'
            ]);

            self::logMovement($number, 'reserva', $oldStatus, 'reservado', $userId, "Contrato reservado temporalmente por token {$token}");

            return $number;
        });
    }

    /**
     * Consume un número de contrato y lo asocia permanentemente a un afiliado.
     */
    public static function consumeNumber($numberId, $affiliateId, $userId = null)
    {
        return DB::transaction(function () use ($numberId, $affiliateId, $userId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            if ($number->status !== 'disponible' && $number->status !== 'reservado') {
                throw new \Exception("El contrato {$number->contract_number} no se puede consumir porque está en estado: {$number->status}.");
            }

            $oldStatus = $number->status;

            // Marcar reserva anterior como consumida si existiese
            AffiliationContractReservation::where('contract_number_id', $number->id)
                ->where('status', 'activa')
                ->update([
                    'status' => 'consumida',
                    'consumed_at' => now()
                ]);

            $number->update([
                'status' => 'usado',
                'assigned_to_affiliate_id' => $affiliateId,
                'used_at' => now()
            ]);

            self::logMovement($number, 'consumo', $oldStatus, 'usado', $userId, "Contrato consumido y asignado al afiliado ID: {$affiliateId}", $affiliateId);

            return $number;
        });
    }

    /**
     * Libera un número de contrato reservado.
     */
    public static function releaseNumber($numberId, $reason = 'Liberado por el sistema/usuario', $userId = null)
    {
        return DB::transaction(function () use ($numberId, $reason, $userId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            if ($number->status !== 'reservado') {
                return $number; // Ya fue liberado o consumido
            }

            $oldStatus = $number->status;

            // Marcar reserva anterior como liberada
            AffiliationContractReservation::where('contract_number_id', $number->id)
                ->where('status', 'activa')
                ->update([
                    'status' => 'liberada',
                    'released_at' => now()
                ]);

            $number->update([
                'status' => 'disponible',
                'reservation_token' => null,
                'reserved_at' => null,
                'reservation_expires_at' => null,
                'assigned_to_user_id' => null,
                'released_at' => now(),
                'released_by' => $userId,
                'release_reason' => $reason
            ]);

            self::logMovement($number, 'liberacion', $oldStatus, 'disponible', $userId, "Contrato liberado de la reserva. Motivo: {$reason}");

            return $number;
        });
    }

    /**
     * Bloquea un número de contrato.
     */
    public static function blockNumber($numberId, $reason = 'Bloqueado por auditoría/daño', $userId = null)
    {
        return DB::transaction(function () use ($numberId, $reason, $userId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            $oldStatus = $number->status;

            // Marcar reserva anterior como liberada si aplicase
            if ($oldStatus === 'reservado') {
                AffiliationContractReservation::where('contract_number_id', $number->id)
                    ->where('status', 'activa')
                    ->update([
                        'status' => 'liberada',
                        'released_at' => now()
                    ]);
            }

            $number->update([
                'status' => 'bloqueado',
                'blocked_at' => now(),
                'blocked_by' => $userId,
                'block_reason' => $reason
            ]);

            self::logMovement($number, 'bloqueo', $oldStatus, 'bloqueo', $userId, "Contrato bloqueado. Motivo: {$reason}");

            return $number;
        });
    }

    /**
     * Marca un número como enviado a Unipago.
     */
    public static function markAsSentToUnipago($numberId, $batchId)
    {
        return DB::transaction(function () use ($numberId, $batchId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            $oldStatus = $number->status;
            $number->update([
                'status' => 'enviado_unipago',
                'assigned_to_batch_id' => $batchId,
                'sent_to_unipago_at' => now()
            ]);

            self::logMovement($number, 'envio_unipago', $oldStatus, 'enviado_unipago', null, "Enviado a Unipago en Lote ID: {$batchId}", null, $batchId);

            return $number;
        });
    }

    /**
     * Marca un número como aprobado exitosamente.
     */
    public static function markAsOk($numberId)
    {
        return DB::transaction(function () use ($numberId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            $oldStatus = $number->status;
            $number->update([
                'status' => 'ok',
                'unipago_response_status' => 'OK'
            ]);

            self::logMovement($number, 'respuesta_ok', $oldStatus, 'ok', null, "Aceptado exitosamente por Unipago.");

            return $number;
        });
    }

    /**
     * Marca un número como pendiente en Unipago.
     */
    public static function markAsPending($numberId)
    {
        return DB::transaction(function () use ($numberId) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            $oldStatus = $number->status;
            $number->update([
                'status' => 'pe',
                'unipago_response_status' => 'PE'
            ]);

            self::logMovement($number, 'respuesta_pe', $oldStatus, 'pe', null, "Colocado en estatus Pendiente (PE) por Unipago.");

            return $number;
        });
    }

    /**
     * Marca un número como rechazado en Unipago.
     */
    public static function markAsRejected($numberId, $reason)
    {
        return DB::transaction(function () use ($numberId, $reason) {
            $number = AffiliationContractNumber::lockForUpdate()->findOrFail($numberId);

            $oldStatus = $number->status;
            $number->update([
                'status' => 're',
                'unipago_response_status' => 'RE',
                'unipago_response_message' => $reason
            ]);

            self::logMovement($number, 'respuesta_re', $oldStatus, 're', null, "Rechazado por Unipago. Motivo: {$reason}");

            return $number;
        });
    }

    /**
     * Valida si un número pertenece a un rango de formularios activo.
     */
    public static function validateNumberBelongsToActiveRange($number)
    {
        return AffiliationContractRange::where('status', 'activo')
            ->where('start_number', '<=', $number)
            ->where('end_number', '>=', $number)
            ->exists();
    }

    /**
     * Valida si un número de contrato está libre para uso.
     */
    public static function validateNumberIsAvailable($number)
    {
        $numRecord = AffiliationContractNumber::where('contract_number', $number)->first();
        return $numRecord && $numRecord->status === 'disponible';
    }

    /**
     * Valida que no se duplique el uso de un formulario.
     */
    public static function validateNoDuplicateUsage($number)
    {
        return !AffiliationContractNumber::where('contract_number', $number)
            ->whereIn('status', ['usado', 'enviado_unipago', 'ok', 'pe'])
            ->exists();
    }

    /**
     * Helper privado para registrar el movimiento de auditoría e invocar el recálculo atómico de contadores.
     */
    private static function logMovement(AffiliationContractNumber $number, $type, $old, $new, $userId, $desc, $affiliateId = null, $batchId = null)
    {
        AffiliationContractMovement::create([
            'contract_number_id' => $number->id,
            'movement_type' => $type,
            'old_status' => $old,
            'new_status' => $new,
            'user_id' => $userId,
            'affiliate_id' => $affiliateId,
            'batch_id' => $batchId,
            'description' => $desc,
            'created_at' => now()
        ]);

        // Recalcular los contadores del rango de forma atómica
        $number->range->recalculateCounts();
    }
}
