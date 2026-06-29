@extends('layouts.virtual-classroom')

@section('title', 'Catálogo de Cursos')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Catálogo de Cursos Disponibles</h2>
            <p class="text-xs text-slate-400 mt-0.5">Inscríbete y capacítate en el uso de herramientas, derechos de afiliación y normativas operativas.</p>
        </div>
        
        <form action="{{ route('classroom.cursos') }}" method="GET" class="w-full md:w-80 relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por tema o categoría..."
                   class="block w-full rounded-full border-slate-200 py-2.5 pl-4 pr-10 text-xs focus:ring-emerald-600 focus:border-emerald-600 shadow-sm">
            <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400">
                <span class="material-symbols-outlined text-sm" data-icon="search">search</span>
            </button>
        </form>
    </div>

    <!-- Cursos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($cursos as $curso)
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <img src="{{ $curso->image ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=500&auto=format&fit=crop&q=60' }}" 
                     alt="{{ $curso->title }}" class="h-40 w-full object-cover">
                
                <div class="p-6 flex-grow flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $curso->category }}
                            </span>
                            <span class="text-[9px] text-slate-400 font-bold font-mono">
                                {{ $curso->hours }} Horas
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-xs leading-snug mb-2">{{ $curso->title }}</h4>
                        <p class="text-[10px] text-slate-400 leading-normal">{{ Str::limit($curso->description, 130) }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        @php
                            $status = $misInscripciones[$curso->id] ?? null;
                        @endphp
                        
                        @if($status)
                            <span class="text-[10px] font-bold {{ $status === 'Completado' ? 'text-emerald-600' : 'text-amber-600' }}">
                                Inscrito ({{ $status }})
                            </span>
                            <a href="{{ route('classroom.curso', $curso->id) }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-700 transition">
                                Ver Curso
                            </a>
                        @else
                            <span class="text-[10px] text-slate-400">No Inscrito</span>
                            <form action="{{ route('classroom.matricular', $curso->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-[10px] font-bold text-white transition">
                                    Matricularse
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-8 text-center text-slate-400 border border-slate-100 rounded-3xl">
                No se encontraron cursos de capacitación.
            </div>
        @endforelse
    </div>
</div>
@endsection
