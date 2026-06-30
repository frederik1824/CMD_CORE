@extends('layouts.ars')

@section('title', 'Dashboard de PyP')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard de PyP</h2>
            <p class="text-xs text-slate-500 font-medium">Indicadores de Promoción y Prevención de Salud.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <!-- Bento Grid PyP -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Inscripciones Activas</span>
            <span class="text-3xl font-extrabold text-slate-900 block mt-2 font-mono">1,482</span>
            <p class="text-slate-400 text-[10px] mt-1">Afiliados enrolados en programas preventivos.</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Candidatos Detectados</span>
            <span class="text-3xl font-extrabold text-slate-900 block mt-2 font-mono">2,389</span>
            <p class="text-slate-400 text-[10px] mt-1">Identificados por algoritmo de riesgos epidemiológicos.</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Programas de Salud</span>
            <span class="text-3xl font-extrabold text-slate-900 block mt-2 font-mono">{{ $programas->count() }}</span>
            <p class="text-slate-400 text-[10px] mt-1">Programas preventivos configurados en el core.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Listado de Programas -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                <h3 class="font-bold text-slate-800">Programas de Prevención</h3>
                <a href="{{ route('ars.pyp.programas') }}" class="text-blue-600 font-bold hover:underline">Configurar</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Programa</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-center">Candidatos</th>
                            <th class="px-4 py-3 text-center">Inscritos</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($programas as $p)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $p->program_type }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-blue-900">{{ $p->candidates_count }}</td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-teal-600">{{ $p->enrollments_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $p->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Próximas Actividades -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                <h3 class="font-bold text-slate-800">Actividades no Asistenciales</h3>
                <a href="{{ route('ars.pyp.actividades') }}" class="text-blue-600 font-bold hover:underline">Programar</a>
            </div>
            <div class="space-y-3">
                @forelse($calendar as $event)
                    <div class="p-3 rounded-2xl bg-slate-50/50 border border-slate-100 flex items-start space-x-3 hover:bg-slate-50 transition">
                        <span class="material-symbols-outlined text-teal-600 mt-0.5">event</span>
                        <div>
                            <h4 class="font-bold text-slate-800">{{ $event->service_name }}</h4>
                            <p class="text-[9px] text-slate-400 font-medium">Programa: {{ $event->program->name }}</p>
                            <span class="text-[9px] text-slate-500 font-mono mt-1 block">{{ $event->scheduled_date }} - Cap: {{ $event->capacity }} personas</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400 font-semibold">No hay actividades programadas.</div>
                @endforelse
            </div>
        </div>
    </div>


</div>
@endsection
