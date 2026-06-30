<?php

namespace App\Http\Controllers;

use App\Models\Pss;
use App\Models\MedicalAuditor;
use App\Models\ProviderGroup;
use App\Models\ProviderNetwork;
use App\Models\ProviderContractedService;
use App\Models\ProviderPriceAgreement;
use App\Models\ProviderGeoLocation;
use App\Models\CapitatedServiceContract;
use App\Models\CapitatedServicePayment;
use App\Models\HealthPlan;
use App\Models\ServicioMedico;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class PrestadoresController extends Controller
{
    public function personasFisicas()
    {
        $prestadores = Pss::where("pss_nature", "Física")->get();
        return view("ars.prestadores.personas_fisicas", compact("prestadores"));
    }

    public function personasJuridicas()
    {
        $prestadores = Pss::where("pss_nature", "Jurídica")->get();
        return view("ars.prestadores.personas_juridicas", compact("prestadores"));
    }

    public function guardarPrestador(Request $request)
    {
        $request->validate([
            "nombre" => "required|string",
            "rnc" => "required|string|unique:pss,rnc",
            "tipo_entidad" => "required|string",
            "pss_nature" => "required|string",
            "telefono" => "nullable|string",
            "correo" => "nullable|email",
            "direccion" => "nullable|string"
        ]);

        Pss::create([
            "nombre" => $request->nombre,
            "rnc" => $request->rnc,
            "tipo_entidad" => $request->tipo_entidad,
            "pss_nature" => $request->pss_nature,
            "telefono" => $request->telefono,
            "correo" => $request->correo,
            "direccion" => $request->direccion,
            "estado" => "Activa"
        ]);

        Bitacora::registrar("Prestadores", "Creado prestador: {$request->nombre}");

        return redirect()->back()->with("success", "Prestador de servicios de salud guardado.");
    }

    public function auditoresMedicos()
    {
        $auditores = MedicalAuditor::with("user")->get();
        return view("ars.prestadores.auditores_medicos", compact("auditores"));
    }

    public function guardarAuditor(Request $request)
    {
        $request->validate([
            "auditor_code" => "required|string|unique:medical_auditors,auditor_code",
            "exequatur" => "required|string",
            "professional_type" => "required|string"
        ]);

        MedicalAuditor::create([
            "auditor_code" => $request->auditor_code,
            "exequatur" => $request->exequatur,
            "professional_type" => $request->professional_type,
            "auditor_type" => "fisico",
            "status" => "Activo"
        ]);

        return redirect()->route("ars.prestadores.auditores_medicos")->with("success", "Auditor médico registrado.");
    }

    public function serviciosContratados()
    {
        $servicios = ProviderContractedService::with(["pss", "service"])->paginate(15);
        $pss = Pss::all();
        $serviciosMedicos = ServicioMedico::all();
        return view("ars.prestadores.servicios_contratados", compact("servicios", "pss", "serviciosMedicos"));
    }

    public function guardarServicioContratado(Request $request)
    {
        $request->validate([
            "pss_id" => "required|exists:pss,id",
            "servicio_medico_id" => "required|exists:servicios_medicos,id"
        ]);

        ProviderContractedService::create([
            "pss_id" => $request->pss_id,
            "servicio_medico_id" => $request->servicio_medico_id,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.prestadores.servicios_contratados")->with("success", "Servicio contratado registrado para prestadora.");
    }

    public function conveniosPrecios()
    {
        $convenios = ProviderPriceAgreement::with(["pss", "plan", "service"])->get();
        return view("ars.prestadores.convenios_precios", compact("convenios"));
    }

    public function preciosConvenidos()
    {
        $convenios = ProviderPriceAgreement::with(["pss", "plan", "service"])->paginate(15);
        $pss = Pss::all();
        $planes = HealthPlan::all();
        $servicios = ServicioMedico::all();
        return view("ars.prestadores.precios_convenidos", compact("convenios", "pss", "planes", "servicios"));
    }

    public function guardarPrecioConvenido(Request $request)
    {
        $request->validate([
            "pss_id" => "required|exists:pss,id",
            "health_plan_id" => "required|exists:health_plans,id",
            "servicio_medico_id" => "required|exists:servicios_medicos,id",
            "price" => "required|numeric|min:0"
        ]);

        ProviderPriceAgreement::create([
            "pss_id" => $request->pss_id,
            "health_plan_id" => $request->health_plan_id,
            "servicio_medico_id" => $request->servicio_medico_id,
            "price" => $request->price,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.prestadores.precios_convenidos")->with("success", "Precio convenido configurado exitosamente.");
    }

    public function grupos()
    {
        $grupos = ProviderGroup::all();
        return view("ars.prestadores.grupos", compact("grupos"));
    }

    public function guardarGrupo(Request $request)
    {
        $request->validate(["name" => "required|string"]);
        ProviderGroup::create($request->all());
        return redirect()->route("ars.prestadores.grupos")->with("success", "Grupo de prestadoras creado.");
    }

    public function redPorPlan()
    {
        $redes = ProviderNetwork::with("plans")->get();
        $planes = HealthPlan::all();
        return view("ars.prestadores.red_por_plan", compact("redes", "planes"));
    }

    public function guardarRedPorPlan(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "plan_ids" => "required|array"
        ]);

        $red = ProviderNetwork::create(["name" => $request->name, "status" => "Activo"]);
        $red->plans()->attach($request->plan_ids);

        return redirect()->route("ars.prestadores.red_por_plan")->with("success", "Red por plan creada.");
    }

    public function habilitacionServicios()
    {
        $servicios = ProviderContractedService::with(["pss", "service"])->get();
        return view("ars.prestadores.habilitacion_servicios", compact("servicios"));
    }

    public function georreferencial()
    {
        $locations = ProviderGeoLocation::with("pss")->get();
        return view("ars.prestadores.georreferencial", compact("locations"));
    }

    public function capitadosContratos()
    {
        $contratos = CapitatedServiceContract::with("pss")->get();
        $pss = Pss::all();
        return view("ars.prestadores.capitados_contratos", compact("contratos", "pss"));
    }

    public function guardarCapitadoContrato(Request $request)
    {
        $request->validate([
            "pss_id" => "required|exists:pss,id",
            "contract_number" => "required|string|unique:capitated_service_contracts,contract_number",
            "coverage_population_count" => "required|integer",
            "monthly_capitation_rate" => "required|numeric",
            "start_date" => "required|date",
            "end_date" => "required|date"
        ]);

        $totalAmount = $request->coverage_population_count * $request->monthly_capitation_rate;

        CapitatedServiceContract::create([
            "pss_id" => $request->pss_id,
            "contract_number" => $request->contract_number,
            "coverage_population_count" => $request->coverage_population_count,
            "monthly_capitation_rate" => $request->monthly_capitation_rate,
            "total_monthly_amount" => $totalAmount,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "status" => "Activo"
        ]);

        return redirect()->route("ars.prestadores.capitados_contratos")->with("success", "Contrato capitado registrado.");
    }

    public function capitadosPagos()
    {
        $pagos = CapitatedServicePayment::with("contract.pss")->get();
        $contratos = CapitatedServiceContract::where("status", "Activo")->get();
        return view("ars.prestadores.capitados_pagos", compact("pagos", "contratos"));
    }

    public function guardarCapitadoPago(Request $request)
    {
        $request->validate([
            "capitated_contract_id" => "required|exists:capitated_service_contracts,id",
            "period" => "required|string",
            "payment_reference" => "nullable|string"
        ]);

        $contract = CapitatedServiceContract::findOrFail($request->capitated_contract_id);

        CapitatedServicePayment::create([
            "capitated_contract_id" => $contract->id,
            "period" => $request->period,
            "amount_paid" => $contract->total_monthly_amount,
            "paid_at" => now()->toDateString(),
            "payment_reference" => $request->payment_reference,
            "status" => "Pagado"
        ]);

        return redirect()->route("ars.prestadores.capitados_pagos")->with("success", "Pago de cápita mensual registrado y conciliado.");
    }
}