<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Catalogo;
use App\Models\CapitationNotification;
use App\Models\DispersionCut;
use App\Models\DispersionCutDetail;
use App\Models\UnipagoMockNotification;
use Illuminate\Support\Facades\DB;

class MassiveDataSeeder extends Seeder
{
    private array $nombresM = [
        'Luis','Carlos','José','Juan','Miguel','Manuel','Ramón','Francisco','Rafael','Pedro',
        'David','Jorge','Julio','Ángel','Roberto','Eduardo','Héctor','Mario','Rubén','Alejandro',
        'Daniel','Fernando','Ricardo','Alberto','Raúl','Antonio','Enrique','Sergio','Andrés','Oscar',
        'Eugenio','Germán','Jaime','Arturo','Félix','Guillermo','Víctor','Ernesto','René','Samuel',
        'Tomás','Emilio','Gilberto','Nelson','Rogelio','Reinaldo','César','Iván','Belisario','Leandro',
    ];

    private array $nombresF = [
        'María','Ana','Carmen','Juana','Rosa','Francisca','Yolanda','Margarita','Luz','Sandra',
        'Patricia','Elizabeth','Laura','Carolina','Diana','Marta','Teresa','Clara','Elena','Sofía',
        'Beatriz','Raquel','Julia','Isabel','Mónica','Adriana','Claudia','Silvia','Verónica','Lucía',
        'Gloria','Cristina','Alejandra','Andrea','Bárbara','Daniela','Esther','Gabriela','Irene','Karen',
        'Leticia','Melissa','Natalia','Olga','Paula','Rocío','Susana','Valeria','Vanessa','Xiomara',
    ];

    private array $apellidos = [
        'Martínez','Rodríguez','Gómez','Pérez','Sánchez','Díaz','Fernández','Álvarez','Cruz','Ramírez',
        'Flores','Vázquez','Guzmán','Ortiz','Castillo','Reyes','Ramos','Espinal','Vargas','Jiménez',
        'Mejía','Castro','Arias','Santos','Brito','Hernández','López','Mendoza','Núñez','Peña',
        'Bautista','Cordero','De León','Durán','Espinosa','Franco','García','Henríquez','Izquierdo','Lara',
        'Medina','Nova','Olivo','Paredes','Quiroz','Rivera','Sosa','Torres','Ureña','Valdez',
    ];

    private array $provinciasMunicipios = [
        'Distrito Nacional|Santo Domingo de Guzmán',
        'Santo Domingo|Santo Domingo Este',
        'Santiago|Santiago de los Caballeros',
        'San Cristóbal|San Cristóbal',
        'La Altagracia|Salvaleón de Higüey',
        'Duarte|San Francisco de Macorís',
        'Puerto Plata|San Felipe de Puerto Plata',
        'La Vega|Concepción de La Vega',
        'San Pedro de Macorís|San Pedro de Macorís',
        'La Romana|La Romana',
        'Hato Mayor|Hato Mayor del Rey',
        'Monte Plata|Monte Plata',
        'Monseñor Nouel|Bonao',
        'Samaná|Samaná',
        'Espaillat|Moca',
        'Hermanas Mirabal|Salcedo',
        'Valverde|Mao',
        'Dajabón|Dajabón',
        'Peravia|Baní',
        'Azua|Azua de Compostela',
    ];

    private int $tipoIdCed = 0;
    private int $parentescoConyuge = 0;
    private int $parentescoHijo = 0;

