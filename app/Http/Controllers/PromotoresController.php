<?php

namespace App\Http\Controllers;

use App\Models\Promoter;
use App\Models\PromoterContract;
use App\Models\PromoterCampaign;
use App\Models\PromoterCommission;
use App\Models\Afiliado;
use App\Models\AccountPayable;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotoresController extends Controller
{
    public function personasFisicas()
    {
        $promotores = Promoter::where("promoter_type", "persona_fisica")->get();
        return view("ars.promotores.personas_fisicas", compact("promotores"));
    }

    public function empresas()
    {
        $promotores = Promoter::where("promoter_type", "empresa")->get();
        return view("ars.promotores.empresas", compact("promotores"));
    }

    public function guardarPromotor(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "promoter_type" => "required|string",
            "identification_number" => "required|string|unique:promoters,identification_number"
        ]);

        Promoter::create($request->all());

        return redirect()->back()->with("success", "Promotor registrado exitosamente.");
    }

    public function tiposContratos()
    {
        $contratos = PromoterContract::with("promoter")->get();
        $promotores = Promoter::all();
        return view("ars.promotores.tipos_contratos", compact("contratos", "promotores"));
    }

    public function guardarContrato(Request $request)
    {
        $request->validate([
            "promoter_id" => "required|exists:promoters,id",
            "contract_number" => "required|string|unique:promoter_contracts,contract_number",
            "start_date" => "required|date",
            "end_date" => "required|date",
            "commission_percent" => "required|numeric"
        ]);

        PromoterContract::create([
            "promoter_id" => $request->promoter_id,
            "contract_number" => $request->contract_number,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "commission_percent" => $request->commission_percent,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.promotores.tipos_contratos")->with("success", "Contrato de promotor configurado.");
    }

    public function campanas()
    {
        $campanas = PromoterCampaign::all();
        return view("ars.promotores.campanas", compact("campanas"));
    }

    public function guardarCampana(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "start_date" => "required|date",
            "end_date" => "required|date",
            "commission_amount" => "required|numeric"
        ]);

        PromoterCampaign::create([
            "name" => $request->name,
            "description" => $request->description,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "commission_amount" => $request->commission_amount,
            "status" => "Activa"
        ]);

        return redirect()->route("ars.promotores.campanas")->with("success", "Campaña comercial configurada.");
    }

    public function esquemasCampana()
    {
        return view("ars.promotores.esquemas_comisiones_campana");
    }

    public function calculoCampana()
    {
        $comisiones = PromoterCommission::with(["promoter", "campaign", "affiliate"])->get();
        $promotores = Promoter::all();
        $campanas = PromoterCampaign::all();
        return view("ars.promotores.calculo_comisiones_campana", compact("comisiones", "promotores", "campanas"));
    }

    public function calcularComisiones(Request $request)
    {
        $request->validate([
            "promoter_id" => "required|exists:promoters,id",
            "campaign_id" => "required|exists:promoter_campaigns,id",
            "payout_period" => "required|string"
        ]);

        $campaign = PromoterCampaign::findOrFail($request->campaign_id);
        $promoter = Promoter::findOrFail($request->promoter_id);

        // Buscar afiliados sin comisiones que hayan sido cargados por este promotor en el periodo
        $afiliados = Afiliado::where("estado_afiliacion", "OK")->take(5)->get(); // Simulado

        $count = 0;
        foreach ($afiliados as $af) {
            // Verificar si ya tiene comision
            $exists = PromoterCommission::where("affiliate_id", $af->id)->exists();
            if (!$exists) {
                $comm = PromoterCommission::create([
                    "promoter_id" => $promoter->id,
                    "campaign_id" => $campaign->id,
                    "affiliate_id" => $af->id,
                    "amount" => $campaign->commission_amount,
                    "payout_period" => $request->payout_period,
                    "status" => "Aprobada"
                ]);

                // Generar Asiento Contable y CXP para el promotor
                $payableNum = "CXP-PROM-" . now()->format("Y") . "-" . str_pad(rand(1, 99999), 5, "0", STR_PAD_LEFT);
                AccountPayable::create([
                    "payable_number" => $payableNum,
                    "account_payable_number" => $payableNum,
                    "claim_id" => 1, // Enlace simulado a reclamacion dummy
                    "authorization_id" => 1,
                    "pss_id" => 1,
                    "amount" => $campaign->commission_amount,
                    "retained_amount" => 0,
                    "gross_amount" => $campaign->commission_amount,
                    "objected_amount" => 0,
                    "approved_amount" => $campaign->commission_amount,
                    "net_amount" => $campaign->commission_amount,
                    "vendor_type" => "Suplidor",
                    "vendor_id" => $promoter->id,
                    "status" => "Generada",
                    "generated_by" => Auth::id() ?? 1,
                    "generated_at" => now()
                ]);

                $count++;
            }
        }

        Bitacora::registrar("Promotores", "Comisiones calculadas para promotor ID {$promoter->id}. Afiliados procesados: {$count}. Periodo: {$request->payout_period}");

        return redirect()->route("ars.promotores.calculo_campana")->with("success", "Comisiones calculadas exitosamente. Se generaron {$count} registros de CXP para el promotor.");
    }

    public function tiposGestion()
    {
        return view("ars.promotores.tipos_gestion");
    }

    public function esquemasGestion()
    {
        return view("ars.promotores.esquemas_comisiones_gestion");
    }

    public function calculoGestion()
    {
        return view("ars.promotores.calculo_comisiones_gestion");
    }

    public function reportes()
    {
        return view("ars.promotores.reportes");
    }

    public function tipificacion()
    {
        return view("ars.promotores.tipificacion");
    }
}