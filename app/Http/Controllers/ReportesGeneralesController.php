<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class ReportesGeneralesController extends Controller
{
    public function index()
    {
        return view("ars.reportes.index_generales");
    }

    public function programarReporte(Request $request)
    {
        $request->validate([
            "report_name" => "required|string",
            "frequency" => "required|string"
        ]);

        Bitacora::registrar("Reportes", "Reporte programado: {$request->report_name} ({$request->frequency})");

        return redirect()->route("ars.reportes_generales.index")->with("success", "Reporte programado exitosamente de manera simulada.");
    }
}