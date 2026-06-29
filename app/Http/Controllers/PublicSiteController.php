<?php

namespace App\Http\Controllers;

use App\Models\Pss;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    /**
     * Muestra la página principal pública de la ARS.
     */
    public function index()
    {
        // Obtener prestadoras activas para mostrarlas en la sección de red médica
        $prestadoras = Pss::where('estado', 'Activa')->take(3)->get();

        // Noticias/Comunicados institucionales mockeados
        $noticias = [
            [
                'title' => 'Lanzamiento de nuestra Plataforma Virtual del Afiliado',
                'description' => 'Ahora puedes consultar tu carnet digital, historial de autorizaciones y núcleo familiar desde cualquier dispositivo.',
                'date' => '25 de Junio, 2026',
                'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Actualización e inclusión de nuevos prestadores a nuestra red nacional',
                'description' => 'Incorporamos nuevos centros médicos y especialistas en la zona norte y este del país para ampliar tu cobertura médica.',
                'date' => '18 de Junio, 2026',
                'image' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=500&auto=format&fit=crop&q=60'
            ],
            [
                'title' => 'Guía rápida: ¿Cómo solicitar autorizaciones médicas de alto costo?',
                'description' => 'Conoce los requisitos y plazos de auditoría médica para procedimientos especializados, quimioterapias y cirugías complejas.',
                'date' => '10 de Junio, 2026',
                'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=500&auto=format&fit=crop&q=60'
            ]
        ];

        return view('public.index', compact('prestadoras', 'noticias'));
    }
}
