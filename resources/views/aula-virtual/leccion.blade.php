@extends('layouts.virtual-classroom')

@section('title', $leccion->title)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Back link -->
    <a href="{{ route('classroom.curso', $curso->id) }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700">
        &larr; Volver al Curso
    </a>

    <!-- Lesson Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-2">
            Tema {{ $leccion->order_index }}
        </span>
        <h2 class="text-lg font-bold text-slate-800 tracking-tight leading-snug">{{ $leccion->title }}</h2>
        <p class="text-xs text-slate-400 mt-1">Duración estimada de lectura/estudio: {{ $leccion->duration_minutes }} minutos</p>
    </div>

    <!-- Video Placeholder & Text Content -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main study pane -->
        <div class="md:col-span-2 space-y-6">
            <!-- Simulated Video Player -->
            <div class="relative bg-slate-900 rounded-3xl overflow-hidden aspect-video shadow-md border border-slate-800 flex items-center justify-center">
                <!-- Video image overlay background -->
                <div class="absolute inset-0 bg-cover bg-center opacity-60" style="background-image: url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80')"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-black/10"></div>
                
                <!-- Play button -->
                <div class="relative z-10 text-center">
                    <button class="w-16 h-16 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all duration-200">
                        <span class="material-symbols-outlined text-3xl font-bold ml-1" data-icon="play_arrow">play_arrow</span>
                    </button>
                    <span class="text-[10px] font-bold text-blue-200 block mt-3 uppercase tracking-wider">Reproducir Video de Capacitación</span>
                </div>
            </div>

            <!-- Written Content -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 text-sm">Contenido de la Lección</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ $leccion->content }}
                </p>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Recuerda revisar detenidamente los conceptos explicados en el video antes de marcar este tema como completado. El contenido de esta lección será evaluado en el cuestionario final del curso.
                </p>
            </div>
        </div>

        <!-- Sidebar (Materials & Actions) -->
        <div class="space-y-6">
            <!-- Action card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-center">
                <span class="material-symbols-outlined text-4xl text-emerald-600 mb-2" data-icon="task_alt">task_alt</span>
                <h4 class="text-xs font-bold text-slate-800">¿Terminaste de estudiar?</h4>
                <p class="text-[10px] text-slate-400 mt-1 leading-normal">Marca esta lección como completada para guardar tu progreso y avanzar al siguiente tema.</p>
                
                <form action="{{ route('classroom.leccion.completar', [$curso->id, $leccion->id]) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-md shadow-emerald-700/10">
                        Marcar como Completado
                    </button>
                </form>
            </div>

            <!-- Downloadable materials -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Materiales de Soporte</h4>
                
                @forelse($leccion->materials as $mat)
                    <div class="flex items-center justify-between text-xs p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 text-lg" data-icon="picture_as_pdf">picture_as_pdf</span>
                            <div>
                                <span class="font-bold text-slate-700 block truncate max-w-[120px]">{{ $mat->name }}</span>
                                <span class="text-[9px] text-slate-400 font-mono">{{ round($mat->size_bytes / (1024 * 1024), 2) }} MB</span>
                            </div>
                        </div>
                        <a href="#" onclick="alert('Descargando archivo demo...')" class="p-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-emerald-600 transition" title="Descargar">
                            <span class="material-symbols-outlined text-base" data-icon="download">download</span>
                        </a>
                    </div>
                @empty
                    <p class="text-[10px] text-slate-400 italic">No hay materiales descargables para este tema.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
