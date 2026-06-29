<?php

namespace App\Http\Controllers;

use App\Models\PdssService;
use App\Models\PdssGroup;
use App\Models\PdssSubgroup;
use App\Models\PssServiceContract;
use App\Models\Autorizacion;
use App\Models\Bitacora;
use App\Models\PdssImportLog;
use App\Models\PssContract;
use App\Models\PssContractVersion;
use App\Models\PssTariffSchedule;
use App\Models\PssTariffItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PdssController extends Controller
{
    /**
     * Muestra la bandeja del Catálogo PDSS con filtros dinámicos.
     */
    public function catalogo(Request $request)
    {
        $search = $request->get('search');
        $grupoId = $request->get('group_id');
        $subgrupoId = $request->get('subgroup_id');
        $coberturaTipo = $request->get('coverage_type');
        $nivel = $request->get('nivel'); // 1, 2, 3

        // Filtros rápidos
        $isHighCost = $request->has('is_high_cost');
        $isEmergency = $request->has('is_emergency');
        $isHospitalization = $request->has('is_hospitalization');
        $isSurgery = $request->has('is_surgery');
        $isDiagnosticSupport = $request->has('is_diagnostic_support');
        $isMedicine = $request->has('is_medicine');

        $query = PdssService::with(['group', 'subgroup']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('simon_code', 'like', "%{$search}%")
                  ->orWhere('cups_code', 'like', "%{$search}%")
                  ->orWhere('coverage_description', 'like', "%{$search}%")
                  ->orWhere('coverage_type', 'like', "%{$search}%");
            });
        }

        if ($grupoId) $query->where('pdss_group_id', $grupoId);
        if ($subgrupoId) $query->where('pdss_subgroup_id', $subgrupoId);
        if ($coberturaTipo) $query->where('coverage_type', $coberturaTipo);

        if ($nivel) {
            if ($nivel == 1) $query->where('level_1_covered', 'S');
            if ($nivel == 2) $query->where('level_2_covered', 'S');
            if ($nivel == 3) $query->where('level_3_covered', 'S');
        }

        if ($isHighCost) $query->where('is_high_cost', true);
        if ($isEmergency) $query->where('is_emergency', true);
        if ($isHospitalization) $query->where('is_hospitalization', true);
        if ($isSurgery) $query->where('is_surgery', true);
        if ($isDiagnosticSupport) $query->where('is_diagnostic_support', true);
        if ($isMedicine) $query->where('is_medicine', true);

        $servicios = $query->paginate(20)->withQueryString();

        $grupos = PdssGroup::orderBy('code')->get();
        $subgrupos = $grupoId ? PdssSubgroup::where('pdss_group_id', $grupoId)->orderBy('code')->get() : collect();
        $tiposCobertura = PdssService::select('coverage_type')->distinct()->whereNotNull('coverage_type')->pluck('coverage_type');

        // Logs de importación recientes
        $importLogs = PdssImportLog::orderBy('created_at', 'desc')->take(5)->get();

        return view('ars.pdss.catalogo', compact(
            'servicios', 'grupos', 'subgrupos', 'tiposCobertura', 'importLogs',
            'search', 'grupoId', 'subgrupoId', 'coberturaTipo', 'nivel',
            'isHighCost', 'isEmergency', 'isHospitalization', 'isSurgery', 'isDiagnosticSupport', 'isMedicine'
        ));
    }

    /**
     * Muestra el detalle completo de una prestación del PDSS.
     */
    public function show($id)
    {
        $servicio = PdssService::with(['group', 'subgroup'])->findOrFail($id);

        // Clínicas / PSS contratadas que ofrecen este servicio
        $contratos = PssServiceContract::where('pdss_service_id', $id)
            ->where('is_active', true)
            ->with('pss')
            ->get();

        // Historial de solicitudes de autorización para este servicio específico
        $autorizaciones = Autorizacion::where('pdss_service_id', $id)
            ->with(['pss'])
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        // Estadísticas rápidas
        $stats = [
            'total' => Autorizacion::where('pdss_service_id', $id)->count(),
            'aprobadas' => Autorizacion::where('pdss_service_id', $id)->where('estado', 'Aprobada')->count(),
            'rechazadas' => Autorizacion::where('pdss_service_id', $id)->where('estado', 'Rechazada')->count(),
            'auditoria' => Autorizacion::where('pdss_service_id', $id)->where('estado', 'Auditoría')->count(),
        ];

        return view('ars.pdss.show', compact('servicio', 'contratos', 'autorizaciones', 'stats'));
    }

    /**
     * Ejecuta la importación del PDF desde el panel.
     */
    public function importarPdf(Request $request)
    {
        try {
            $path = storage_path('app/imports/pdss/Catalogo-PDSS.pdf');
            
            if (!file_exists($path)) {
                return redirect()->back()->with('error', 'El archivo Catalogo-PDSS.pdf no se encuentra en storage/app/imports/pdss/');
            }

            Artisan::call('pdss:import', ['path' => $path]);

            Bitacora::registrar('PDSS', 'Catálogo importado desde PDF por el administrador.');

            return redirect()->route('ars.pdss.catalogo')->with('success', 'Importación del PDF completada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar catálogo: ' . $e->getMessage());
        }
    }

    /**
     * Ejecuta la importación del CSV desde el panel.
     */
    public function importarCsv(Request $request)
    {
        try {
            $path = storage_path('app/imports/pdss/pdss_catalog.csv');
            
            if (!file_exists($path)) {
                return redirect()->back()->with('error', 'El archivo pdss_catalog.csv no se encuentra en storage/app/imports/pdss/');
            }

            Artisan::call('pdss:import-csv', ['path' => $path]);

            Bitacora::registrar('PDSS', 'Catálogo importado desde CSV por el administrador.');

            return redirect()->route('ars.pdss.catalogo')->with('success', 'Importación del CSV completada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al importar catálogo desde CSV: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la pantalla de configuración de reglas dinámicas del catálogo.
     */
    public function reglasPdss()
    {
        // Contar servicios por banderas para mostrar información útil
        $stats = [
            'total' => PdssService::count(),
            'high_cost_audit' => PdssService::where('is_high_cost', true)->where('requires_medical_audit', true)->count(),
            'emergency_auto' => PdssService::where('is_emergency', true)->where('requires_authorization', true)->count(),
            'surgery_audit' => PdssService::where('is_surgery', true)->where('requires_medical_audit', true)->count(),
            'hosp_audit' => PdssService::where('is_hospitalization', true)->where('requires_medical_audit', true)->count(),
            'medicine_doc' => PdssService::where('is_medicine', true)->where('requires_authorization', true)->count(),
        ];

        return view('ars.pdss.reglas', compact('stats'));
    }

    /**
     * Guarda la configuración de reglas dinámicas del catálogo actualizando servicios en lote.
     */
    public function guardarReglasPdss(Request $request)
    {
        $grupoAltoCosto = $request->has('grupo_alto_costo_audit');
        $grupoEmergencia = $request->has('grupo_emergencia_no_audit');
        $grupoCirugia = $request->has('grupo_cirugia_audit');
        $grupoHospitalizacion = $request->has('grupo_hospitalizacion_audit');
        $grupoMedicina = $request->has('grupo_medicina_audit');

        // Aplicar actualizaciones en base de datos
        DB::transaction(function() use ($grupoAltoCosto, $grupoEmergencia, $grupoCirugia, $grupoHospitalizacion, $grupoMedicina) {
            // Alto costo siempre requiere auditoría médica si está activada la regla
            PdssService::where('is_high_cost', true)->update([
                'requires_medical_audit' => $grupoAltoCosto,
                'requires_authorization' => true
            ]);

            // Emergencias
            PdssService::where('is_emergency', true)->update([
                'requires_medical_audit' => false,
                'requires_authorization' => !$grupoEmergencia // si no requiere autorización, se aprueba directo
            ]);

            // Cirugías
            PdssService::where('is_surgery', true)->update([
                'requires_medical_audit' => $grupoCirugia
            ]);

            // Hospitalización
            PdssService::where('is_hospitalization', true)->update([
                'requires_medical_audit' => $grupoHospitalizacion
            ]);

            // Medicamentos
            PdssService::where('is_medicine', true)->update([
                'requires_medical_audit' => $grupoMedicina
            ]);
        });

        Bitacora::registrar('PDSS', 'Configuración de reglas del catálogo PDSS actualizada.');

        return redirect()->route('ars.autorizaciones.reglas_pdss')->with('success', 'Reglas operativas del catálogo PDSS actualizadas con éxito.');
    }

    /**
     * Busca servicios del catálogo PDSS por AJAX.
     */
    public function buscarServicioAjax(Request $request)
    {
        $term = $request->get('q', '');
        $pssId = $request->get('pss_id');
        $nivel = $request->get('nivel');
        $grupoId = $request->get('group_id');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $query = PdssService::with(['group', 'subgroup']);

        if ($pssId) {
            $contract = PssContract::where('pss_id', $pssId)
                ->where('status', 'vigente')
                ->first();
            
            if ($contract) {
                $version = PssContractVersion::where('pss_contract_id', $contract->id)
                    ->where('status', 'vigente')
                    ->first();
                
                if ($version) {
                    $schedule = PssTariffSchedule::where('pss_contract_version_id', $version->id)
                        ->where('status', 'vigente')
                        ->first();
                    
                    if ($schedule) {
                        $serviceIds = PssTariffItem::where('pss_tariff_schedule_id', $schedule->id)
                            ->where('status', 'activo')
                            ->pluck('pdss_service_id');
                        
                        $query->whereIn('id', $serviceIds);
                    } else {
                        $query->whereRaw('1 = 0'); // Forzar consulta vacía
                    }
                } else {
                    $query->whereRaw('1 = 0');
                }
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Filtrar por grupo si se especifica
        if ($grupoId) {
            $query->where('pdss_group_id', $grupoId);
        }

        // Filtrar por nivel de atención cubierto
        if ($nivel == 1) $query->where('level_1_covered', 'S');
        elseif ($nivel == 2) $query->where('level_2_covered', 'S');
        elseif ($nivel == 3) $query->where('level_3_covered', 'S');

        $query->where('is_active', true);

        // Búsqueda flexible: por código SIMON, código CUPS, nombre/descripción, tipo de cobertura
        $query->where(function($q) use ($term) {
            $q->where('simon_code', 'like', "%{$term}%")
              ->orWhere('cups_code', 'like', "%{$term}%")
              ->orWhere('coverage_description', 'like', "%{$term}%")
              ->orWhere('coverage_type', 'like', "%{$term}%");
        });

        $results = $query->take(30)->get()->map(fn($s) => [
            'id' => $s->id,
            'simon_code' => $s->simon_code,
            'cups_code' => $s->cups_code,
            'description' => $s->coverage_description,
            'coverage_type' => $s->coverage_type,
            'group_name' => $s->group->name ?? '',
            'group_code' => $s->group->code ?? '',
            'subgroup_name' => $s->subgroup->name ?? '',
            'requires_audit' => (bool)$s->requires_medical_audit,
            'requires_authorization' => (bool)$s->requires_authorization,
            'is_high_cost' => (bool)$s->is_high_cost,
            'is_emergency' => (bool)$s->is_emergency,
            'is_hospitalization' => (bool)$s->is_hospitalization,
            'is_surgery' => (bool)$s->is_surgery,
            'is_diagnostic_support' => (bool)$s->is_diagnostic_support,
            'is_medicine' => (bool)$s->is_medicine,
            'amount_coverage' => $s->amount_coverage,
            'copay_type' => $s->copay_type,
            'level_1' => $s->level_1_covered,
            'level_2' => $s->level_2_covered,
            'level_3' => $s->level_3_covered,
            'label' => '[' . $s->simon_code . '] ' . $s->coverage_description . ($s->cups_code && $s->cups_code !== '0' ? ' (CUPS: ' . $s->cups_code . ')' : ''),
            'restrictions' => self::getServiceRestrictions($s),
        ]);

        return response()->json($results);
    }

    /**
     * Retorna los grupos del catálogo PDSS para los filtros
     */
    public function grupos(Request $request)
    {
        $grupos = PdssGroup::orderBy('code')->get()->map(fn($g) => [
            'id' => $g->id,
            'code' => $g->code,
            'name' => $g->name,
        ]);

        return response()->json($grupos);
    }

    /**
     * Retorna las restricciones de negocio de un servicio para mostrar al usuario
     */
    private static function getServiceRestrictions(PdssService $service): array
    {
        $restrictions = [];

        if ($service->requires_medical_audit) {
            $restrictions[] = 'Requiere auditoría médica';
        }
        if ($service->is_high_cost) {
            $restrictions[] = 'Servicio de alto costo';
        }
        if ($service->is_surgery) {
            $restrictions[] = 'Procedimiento quirúrgico - requiere indicación médica';
        }
        if ($service->is_hospitalization) {
            $restrictions[] = 'Requiere autorización de hospitalización';
        }
        if ($service->is_medicine) {
            $restrictions[] = 'Medicamento - sujeto a disponibilidad en red';
        }
        if ($service->is_diagnostic_support) {
            $restrictions[] = 'Apoyo diagnóstico - requiere orden médica';
        }

        return $restrictions;
    }
}
