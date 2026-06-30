<?php

namespace App\Http\Controllers;

use App\Models\CarnetRequest;
use App\Models\CarnetDelivery;
use App\Models\CarnetTransfer;
use App\Models\PrintingCenter;
use App\Models\PrintingSupply;
use App\Models\PrintingSupplyMovement;
use App\Models\CarnetAdjustment;
use App\Models\Afiliado;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarnetizacionController extends Controller
{
    public function solicitudes()
    {
        $solicitudes = CarnetRequest::with("printingCenter")->orderBy("request_date", "desc")->get();
        $afiliados = Afiliado::where("estado_afiliacion", "OK")->get();
        $centros = PrintingCenter::all();
        return view("ars.carnetizacion.solicitudes", compact("solicitudes", "afiliados", "centros"));
    }

    public function crearSolicitud(Request $request)
    {
        $request->validate([
            "affiliate_id" => "required|exists:afiliados,id",
            "printing_center_id" => "required|exists:printing_centers,id",
            "request_type" => "required|string"
        ]);

        CarnetRequest::create([
            "affiliate_id" => $request->affiliate_id,
            "affiliate_type" => "titular",
            "request_type" => $request->request_type,
            "printing_center_id" => $request->printing_center_id,
            "request_date" => now()->toDateString(),
            "status" => "Solicitado"
        ]);

        return redirect()->route("ars.carnetizacion.solicitudes")->with("success", "Solicitud de carnetización registrada.");
    }

    public function impresion()
    {
        $solicitudes = CarnetRequest::where("status", "Solicitado")->with("printingCenter")->get();
        $centros = PrintingCenter::all();
        return view("ars.carnetizacion.impresion", compact("solicitudes", "centros"));
    }

    public function procesarImpresion(Request $request)
    {
        $request->validate(["request_ids" => "required|array"]);

        foreach ($request->request_ids as $reqId) {
            $req = CarnetRequest::findOrFail($reqId);
            
            // Consumir insumo plástico
            $supply = PrintingSupply::where("supply_family", "plastico")->first();
            if ($supply && $supply->current_stock > 0) {
                $supply->decrement("current_stock", 1);
                
                PrintingSupplyMovement::create([
                    "supply_id" => $supply->id,
                    "printing_center_id" => $req->printing_center_id,
                    "movement_type" => "salida",
                    "quantity" => 1,
                    "reason" => "Impresión de carnet request ID {$req->id}",
                    "user_id" => Auth::id() ?? 1
                ]);
            }

            $req->update([
                "status" => "Impreso",
                "print_date" => now()->toDateString(),
                "batch_number" => "BATCH-" . now()->format("Ymd")
            ]);
            
            // Actualizar afiliado
            $afiliado = Afiliado::find($req->affiliate_id);
            if ($afiliado) {
                $afiliado->update(["esta_carnetizado" => true]);
            }
        }

        return redirect()->route("ars.carnetizacion.solicitudes")->with("success", "Lote de carnets impreso.");
    }

    public function tiposCarnets()
    {
        return view("ars.carnetizacion.tipos_carnets");
    }

    public function conceptos()
    {
        return view("ars.carnetizacion.conceptos");
    }

    public function entregas()
    {
        $entregas = CarnetDelivery::with("request")->get();
        $impresos = CarnetRequest::where("status", "Impreso")->get();
        return view("ars.carnetizacion.entregas", compact("entregas", "impresos"));
    }

    public function registrarEntrega(Request $request)
    {
        $request->validate([
            "carnet_request_id" => "required|exists:carnet_requests,id",
            "recipient_name" => "required|string"
        ]);

        CarnetDelivery::create([
            "carnet_request_id" => $request->carnet_request_id,
            "recipient_name" => $request->recipient_name,
            "delivery_date" => now()->toDateString(),
            "status" => "Entregado"
        ]);

        $req = CarnetRequest::findOrFail($request->carnet_request_id);
        $req->update(["status" => "Entregado"]);

        return redirect()->route("ars.carnetizacion.entregas")->with("success", "Carnet entregado formalmente al afiliado.");
    }

    public function transferencias()
    {
        $transferencias = CarnetTransfer::with("request")->get();
        return view("ars.carnetizacion.transferencias", compact("transferencias"));
    }

    public function registrarTransferencia(Request $request)
    {
        $request->validate([
            "carnet_request_id" => "required|exists:carnet_requests,id",
            "origin_location" => "required|string",
            "destination_location" => "required|string"
        ]);

        CarnetTransfer::create([
            "carnet_request_id" => $request->carnet_request_id,
            "origin_location" => $request->origin_location,
            "destination_location" => $request->destination_location,
            "sent_date" => now()->toDateString(),
            "status" => "En tránsito"
        ]);

        return redirect()->route("ars.carnetizacion.transferencias")->with("success", "Transferencia despachada.");
    }

    public function localizaciones()
    {
        return view("ars.carnetizacion.localizaciones");
    }

    public function centrosImpresion()
    {
        $centros = PrintingCenter::all();
        return view("ars.carnetizacion.centros_impresion", compact("centros"));
    }

    public function guardarCentroImpresion(Request $request)
    {
        $request->validate(["name" => "required|string"]);
        PrintingCenter::create($request->all());
        return redirect()->route("ars.carnetizacion.centros_impresion")->with("success", "Centro de impresión guardado.");
    }

    public function insumos()
    {
        $insumos = PrintingSupply::all();
        $movimientos = PrintingSupplyMovement::with(["supply", "printingCenter"])->orderBy("created_at", "desc")->take(30)->get();
        $centros = PrintingCenter::all();
        return view("ars.carnetizacion.insumos", compact("insumos", "movimientos", "centros"));
    }

    public function guardarInsumo(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "supply_family" => "required|string",
            "initial_stock" => "required|integer"
        ]);

        PrintingSupply::create([
            "name" => $request->name,
            "supply_family" => $request->supply_family,
            "initial_stock" => $request->initial_stock,
            "current_stock" => $request->initial_stock,
            "unit" => "Unidad"
        ]);

        return redirect()->route("ars.carnetizacion.insumos")->with("success", "Insumo registrado.");
    }

    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            "supply_id" => "required|exists:printing_supplies,id",
            "printing_center_id" => "nullable|exists:printing_centers,id",
            "movement_type" => "required|string",
            "quantity" => "required|integer|min:1",
            "reason" => "nullable|string"
        ]);

        $supply = PrintingSupply::findOrFail($request->supply_id);

        if ($request->movement_type === "salida") {
            $supply->decrement("current_stock", $request->quantity);
        } else {
            $supply->increment("current_stock", $request->quantity);
        }

        PrintingSupplyMovement::create([
            "supply_id" => $supply->id,
            "printing_center_id" => $request->printing_center_id,
            "movement_type" => $request->movement_type,
            "quantity" => $request->quantity,
            "reason" => $request->reason,
            "user_id" => Auth::id() ?? 1
        ]);

        return redirect()->route("ars.carnetizacion.insumos")->with("success", "Movimiento de inventario guardado.");
    }

    public function ajustes()
    {
        $ajustes = CarnetAdjustment::with(["supply", "printingCenter"])->get();
        return view("ars.carnetizacion.ajustes", compact("ajustes"));
    }

    public function registrarAjuste(Request $request)
    {
        $request->validate([
            "supply_id" => "required|exists:printing_supplies,id",
            "printing_center_id" => "required|exists:printing_centers,id",
            "adjustment_type" => "required|string",
            "quantity" => "required|integer",
            "reason" => "required|string"
        ]);

        CarnetAdjustment::create([
            "supply_id" => $request->supply_id,
            "printing_center_id" => $request->printing_center_id,
            "adjustment_type" => $request->adjustment_type,
            "quantity" => $request->quantity,
            "reason" => $request->reason,
            "user_id" => Auth::id() ?? 1
        ]);

        return redirect()->route("ars.carnetizacion.ajustes")->with("success", "Ajuste de impresión guardado.");
    }

    public function despachos()
    {
        $movimientos = PrintingSupplyMovement::where("movement_type", "transferencia")->with("supply")->get();
        return view("ars.carnetizacion.despachos", compact("movimientos"));
    }

    public function devoluciones()
    {
        $devoluciones = CarnetTransfer::where("status", "Devuelto")->with("request")->get();
        return view("ars.carnetizacion.devoluciones", compact("devoluciones"));
    }

    public function reportes()
    {
        return view("ars.carnetizacion.reportes");
    }
}