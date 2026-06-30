<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\Bitacora;
use Illuminate\Http\Request;

class CatalogosGeneralesController extends Controller
{
    public function index()
    {
        $catalogos = Catalogo::select("grupo")->groupBy("grupo")->get();
        return view("ars.catalogos.index", compact("catalogos"));
    }

    public function verGrupo($grupo)
    {
        $items = Catalogo::where("grupo", $grupo)->get();
        return view("ars.catalogos.ver", compact("items", "grupo"));
    }

    public function guardarItem(Request $request)
    {
        $request->validate([
            "grupo" => "required|string",
            "codigo" => "required|string|unique:catalogos,codigo",
            "descripcion" => "required|string"
        ]);

        Catalogo::create([
            "grupo" => $request->grupo,
            "codigo" => $request->codigo,
            "descripcion" => $request->descripcion,
            "activo" => true
        ]);

        Bitacora::registrar("Catálogos", "Creado elemento de catálogo en {$request->grupo}: {$request->codigo}");

        return redirect()->route("ars.catalogos.ver", $request->grupo)->with("success", "Elemento de catálogo agregado.");
    }

    public function toggleStatus($id)
    {
        $item = Catalogo::findOrFail($id);
        $item->update(["activo" => !$item->activo]);

        return redirect()->route("ars.catalogos.ver", $item->grupo)->with("success", "Estado del elemento actualizado.");
    }
}