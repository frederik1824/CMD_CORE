@extends('layouts.virtual-classroom')

@section('title', 'Mi Panel Aula Virtual')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome banner -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 tracking-tight">¡Bienvenido al Aula Virtual, {{ Auth::user()->name }}!</h2>
            <p class="text-xs text-slate-400 mt-0.5">Sigue fortaleciendo tus conocimientos sobre los procesos de salud y el ecosistema ARS.</p>
        </div>
        <div>
            <a href="{{ route('classroom.cursos') }}" class="inline-flex items-center px-4 py-2.5 rounded-full shadow-sm text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                <span class="material-symbols-outlined text-sm mr-1.5" data-icon="search">search</span>
                Explorar Catálogo
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] transition">
            <div>
                <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider block">Cursos en Progreso</span>
                <span class="text-2xl font-extrabold text-slate-800 block mt-1">{{ $stats['en_curso'] }}</span>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-full">
                <span class="material-symbols-outlined text-xl" data-icon="menu_book">menu_book</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] transition">
            <div>
                <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider block">Cursos Completados</span>
                <span class="text-2xl font-extrabold text-emerald-600 block mt-1">{{ $stats['completados'] }}</span>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-full">
                <span class="material-symbols-outlined text-xl" data-icon="task_alt">task_alt</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:scale-[1.01] transition">
            <div>
                <span class="text-[9px] font-bold text-slate-450 uppercase tracking-wider block">Certificados Emitidos</span>
                <span class="text-2xl font-extrabold text-blue-600 block mt-1">{{ $stats['certificados'] }}</span>
            </div>
            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-full">
                <span class="material-symbols-outlined text-xl" data-icon="workspace_premium">workspace_premium</span>
            </div>
        </div>
    </div>

    <!-- Enrolled courses -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Mis Cursos</h3>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($inscripciones as $ins)
                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition">
                    <div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-2">
                            {{ $ins->course->category }}
                        </span>
                        <h4 class="text-sm font-bold text-slate-800">{{ $ins->course->title }}</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">{{ $ins->course->description }}</p>
                    </div>

                    <div class="flex items-center gap-6 self-end md:self-auto">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-mono">Estado</span>
                            <span class="text-xs font-bold {{ $ins->status === 'Completado' ? 'text-emerald-600' : 'text-amber-600' }}">
                                {{ $ins->status }}
                            </span>
                        </div>
                        <a href="{{ route('classroom.curso', $ins->course->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                            {{ $ins->status === 'Completado' ? 'Repasar Curso' : 'Continuar' }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    No estás inscrito en ningún curso actualmente. <a href="{{ route('classroom.cursos') }}" class="text-emerald-600 font-bold hover:underline">Explora el catálogo</a>.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
