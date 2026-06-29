<?php

namespace App\Services;

use App\Models\AuthorizationClaim;
use Carbon\Carbon;

class ClaimAgingService
{
    /**
     * Calcula la antigüedad y la banda de antigüedad de una reclamación.
     */
    public static function getAgingData(AuthorizationClaim $claim): array
    {
        $startDate = $claim->received_at ? Carbon::parse($claim->received_at) : Carbon::parse($claim->created_at);
        $endDate = in_array($claim->status, ['Pagada', 'Conciliada', 'Cerrada']) && $claim->updated_at 
            ? Carbon::parse($claim->updated_at) 
            : Carbon::now();

        $days = (int)$startDate->diffInDays($endDate);
        
        // Determinar banda de antigüedad
        $bucket = '1 a 30 días';
        $color = 'emerald'; // Verde
        
        if ($days > 90) {
            $bucket = 'Más de 90 días';
            $color = 'rose'; // Rojo
        } elseif ($days > 60) {
            $bucket = '61 a 90 días';
            $color = 'orange'; // Naranja
        } elseif ($days > 30) {
            $bucket = '31 a 60 días';
            $color = 'amber'; // Amarillo
        }

        // Si la reclamación está detenida por falta de documentos
        if ($claim->status === 'Pendiente de documento') {
            $bucket = 'Detenida por documentos';
            $color = 'slate';
        }

        // Plazo máximo legal (e.g., 30 días para tramitar)
        $dueDate = $startDate->copy()->addDays(30);
        $daysRemaining = (int)Carbon::now()->diffInDays($dueDate, false);
        $isOverdue = Carbon::now()->greaterThan($dueDate) && !in_array($claim->status, ['Pagada', 'Conciliada', 'Cerrada']);

        return [
            'official_entry_date' => $startDate->toDateString(),
            'current_age_days' => $days,
            'aging_bucket' => $bucket,
            'color_class' => $color,
            'due_date' => $dueDate->toDateString(),
            'days_remaining' => $daysRemaining,
            'is_overdue' => $isOverdue,
            'effective_age_days' => $days
        ];
    }

    /**
     * Retorna estadísticas agrupadas de antigüedad para tableros de control.
     */
    public static function getAgingStats(): array
    {
        $claims = AuthorizationClaim::all();
        
        $stats = [
            'bucket_1_30' => 0,
            'bucket_31_60' => 0,
            'bucket_61_90' => 0,
            'bucket_90_plus' => 0,
            'overdue' => 0,
            'total_claims' => $claims->count(),
            'average_days' => 0,
            'claimed_by_bucket' => [
                '1-30' => 0.0,
                '31-60' => 0.0,
                '61-90' => 0.0,
                '90+' => 0.0
            ]
        ];

        if ($claims->isEmpty()) {
            return $stats;
        }

        $totalDays = 0;
        foreach ($claims as $claim) {
            $aging = self::getAgingData($claim);
            $totalDays += $aging['current_age_days'];

            if ($aging['is_overdue']) {
                $stats['overdue']++;
            }

            $amount = (float)$claim->claimed_amount;

            switch ($aging['aging_bucket']) {
                case '1 a 30 días':
                    $stats['bucket_1_30']++;
                    $stats['claimed_by_bucket']['1-30'] += $amount;
                    break;
                case '31 a 60 días':
                    $stats['bucket_31_60']++;
                    $stats['claimed_by_bucket']['31-60'] += $amount;
                    break;
                case '61 a 90 días':
                    $stats['bucket_61_90']++;
                    $stats['claimed_by_bucket']['61-90'] += $amount;
                    break;
                case 'Más de 90 días':
                    $stats['bucket_90_plus']++;
                    $stats['claimed_by_bucket']['90+'] += $amount;
                    break;
            }
        }

        $stats['average_days'] = round($totalDays / $claims->count(), 1);

        return $stats;
    }
}
