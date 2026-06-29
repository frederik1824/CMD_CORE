@extends('layouts.ars')

@section('title', 'Resumen de Importación')

@section('content')
<div class="max-w-2xl mx-auto space-y-8 animate-fade-in">
    <!-- Stepper Wizard -->
    <div class="flex items-center justify-between max-w-xl mx-auto mb-8 bg-white/60 p-4 rounded-full border border-[#e0e0e0] shadow-sm backdrop-blur-sm">
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold bg-[#e6f4ea] text-[#137333] border border-[#ceead6]">✓</div>
            <span class="text-sm font-medium text-[#5f6368]">Subir CSV</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-[#137333]/30"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold bg-[#e6f4ea] text-[#137333] border border-[#ceead6]">✓</div>
            <span class="text-sm font-medium text-[#5f6368]">Prevalidar</span>
        </div>
        <div class="flex-1 h-0.5 mx-3 bg-[#137333]/30"></div>
        <div class="flex items-center space-x-2.5 px-3">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold bg-[#0b57d0] text-white">3</div>
            <span class="text-sm font-semibold text-[#1f1f1f]">Resultado</span>
        </div>
    </div>

    <!-- Icono de Éxito -->
    <div class="text-center space-y-3">
        <div class="inline-flex p-5 bg-[#e6f4ea] text-[#137333] rounded-full border border-[#ceead6] shadow-sm mb-2">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-semibold text-[#1f1f1f] tracking-tight">Procesamiento de Archivo Completado</h2>
        <p class="text-sm text-[#5f6368] max-w-sm mx-auto">La importación masiva finalizó. Los registros aptos han sido agrupados en un lote para su envío formal a Unipago.</p>
    </div>

    <!-- Panel de Resultados -->
    <div class="bg-white p-6 shadow-sm rounded-3xl border border-[#e0e0e0] divide-y divide-[#f1f3f4] text-sm">
        <div class="pb-4 flex justify-between items-center">
            <span class="text-[#5f6368] font-medium">Total de Registros Evaluados</span>
            <span class="font-bold text-[#1f1f1f] font-mono text-base">{{ $resumen['total'] }}</span>
        </div>
        <div class="py-4 flex justify-between items-center">
            <span class="text-[#5f6368] font-medium">Importados & Aceptados (Aptos)</span>
            <span class="font-bold text-[#137333] font-mono text-base">{{ $resumen['aptos'] }}</span>
        </div>
        <div class="py-4 flex justify-between items-center">
            <span class="text-[#5f6368] font-medium">Rechazados Directamente en Carga</span>
            <span class="font-bold text-[#c5221f] font-mono text-base">{{ $resumen['rechazados'] }}</span>
        </div>
        
        @if($lote)
            <div class="pt-4 space-y-3">
                <div class="p-4 bg-[#f8f9fa] rounded-2xl border border-[#e0e0e0] flex justify-between items-center gap-3">
                    <div>
                        <span class="text-xs font-semibold text-[#5f6368] uppercase tracking-wider block">Lote de Afiliación Creado</span>
                        <span class="text-base font-bold text-[#0b57d0] font-mono mt-0.5 block">{{ $lote->numero_lote }}</span>
                    </div>
                    <a href="{{ route('ars.lotes.show', $lote->id) }}" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-full text-xs font-semibold text-white bg-[#0b57d0] hover:bg-[#0b57d0]/90 transition duration-150 shadow-sm active:scale-98">
                        Ver Lote & Procesar
                    </a>
                </div>
            </div>
        @else
            <div class="pt-4">
                <div class="p-4 bg-[#fce8e6] border-l-4 border-[#c5221f] rounded-r-2xl text-[#c5221f] text-xs">
                    No se ha generado ningún lote debido a que el 100% de los registros evaluados fueron catalogados como NO APTOS por incidencias TSS.
                </div>
            </div>
        @endif
    </div>

    <!-- Botón Volver -->
    <div class="text-center pt-2">
        <a href="{{ route('ars.titulares.index') }}" class="inline-flex items-center px-6 py-3 border border-[#dcdcdc] rounded-full text-sm font-semibold text-[#5f6368] bg-white hover:bg-[#f8f9fa] transition duration-150">
            Volver a Titulares
        </a>
    </div>
</div>
@endsection

