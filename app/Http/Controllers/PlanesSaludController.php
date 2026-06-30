<?php

namespace App\Http\Controllers;

use App\Models\HealthPlan;
use App\Models\HealthPlanCoverage;
use App\Models\CoverageDerivationRule;
use App\Models\CoverageLimit;
use App\Models\PdssService;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class PlanesSaludController extends Controller
{
    public function planes()
    {
        $planes = HealthPlan::all();
        return view("ars.planes-salud.planes", compact("planes"));
    }

    public function guardarPlan(Request $request)
    {
        $request->validate([
            "code" => "required|string|unique:health_plans,code",
            "name" => "required|string",
            "plan_type" => "required|string",
            "description" => "nullable|string",
            "effective_from" => "required|date",
            "effective_to" => "nullable|date"
        ]);

        HealthPlan::create([
            "code" => $request->code,
            "name" => $request->name,
            "plan_type" => $request->plan_type,
            "description" => $request->description,
            "status" => "Activo",
            "effective_from" => $request->effective_from,
            "effective_to" => $request->effective_to
        ]);

        Bitacora::registrar("Planes de Salud", "Creado plan de salud: {$request->name}");

        return redirect()->route("ars.planes_salud.planes")->with("success", "Plan de salud creado exitosamente.");
    }

    public function catalogoPdss()
    {
        $servicios = PdssService::all();
        return view("ars.planes-salud.catalogo_pdss", compact("servicios"));
    }

    public function catalogoAlternativos()
    {
        $servicios = PdssService::where("subgroup_id", "!=", 1)->take(25)->get(); // Simulado alternativos
        return view("ars.planes-salud.catalogo_alternativos", compact("servicios"));
    }

    public function coberturas()
    {
        $planes = HealthPlan::all();
        $coberturas = HealthPlanCoverage::with(["plan", "service"])->paginate(15);
        $servicios = PdssService::all();
        return view("ars.planes-salud.coberturas", compact("planes", "coberturas", "servicios"));
    }

    public function guardarCobertura(Request $request)
    {
        $request->validate([
            "health_plan_id" => "required|exists:health_plans,id",
            "pdss_service_id" => "required|exists:pdss_services,id",
            "coverage_percent" => "required|numeric|min:0|max:100",
            "copay_percent" => "required|numeric|min:0|max:100",
            "fixed_copay" => "required|numeric|min:0",
            "limit_amount" => "required|numeric|min:0",
            "limit_period" => "required|string",
            "waiting_period_days" => "required|integer|min:0",
            "requires_authorization" => "required|boolean"
        ]);

        HealthPlanCoverage::create($request->all());

        return redirect()->route("ars.planes_salud.coberturas")->with("success", "Cobertura de plan guardada con éxito.");
    }

    public function detalleServicio()
    {
        $coberturas = HealthPlanCoverage::with(["plan", "service"])->get();
        return view("ars.planes-salud.detalle_servicio", compact("coberturas"));
    }

    public function derivaciones()
    {
        $derivaciones = CoverageDerivationRule::with("plan")->get();
        $planes = HealthPlan::all();
        return view("ars.planes-salud.derivaciones", compact("derivaciones", "planes"));
    }

    public function guardarDerivacion(Request $request)
    {
        $request->validate([
            "health_plan_id" => "required|exists:health_plans,id",
            "derivation_type" => "required|string",
            "condition" => "required|string",
            "result" => "required|string",
            "priority" => "required|integer"
        ]);

        CoverageDerivationRule::create([
            "health_plan_id" => $request->health_plan_id,
            "derivation_type" => $request->derivation_type,
            "condition_json" => json_decode($request->condition, true) ?: ["condition" => $request->condition],
            "result_json" => json_decode($request->result, true) ?: ["result" => $request->result],
            "priority" => $request->priority,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.planes_salud.derivaciones")->with("success", "Regla de derivación guardada exitosamente.");
    }

    public function periodosEspera()
    {
        $coberturas = HealthPlanCoverage::where("waiting_period_days", ">", 0)->with(["plan", "service"])->get();
        return view("ars.planes-salud.periodos_espera", compact("coberturas"));
    }

    public function topes()
    {
        $topes = CoverageLimit::with("plan")->get();
        $planes = HealthPlan::all();
        return view("ars.planes-salud.topes", compact("topes", "planes"));
    }

    public function guardarTope(Request $request)
    {
        $request->validate([
            "health_plan_id" => "required|exists:health_plans,id",
            "limit_type" => "required|string",
            "amount" => "required|numeric|min:0",
            "period" => "required|string"
        ]);

        CoverageLimit::create([
            "health_plan_id" => $request->health_plan_id,
            "service_group" => $request->service_group,
            "origin" => $request->origin,
            "limit_type" => $request->limit_type,
            "amount" => $request->amount,
            "period" => $request->period,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.planes_salud.topes")->with("success", "Límite/Tope configurado correctamente.");
    }

    public function reportes()
    {
        return view("ars.planes-salud.reportes");
    }
}