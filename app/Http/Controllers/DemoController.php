<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Lote;
use App\Models\Novedad;
use App\Models\Autorizacion;
use App\Models\Pss;
use App\Models\Catalogo;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    /**
     * Muestra la página de entrada (Landing Page) con las tarjetas de roles demo.
     */
    public function landing()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'Usuario PSS'
                ? redirect()->route('pss.dashboard')
                : redirect()->route('ars.dashboard');
        }

        // ── KPIs del sistema (datos reales) ──────────────────────────────────
        $kpis = [
            'afiliados_activos'      => Afiliado::where('estado_afiliacion', 'OK')->count(),
            'solicitudes_pendientes' => Autorizacion::where('estado', 'Pendiente')->count(),
            'autorizaciones_dia'     => Autorizacion::whereDate('fecha_solicitud', now()->toDateString())->count(),
            'novedades_pendientes'   => Novedad::where('estado', 'Pendiente')->count(),
            'lotes_procesados'       => Lote::whereIn('estado_lote', ['EV','PE','RE'])->count(),
            'pss_activas'            => Pss::where('estado', 'Activa')->count(),
        ];

        // ── Alertas operativas ────────────────────────────────────────────────
        $alertas = [];
        $lotesPend = Lote::where('estado_lote', 'PE')->count();
        if ($lotesPend > 0)
            $alertas[] = ['tipo'=>'warning', 'icono'=>'layers',  'texto'=> "$lotesPend lote(s) pendiente(s) de procesar.",         'accion'=>route('switch.role','admin@ars.com')];

        $autAudit = Autorizacion::where('estado','Auditoría')->count();
        if ($autAudit > 0)
            $alertas[] = ['tipo'=>'info',    'icono'=>'eye',     'texto'=> "$autAudit autorización(es) en espera de auditoría médica.", 'accion'=>route('switch.role','auditor@ars.com')];

        $novRech = Novedad::where('estado','Rechazada')->count();
        if ($novRech > 0)
            $alertas[] = ['tipo'=>'error',   'icono'=>'x',       'texto'=> "$novRech novedad(es) rechazada(s) requieren revisión.",   'accion'=>route('switch.role','supervisor@ars.com')];

        $pssPend = Autorizacion::where('estado','Pendiente')->count();
        if ($pssPend > 0)
            $alertas[] = ['tipo'=>'warning', 'icono'=>'building', 'texto'=> "$pssPend solicitud(es) de PSS en espera de respuesta.", 'accion'=>route('switch.role','autorizaciones@ars.com')];

        // ── Módulos agrupados por categoría ──────────────────────────────────
        $categorias = [
            [
                'nombre' => 'Afiliación',
                'icono'  => 'users',
                'color'  => '#0b57d0',
                'modulos' => [
                    [
                        'id'          => 'dashboard',
                        'nombre'      => 'Dashboard Ejecutivo',
                        'descripcion' => 'KPIs, gráficos de afiliaciones, autorizaciones y novedades en tiempo real.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'chart-bar',
                        'badge'       => $kpis['afiliados_activos'].' afiliados',
                        'estado'      => 'activo',
                        'color'       => 'blue',
                    ],
                    [
                        'id'          => 'titulares',
                        'nombre'      => 'Afiliación de Titulares',
                        'descripcion' => 'Registro, consulta y gestión de titulares.',
                        'ruta'        => route('switch.role','analista@ars.com'),
                        'icon'        => 'user',
                        'badge'       => Afiliado::count().' registros',
                        'estado'      => 'activo',
                        'color'       => 'teal',
                    ],
                    [
                        'id'          => 'dependientes',
                        'nombre'      => 'Afiliación de Dependientes',
                        'descripcion' => 'Gestión de beneficiarios vinculados a titulares activos.',
                        'ruta'        => route('switch.role','analista@ars.com'),
                        'icon'        => 'user-group',
                        'badge'       => Dependiente::count().' dependientes',
                        'estado'      => 'activo',
                        'color'       => 'cyan',
                    ],
                    [
                        'id'          => 'carga-masiva',
                        'nombre'      => 'Carga Masiva',
                        'descripcion' => 'Importación de afiliados en lote mediante CSV con prevalidación.',
                        'ruta'        => route('switch.role','analista@ars.com'),
                        'icon'        => 'upload',
                        'badge'       => null,
                        'estado'      => 'activo',
                        'color'       => 'indigo',
                    ],
                    [
                        'id'          => 'lotes',
                        'nombre'      => 'Lotes de Afiliación',
                        'descripcion' => 'Control, procesamiento y seguimiento de lotes de afiliados.',
                        'ruta'        => route('switch.role','supervisor@ars.com'),
                        'icon'        => 'layers',
                        'badge'       => Lote::count().' lotes',
                        'estado'      => 'activo',
                        'color'       => 'violet',
                    ],
                ],
            ],
            [
                'nombre' => 'Operaciones',
                'icono'  => 'bolt',
                'color'  => '#e65100',
                'modulos' => [
                    [
                        'id'          => 'novedades',
                        'nombre'      => 'Novedades de Afiliación',
                        'descripcion' => 'Cambios de estado, bajas, rehabilitaciones y lotes Unipago.',
                        'ruta'        => route('switch.role','supervisor@ars.com'),
                        'icon'        => 'bell',
                        'badge'       => $kpis['novedades_pendientes'].' pendientes',
                        'estado'      => 'activo',
                        'color'       => 'amber',
                    ],
                ],
            ],
            [
                'nombre' => 'Autorizaciones',
                'icono'  => 'shield-check',
                'color'  => '#137333',
                'modulos' => [
                    [
                        'id'          => 'autorizaciones',
                        'nombre'      => 'Autorizaciones Médicas ARS',
                        'descripcion' => 'Bandeja de solicitudes, aprobación/rechazo y auditoría médica.',
                        'ruta'        => route('switch.role','autorizaciones@ars.com'),
                        'icon'        => 'clipboard-check',
                        'badge'       => $kpis['solicitudes_pendientes'].' pendientes',
                        'estado'      => 'activo',
                        'color'       => 'green',
                    ],
                ],
            ],
            [
                'nombre' => 'Prestadores',
                'icono'  => 'building',
                'color'  => '#6b21a8',
                'modulos' => [
                    [
                        'id'          => 'portal-pss',
                        'nombre'      => 'Portal / Simulador PSS',
                        'descripcion' => 'Portal del prestador: consulta de cobertura y solicitudes de autorización.',
                        'ruta'        => route('switch.role','pss@ars.com'),
                        'icon'        => 'building-office',
                        'badge'       => $kpis['pss_activas'].' PSS activas',
                        'estado'      => 'activo',
                        'color'       => 'purple',
                    ],
                    [
                        'id'          => 'red-pss',
                        'nombre'      => 'Red de Prestadores',
                        'descripcion' => 'Directorio completo de prestadores de servicios de salud.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'map',
                        'badge'       => $kpis['pss_activas'].' activas',
                        'estado'      => 'activo',
                        'color'       => 'sky',
                    ],
                    [
                        'id'          => 'contratos',
                        'nombre'      => 'Contratos y Tarifas',
                        'descripcion' => 'Gestión de contratos, tarifarios y servicios contratados por PSS.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'document-text',
                        'badge'       => null,
                        'estado'      => 'demo',
                        'color'       => 'slate',
                    ],
                ],
            ],
            [
                'nombre' => 'Reportes',
                'icono'  => 'chart-pie',
                'color'  => '#00695c',
                'modulos' => [
                    [
                        'id'          => 'reportes',
                        'nombre'      => 'Reportes',
                        'descripcion' => 'Reportería estadística de afiliaciones, autorizaciones y novedades.',
                        'ruta'        => route('switch.role','consulta@ars.com'),
                        'icon'        => 'chart-bar',
                        'badge'       => null,
                        'estado'      => 'activo',
                        'color'       => 'emerald',
                    ],
                ],
            ],
            [
                'nombre' => 'Administración',
                'icono'  => 'cog',
                'color'  => '#5f6368',
                'modulos' => [
                    [
                        'id'          => 'catalogos',
                        'nombre'      => 'Catálogos',
                        'descripcion' => 'Catálogos generales del sistema: diagnósticos, servicios, tipos, etc.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'list-bullet',
                        'badge'       => null,
                        'estado'      => 'activo',
                        'color'       => 'slate',
                    ],
                    [
                        'id'          => 'bitacora',
                        'nombre'      => 'Bitácora / Auditoría',
                        'descripcion' => 'Registro cronológico de todas las acciones del sistema.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'clock',
                        'badge'       => null,
                        'estado'      => 'activo',
                        'color'       => 'rose',
                    ],
                    [
                        'id'          => 'administracion',
                        'nombre'      => 'Administración',
                        'descripcion' => 'Usuarios, roles, permisos y configuración del sistema.',
                        'ruta'        => route('switch.role','admin@ars.com'),
                        'icon'        => 'cog',
                        'badge'       => null,
                        'estado'      => 'activo',
                        'color'       => 'gray',
                    ],
                ],
            ],
        ];

        return view('core.login', compact('categorias', 'kpis', 'alertas'));
    }

    /**
     * Inicia sesión y cambia de rol demo de manera instantánea.
     */
    public function switchRole($role)
    {
        $user = User::where('role', $role)->first();
        if (!$user) {
            // Caso especial para login por email
            $user = User::where('email', $role)->first();
        }

        if ($user) {
            Auth::login($user);
            Bitacora::registrar('Seguridad', 'Cambio de sesión rápida al rol: ' . $user->role);

            if ($user->role === 'Usuario PSS') {
                return redirect()->route('pss.dashboard')->with('success', 'Sesión cambiada a Portal PSS');
            } elseif ($user->role === 'Estudiante') {
                return redirect()->route('classroom.dashboard')->with('success', 'Sesión cambiada a Aula Virtual');
            } else {
                return redirect()->route('ars.dashboard')->with('success', 'Sesión cambiada a Portal ARS');
            }
        }

        return redirect()->route('login')->with('error', 'Usuario demo no encontrado.');
    }

    /**
     * Cierra la sesión activa.
     */
    public function logout()
    {
        if (Auth::check()) {
            Bitacora::registrar('Seguridad', 'Cierre de sesión del usuario: ' . Auth::user()->name);
        }
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Muestra el dashboard ejecutivo de la ARS.
     */
    public function dashboard()
    {
        // Métricas KPI
        $kpis = [
            'afiliados_activos' => Afiliado::where('estado_afiliacion', 'OK')->count(),
            'titulares' => Afiliado::count(),
            'dependientes' => Dependiente::count(),
            'solicitudes_pendientes' => Autorizacion::where('estado', 'Pendiente')->count(),
            'lotes_procesados' => Lote::whereIn('estado_lote', ['EV', 'PE', 'RE'])->count(),
            'novedades_pendientes' => Novedad::where('estado', 'Pendiente')->count(),
            'autorizaciones_dia' => Autorizacion::whereDate('fecha_solicitud', now()->toDateString())->count(),
            'auditorias_pendientes' => Autorizacion::where('estado', 'Auditoría')->count(),
            'pss_activas' => Pss::where('estado', 'Activa')->count(),
            'rechazos_mes' => Autorizacion::where('estado', 'Rechazada')->where('fecha_solicitud', '>=', now()->subDays(30))->count(),
        ];

        // Gráfico 1: Afiliaciones por mes (últimos 6 meses)
        $afiliacionesMes = Afiliado::select(
                DB::raw("strftime('%m', created_at) as mes"),
                DB::raw('count(*) as total')
            )
            ->where('estado_afiliacion', 'OK')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->take(6)
            ->get();
        
        $mesesNombres = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];

        $chartAfiliaciones = [
            'labels' => $afiliacionesMes->map(fn($item) => $mesesNombres[$item->mes] ?? $item->mes)->toArray(),
            'data' => $afiliacionesMes->map(fn($item) => $item->total)->toArray()
        ];
        
        // Si está vacío (SQLite date formats), rellenar con datos demo representativos
        if (empty($chartAfiliaciones['labels'])) {
            $chartAfiliaciones = [
                'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                'data' => [12, 19, 25, 30, 42, 48]
            ];
        }

        // Gráfico 2: Autorizaciones por estado
        $autEstados = Autorizacion::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        $chartEstados = [
            'labels' => $autEstados->map(fn($item) => $item->estado)->toArray(),
            'data' => $autEstados->map(fn($item) => $item->total)->toArray()
        ];

        // Gráfico 3: Solicitudes por prioridad/tipo
        $autPrioridades = Autorizacion::select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->get();
        
        $chartPrioridades = [
            'labels' => $autPrioridades->map(fn($item) => $item->prioridad)->toArray(),
            'data' => $autPrioridades->map(fn($item) => $item->total)->toArray()
        ];

        // Gráfico 4: Novedades por tipo
        $novedadesTipo = Novedad::select('tipo_novedad_id', DB::raw('count(*) as total'))
            ->groupBy('tipo_novedad_id')
            ->with('tipoNovedad')
            ->get();
            
        $chartNovedades = [
            'labels' => $novedadesTipo->map(fn($item) => $item->tipoNovedad ? $item->tipoNovedad->codigo : 'OTRO')->toArray(),
            'data' => $novedadesTipo->map(fn($item) => $item->total)->toArray()
        ];

        // Últimas 5 autorizaciones médicas
        $ultimasAutorizaciones = Autorizacion::with('pss', 'servicio')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Últimos lotes
        $ultimosLotes = Lote::orderBy('created_at', 'desc')->take(5)->get();

        return view('ars.dashboard', compact(
            'kpis',
            'chartAfiliaciones',
            'chartEstados',
            'chartPrioridades',
            'chartNovedades',
            'ultimasAutorizaciones',
            'ultimosLotes'
        ));
    }

    /**
     * Administración: Usuarios del sistema.
     */
    public function usuariosIndex()
    {
        $usuarios = User::all();
        return view('ars.admin.usuarios', compact('usuarios'));
    }

    /**
     * Administración: Catálogos generales.
     */
    public function catalogosIndex(Request $request)
    {
        $grupo = $request->get('grupo');
        $grupos = Catalogo::select('grupo')->distinct()->pluck('grupo');

        $query = Catalogo::query();
        if ($grupo) {
            $query->where('grupo', $grupo);
        }
        $catalogos = $query->paginate(15);

        return view('ars.admin.catalogos', compact('catalogos', 'grupos', 'grupo'));
    }

    /**
     * Administración: Auditoría (Bitácora de eventos).
     */
    public function bitacoraIndex(Request $request)
    {
        $modulo = $request->get('modulo');
        $modulos = Bitacora::select('modulo')->distinct()->pluck('modulo');

        $query = Bitacora::with('usuario')->orderBy('fecha_registro', 'desc');
        if ($modulo) {
            $query->where('modulo', $modulo);
        }
        $bitacoras = $query->paginate(20);

        return view('ars.admin.bitacora', compact('bitacoras', 'modulos', 'modulo'));
    }
}
