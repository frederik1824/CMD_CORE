<?php

namespace App\Http\Controllers;

use App\Models\VirtualCourse;
use App\Models\VirtualLesson;
use App\Models\VirtualEnrollment;
use App\Models\VirtualProgress;
use App\Models\VirtualAssessment;
use App\Models\VirtualCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VirtualClassroomController extends Controller
{
    /**
     * Muestra la pantalla de login del Aula Virtual.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'Estudiante') {
                return redirect()->route('classroom.dashboard');
            }
            return redirect()->route('ars.dashboard');
        }

        return view('aula-virtual.login');
    }

    /**
     * Procesa el inicio de sesión para el Aula Virtual.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'Estudiante') {
                return redirect()->route('classroom.dashboard')
                    ->with('success', 'Sesión iniciada en el Aula Virtual.');
            }
            return redirect()->route('ars.dashboard');
        }

        return redirect()->back()
            ->with('error', 'Credenciales inválidas para ingresar al Aula Virtual.');
    }

    /**
     * Cierra la sesión del estudiante.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('classroom.login')
            ->with('success', 'Sesión del Aula Virtual cerrada.');
    }

    /**
     * Dashboard del estudiante.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Cursos en los que está inscrito
        $inscripciones = VirtualEnrollment::where('user_id', $user->id)
            ->with('course.lessons')
            ->get();

        // Certificados obtenidos
        $certificados = VirtualCertificate::where('user_id', $user->id)
            ->with('course')
            ->get();

        // Estadísticas
        $stats = [
            'en_curso' => $inscripciones->where('status', 'En Curso')->count(),
            'completados' => $inscripciones->where('status', 'Completado')->count(),
            'certificados' => $certificados->count()
        ];

        return view('aula-virtual.dashboard', compact('inscripciones', 'certificados', 'stats'));
    }

    /**
     * Catálogo de cursos del Aula Virtual.
     */
    public function cursos(Request $request)
    {
        $search = $request->get('search');
        $query = VirtualCourse::where('status', 'Activo');

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        $cursos = $query->get();
        $user = Auth::user();

        // Obtener inscripciones del usuario para saber cuáles está cursando
        $misInscripciones = VirtualEnrollment::where('user_id', $user->id)
            ->pluck('status', 'course_id')
            ->toArray();

        return view('aula-virtual.cursos', compact('cursos', 'misInscripciones', 'search'));
    }

    /**
     * Detalle de un curso.
     */
    public function curso($id)
    {
        $curso = VirtualCourse::with('lessons.materials')->findOrFail($id);
        $user = Auth::user();

        $inscripcion = VirtualEnrollment::where('user_id', $user->id)
            ->where('course_id', $curso->id)
            ->first();

        // Calcular progreso
        $leccionesCompletadas = [];
        $progresoPorcentaje = 0;

        if ($inscripcion) {
            $leccionesCompletadas = VirtualProgress::where('enrollment_id', $inscripcion->id)
                ->pluck('lesson_id')
                ->toArray();
            
            $totalLessons = $curso->lessons->count();
            if ($totalLessons > 0) {
                $progresoPorcentaje = round((count($leccionesCompletadas) / $totalLessons) * 100);
            }
        }

        return view('aula-virtual.curso', compact('curso', 'inscripcion', 'leccionesCompletadas', 'progresoPorcentaje'));
    }

    /**
     * Matricularse en un curso.
     */
    public function matricular($id)
    {
        $curso = VirtualCourse::findOrFail($id);
        $user = Auth::user();

        // Crear inscripción
        VirtualEnrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $curso->id
        ], [
            'status' => 'En Curso',
            'enrolled_at' => now()
        ]);

        return redirect()->route('classroom.curso', $curso->id)
            ->with('success', 'Te has matriculado en el curso: ' . $curso->title);
    }

    /**
     * Visualización de una lección.
     */
    public function leccion($courseId, $lessonId)
    {
        $curso = VirtualCourse::findOrFail($courseId);
        $leccion = VirtualLesson::with('materials')->where('course_id', $courseId)->findOrFail($lessonId);
        $user = Auth::user();

        $inscripcion = VirtualEnrollment::where('user_id', $user->id)
            ->where('course_id', $curso->id)
            ->firstOrFail();

        // Lecciones completadas
        $completadas = VirtualProgress::where('enrollment_id', $inscripcion->id)
            ->pluck('lesson_id')
            ->toArray();

        // Obtener siguiente lección si existe
        $siguienteLeccion = VirtualLesson::where('course_id', $courseId)
            ->where('order_index', '>', $leccion->order_index)
            ->orderBy('order_index', 'asc')
            ->first();

        return view('aula-virtual.leccion', compact('curso', 'leccion', 'inscripcion', 'completadas', 'siguienteLeccion'));
    }

    /**
     * Completar lección y avanzar.
     */
    public function completarLeccion($courseId, $lessonId)
    {
        $user = Auth::user();
        $inscripcion = VirtualEnrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Registrar progreso
        VirtualProgress::firstOrCreate([
            'enrollment_id' => $inscripcion->id,
            'lesson_id' => $lessonId
        ], [
            'completed' => true,
            'completed_at' => now()
        ]);

        // Buscar siguiente lección
        $leccionActual = VirtualLesson::findOrFail($lessonId);
        $siguiente = VirtualLesson::where('course_id', $courseId)
            ->where('order_index', '>', $leccionActual->order_index)
            ->orderBy('order_index', 'asc')
            ->first();

        if ($siguiente) {
            return redirect()->route('classroom.leccion', [$courseId, $siguiente->id])
                ->with('success', 'Tema completado. ¡Continúa con el siguiente!');
        }

        // Si no hay siguiente, ir a la evaluación final
        return redirect()->route('classroom.evaluacion', $courseId)
            ->with('success', '¡Felicidades! Has terminado todos los temas. Es hora de realizar la evaluación final.');
    }

    /**
     * Pantalla de evaluación final del curso.
     */
    public function evaluacion($courseId)
    {
        $curso = VirtualCourse::findOrFail($courseId);
        $evaluacion = VirtualAssessment::where('course_id', $courseId)->firstOrFail();
        $user = Auth::user();

        $inscripcion = VirtualEnrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return view('aula-virtual.evaluacion', compact('curso', 'evaluacion', 'inscripcion'));
    }

    /**
     * Procesa las respuestas de la evaluación.
     */
    public function procesarEvaluacion(Request $request, $courseId)
    {
        $user = Auth::user();
        $evaluacion = VirtualAssessment::where('course_id', $courseId)->firstOrFail();
        $inscripcion = VirtualEnrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->firstOrFail();

        $respuestas = $request->input('respuestas', []);
        $questions = $evaluacion->questions_json;
        
        $correctCount = 0;
        foreach ($questions as $index => $q) {
            $respIndex = $respuestas[$index] ?? null;
            if ($respIndex !== null && (int)$respIndex === (int)$q['correcta']) {
                $correctCount++;
            }
        }

        $score = round(($correctCount / count($questions)) * 100);

        if ($score >= $evaluacion->min_score) {
            // Completado con éxito
            $inscripcion->update([
                'status' => 'Completado',
                'completed_at' => now()
            ]);

            // Crear certificado
            $certCode = 'CERT-' . strtoupper(uniqid());
            VirtualCertificate::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $courseId
            ], [
                'certificate_code' => $certCode,
                'issued_at' => now()
            ]);

            return redirect()->route('classroom.curso', $courseId)
                ->with('success', "¡Excelente! Aprobaste con {$score}% de respuestas correctas. Tu certificado ha sido emitido.");
        }

        return redirect()->route('classroom.evaluacion', $courseId)
            ->with('error', "No has obtenido la puntuación mínima (Obtuviste {$score}%, requerido {$evaluacion->min_score}%). Inténtalo de nuevo.");
    }

    /**
     * Muestra e imprime certificados del estudiante.
     */
    public function certificados()
    {
        $user = Auth::user();
        $certificados = VirtualCertificate::where('user_id', $user->id)
            ->with('course')
            ->get();

        return view('aula-virtual.certificados', compact('certificados'));
    }
}
