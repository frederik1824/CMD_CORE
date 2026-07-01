<?php

namespace App\Http\Controllers;

use App\Models\Novedad;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Catalogo;
use App\Models\Lote;
use App\Models\Bitacora;
use App\Services\UnipagoMockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NovedadController extends Controller
{
    /**
     * Muestra la bandeja general de novedades.
     */
    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $tipo = $request->get('tipo');

        $query = Novedad::with(['tipoNovedad', 'creador', 'lote']);

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($tipo) {
            $query->where('tipo_novedad_id', $tipo);
        }

        $novedades = $query->orderBy('created_at', 'desc')->paginate(15);
        $tiposNovedad = Catalogo::getByGrupo('tipo_novedad');

        return view('ars.novedades.index', compact('novedades', 'tiposNovedad', 'estado', 'tipo'));
    }

    /**
     * Formulario para registrar una novedad.
     */
    public function create(Request $request)
    {
        $afiliadoId = $request->get('afiliado_id');
        $afiliadoType = $request->get('afiliado_type', 'titular');
        
        $afiliado = null;
        if ($afiliadoId) {
            $afiliado = $afiliadoType === 'titular' 
                ? Afiliado::findOrFail($afiliadoId) 
                : Dependiente::findOrFail($afiliadoId);
        }

        $tiposNovedad = Catalogo::getByGrupo('tipo_novedad');
        $afiliados = Afiliado::where('estado_afiliacion', 'OK')->orderBy('nombres')->limit(10)->get();

        return view('ars.novedades.create', compact('afiliado', 'afiliadoId', 'afiliadoType', 'tiposNovedad', 'afiliados'));
    }

    /**
     * Almacena una solicitud de novedad en estado Pendiente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'afiliado_type' => 'required|string',
            'afiliado_id' => 'required|integer',
            'tipo_novedad_id' => 'required|exists:catalogos,id',
        ]);

        $tipoNovedad = Catalogo::findOrFail($request->tipo_novedad_id);
        
        // Mapear campos a modificar según el tipo de novedad
        $camposModificados = [];
        if ($tipoNovedad->codigo === 'UBICACION') {
            $request->validate([
                'provincia' => 'required|string',
                'municipio' => 'required|string'
            ]);
            $camposModificados = [
                'provincia' => $request->provincia,
                'municipio' => $request->municipio
            ];
        } elseif ($tipoNovedad->codigo === 'DATO') {
            $request->validate([
                'telefono' => 'required|string',
                'correo' => 'required|email'
            ]);
            $camposModificados = [
                'telefono' => $request->telefono,
                'correo' => $request->correo
            ];
        } elseif ($tipoNovedad->codigo === 'FALLECE') {
            $camposModificados = [
                'estado_afiliacion' => 'RE',
                'motivo_estado' => 'Inactivo por Notificación de Fallecimiento.'
            ];
        } elseif ($tipoNovedad->codigo === 'BAJA' || $tipoNovedad->codigo === 'EMPLEO') {
            $camposModificados = [
                'estado_afiliacion' => 'RE',
                'motivo_estado' => 'Baja de afiliación por pérdida de empleo o solicitud voluntaria.'
            ];
        }

        $novedad = Novedad::create([
            'afiliado_type' => $request->afiliado_type,
            'afiliado_id' => $request->afiliado_id,
            'tipo_novedad_id' => $request->tipo_novedad_id,
            'campos_modificados' => $camposModificados,
            'estado' => 'PE', // Pendiente de Lote
            'motivo_estado' => 'Novedad registrada en espera de generación de lote Unipago.',
            'creado_por' => Auth::id(),
            'fecha_novedad' => now()
        ]);

        $nombreAfil = $novedad->afiliado ? $novedad->afiliado->nombre_completo : 'N/A';
        Bitacora::registrar('Novedades', "Registrada novedad de tipo {$tipoNovedad->descripcion} para el afiliado: {$nombreAfil}");

        if ($request->afiliado_type === 'titular') {
            return redirect()->route('ars.titulares.show', $request->afiliado_id)->with('success', 'Novedad registrada con éxito en estado Pendiente.');
        }

        return redirect()->route('ars.novedades.index')->with('success', 'Novedad registrada con éxito en estado Pendiente.');
    }

    /**
     * Listar lotes de novedades.
     */
    public function lotesIndex()
    {
        $lotes = Lote::where('tipo_lote', 'novedades')
            ->with('creador')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('ars.afiliados.lotes.index', compact('lotes'));
    }

    /**
     * Genera un lote con todas las novedades en estado Pendiente (PE).
     */
    public function generarLote()
    {
        $novedadesPendientes = Novedad::where('estado', 'PE')->get();

        if ($novedadesPendientes->isEmpty()) {
            return redirect()->route('ars.novedades.index')->with('error', 'No existen novedades pendientes de envío a Unipago.');
        }

        $items = [];
        foreach ($novedadesPendientes as $nov) {
            $items[] = [
                'type' => 'novedad',
                'id' => $nov->id
            ];
        }

        // Generar lote utilizando el servicio mock
        $lote = UnipagoMockService::generarLote('novedades', $items, Auth::id());

        // Actualizar el lote_id en las novedades
        Novedad::where('estado', 'PE')->update([
            'lote_id' => $lote->id,
            'estado' => 'PE' // Sigue pendiente hasta procesar el lote
        ]);

        Bitacora::registrar('Novedades', "Lote {$lote->numero_lote} generado para procesar " . count($items) . " novedades.");

        return redirect()->route('ars.lotes.show', $lote->id)
            ->with('success', "Lote de novedades {$lote->numero_lote} generado. Listo para simular procesamiento.");
    }
}
