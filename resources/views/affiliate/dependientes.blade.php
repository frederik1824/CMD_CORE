@extends('layouts.affiliate')

@section('title', 'Mi Núcleo Familiar')

@section('content')
<div class="space-y-6 font-sans">
    
    <!-- HEADER -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 shrink-0">
            <span class="material-symbols-outlined text-2xl" data-icon="family_history">family_history</span>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800 tracking-tight">Mi Núcleo Familiar (Dependientes)</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium leading-relaxed font-sans">Listado de familiares directos que se encuentran registrados y cubiertos bajo tu póliza de salud contributiva de ARS CMD.</p>
        </div>
    </div>

    <!-- NUCLEO FAMILIAR LISTADO -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            Miembros del Núcleo Asegurado
        </div>
        
        <div class="divide-y divide-slate-100">
            @forelse($dependientes as $dep)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5 hover:bg-slate-50/70 transition duration-150 text-xs font-semibold text-slate-700">
                    <div class="flex items-center gap-4">
                        <!-- Avatar con Inicial -->
                        <div class="w-11 h-11 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base border border-blue-100 shrink-0">
                            {{ substr($dep->nombres, 0, 1) }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 text-sm block">{{ $dep->nombre_completo }}</span>
                            <span class="text-[11px] text-slate-400 font-medium mt-1 block">Sexo: {{ $dep->sexo }} • Edad: {{ $dep->edad }} años</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs font-medium sm:text-right items-center">
                        <div>
                            <span class="text-[8.5px] text-slate-400 block uppercase tracking-wider">Relación</span>
                            <span class="text-slate-700 block font-bold mt-1">{{ optional($dep->parentesco)->descripcion ?? 'Dependiente' }}</span>
                        </div>
                        <div class="font-mono">
                            <span class="text-[8.5px] text-slate-400 block uppercase tracking-wider">Cédula / NSS</span>
                            <span class="text-slate-600 block mt-1">Ced: {{ $dep->cedula ?: 'N/A' }}</span>
                            <span class="text-slate-400 block text-[10px] mt-0.5">NSS: {{ $dep->nss }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-center justify-end sm:justify-end">
                            @if($dep->estado_afiliacion === 'OK' || $dep->estado_afiliacion === 'Activo')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-rose-500 rounded-full"></span>
                                    {{ $dep->estado_afiliacion }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 font-medium">
                    No tienes dependientes registrados bajo tu póliza de salud.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
