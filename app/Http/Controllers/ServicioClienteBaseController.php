<?php

namespace App\Http\Controllers;

use App\Models\CustomerCase;
use App\Models\Afiliado;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class ServicioClienteBaseController extends Controller
{
    public function index()
    {
        $casos = CustomerCase::with("affiliate")->orderBy("created_at", "desc")->get();
        $afiliados = Afiliado::where("estado_afiliacion", "OK")->get();
        return view("ars.servicio-cliente.index", compact("casos", "afiliados"));
    }

    public function casos()
    {
        $casos = CustomerCase::with("affiliate")->paginate(15);
        return view("ars.servicio-cliente.casos", compact("casos"));
    }

    public function registrarCaso(Request $request)
    {
        $request->validate([
            "affiliate_id" => "required|exists:afiliados,id",
            "case_type" => "required|string",
            "description" => "required|string"
        ]);

        CustomerCase::create([
            "affiliate_id" => $request->affiliate_id,
            "case_type" => $request->case_type,
            "description" => $request->description,
            "status" => "Abierto",
            "priority" => "Media",
            "sla_hours" => 72
        ]);

        Bitacora::registrar("Servicio al Cliente", "Registrado caso de servicio al cliente tipo {$request->case_type}");

        return redirect()->route("ars.servicio_cliente.index")->with("success", "Caso de servicio al cliente registrado exitosamente.");
    }

    public function seguimiento()
    {
        $casos = CustomerCase::where("status", "En proceso")->with("affiliate")->get();
        return view("ars.servicio-cliente.seguimiento", compact("casos"));
    }

    public function resolverCaso(Request $request, $id)
    {
        $request->validate(["resolution_details" => "required|string"]);
        
        $caso = CustomerCase::findOrFail($id);
        $caso->update([
            "status" => "Resuelto",
            "resolved_at" => now(),
            "resolution_details" => $request->resolution_details
        ]);

        return redirect()->route("ars.servicio_cliente.index")->with("success", "Caso resuelto y cerrado.");
    }
}