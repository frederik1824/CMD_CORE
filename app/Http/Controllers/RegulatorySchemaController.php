<?php

namespace App\Http\Controllers;

use App\Models\RegulatorySchema;
use App\Models\RegulatoryPeriod;
use App\Models\RegulatoryCatalog;
use App\Models\RegulatorySchemaRun;
use App\Models\SimonMockSubmission;
use Illuminate\Http\Request;

class RegulatorySchemaController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_schemas' => RegulatorySchema::count(),
            'runs_this_month' => RegulatorySchemaRun::whereMonth('generated_at', now()->month)->count(),
            'approved_runs' => SimonMockSubmission::where('status', 'aprobado')->count(),
            'rejected_runs' => SimonMockSubmission::where('status', 'rechazado')->count(),
            'pending_runs' => SimonMockSubmission::where('status', 'recibido')->count() + SimonMockSubmission::where('status', 'validando_estructura')->count(),
        ];

        $esquemas = RegulatorySchema::all()->map(function ($s) {
            $lastRun = RegulatorySchemaRun::where('regulatory_schema_id', $s->id)->orderBy('generated_at', 'desc')->first();
            $s->last_run = $lastRun;
            return $s;
        });

        $periodoActual = RegulatoryPeriod::where('status', 'abierto')->orderBy('start_date', 'asc')->first() 
            ?? RegulatoryPeriod::orderBy('start_date', 'desc')->first();

        return view('ars.sisalril.dashboard', compact('stats', 'esquemas', 'periodoActual'));
    }

    public function index()
    {
        $esquemas = RegulatorySchema::all();
        return view('ars.sisalril.index', compact('esquemas'));
    }

    public function catalogos()
    {
        $catalogos = RegulatoryCatalog::with('items')->get();
        return view('ars.sisalril.catalogos', compact('catalogos'));
    }

    public function configuracion()
    {
        // Configuración de Entidad simulada (guardada en sesión o hardcoded para demo)
        $config = session('regulatory_entity_config') ?: [
            'tipo_institucion' => 'ARS',
            'codigo_institucion' => 'ARS-001',
            'sigla' => 'ARS CENTRAL',
            'nombre' => 'ARS Dominicano Central',
            'rnc' => '101928282',
            'codigo_sisalril' => 'SIS-9028',
            'codigo_simon' => 'SIM-88282',
            'responsable_tecnico' => 'Ing. Frederik López',
            'responsable_aprobador' => 'Dra. María Altagracia',
            'correo' => 'regulaciones@arsdominicano.com',
            'telefono' => '809-555-0199',
            'formato_nombre' => '{sigla}_{schema_code}_{year}{month}_{sequence}.txt'
        ];

        return view('ars.sisalril.configuracion', compact('config'));
    }

    public function guardarConfiguracion(Request $request)
    {
        session(['regulatory_entity_config' => $request->except('_token')]);
        return redirect()->route('sisalril.configuracion')->with('success', 'Configuración de entidad regulatoria guardada con éxito.');
    }

    public function show($code)
    {
        $esquema = RegulatorySchema::where('schema_code', $code)->firstOrFail();
        $corridas = RegulatorySchemaRun::where('regulatory_schema_id', $esquema->id)->orderBy('generated_at', 'desc')->get();
        
        return view('ars.sisalril.detalles_esquema', compact('esquema', 'corridas'));
    }
}
