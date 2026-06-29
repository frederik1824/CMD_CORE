@extends('layouts.affiliate')

@section('title', 'Historial de Autorizaciones')

@section('content')
<div class="space-y-6 font-sans" 
     x-data="{
        buscarTexto: '',
        autorizaciones: [
            @foreach($autorizaciones as $sol)
            {
                numero: '{{ $sol->numero_autorizacion }}',
                procedimiento: '{{ addslashes($sol->procedimiento) }}',
                pss: '{{ addslashes($sol->pss->nombre) }}',
                fecha: '{{ $sol->fecha_solicitud->format('d/m/Y H:i') }}',
                monto: '{{ number_format($sol->monto_solicitado, 2) }}',
                estado: '{{ $sol->estado }}'
            },
            @endforeach
        ],
        get resultados() {
            if (!this.buscarTexto) return this.autorizaciones;
            return this.autorizaciones.filter(a => {
                return a.numero.toLowerCase().includes(this.buscarTexto.toLowerCase()) || 
                       a.procedimiento.toLowerCase().includes(this.buscarTexto.toLowerCase()) || 
                       a.pss.toLowerCase().includes(this.buscarTexto.toLowerCase());
            });
        }
     }">
     
    <!-- HEADER -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 shrink-0">
                <span class="material-symbols-outlined text-2xl" data-icon="clinical_notes">clinical_notes</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800 tracking-tight">Historial de Autorizaciones Médicas</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium leading-relaxed font-sans">Consulte e interactúe con el histórico completo de coberturas y solicitudes médicas autorizadas.</p>
            </div>
        </div>
    </div>

    <!-- BUSCADOR TIPO GMAIL (INBOX SEARCH) -->
    <div class="relative w-full max-w-xl">
        <span class="material-symbols-outlined text-slate-400 absolute left-3.5 top-3.5 text-[17px]" data-icon="search">search</span>
        <input type="text" 
               x-model="buscarTexto" 
               placeholder="Buscar por número de orden, procedimiento o centro médico..." 
               class="w-full rounded-full border border-slate-200 bg-white py-3 pl-11 pr-4 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 placeholder:text-slate-450 font-semibold shadow-sm transition">
    </div>

    <!-- BANDEJA DE HISTORIAL (GMAIL INBOX LAYOUT) -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            <span>Listado de Solicitudes</span>
            <span x-text="`${resultados.length} Registro(s)`"></span>
        </div>

        <div class="divide-y divide-slate-100">
            <template x-for="sol in resultados">
                <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/50 transition duration-150 text-xs font-semibold text-slate-700">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <!-- Número -->
                            <strong class="text-slate-800 font-mono font-bold" x-text="sol.numero"></strong>
                            <span class="text-[10px] text-slate-300 font-mono">|</span>
                            <!-- Procedimiento -->
                            <span class="text-slate-600 font-medium" x-text="sol.procedimiento"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono flex flex-wrap items-center gap-x-2 gap-y-0.5">
                            <span x-text="`Fecha: ${sol.fecha}`"></span>
                            <span>•</span>
                            <span x-text="`PSS: ${sol.pss}`"></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-5 shrink-0">
                        <span class="font-mono text-slate-850 font-bold" x-text="`DOP ${sol.monto}`"></span>
                        
                        <!-- Badges Google Style -->
                        <span :class="sol.estado === 'Aprobada' ? 'bg-blue-50 text-blue-600 border-blue-100' : (
                                      sol.estado === 'Rechazada' ? 'bg-red-50 text-red-600 border-red-100' : (
                                      sol.estado === 'Auditoría' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-slate-100 text-slate-500 border-slate-200')
                                    )"
                              class="px-2.5 py-0.5 rounded-full text-[9px] font-bold border"
                              x-text="sol.estado"></span>

                        <!-- Botón alusivo Ver Detalle con icono -->
                        <a href="#" class="text-blue-600 hover:text-blue-700 font-bold flex items-center gap-1.5 hover:underline ml-2 shrink-0">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Detalle</span>
                        </a>
                    </div>
                </div>
            </template>
            <div x-show="resultados.length === 0" class="p-8 text-center text-xs text-slate-400 font-medium" x-cloak>
                No se encontraron autorizaciones que coincidan con la búsqueda.
            </div>
        </div>

        @if($autorizaciones->hasPages())
            <div class="p-3 bg-slate-50 border-t border-slate-200">
                {{ $autorizaciones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
