@extends('layouts.ars')

@section('title', 'Contratos & Tarifarios')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="sm:flex sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:text-3xl sm:truncate tracking-tight">
                Contratos & Tarifarios PSS
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Gestión de convenios de servicios y tarifas asignadas a cada prestador del seguro de salud.
            </p>
        </div>
    </div>

    <!-- Listado de Contratos con Acordeón de Tarifas -->
    <div class="space-y-4" x-data="{ activeAccordion: '' }">
        @forelse($contratos as $contrato)
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden transition-all duration-300">
                <!-- Encabezado de Contrato -->
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between cursor-pointer hover:bg-slate-50 transition gap-4" @click="activeAccordion = (activeAccordion === 'c-{{ $contrato->id }}' ? '' : 'c-{{ $contrato->id }}')">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-brand-50 text-brand-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 font-mono">{{ $contrato->numero_contrato }}</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Prestador: <span class="text-slate-700 font-bold">{{ $contrato->pss->nombre }}</span></p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Vigencia: {{ $contrato->fecha_inicio->format('d/m/Y') }} al {{ $contrato->fecha_fin->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 justify-end">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide {{ 
                            $contrato->estado === 'Activo' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'
                        }}">
                            {{ $contrato->estado }}
                        </span>
                        
                        <button class="text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg class="h-5 w-5 transform transition-transform duration-300" :class="activeAccordion === 'c-{{ $contrato->id }}' ? 'rotate-185' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Detalles / Tarifario (Se expande) -->
                <div x-show="activeAccordion === 'c-{{ $contrato->id }}'" x-transition class="border-t border-slate-100 p-6 bg-slate-50/50 space-y-4" x-cloak>
                    <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                        <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tarifario de Procedimientos Contratados</h5>
                        <span class="text-[10px] font-semibold bg-brand-50 text-brand-700 px-2 py-0.5 rounded border border-brand-100">{{ $contrato->tarifas->count() }} Servicios Cubiertos</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-80 overflow-y-auto pr-2">
                        @foreach($contrato->tarifas as $tarifa)
                            <div class="bg-white p-3 rounded-xl border border-slate-150 flex items-center justify-between text-xs hover:border-brand-200 transition shadow-sm">
                                <div>
                                    <span class="font-mono font-bold text-slate-400 block">{{ $tarifa->servicio->codigo }}</span>
                                    <span class="text-slate-700 font-semibold block mt-0.5">{{ $tarifa->servicio->descripcion }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Cobertura ARS: {{ $tarifa->servicio->cobertura_base }}%</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tarifa Acordada</span>
                                    <span class="text-sm font-bold text-slate-800">${{ number_format($tarifa->monto_tarifa, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center text-slate-400 text-sm shadow-sm rounded-2xl border border-slate-200">
                No hay contratos vigentes registrados.
            </div>
        @endforelse
    </div>
    
    <!-- Paginación -->
    @if($contratos->hasPages())
        <div class="mt-4">
            {{ $contratos->links() }}
        </div>
    @endif
</div>
@endsection
