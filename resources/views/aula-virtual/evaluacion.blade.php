@extends('layouts.virtual-classroom')

@section('title', 'Evaluación Final: ' . $curso->title)

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <!-- Back Link -->
    <a href="{{ route('classroom.curso', $curso->id) }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700">
        &larr; Cancelar y Volver al Curso
    </a>

    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100 mb-2">
            Examen Final
        </span>
        <h2 class="text-lg font-bold text-slate-800 tracking-tight leading-snug">{{ $evaluacion->title }}</h2>
        <p class="text-xs text-slate-400 mt-1">Responde las siguientes preguntas. Se requiere una puntuación de al menos {{ $evaluacion->min_score }}% para aprobar y obtener tu certificado.</p>
    </div>

    <form action="{{ route('classroom.evaluacion.procesar', $curso->id) }}" method="POST" class="space-y-6">
        @csrf
        
        @foreach($evaluacion->questions_json as $index => $q)
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pregunta {{ $index + 1 }}</span>
                <h4 class="text-xs font-bold text-slate-800 leading-snug">{{ $q['pregunta'] }}</h4>
                
                <div class="space-y-2 pt-2">
                    @foreach($q['opciones'] as $oIdx => $option)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-emerald-250 cursor-pointer transition text-xs font-medium">
                            <input type="radio" name="respuestas[{{ $index }}]" value="{{ $oIdx }}" required class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-350">
                            <span class="text-slate-700">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-md shadow-emerald-700/10">
            Enviar Respuestas y Calificar
        </button>
    </form>
</div>
@endsection
