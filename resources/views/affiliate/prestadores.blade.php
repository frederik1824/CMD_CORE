@extends('layouts.affiliate')

@section('title', 'Red de Prestadores')

@section('content')
<div class="space-y-6 font-sans">
    
    <!-- HEADER & BUSCADOR (GOOGLE DIRECTORY HEADER) -->
    <div class="bg-white p-6 rounded-2xl border border-google-border flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
        <div>
            <h2 class="text-base font-bold text-google-textMain tracking-tight">Red Nacional de Prestadores Afiliados</h2>
            <p class="text-xs text-google-textSub mt-0.5 font-medium">Encuentra clínicas, hospitales, laboratorios clínicos y farmacias contratadas por tu plan.</p>
        </div>
        
        <!-- Buscador estilo Google Search Bar -->
        <form action="{{ route('affiliate.prestadores') }}" method="GET" class="w-full md:w-80 relative font-semibold">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o dirección..."
                   class="block w-full rounded-full border border-google-border py-2 pl-4 pr-10 text-xs text-google-textMain focus:outline-none focus:ring-1 focus:ring-google-blue focus:border-google-blue bg-white shadow-2xs placeholder:text-slate-400">
            <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center text-google-textSub hover:text-google-textMain transition">
                <span class="material-symbols-outlined text-[15px]" data-icon="search">search</span>
            </button>
        </form>
    </div>

    <!-- RESULTADOS GRID DE TARJETAS GOOGLE -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($prestadores as $prestador)
            <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-250 group">
                <div>
                    <!-- Badge del tipo de entidad -->
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-600 border border-blue-100 mb-3.5">
                        {{ $prestador->tipo_entidad }}
                    </span>
                    <h4 class="font-bold text-slate-800 text-sm leading-snug tracking-tight group-hover:text-blue-600 transition-colors">{{ $prestador->nombre }}</h4>
                    <p class="text-[11px] text-slate-500 mt-2.5 flex items-start gap-1 font-medium leading-relaxed">
                        <span class="material-symbols-outlined text-[14px] text-slate-400 mt-0.5 shrink-0" data-icon="location_on">location_on</span>
                        <span>{{ $prestador->direccion }}</span>
                    </p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] font-semibold">
                    <span class="text-slate-400 font-mono">RNC: {{ $prestador->rnc }}</span>
                    <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Activo</span>
                </div>
            </div>
        @empty
            <div class="col-span-4 bg-white p-8 text-center text-slate-500 rounded-2xl border border-slate-200 shadow-xs font-medium">
                No se encontraron prestadores que coincidan con la búsqueda.
            </div>
        @endforelse
    </div>

    @if($prestadores->hasPages())
        <div class="p-3 bg-white border border-google-border rounded-2xl shadow-xs">
            {{ $prestadores->appends(['search' => $search])->links() }}
        </div>
    @endif

</div>
@endsection
