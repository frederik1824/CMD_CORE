<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Dependiente;
use App\Models\Autorizacion;
use App\Models\Pss;
use Illuminate\Http\Request;

class AffiliatePortalController extends Controller
{
    /**
     * Muestra la pantalla de login del afiliado.
     */
    public function showLogin()
    {
        if (session()->has('affiliate_id')) {
            return redirect()->route('affiliate.dashboard');
        }

        // Obtener 3 afiliados demo para el selector rápido
        $demoAffiliates = Afiliado::where('estado_afiliacion', 'OK')
            ->whereIn('cedula', ['07900175907', '00100012345', '00100024690', '00100000000'])
            ->take(3)
            ->get();
        
        // Si por alguna razón no los encuentra por cédula específica, tomar cualquiera de los activos
        if ($demoAffiliates->isEmpty()) {
            $demoAffiliates = Afiliado::where('estado_afiliacion', 'OK')->take(3)->get();
        }

        return view('affiliate.login', compact('demoAffiliates'));
    }

    /**
     * Procesa la entrada al portal del afiliado.
     */
    public function login(Request $request)
    {
        $cedula = preg_replace('/[^0-9]/', '', $request->input('cedula'));
        $afiliado = Afiliado::where('cedula', $cedula)->first();

        if (!$afiliado) {
            // Buscar por ID directo si es del selector rápido
            $afiliado = Afiliado::find($request->input('id'));
        }

        if (!$afiliado || $afiliado->estado_afiliacion !== 'OK') {
            return redirect()->back()
                ->with('error', 'Cédula no válida, no registrada o afiliado inactivo en nuestro padrón.');
        }

        session(['affiliate_id' => $afiliado->id]);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Sesión iniciada correctamente en tu Plataforma Virtual.');
    }

    /**
     * Cierra la sesión del afiliado.
     */
    public function logout()
    {
        session()->forget('affiliate_id');
        return redirect()->route('affiliate.login')
            ->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Dashboard principal del afiliado.
     */
    public function dashboard()
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        // Dependientes
        $dependientes = $afiliado->dependientes;

        // Últimas autorizaciones
        $autorizaciones = Autorizacion::where('afiliado_type', 'titular')
            ->where('afiliado_id', $afiliado->id)
            ->with('servicio', 'pss')
            ->orderBy('fecha_solicitud', 'desc')
            ->take(5)
            ->get();

        // Calcular consumo de medicamentos (Límite anual: 12,000)
        $autorizacionesAprobadasMED = Autorizacion::where('afiliado_type', 'titular')
            ->where('afiliado_id', $afiliado->id)
            ->where('estado', 'Aprobada')
            ->whereHas('servicio', function($query) {
                $query->where('codigo', 'like', 'MED-%');
            })
            ->get();

        $consumidoMED = 0;
        foreach ($autorizacionesAprobadasMED as $aut) {
            $coberturaPorcentaje = $aut->servicio->cobertura_base ?? 70.00;
            $montoBase = $aut->monto_contratado > 0 ? $aut->monto_contratado : $aut->monto_solicitado;
            $consumidoMED += $montoBase * ($coberturaPorcentaje / 100);
        }

        $limiteAnualMED = 12000.00;
        $disponibleMED = max(0, $limiteAnualMED - $consumidoMED);

        return view('affiliate.dashboard', compact(
            'afiliado', 
            'dependientes', 
            'autorizaciones', 
            'limiteAnualMED', 
            'consumidoMED', 
            'disponibleMED'
        ));
    }

    /**
     * Muestra la lista de dependientes (Núcleo Familiar).
     */
    public function dependientes()
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        $dependientes = $afiliado->dependientes;

        return view('affiliate.dependientes', compact('afiliado', 'dependientes'));
    }

    /**
     * Muestra las autorizaciones y reclamaciones del afiliado.
     */
    public function autorizaciones()
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        $autorizaciones = Autorizacion::where('afiliado_type', 'titular')
            ->where('afiliado_id', $afiliado->id)
            ->with('servicio', 'pss')
            ->orderBy('fecha_solicitud', 'desc')
            ->paginate(10);

        return view('affiliate.autorizaciones', compact('afiliado', 'autorizaciones'));
    }

    /**
     * Muestra el carnet digital del afiliado.
     */
    public function carnet()
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        return view('affiliate.carnet', compact('afiliado'));
    }

    /**
     * Muestra la red de prestadores (PSS).
     */
    public function prestadores(Request $request)
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        $search = $request->get('search');
        $query = Pss::where('estado', 'Activa');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('tipo_entidad', 'like', "%{$search}%")
                  ->orWhere('direccion', 'like', "%{$search}%");
            });
        }

        $prestadores = $query->paginate(8);

        return view('affiliate.prestadores', compact('afiliado', 'prestadores', 'search'));
    }

    /**
     * Procesa y muestra la solicitud de un servicio.
     */
    public function solicitudes(Request $request)
    {
        $afiliado = $this->getLoggedAffiliate();
        if (!$afiliado) return redirect()->route('affiliate.login');

        if ($request->isMethod('post')) {
            $request->validate([
                'tipo_solicitud' => 'required',
                'descripcion' => 'required|min:10',
                'soporte' => 'nullable|file|max:5120'
            ]);

            $soportePath = null;
            if ($request->hasFile('soporte')) {
                $file = $request->file('soporte');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/solicitudes'), $filename);
                $soportePath = 'uploads/solicitudes/' . $filename;
            }

            \App\Models\SolicitudServicio::create([
                'afiliado_id' => $afiliado->id,
                'tipo_solicitud' => $request->input('tipo_solicitud'),
                'descripcion' => $request->input('descripcion'),
                'soporte_path' => $soportePath,
                'estado' => 'Pendiente'
            ]);

            return redirect()->route('affiliate.solicitudes')
                ->with('success', 'Tu solicitud ha sido guardada y enviada al Core Administrativo con éxito. El equipo de auditoría la evaluará a la brevedad.');
        }

        $solicitudes = \App\Models\SolicitudServicio::where('afiliado_id', $afiliado->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('affiliate.solicitudes', compact('afiliado', 'solicitudes'));
    }

    /**
     * Retorna el afiliado actualmente logueado.
     */
    private function getLoggedAffiliate()
    {
        $id = session('affiliate_id');
        if (!$id) return null;

        return Afiliado::find($id);
    }
}
