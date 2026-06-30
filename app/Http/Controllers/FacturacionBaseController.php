<?php

namespace App\Http\Controllers;

use App\Models\BillingInvoice;
use App\Models\AffiliateGroup;
use App\Models\HealthPlan;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class FacturacionBaseController extends Controller
{
    public function index()
    {
        $invoices = BillingInvoice::with(["plan", "group"])->get();
        $planes = HealthPlan::all();
        $grupos = AffiliateGroup::all();
        return view("ars.facturacion.index", compact("invoices", "planes", "grupos"));
    }

    public function planesAlternativos()
    {
        $invoices = BillingInvoice::whereNotNull("health_plan_id")->with("plan")->get();
        return view("ars.facturacion.planes_alternativos", compact("invoices"));
    }

    public function gruposAfiliados()
    {
        $invoices = BillingInvoice::whereNotNull("affiliate_group_id")->with("group")->get();
        return view("ars.facturacion.grupos_afiliados", compact("invoices"));
    }

    public function comprobantes()
    {
        $invoices = BillingInvoice::whereNotNull("ncf")->get();
        return view("ars.facturacion.comprobantes", compact("invoices"));
    }

    public function emitirFactura(Request $request)
    {
        $request->validate([
            "amount" => "required|numeric|min:1"
        ]);

        $invNum = "FAC-" . now()->format("Ymd") . "-" . str_pad(rand(1, 9999), 4, "0", STR_PAD_LEFT);
        $ncf = "B01" . str_pad(rand(1, 99999999), 8, "0", STR_PAD_LEFT);

        BillingInvoice::create([
            "invoice_number" => $invNum,
            "health_plan_id" => $request->health_plan_id,
            "affiliate_group_id" => $request->affiliate_group_id,
            "amount" => $request->amount,
            "ncf" => $ncf,
            "status" => "Emitida",
            "issued_at" => now()->toDateString(),
            "due_date" => now()->addDays(30)->toDateString()
        ]);

        Bitacora::registrar("Facturación", "Factura emitida {$invNum} con NCF {$ncf} por DOP {$request->amount}");

        return redirect()->route("ars.facturacion.index")->with("success", "Factura emitida exitosamente.");
    }
}