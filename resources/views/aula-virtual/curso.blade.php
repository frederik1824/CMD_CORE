@extends('layouts.virtual-classroom')

@section('title', $curso->title)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    
    <!-- Course header -->
    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm flex flex-col md:flex-row">
        <img src="{{ $curso->image }}" alt="{{ $curso->title }}" class="w-full md:w-80 h-48 md:h-auto object-cover">
        
        <div class="p-6 md:p-8 flex flex-col justify-between flex-grow">
            <div>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-3">
                    {{ $curso->category }}
                </span>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight leading-snug">{{ $curso->title }}</h2>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">{{ $curso->description }}</p>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-50 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div class="text-xs">
                        <span class="text-slate-400 block font-mono">Duración</span>
                        <span class="font-bold text-slate-800">{{ $curso->hours }} Horas</span>
                    </div>
                    @if($inscripcion)
                        <div class="text-xs">
                            <span class="text-slate-400 block font-mono">Progreso</span>
                            <span class="font-bold text-emerald-600">{{ $progresoPorcentaje }}% Completado</span>
                        </div>
                    @endif
                </div>

                @if($inscripcion)
                    @if($inscripcion->status === 'Completado')
                        <span class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-150 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm" data-icon="verified">verified</span>
                            Curso Completado
                        </span>
                    @else
                        <!-- Progress bar -->
                        <div class="w-44 bg-slate-100 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $progresoPorcentaje }}%"></div>
                        </div>
                    @endif
                @else
                    <form action="{{ route('classroom.matricular', $curso->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white transition">
                            Matricularse en el Curso
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Lecciones / Contenido -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Plan de Temas del Curso</h3>
            @if($inscripcion && $inscripcion->status !== 'Completado')
                <span class="text-[10px] text-slate-400 font-medium">Completa todos los temas para desbloquear el examen.</span>
            @endif
        </div>
        
        <div class="divide-y divide-slate-100">
            @foreach($curso->lessons as $lesson)
                @php
                    $isCompleted = in_array($lesson->id, $leccionesCompletadas);
                @endphp
                <div class="p-5 flex items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $isCompleted ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400' }}">
                            @if($isCompleted)
                                <span class="material-symbols-outlined text-base" data-icon="check">check</span>
                            @else
                                <span class="text-xs font-bold">{{ $lesson->order_index }}</span>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 leading-snug">{{ $lesson->title }}</h4>
                            <span class="text-[9px] text-slate-400 font-mono mt-0.5 block">Duración: {{ $lesson->duration_minutes }} minutos</span>
                        </div>
                    </div>

                    @if($inscripcion)
                        <a href="{{ route('classroom.leccion', [$curso->id, $lesson->id]) }}" class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition {{ $isCompleted ? 'bg-slate-100 hover:bg-slate-200 text-slate-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}">
                            {{ $isCompleted ? 'Repasar' : 'Iniciar' }}
                        </a>
                    @else
                        <span class="text-[10px] text-slate-400 font-semibold italic">Bloqueado</span>
                    @endif
                </div>
            @endforeach
        </div>

        @if($inscripcion)
            <!-- Examen / Evaluación Final -->
            <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Evaluación Final del Curso</h4>
                    <p class="text-[10px] text-slate-400 mt-0.5">Requiere aprobar un cuestionario de opción múltiple con un mínimo de 70%.</p>
                </div>
                
                @if($inscripcion->status === 'Completado')
                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm" data-icon="check_circle">check_circle</span>
                        Aprobado con Éxito
                    </span>
                @else
                    @php
                        $canTakeExam = count($leccionesCompletadas) === $curso->lessons->count();
                    @endphp
                    
                    @if($canTakeExam)
                        <a href="{{ route('classroom.evaluacion', $curso->id) }}" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white transition shadow-sm">
                            Iniciar Evaluación
                        </a>
                    @else
                        <button disabled class="px-5 py-2 rounded-xl bg-slate-150 text-slate-400 text-xs font-bold cursor-not-allowed border border-slate-200">
                            Completa los Temas Primero
                        </button>
                    @endif
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
