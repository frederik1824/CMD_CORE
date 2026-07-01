<?php

namespace App\Http\Controllers;

use App\Models\PypRiskGroup;
use App\Models\PypRiskFactor;
use App\Models\PypProgram;
use App\Models\PypProgramCandidate;
use App\Models\PypProgramEnrollment;
use App\Models\PypProgramCalendar;
use App\Models\Afiliado;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PypController extends Controller
{
    public function dashboard()
    {
        $programas = PypProgram::withCount(["candidates", "enrollments"])->get();
        $gruposRiesgo = PypRiskGroup::all();
        $factoresRiesgo = PypRiskFactor::all();
        $calendar = PypProgramCalendar::with("program")->orderBy("scheduled_date", "asc")->get();

        return view("ars.pyp.dashboard", compact("programas", "gruposRiesgo", "factoresRiesgo", "calendar"));
    }

    public function actividades(Request $request)
    {
        $programas = PypProgram::all();
        return view("ars.pyp.actividades", compact("programas"));
    }

    public function guardarActividad(Request $request)
    {
        $request->validate([
            "program_id" => "required|exists:pyp_programs,id",
            "service_name" => "required|string",
            "scheduled_date" => "required|date",
            "location" => "nullable|string",
            "capacity" => "required|integer|min:1"
        ]);

        PypProgramCalendar::create([
            "program_id" => $request->program_id,
            "service_name" => $request->service_name,
            "scheduled_date" => $request->scheduled_date,
            "location" => $request->location,
            "capacity" => $request->capacity,
            "status" => "Programado"
        ]);

        Bitacora::registrar("PyP", "Actividad no asistencial programada: {$request->service_name}");

        return redirect()->route("ars.pyp.dashboard")->with("success", "Actividad no asistencial programada exitosamente.");
    }

    public function gruposRiesgo()
    {
        $grupos = PypRiskGroup::all();
        return view("ars.pyp.grupos_riesgo", compact("grupos"));
    }

    public function guardarGrupoRiesgo(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:pyp_risk_groups,name",
            "description" => "nullable|string",
            "criteria" => "nullable|string"
        ]);

        PypRiskGroup::create($request->all());

        return redirect()->route("ars.pyp.dashboard")->with("success", "Grupo de riesgo creado exitosamente.");
    }

    public function factoresRiesgo()
    {
        $factores = PypRiskFactor::all();
        return view("ars.pyp.factores_riesgo", compact("factores"));
    }

    public function guardarFactorRiesgo(Request $request)
    {
        $request->validate([
            "name" => "required|string|unique:pyp_risk_factors,name",
            "description" => "nullable|string"
        ]);

        PypRiskFactor::create($request->all());

        return redirect()->route("ars.pyp.dashboard")->with("success", "Factor de riesgo creado exitosamente.");
    }

    public function tiposProgramas()
    {
        return view("ars.pyp.tipos_programas");
    }

    public function programas()
    {
        $programas = PypProgram::with("riskGroup")->get();
        $grupos = PypRiskGroup::all();
        return view("ars.pyp.programas", compact("programas", "grupos"));
    }

    public function guardarPrograma(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "program_type" => "required|string",
            "risk_group_id" => "required|exists:pyp_risk_groups,id",
            "start_date" => "required|date",
            "end_date" => "nullable|date",
            "description" => "nullable|string",
            "target_population" => "nullable|string"
        ]);

        PypProgram::create([
            "name" => $request->name,
            "program_type" => $request->program_type,
            "risk_group_id" => $request->risk_group_id,
            "start_date" => $request->start_date,
            "end_date" => $request->end_date,
            "description" => $request->description,
            "target_population" => $request->target_population,
            "status" => "Activo",
            "created_by" => Auth::id() ?? 1
        ]);

        return redirect()->route("ars.pyp.programas")->with("success", "Programa de salud preventivo creado exitosamente.");
    }

    public function calendario()
    {
        $calendar = PypProgramCalendar::with("program")->orderBy("scheduled_date", "asc")->get();
        $programas = PypProgram::all();
        return view("ars.pyp.calendario", compact("calendar", "programas"));
    }

    public function guardarEventoCalendar(Request $request)
    {
        return $this->guardarActividad($request);
    }

    public function candidatos()
    {
        $candidatos = PypProgramCandidate::with(["program", "affiliate", "riskGroup"])->orderBy("detected_at", "desc")->get();
        $programas = PypProgram::all();
        return view("ars.pyp.candidatos", compact("candidatos", "programas"));
    }

    public function enrolarCandidato(Request $request, $id)
    {
        $candidate = PypProgramCandidate::findOrFail($id);
        
        PypProgramEnrollment::create([
            "affiliate_id" => $candidate->affiliate_id,
            "program_id" => $candidate->program_id,
            "enrollment_date" => now()->toDateString(),
            "status" => "Activo"
        ]);

        $candidate->update(["status" => "Enrolado"]);

        Bitacora::registrar("PyP", "Candidato ID {$candidate->id} enrolado exitosamente en programa ID {$candidate->program_id}.");

        return redirect()->route("ars.pyp.candidatos")->with("success", "Candidato inscrito exitosamente en el programa de salud.");
    }

    public function descartarCandidato(Request $request, $id)
    {
        $request->validate(["reason" => "required|string"]);
        
        $candidate = PypProgramCandidate::findOrFail($id);
        $candidate->update([
            "status" => "No Aceptado",
            "reason_not_enrolled" => $request->reason
        ]);

        return redirect()->route("ars.pyp.candidatos")->with("success", "Candidato descartado.");
    }

    public function inscripciones()
    {
        $inscripciones = PypProgramEnrollment::with(["program", "affiliate"])->get();
        $afiliados = Afiliado::where("estado_afiliacion", "OK")->orderBy("nombres")->limit(15)->get();
        $programas = PypProgram::all();
        return view("ars.pyp.inscripciones", compact("inscripciones", "afiliados", "programas"));
    }

    public function inscribirManual(Request $request)
    {
        $request->validate([
            "affiliate_id" => "required|exists:afiliados,id",
            "program_id" => "required|exists:pyp_programs,id"
        ]);

        $exists = PypProgramEnrollment::where("affiliate_id", $request->affiliate_id)
            ->where("program_id", $request->program_id)
            ->where("status", "Activo")
            ->exists();

        if ($exists) {
            return redirect()->route("ars.pyp.inscripciones")->with("error", "El afiliado ya está inscrito activamente en este programa.");
        }

        PypProgramEnrollment::create([
            "affiliate_id" => $request->affiliate_id,
            "program_id" => $request->program_id,
            "enrollment_date" => now()->toDateString(),
            "status" => "Activo"
        ]);

        Bitacora::registrar("PyP", "Inscripción manual de afiliado ID {$request->affiliate_id} al programa PyP ID {$request->program_id}.");

        return redirect()->route("ars.pyp.inscripciones")->with("success", "Afiliado inscrito exitosamente.");
    }

    public function cancelaciones()
    {
        $cancelaciones = PypProgramEnrollment::where("status", "Cancelado")->with(["program", "affiliate"])->get();
        return view("ars.pyp.cancelaciones", compact("cancelaciones"));
    }

    public function cancelarInscripcion(Request $request, $id)
    {
        $request->validate(["cancellation_reason" => "required|string"]);
        
        $enrollment = PypProgramEnrollment::findOrFail($id);
        $enrollment->update([
            "status" => "Cancelado",
            "cancellation_reason" => $request->cancellation_reason,
            "cancelled_at" => now()
        ]);

        Bitacora::registrar("PyP", "Cancelación de inscripción de afiliado ID {$enrollment->affiliate_id} al programa PyP ID {$enrollment->program_id}.");

        return redirect()->route("ars.pyp.inscripciones")->with("success", "Inscripción cancelada de manera formal.");
    }

    public function reportes()
    {
        $stats = [
            "total_enrollments" => PypProgramEnrollment::count(),
            "active_enrollments" => PypProgramEnrollment::where("status", "Activo")->count(),
            "candidates" => PypProgramCandidate::where("status", "Detectado")->count(),
            "programs_count" => PypProgram::count()
        ];
        
        $programEnrollments = PypProgram::withCount("enrollments")->get();

        return view("ars.pyp.reportes", compact("stats", "programEnrollments"));
    }
}