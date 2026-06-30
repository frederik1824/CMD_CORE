<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class SisalrilController extends Controller
{
    public function esquema31()
    {
        // Afiliados con planes complementarios/alternativos
        $afiliados = Afiliado::where("tipo_afiliacion", "Traspaso")->get(); // Simulado
        return view("ars.sisalril.esquema31", compact("afiliados"));
    }

    public function esquema33()
    {
        // Titulares voluntarios
        $afiliados = Afiliado::where("sexo", "F")->take(15)->get(); // Simulado
        return view("ars.sisalril.esquema33", compact("afiliados"));
    }

    public function esquema34()
    {
        // Dependientes voluntarios
        $afiliados = Afiliado::where("sexo", "M")->take(15)->get(); // Simulado
        return view("ars.sisalril.esquema34", compact("afiliados"));
    }

    public function reportes()
    {
        return view("ars.sisalril.reportes");
    }

    public function exportaciones()
    {
        return view("ars.sisalril.exportaciones");
    }

    public function exportarEsquema(Request $request, $esquema)
    {
        Bitacora::registrar("SISALRIL", "Exportado reporte regulatorio de SISALRIL: Esquema {$esquema}.");
        
        // Simular descarga de TXT
        $headers = [
            "Content-type" => "text/plain",
            "Content-Disposition" => "attachment; filename=esquema_{$esquema}_" . now()->format("Ymd") . ".txt",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ];

        $callback = function() use ($esquema) {
            $file = fopen("php://output", "w");
            fwrite($file, "PERIODO|RNC|NSS|CEDULA|NOMBRES|PRIMER_APELLIDO|SEXO|PLAN\n");
            fwrite($file, "202606|101002034|10896189790|07900175907|JUAN|PEREZ|M|Complementario\n");
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}