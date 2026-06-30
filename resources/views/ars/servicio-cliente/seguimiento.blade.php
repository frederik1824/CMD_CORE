@extends('layouts.ars')
@section('title', 'Seguimiento SLAs')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Monitoreo de SLAs</h2>
            <p class="text-xs text-slate-500 font-medium">Relojes y tiempos límites de respuesta para quejas y solicitudes.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Casos Activos y Tiempos de Respuesta</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($casos as $cas)
                <div class="p-5 rounded-3xl border border-slate-100 bg-slate-50/20 space-y-3 hover:bg-slate-50/60 transition flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-slate-800 text-[11px]">{{ $cas->case_type }}</h4>
                            <p class="text-[9px] text-slate-400 mt-0.5">Afiliado: {{ $cas->affiliate->nombres }} {{ $cas->affiliate->primer_apellido }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[9px] font-bold text-rose-700 border border-rose-200">En Proceso</span>
                    </div>

                    <p class="text-[10px] text-slate-500 leading-relaxed italic">"{{ $cas->description }}"</p>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-1 text-slate-450">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            <span class="font-mono font-semibold">Plazo SLA: {{ $cas->sla_hours }} Horas</span>
                        </div>
                        <!-- Reloj Semaforo -->
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-[9px] font-extrabold text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-2 animate-ping"></span>
                            34 Horas Restantes
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-2 p-10 text-center text-slate-400 border border-dashed border-slate-200 rounded-3xl font-semibold">
                    No se registran casos pendientes de resolución en proceso de seguimiento de ANS/SLA.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection