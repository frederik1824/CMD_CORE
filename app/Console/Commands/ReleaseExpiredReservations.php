<?php

namespace App\Console\Commands;

use App\Models\AffiliationContractNumber;
use App\Models\AffiliationContractReservation;
use App\Services\AffiliationContractNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:release-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera automáticamente las reservas de números de contrato/formulario de afiliación que han expirado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando liberación de reservas expiradas...');

        $expiredReservations = AffiliationContractReservation::where('status', 'activa')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expiredReservations as $res) {
            DB::transaction(function () use ($res, &$count) {
                // Marcar la reserva como expirada
                $res->update(['status' => 'expirada']);

                // Liberar el número de contrato correspondiente
                AffiliationContractNumberService::releaseNumber(
                    $res->contract_number_id,
                    'Reserva temporal expirada por límite de tiempo'
                );

                $count++;
            });
        }

        $this->info("Proceso completado. Se liberaron {$count} reservas de formularios.");
        return Command::SUCCESS;
    }
}
