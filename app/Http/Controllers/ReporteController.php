<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Novedad;
use App\Models\Autorizacion;
use App\Models\Lote;
use App\Models\Pss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Muestra la pantalla general de Reportes Ejecutivos.
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->toDateString());
        $fechaFin = $request->get('fecha_fin', now()->toDateString());

        // 1. Afiliaciones por estado
        $afiliacionesEstado = Afiliado::select('estado_afiliacion', DB::raw('count(*) as total'))
            ->groupBy('estado_afiliacion')
            ->get();

        // 2. Dependientes por estado
        $dependientesEstado = Dependiente::select('estado_afiliacion', DB::raw('count(*) as total'))
            ->groupBy('estado_afiliacion')
            ->get();

        // 3. Novedades por tipo
        $novedadesTipo = Novedad::select('tipo_novedad_id', DB::raw('count(*) as total'))
            ->groupBy('tipo_novedad_id')
            ->with('tipoNovedad')
            ->get();

        // 4. Autorizaciones por PSS (Top 5)
        $autorizacionesPss = Autorizacion::select('pss_id', DB::raw('count(*) as total'), DB::raw('sum(monto_solicitado) as total_monto'))
            ->whereBetween('fecha_solicitud', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->groupBy('pss_id')
            ->with('pss')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 5. Autorizaciones por estado
        $autorizacionesEstado = Autorizacion::select('estado', DB::raw('count(*) as total'))
            ->whereBetween('fecha_solicitud', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->groupBy('estado')
            ->get();

        // 6. Tiempo promedio de respuesta (en minutos)
        // Para SQLite, usamos strftime o resta de timestamps
        $tiempoRespuestaRaw = Autorizacion::whereNotNull('fecha_respuesta')
            ->whereBetween('fecha_solicitud', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->get();

        $tiempoPromedioMinutos = 0;
        if ($tiempoRespuestaRaw->count() > 0) {
            $sumDiferencia = 0;
            foreach ($tiempoRespuestaRaw as $aut) {
                $sol = \Carbon\Carbon::parse($aut->fecha_solicitud);
                $resp = \Carbon\Carbon::parse($aut->fecha_respuesta);
                $sumDiferencia += $resp->diffInMinutes($sol);
            }
            $tiempoPromedioMinutos = round($sumDiferencia / $tiempoRespuestaRaw->count(), 1);
        }

        // 7. Lotes procesados por tipo
        $lotesTipo = Lote::select('tipo_lote', DB::raw('count(*) as total'))
            ->groupBy('tipo_lote')
            ->get();

        // 8. Rechazos de autorizaciones por motivo (Top 5)
        $rechazosMotivo = Autorizacion::select('motivo_estado', DB::raw('count(*) as total'))
            ->where('estado', 'Rechazada')
            ->whereBetween('fecha_solicitud', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->groupBy('motivo_estado')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return view('ars.reportes.index', compact(
            'fechaInicio',
            'fechaFin',
            'afiliacionesEstado',
            'dependientesEstado',
            'novedadesTipo',
            'autorizacionesPss',
            'autorizacionesEstado',
            'tiempoPromedioMinutos',
            'lotesTipo',
            'rechazosMotivo'
        ));
    }
}