    public function run(): void
    {
        DB::statement('PRAGMA journal_mode=WAL');
        DB::statement('PRAGMA busy_timeout=5000');

        $this->tipoIdCed = Catalogo::where('grupo', 'tipo_identificacion')->where('codigo', 'CED')->first()->id;
        $this->parentescoConyuge = Catalogo::where('grupo', 'parentesco')->where('codigo', 'CONYUGE')->first()->id;
        $this->parentescoHijo = Catalogo::where('grupo', 'parentesco')->where('codigo', 'HIJO')->first()->id;

        $this->command->info("Creando 5,000 afiliados...");
        $affiliateIds = $this->createAffiliates(5000);
        $this->command->info("Total afiliados: " . Afiliado::count());

        $this->command->info("Creando ~4,500 dependientes...");
        $this->createDependents($affiliateIds);
        $this->command->info("Total dependientes: " . Dependiente::count());

        $this->command->info("Creando notificaciones de capitación (6 periodos)...");
        $this->createCapitationNotifications(6);
        $this->command->info("Total capitaciones: " . CapitationNotification::count());

        $this->command->info("Creando cortes de dispersión...");
        $this->createDispersionCuts();
        $this->command->info("Total cortes: " . DispersionCut::count());
        $this->command->info("Total detalles: " . DispersionCutDetail::count());

        $this->command->info("Creando notificaciones Unipago...");
        $this->createNotifications();
        $this->command->info("Total notificaciones: " . UnipagoMockNotification::count());

        $total = Afiliado::count() + Dependiente::count() + CapitationNotification::count()
            + DispersionCut::count() + DispersionCutDetail::count() + UnipagoMockNotification::count();

        $this->command->info("=== RESUMEN ===");
        $this->command->info("  Afiliados:         " . number_format(Afiliado::count()));
        $this->command->info("  Dependientes:      " . number_format(Dependiente::count()));
        $this->command->info("  Capitaciones:      " . number_format(CapitationNotification::count()));
        $this->command->info("  Cortes dispersión: " . DispersionCut::count());
        $this->command->info("  Detalles dispersión:" . number_format(DispersionCutDetail::count()));
        $this->command->info("  Notificaciones:    " . number_format(UnipagoMockNotification::count()));
        $this->command->info("  TOTAL:             " . number_format($total));
    }

