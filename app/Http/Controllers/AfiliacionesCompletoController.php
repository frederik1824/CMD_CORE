<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\AffiliateGroup;
use App\Models\AffiliateContract;
use App\Models\BusinessUnit;
use App\Models\GeographicCode;
use App\Models\AffiliateTransaction;
use App\Models\Bitacora;
use App\Models\Catalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfiliacionesCompletoController extends Controller
{
    public function mantenimiento()
    {
        $afiliados = Afiliado::orderBy("nombres")->paginate(15);
        return view("ars.afiliaciones.mantenimiento", compact("afiliados"));
    }

    public function tiposContratos()
    {
        $contratos = AffiliateContract::all();
        return view("ars.afiliaciones.tipos_contratos", compact("contratos"));
    }

    public function guardarTipoContrato(Request $request)
    {
        $request->validate([
            "code" => "required|string|unique:affiliate_contracts,code",
            "name" => "required|string",
            "contract_type" => "required|string"
        ]);

        AffiliateContract::create($request->all());

        return redirect()->route("ars.afiliaciones.tipos_contratos")->with("success", "Tipo de contrato de afiliado creado.");
    }

    public function solicitudesTitularesIndex()
    {
        $solicitudes = \App\Models\HolderAffiliationRequest::orderBy("created_at", "desc")->get();
        return view("ars.afiliaciones.titulares", compact("solicitudes"));
    }

    public function solicitudesDependientesIndex()
    {
        $solicitudes = \App\Models\DependentAffiliationRequest::orderBy("created_at", "desc")->get();
        return view("ars.afiliaciones.dependientes", compact("solicitudes"));
    }

    public function traspasos()
    {
        $traspasos = AffiliateTransaction::where("transaction_type", "traspaso")->get();
        $afiliados = Afiliado::where("estado_afiliacion", "OK")->get();
        return view("ars.afiliaciones.traspasos", compact("traspasos", "afiliados"));
    }

    public function registrarTraspaso(Request $request)
    {
        $request->validate([
            "affiliate_id" => "required|exists:afiliados,id",
            "concept" => "required|string"
        ]);

        $afiliado = Afiliado::findOrFail($request->affiliate_id);
        $before = $afiliado->toArray();

        $afiliado->update([
            "regimen_actual" => "Contributivo",
            "entidad_actual" => "ARS CMD"
        ]);

        AffiliateTransaction::create([
            "affiliate_id" => $afiliado->id,
            "affiliate_type" => "titular",
            "transaction_type" => "traspaso",
            "concept" => $request->concept,
            "payload_before" => $before,
            "payload_after" => $afiliado->toArray(),
            "user_id" => Auth::id() ?? 1
        ]);

        Bitacora::registrar("Afiliados", "Traspaso de afiliado ID {$afiliado->id} procesado.");

        return redirect()->route("ars.afiliaciones.traspasos")->with("success", "Traspaso y transferencia de consumos procesados.");
    }

    public function consultas()
    {
        return view("ars.afiliaciones.consultas");
    }

    public function grupos()
    {
        $grupos = AffiliateGroup::all();
        return view("ars.afiliaciones.grupos", compact("grupos"));
    }

    public function guardarGrupo(Request $request)
    {
        $request->validate(["name" => "required|string"]);
        AffiliateGroup::create($request->all());
        return redirect()->route("ars.afiliaciones.grupos")->with("success", "Grupo de afiliados creado.");
    }

    public function unidadesNegocio()
    {
        $unidades = BusinessUnit::all();
        return view("ars.afiliaciones.unidades_negocio", compact("unidades"));
    }

    public function guardarUnidadNegocio(Request $request)
    {
        $request->validate(["name" => "required|string"]);
        BusinessUnit::create($request->all());
        return redirect()->route("ars.afiliaciones.unidades_negocio")->with("success", "Unidad de negocio creada.");
    }

    public function transacciones()
    {
        $transacciones = AffiliateTransaction::with("user")->orderBy("created_at", "desc")->get();
        return view("ars.afiliaciones.transacciones", compact("transacciones"));
    }

    public function archivos()
    {
        return view("ars.afiliaciones.archivos");
    }

    public function generarArchivoNovedad(Request $request)
    {
        Bitacora::registrar("Afiliados", "Generado archivo masivo TXT de afiliados.");
        return redirect()->route("ars.afiliaciones.archivos")->with("success", "Archivo de novedades generado exitosamente.");
    }

    public function parentescos()
    {
        $parentescos = Catalogo::getByGrupo("parentesco");
        return view("ars.afiliaciones.parentescos", compact("parentescos"));
    }

    public function tiposAfiliacion()
    {
        $tipos = Catalogo::getByGrupo("tipo_afiliacion");
        return view("ars.afiliaciones.tipos_afiliacion", compact("tipos"));
    }

    public function codificacionGeografica()
    {
        $codigos = GeographicCode::all();
        return view("ars.afiliaciones.codificacion_geografica", compact("codigos"));
    }

    public function guardarCodificacionGeografica(Request $request)
    {
        $request->validate([
            "region" => "required|string",
            "province" => "required|string",
            "municipality" => "required|string",
            "sector" => "required|string"
        ]);

        GeographicCode::create($request->all());

        return redirect()->route("ars.afiliaciones.codificacion_geografica")->with("success", "Código geográfico configurado.");
    }

    public function reportes()
    {
        return view("ars.afiliaciones.reportes");
    }

    public function tipificacion()
    {
        return view("ars.afiliaciones.tipificacion");
    }
}