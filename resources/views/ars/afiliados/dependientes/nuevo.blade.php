@extends('layouts.ars')

@section('title', 'Afiliación Individual de Dependiente')

@section('content')
<div class="space-y-6" x-data="dependentWizard()">

    {{-- ── HEADER ── --}}
    <div class="sm:flex sm:items-start sm:justify-between border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold mb-1">
                <span class="material-symbols-outlined text-[14px]">group</span>
                <span>Afiliaciones</span>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-blue-600">Nuevo Dependiente</span>
            </div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight sm:text-3xl">
                Afiliación Individual de Dependiente
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Selecciona un titular activo y registra a su familiar como dependiente en el sistema.
            </p>
        </div>
        <a href="{{ route('ars.solicitudes.titulares.index') }}"
           class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition shadow-sm">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Volver a Bandeja
        </a>
    </div>

    {{-- ── STEPPER + FORM LAYOUT ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

        {{-- COLUMNA IZQUIERDA: Guía de pasos --}}
        <div class="lg:col-span-1 space-y-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Guía de registro</p>
                </div>
                <div class="p-4 space-y-1">
                    <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl transition"
                         :class="!selectedHolder ? 'bg-blue-50 text-blue-700' : 'text-slate-500'">
                        <span class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold border-2 mt-0.5"
                              :class="!selectedHolder ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-400'">1</span>
                        <div>
                            <p class="font-bold text-xs" :class="!selectedHolder ? 'text-blue-700' : 'text-slate-500'">Buscar Titular</p>
                            <p class="text-[10px] mt-0.5" :class="!selectedHolder ? 'text-blue-400' : 'text-slate-400'">Localiza al cotizante activo</p>
                        </div>
                    </div>

                    <div class="ml-6 w-px h-4 bg-slate-200 mx-auto" style="margin-left:18px"></div>

                    <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl transition"
                         :class="selectedHolder ? 'bg-blue-50 text-blue-700' : 'text-slate-400'">
                        <span class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold border-2 mt-0.5"
                              :class="selectedHolder ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-300'">2</span>
                        <div>
                            <p class="font-bold text-xs" :class="selectedHolder ? 'text-blue-700' : 'text-slate-400'">Datos del Dependiente</p>
                            <p class="text-[10px] mt-0.5" :class="selectedHolder ? 'text-blue-400' : 'text-slate-300'">Información personal y parentesco</p>
                        </div>
                    </div>

                    <div class="ml-6 w-px h-4 bg-slate-200 mx-auto" style="margin-left:18px"></div>

                    <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl text-slate-400">
                        <span class="flex-shrink-0 h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold border-2 border-slate-200 bg-white text-slate-300 mt-0.5">3</span>
                        <div>
                            <p class="font-bold text-xs text-slate-400">Enviar a Unipago</p>
                            <p class="text-[10px] mt-0.5 text-slate-300">Validación y activación</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500 text-[16px]">info</span>
                    <p class="text-xs font-bold text-blue-700">¿Quiénes son dependientes?</p>
                </div>
                <ul class="space-y-1 text-[11px] text-blue-600 font-semibold pl-1">
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[12px]">favorite</span> Cónyuge o compañero</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[12px]">child_care</span> Hijos menores de 18 años</li>
                    <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[12px]">elderly</span> Padres dependientes</li>
                </ul>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Formulario principal --}}
        <div class="lg:col-span-3">
            <form action="{{ route('ars.solicitudes.dependientes.guardar') }}" method="POST"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- ── SECCIÓN 1: BUSCAR TITULAR ── --}}
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold">1</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Titular Cotizante</h3>
                            <p class="text-[11px] text-slate-400">Busca y selecciona el afiliado titular al que se vinculará el dependiente</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Buscador --}}
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                            </div>
                            <input type="text"
                                   x-model="searchQuery"
                                   @input.debounce.350ms="searchHolder()"
                                   @focus="showResults = searchResults.length > 0"
                                   placeholder="Busca por cédula, NSS o nombre del titular activo..."
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            <template x-if="searchQuery.length >= 3 && !selectedHolder">
                                <span class="absolute right-4 top-3.5 h-4 w-4 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"
                                      x-show="searching"></span>
                            </template>
                        </div>

                        {{-- Resultados del buscador --}}
                        <div x-show="searchResults.length > 0 && showResults"
                             class="border border-slate-200 rounded-xl overflow-hidden shadow-lg bg-white divide-y divide-slate-100">
                            <template x-for="t in searchResults" :key="t.id">
                                <div @click="selectHolder(t)"
                                     class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 cursor-pointer transition group">
                                    <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-blue-600 text-[16px]">person</span>
                                    </span>
                                    <div>
                                        <p class="font-bold text-sm text-slate-800 group-hover:text-blue-700" x-text="t.text"></p>
                                        <p class="text-[10px] text-slate-400">Titular activo · Haz clic para seleccionar</p>
                                    </div>
                                    <span class="ml-auto material-symbols-outlined text-slate-300 group-hover:text-blue-500 text-[18px]">add_circle</span>
                                </div>
                            </template>
                        </div>

                        {{-- Sin resultados --}}
                        <div x-show="searchQuery.length >= 3 && searchResults.length === 0 && !selectedHolder && !searching"
                             class="flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-xs font-semibold">
                            <span class="material-symbols-outlined text-[16px]">search_off</span>
                            No se encontraron titulares activos con ese criterio.
                        </div>

                        {{-- Titular seleccionado --}}
                        <template x-if="selectedHolder">
                            <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                                <span class="h-10 w-10 rounded-full bg-emerald-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-white text-[18px]">person_check</span>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-emerald-800 text-sm truncate" x-text="selectedHolder.text"></p>
                                    <p class="text-[11px] text-emerald-600">Titular activo seleccionado correctamente</p>
                                </div>
                                <button type="button" @click="clearHolder()"
                                        class="text-emerald-400 hover:text-rose-500 transition p-1 rounded-full hover:bg-rose-50"
                                        title="Cambiar titular">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                                <input type="hidden" name="holder_id" :value="selectedHolder.id">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── SECCIÓN 2: DATOS DEL DEPENDIENTE ── --}}
                <div x-show="selectedHolder"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-xs font-bold">2</span>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Datos del Dependiente</h3>
                            <p class="text-[11px] text-slate-400">Información personal del familiar a registrar</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Fila A: Tipo y Parentesco --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">badge</span>
                                    Tipo de Identificación
                                </label>
                                <select name="tipo_identificacion_id"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    @foreach($tiposIdentificacion as $tipo)
                                        <option value="{{ $tipo->id }}" style="color:#1e293b;background:#fff">{{ $tipo->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">family_restroom</span>
                                    Relación / Parentesco
                                </label>
                                <select name="relationship"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    @foreach($parentescos as $par)
                                        <option value="{{ $par->codigo }}" style="color:#1e293b;background:#fff">{{ $par->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila B: Cédula y NSS --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">fingerprint</span>
                                    Cédula de Identidad
                                </label>
                                <input type="text" name="cedula"
                                       placeholder="Ej: 40200000021"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">tag</span>
                                    NSS Dependiente
                                    <span class="ml-1 text-[9px] font-bold bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded-full">OPCIONAL</span>
                                </label>
                                <input type="text" name="nss"
                                       placeholder="Ej: 200000001"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 font-mono transition">
                            </div>
                        </div>

                        {{-- Fila C: Nombres y Apellidos --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person</span>
                                    Nombres
                                </label>
                                <input type="text" name="nombres"
                                       placeholder="Nombres del dependiente"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">person</span>
                                    Apellidos
                                </label>
                                <input type="text" name="apellidos"
                                       placeholder="Apellidos del dependiente"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 placeholder-slate-400 transition">
                            </div>
                        </div>

                        {{-- Fila D: Fecha Nac y Sexo --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">cake</span>
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" name="fecha_nacimiento"
                                       class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                            </div>

                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">wc</span>
                                    Sexo
                                </label>
                                <select name="sexo"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-semibold text-slate-900 transition">
                                    <option value="M" style="color:#1e293b;background:#fff">Masculino</option>
                                    <option value="F" style="color:#1e293b;background:#fff">Femenino</option>
                                </select>
                            </div>
                        </div>

                        {{-- Zona de carga de documento --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[13px] text-slate-400">upload_file</span>
                                Acta de Nacimiento / Soporte de Parentesco
                                <span class="ml-1 text-[9px] font-bold bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full">REQUERIDO</span>
                            </label>
                            <label class="cursor-pointer block">
                                <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-2xl p-7 flex flex-col items-center justify-center gap-2 transition duration-200 group bg-slate-50 hover:bg-blue-50/30 relative">
                                    <span class="h-12 w-12 rounded-2xl bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center transition">
                                        <span class="material-symbols-outlined text-slate-400 group-hover:text-blue-500 text-2xl transition">cloud_upload</span>
                                    </span>
                                    <p class="font-bold text-slate-600 group-hover:text-blue-600 text-sm transition">Haz clic para subir el documento</p>
                                    <p class="text-[10px] text-slate-400">PDF, PNG o JPG · Máximo 5 MB</p>
                                    <input type="file" name="documento_acta" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ── FOOTER DE ACCIONES ── --}}
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400 font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        Los datos son cifrados y procesados de forma segura.
                    </p>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('ars.solicitudes.dependientes.index') }}"
                           class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold text-sm hover:bg-slate-50 transition shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit"
                                x-show="selectedHolder"
                                x-transition
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition">
                            <span class="material-symbols-outlined text-[16px]">send</span>
                            Enviar Solicitud a Unipago
                        </button>
                    </div>
                </div>

            </form>
        </div>{{-- /col-span-3 --}}
    </div>{{-- /grid --}}
</div>

<script>
function dependentWizard() {
    return {
        searchQuery: '',
        searchResults: [],
        selectedHolder: null,
        showResults: false,
        searching: false,

        async searchHolder() {
            if (this.searchQuery.length < 3) {
                this.searchResults = [];
                this.showResults = false;
                return;
            }
            this.searching = true;
            try {
                const res = await fetch(`/core/afiliaciones/dependientes/buscar-titular?q=${encodeURIComponent(this.searchQuery)}`);
                this.searchResults = await res.json();
                this.showResults = true;
            } catch (e) {
                console.error('Error buscando titular', e);
            } finally {
                this.searching = false;
            }
        },

        selectHolder(holder) {
            this.selectedHolder = holder;
            this.searchResults = [];
            this.showResults = false;
            this.searchQuery = '';
        },

        clearHolder() {
            this.selectedHolder = null;
            this.searchQuery = '';
            this.searchResults = [];
        }
    }
}
</script>
@endsection