    private function createAffiliates(int $count): array
    {
        $allIds = [];
        $batchSize = 500;

        for ($start = 1; $start <= $count; $start += $batchSize) {
            $end = min($start + $batchSize - 1, $count);
            $batch = [];

            for ($i = $start; $i <= $end; $i++) {
                $sexo = (mt_rand(0, 1) === 0) ? 'M' : 'F';
                $nombre = ($sexo === 'F') ? $this->nombresF[array_rand($this->nombresF)] : $this->nombresM[array_rand($this->nombresM)];
                $ape1 = $this->apellidos[array_rand($this->apellidos)];
                $ape2 = $this->apellidos[array_rand($this->apellidos)];
                $loc = $this->provinciasMunicipios[array_rand($this->provinciasMunicipios)];
                [$prov, $mun] = explode('|', $loc);

                $estado = $this->weightedRandom(['OK' => 7, 'PE' => 1, 'RE' => 1, 'Pendiente' => 1]);
                $regimen = $this->weightedRandom(['Contributivo' => 2, 'Subsidiado' => 1, 'Subsidiado Parcial' => 1]);

                $motivo = match ($estado) {
                    'OK' => 'Afiliado aprobado por Unipago.',
                    'PE' => 'Pendiente de validación Unipago.',
                    'RE' => 'Sin nómina activa reportada en TSS.',
                    default => 'Pendiente de revisión.',
                };

                $batch[] = [
                    'tipo_identificacion_id' => $this->tipoIdCed,
                    'cedula' => '402' . str_pad($i, 7, '0', STR_PAD_LEFT) . mt_rand(0, 9),
                    'nss' => '1' . str_pad($i, 9, '0', STR_PAD_LEFT),
                    'nui' => '3' . str_pad(10000000 + $i, 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombre,
                    'primer_apellido' => $ape1,
                    'segundo_apellido' => $ape2,
                    'fecha_nacimiento' => now()->subYears(mt_rand(18, 72))->subDays(mt_rand(0, 364))->toDateString(),
                    'sexo' => $sexo,
                    'provincia' => $prov,
                    'municipio' => $mun,
                    'telefono' => '809-' . str_pad(mt_rand(200, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'correo' => strtolower($nombre . '.' . $ape1 . mt_rand(1, 9999) . '@correo.com'),
                    'numero_contrato' => 'CONTR-ARS-' . str_pad(10000 + $i, 8, '0', STR_PAD_LEFT),
                    'fecha_suscripcion' => now()->subYears(mt_rand(1, 5))->subDays(mt_rand(0, 364))->toDateString(),
                    'estado_afiliacion' => $estado,
                    'motivo_estado' => $motivo,
                    'activo_nomina' => ($estado === 'OK'),
                    'tiene_aporte' => ($estado === 'OK'),
                    'regimen_actual' => $regimen,
                    'entidad_actual' => 'ARS Core Demo',
                    'tipo_afiliacion' => ($estado === 'OK') ? $this->weightedRandom(['Normal' => 2, 'Automatica' => 1, 'Traspaso' => 1]) : null,
                    'fecha_afiliacion' => ($estado === 'OK') ? now()->subMonths(mt_rand(1, 36))->toDateString() : null,
                    'ultimo_periodo_pagado' => ($estado === 'OK') ? now()->subMonth(mt_rand(0, 2))->format('Ym') : null,
                    'created_at' => now()->subDays(mt_rand(0, 365)),
                    'updated_at' => now(),
                ];
            }

            DB::transaction(function () use ($batch) {
                Afiliado::insert($batch);
            });

            $latestIds = Afiliado::orderBy('id', 'desc')->take(count($batch))->pluck('id')->reverse()->toArray();
            $allIds = array_merge($allIds, $latestIds);
        }

        return $allIds;
    }

    private function createDependents(array $affiliateIds): void
    {
        $batch = [];
        $batchSize = 500;
        $count = 0;
        $maxDeps = 4500;

        foreach ($affiliateIds as $afId) {
            if ($count >= $maxDeps) break;

            $numDeps = mt_rand(0, 3);
            for ($d = 0; $d < $numDeps && $count < $maxDeps; $d++) {
                $sexo = (mt_rand(0, 1) === 0) ? 'M' : 'F';
                $nombre = ($sexo === 'F') ? $this->nombresF[array_rand($this->nombresF)] : $this->nombresM[array_rand($this->nombresM)];
                $edad = mt_rand(1, 65);
                $parentescoId = ($d === 0 && $edad > 17 && $edad < 65) ? $this->parentescoConyuge : $this->parentescoHijo;
                $estado = (mt_rand(1, 10) > 2) ? 'OK' : 'RE';

                $batch[] = [
                    'titular_id' => $afId,
                    'tipo_identificacion_id' => $this->tipoIdCed,
                    'cedula' => '001' . str_pad($afId * 10 + $d, 7, '0', STR_PAD_LEFT) . mt_rand(0, 9),
                    'nss' => '2' . str_pad($afId * 10 + $d, 9, '0', STR_PAD_LEFT),
                    'nui' => '3' . str_pad(20000000 + ($afId * 10 + $d), 8, '0', STR_PAD_LEFT),
                    'nombres' => $nombre,
                    'apellidos' => $this->apellidos[array_rand($this->apellidos)] . ' ' . $this->apellidos[array_rand($this->apellidos)],
                    'fecha_nacimiento' => now()->subYears($edad)->subDays(mt_rand(0, 364))->toDateString(),
                    'sexo' => $sexo,
                    'parentesco_id' => $parentescoId,
                    'tipo_dependiente' => 'Directo',
                    'estudiante' => ($edad >= 18 && $edad <= 25 && mt_rand(0, 1) === 1),
                    'discapacitado' => (mt_rand(1, 100) <= 3),
                    'nacionalidad' => 'Dominicana',
                    'requiere_documento' => true,
                    'estado_afiliacion' => $estado,
                    'motivo_estado' => ($estado === 'OK') ? 'Dependiente aprobado por Unipago.' : 'Dependiente no cumple criterios.',
                    'created_at' => now()->subDays(mt_rand(0, 365)),
                    'updated_at' => now(),
                ];
                $count++;

                if (count($batch) >= $batchSize) {
                    DB::transaction(function () use ($batch) { Dependiente::insert($batch); });
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            DB::transaction(function () use ($batch) { Dependiente::insert($batch); });
        }
    }

    private function createCapitationNotifications(int $periods): void
    {
        $batch = [];
        $batchSize = 1000;
        $counter = 0;
        $statusPool = ['DI','IC','IC','IC','IC','NT','IR','PE'];
        $indivTypes = ['Capita Normal','Capita Normal','Capita Normal','Capita Especial','Capita Temporal'];

        for ($p = 0; $p < $periods; $p++) {
            $periodDate = now()->subMonths($p);
            $period = $periodDate->format('Ym');

            $afiliados = Afiliado::where('estado_afiliacion', 'OK')->pluck('id');
            foreach ($afiliados as $afId) {
                $counter++;
                $amount = mt_rand(80000, 250000) / 100;
                $status = $statusPool[array_rand($statusPool)];

                $notifiedAt = $periodDate->copy()->addDays(mt_rand(1, 10));
                $confirmedAt = in_array($status, ['IC','DI']) ? $notifiedAt->copy()->addDays(mt_rand(1, 5)) : null;
                $rejectedAt = ($status === 'IR') ? $notifiedAt->copy()->addDays(mt_rand(1, 3)) : null;

                $batch[] = [
                    'notification_number' => 'CAP-' . $period . '-' . str_pad($counter, 7, '0', STR_PAD_LEFT),
                    'afiliado_id' => $afId,
                    'period' => $period,
                    'capitation_amount' => $amount,
                    'individualization_type' => $indivTypes[array_rand($indivTypes)],
                    'status' => $status,
                    'notified_at' => $notifiedAt->toDateTimeString(),
                    'confirmed_at' => $confirmedAt?->toDateTimeString(),
                    'rejected_at' => $rejectedAt?->toDateTimeString(),
                    'rejection_reason' => ($status === 'IR')
                        ? $this->weightedRandom(['Datos inconsistentes con TSS' => 1, 'Afiliado inactivo' => 1, 'Período no válido' => 1, 'Duplicado detectado' => 1])
                        : null,
                    'created_at' => $notifiedAt->toDateTimeString(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    DB::transaction(function () use ($batch) { CapitationNotification::insert($batch); });
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            DB::transaction(function () use ($batch) { CapitationNotification::insert($batch); });
        }
    }

    private function createDispersionCuts(): void
    {
        $periods = [
            now()->subMonths(5), now()->subMonths(4), now()->subMonths(3),
            now()->subMonths(2), now()->subMonth(), now(),
        ];
        $statuses = ['Dispersado','Dispersado','Dispersado','Certificado','Generado','En proceso'];
        $cutTypes = ['Primer Corte','Segundo Corte','Operativo'];

        foreach ($periods as $idx => $periodDate) {
            $period = $periodDate->format('Ym');
            $cutNumber = 'DISP-' . $period . '-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $status = $statuses[$idx];

            $generatedAt = ($status !== 'Programado') ? $periodDate->copy()->addDays(5)->toDateTimeString() : null;
            $certifiedAt = in_array($status, ['Certificado','Dispersado']) ? $periodDate->copy()->addDays(8)->toDateTimeString() : null;
            $dispersedAt = ($status === 'Dispersado') ? $periodDate->copy()->addDays(12)->toDateTimeString() : null;
            $closedAt = ($status === 'Dispersado') ? $periodDate->copy()->addDays(15)->toDateTimeString() : null;

            $cut = DispersionCut::create([
                'cut_number' => $cutNumber,
                'period' => $period,
                'cut_type' => $cutTypes[$idx % 3],
                'status' => $status,
                'total_affiliates' => 0,
                'total_holders' => 0,
                'total_dependents' => 0,
                'total_capitations' => 0,
                'total_amount' => 0,
                'generated_at' => $generatedAt,
                'certified_at' => $certifiedAt,
                'dispersed_at' => $dispersedAt,
                'closed_at' => $closedAt,
                'created_at' => $periodDate->toDateTimeString(),
                'updated_at' => now(),
            ]);

            $periodNotifs = CapitationNotification::where('period', $period)->get();
            $detailBatch = [];
            $totalAmount = 0;
            $totalHolders = 0;
            $totalDeps = 0;

            foreach ($periodNotifs as $notif) {
                $amount = (float) $notif->capitation_amount;
                $totalAmount += $amount;
                if ($notif->individualization_type !== 'Capita Temporal') $totalHolders++; else $totalDeps++;

                $detailBatch[] = [
                    'dispersion_cut_id' => $cut->id,
                    'capitation_notification_id' => $notif->id,
                    'afiliado_id' => $notif->afiliado_id,
                    'amount' => $amount,
                    'status' => $this->weightedRandom(['DI' => 3, 'IC' => 1, 'PE' => 1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($detailBatch) >= 500) {
                    DB::transaction(function () use ($detailBatch) { DispersionCutDetail::insert($detailBatch); });
                    $detailBatch = [];
                }
            }

            if (!empty($detailBatch)) {
                DB::transaction(function () use ($detailBatch) { DispersionCutDetail::insert($detailBatch); });
            }

            $cut->update([
                'total_affiliates' => $periodNotifs->count(),
                'total_holders' => $totalHolders,
                'total_dependents' => $totalDeps,
                'total_capitations' => $periodNotifs->count(),
                'total_amount' => round($totalAmount, 2),
            ]);

            $this->command->info("  Corte {$cutNumber}: {$status} - " . number_format($totalAmount, 2) . " DOP ({$periodNotifs->count()} registros)");
        }
    }

    private function createNotifications(): void
    {
        $batch = [];
        $batchSize = 1000;
        $counter = 0;

        $types = [
            ['type' => 'Lote recibido', 'title' => 'Lote procesado exitosamente'],
            ['type' => 'Capita notificada', 'title' => 'Notificación de capitación'],
            ['type' => 'Capita confirmada', 'title' => 'Confirmación de capita'],
            ['type' => 'Capita rechazada', 'title' => 'Rechazo en proceso de capita'],
            ['type' => 'Dispersión procesada', 'title' => 'Dispersión ejecutada'],
            ['type' => 'Corte generado', 'title' => 'Nuevo corte de dispersión'],
            ['type' => 'Certificación pendiente', 'title' => 'Certificación requiere atención'],
            ['type' => 'Rechazo de pago', 'title' => 'Pago rechazado por entidad financiera'],
            ['type' => 'Aviso de afiliación', 'title' => 'Afiliado registrado en plataforma'],
            ['type' => 'Cambio de estado', 'title' => 'Cambio de estado de afiliación'],
            ['type' => 'Recordatorio de aporte', 'title' => 'Recordatorio: Aporte pendiente'],
            ['type' => 'Actualización de datos', 'title' => 'Datos actualizados en padrón'],
            ['type' => 'Solicitud de documento', 'title' => 'Documento requerido'],
            ['type' => 'Aprobación de reclamación', 'title' => 'Reclamación aprobada parcialmente'],
            ['type' => 'Pago procesado', 'title' => 'Pago confirmado por banco'],
        ];

        $affiliateIds = Afiliado::pluck('id')->toArray();

        foreach ($affiliateIds as $afId) {
            $numNotifs = mt_rand(1, 4);
            for ($n = 0; $n < $numNotifs; $n++) {
                $counter++;
                $typeInfo = $types[array_rand($types)];
                $read = (mt_rand(1, 10) <= 6) ? now()->subDays(mt_rand(0, 30))->toDateTimeString() : null;
                $refTypes = ['batch','capitation','dispersion','claim',null];

                $batch[] = [
                    'notification_type' => $typeInfo['type'],
                    'reference_type' => $refTypes[array_rand($refTypes)],
                    'reference_id' => null,
                    'title' => $typeInfo['title'],
                    'message' => "Notificación #{$counter} para el afiliado #{$afId}: {$typeInfo['title']}.",
                    'read_at' => $read,
                    'metadata' => json_encode(['affiliate_id' => $afId, 'period' => now()->format('Ym')]),
                    'created_at' => now()->subDays(mt_rand(0, 60))->toDateTimeString(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    DB::transaction(function () use ($batch) { UnipagoMockNotification::insert($batch); });
                    $batch = [];
                }
            }
        }

        if (!empty($batch)) {
            DB::transaction(function () use ($batch) { UnipagoMockNotification::insert($batch); });
        }
    }

    /**
     * Helper to select a random key based on weights.
     */
    private function weightedRandom(array $weights): mixed
    {
        $r = mt_rand(1, array_sum($weights));
        foreach ($weights as $key => $weight) {
            $r -= $weight;
            if ($r <= 0) {
                return $key;
            }
        }
        return array_key_first($weights);
    }
}
