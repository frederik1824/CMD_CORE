@extends('layouts.ars')

@section('title', 'Calendario de Actividades PyP')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Calendario de Actividades Preventivas (PyP)</h2>
            <p class="text-xs text-slate-500 font-medium">Planificación de charlas, talleres de salud preventiva, campañas de vacunación y chequeos rutinarios.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Actividad -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Programar Taller / Campaña</h3>
            <form action="{{ route('ars.pyp.guardar_actividad') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre de la Actividad <span class="text-rose-500">*</span></label>
                    <input type="text" name="activity_name" placeholder="Ej. Taller de Nutrición Diabética" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Programa Preventivo <span class="text-rose-500">*</span></label>
                    <select name="program_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                        @foreach($programas as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Fecha Programada <span class="text-rose-500">*</span></label>
                        <input type="date" name="scheduled_date" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Ubicación <span class="text-rose-500">*</span></label>
                        <input type="text" name="location" placeholder="Ej. Auditorio Central" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" required>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Detalle o Expositores</label>
                    <textarea name="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Breve nota sobre expositores o cupo..."></textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Programar Evento</button>
            </form>
        </div>

        <!-- Listado de Eventos (Calendario) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Próximos Talleres y Campañas Planificadas</h3>
            <div class="space-y-3">
                @forelse($calendar as $c)
                    <div class="p-4 bg-slate-50 border border-slate-150 rounded-2xl flex items-center justify-between hover:bg-slate-100/50 transition">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-slate-900 text-sm">{{ $c->activity_name }}</span>
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[8px] font-bold text-blue-700 border border-blue-200">{{ $c->program?->name }}</span>
                            </div>
                            <p class="text-slate-500 font-normal leading-relaxed">{{ $c->description ?? 'Sin descripción' }}</p>
                            <div class="flex items-center space-x-3 text-[10px] text-slate-450 font-mono">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">calendar_today</span> {{ $c->scheduled_date }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">location_on</span> {{ $c->location }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                {{ $c->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-8 text-slate-400 font-semibold">No hay actividades preventivas calendarizadas en este momento.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
