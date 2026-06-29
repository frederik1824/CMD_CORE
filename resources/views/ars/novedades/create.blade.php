@extends('layouts.ars')

@section('title', 'Registrar Novedad')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ 
    tipoNovedad: '', 
    afiliadoSelect: '{{ $afiliadoId ?? '' }}',
    updateCampos(e) {
        const option = e.target.options[e.target.selectedIndex];
        this.tipoNovedad = option.getAttribute('data-codigo') || '';
    }
}">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 border-b border-slate-200 pb-5">
        <a href="{{ route('ars.novedades.index') }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight">Registrar Novedad de Afiliación</h2>
            <p class="mt-1 text-sm text-slate-500">Reporta y programa modificaciones contractuales, geográficas o de estado de afiliados.</p>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('ars.novedades.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Afiliado Target -->
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-bold text-brand-700 uppercase tracking-wider border-b border-slate-100 pb-2">Afiliado Destino</h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if($afiliado)
                    <input type="hidden" name="afiliado_type" value="{{ $afiliadoType }}">
                    <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                    
                    <div>
                        <span class="block text-xs font-semibold text-slate-500 mb-1">Afiliado Seleccionado</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <h4 class="text-sm font-bold text-slate-800">{{ $afiliado->nombre_completo }}</h4>
                            <span class="text-xs text-slate-400 font-mono mt-1 block">Cédula: {{ $afiliado->cedula }} | Tipo: <span class="capitalize">{{ $afiliadoType }}</span></span>
                        </div>
                    </div>
                @else
                    <div>
                        <label for="afiliado_type" class="block text-xs font-semibold text-slate-500 mb-1.5">Tipo de Afiliado <span class="text-rose-500">*</span></label>
                        <select name="afiliado_type" id="afiliado_type" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 bg-white">
                            <option value="titular">Titular Cotizante</option>
                        </select>
                    </div>

                    <div class="relative" x-data="afiliadoSearch('{{ $afiliadoId ?? '' }}')">
                        <label for="search_input" class="block text-xs font-semibold text-slate-500 mb-1.5">Buscar y Seleccionar Titular <span class="text-rose-500">*</span></label>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            
                            <input 
                                type="text" 
                                id="search_input"
                                x-model="searchQuery" 
                                @focus="openDropdown = true" 
                                @input="openDropdown = true; selectedId = ''"
                                placeholder="Escriba nombre o cédula para buscar..." 
                                class="block w-full rounded-xl border border-slate-300 py-2.5 pl-9 pr-8 text-xs text-slate-800 bg-white focus:ring-2 focus:ring-blue-100 focus:border-brand-500 focus:outline-none transition"
                                autocomplete="off"
                            >
                            
                            <!-- Botón para limpiar selección -->
                            <button 
                                type="button" 
                                x-show="searchQuery" 
                                @click="clearSelection()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Dropdown list -->
                        <div 
                            x-show="openDropdown" 
                            @click.away="openDropdown = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute top-full left-0 z-50 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto focus:outline-none py-1 text-xs"
                            x-cloak
                        >
                            <template x-if="filteredAfiliados.length === 0">
                                <div class="px-4 py-3 text-slate-400 text-center">
                                    No se encontraron titulares
                                </div>
                            </template>
                            
                            <template x-for="af in filteredAfiliados" :key="af.id">
                                <button 
                                    type="button"
                                    @click="selectAfiliado(af)" 
                                    class="w-full text-left px-4 py-2 hover:bg-slate-50 flex flex-col space-y-0.5 border-b border-slate-50 last:border-0 transition"
                                >
                                    <span class="font-bold text-slate-800" x-text="af.nombre"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'Cédula: ' + af.cedula"></span>
                                </button>
                            </template>
                        </div>

                        <!-- Hidden input to submit the id -->
                        <input type="hidden" name="afiliado_id" :value="selectedId" required>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tipo de Novedad y Campos Dinámicos -->
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-bold text-brand-700 uppercase tracking-wider border-b border-slate-100 pb-2">Tipo de Novedad y Datos</h3>
            
            <div>
                <label for="tipo_novedad_id" class="block text-xs font-semibold text-slate-500 mb-1.5">Seleccionar Novedad <span class="text-rose-500">*</span></label>
                <select name="tipo_novedad_id" id="tipo_novedad_id" required @change="updateCampos($event)" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 bg-white">
                    <option value="">Seleccione un tipo de novedad...</option>
                    @foreach($tiposNovedad as $tn)
                        <option value="{{ $tn->id }}" data-codigo="{{ $tn->codigo }}">{{ $tn->descripcion }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bloque de Campos Dinámicos con Alpine -->
            <!-- 1. CAMBIO DE DATOS DE CONTACTO (DATO) -->
            <div x-show="tipoNovedad === 'DATO'" x-transition class="space-y-4 pt-4 border-t border-slate-100" x-cloak>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Modificar Datos de Contacto:</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="telefono" class="block text-xs font-semibold text-slate-500 mb-1.5">Nuevo Teléfono <span class="text-rose-500">*</span></label>
                        <input type="text" name="telefono" id="telefono" :required="tipoNovedad === 'DATO'" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" placeholder="809-555-0000" value="{{ $afiliado ? $afiliado->telefono : '' }}">
                    </div>
                    <div>
                        <label for="correo" class="block text-xs font-semibold text-slate-500 mb-1.5">Nuevo Correo <span class="text-rose-500">*</span></label>
                        <input type="email" name="correo" id="correo" :required="tipoNovedad === 'DATO'" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" placeholder="correo@ejemplo.com" value="{{ $afiliado ? $afiliado->correo : '' }}">
                    </div>
                </div>
            </div>

            <!-- 2. CAMBIO DE UBICACIÓN (UBICACION) -->
            <div x-show="tipoNovedad === 'UBICACION'" x-transition class="space-y-4 pt-4 border-t border-slate-100" x-cloak>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Modificar Ubicación Geográfica:</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="provincia" class="block text-xs font-semibold text-slate-500 mb-1.5">Nueva Provincia <span class="text-rose-500">*</span></label>
                        <input type="text" name="provincia" id="provincia" :required="tipoNovedad === 'UBICACION'" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" placeholder="Provincia" value="{{ $afiliado ? $afiliado->provincia : '' }}">
                    </div>
                    <div>
                        <label for="municipio" class="block text-xs font-semibold text-slate-500 mb-1.5">Nuevo Municipio <span class="text-rose-500">*</span></label>
                        <input type="text" name="municipio" id="municipio" :required="tipoNovedad === 'UBICACION'" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800" placeholder="Municipio" value="{{ $afiliado ? $afiliado->municipio : '' }}">
                    </div>
                </div>
            </div>

            <!-- 3. NOTIFICACIÓN DE FALLECE / BAJA VOLUNTARIA -->
            <div x-show="['FALLECE', 'BAJA', 'EMPLEO'].includes(tipoNovedad)" x-transition class="pt-4 border-t border-slate-100" x-cloak>
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl">
                    <p class="text-xs text-amber-700 leading-relaxed font-semibold">
                        IMPORTANTE: Al procesar esta novedad en el lote de Unipago, el afiliado seleccionado cambiará su estado de cobertura a INACTIVO/RECHAZADO en el sistema.
                    </p>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('ars.novedades.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 border border-transparent rounded-xl shadow-sm text-xs font-semibold text-white bg-brand-600 hover:bg-brand-700 transition">
                Registrar Novedad
            </button>
        </div>
    </form>
</div>

<script>
    window.searchAfiliadosData = @json($afiliados->map(fn($af) => ['id' => $af->id, 'nombre' => $af->nombre_completo, 'cedula' => $af->cedula]));
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('afiliadoSearch', (initialId = '') => ({
            searchQuery: '',
            selectedId: initialId,
            selectedName: '',
            openDropdown: false,
            afiliadosList: window.searchAfiliadosData || [],
            get filteredAfiliados() {
                if (!this.searchQuery) return this.afiliadosList;
                const q = this.searchQuery.toLowerCase();
                return this.afiliadosList.filter(af => 
                    af.nombre.toLowerCase().includes(q) || 
                    af.cedula.includes(q)
                );
            },
            selectAfiliado(af) {
                this.selectedId = af.id;
                this.selectedName = af.nombre + ' (Céd: ' + af.cedula + ')';
                this.searchQuery = af.nombre + ' (Céd: ' + af.cedula + ')';
                this.openDropdown = false;
            },
            clearSelection() {
                this.selectedId = '';
                this.selectedName = '';
                this.searchQuery = '';
            },
            init() {
                if (this.selectedId) {
                    const found = this.afiliadosList.find(af => af.id == this.selectedId);
                    if (found) this.selectAfiliado(found);
                }
            }
        }));
    });
</script>
@endsection
