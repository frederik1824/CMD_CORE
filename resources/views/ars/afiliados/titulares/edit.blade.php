@extends('layouts.ars')

@section('title', 'Editar Titular')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 border-b border-slate-200 pb-5">
        <a href="{{ route('ars.titulares.show', $titular->id) }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight">Editar Titular: {{ $titular->nombre_completo }}</h2>
            <p class="mt-1 text-sm text-slate-500">Actualiza los datos personales o de ubicación del afiliado.</p>
        </div>
    </div>

    <!-- Formulario -->
    <!-- Formulario en AlpineJS con División Demográfica RD Dinámica -->
    <div x-data="{
        provincias: {
            'Distrito Nacional': {
                'Santo Domingo de Guzmán': ['Piantini', 'Naco', 'Bella Vista', 'Gazcue', 'Los Prados', 'La Julia', 'Ensanche Quisqueya']
            },
            'Santo Domingo': {
                'Santo Domingo Este': ['Alma Rosa', 'Ensanche Ozama', 'El Almirante', 'Lucerna', 'Las Palmas'],
                'Santo Domingo Oeste': ['Herrera', 'Las Caobas', 'El Libertador'],
                'Santo Domingo Norte': ['Villa Mella', 'Sabana Perdida', 'Guaricanos']
            },
            'Santiago': {
                'Santiago de los Caballeros': ['Los Jardines', 'Gurabo', 'El Ensueño', 'Pontezuela'],
                'Villa González': ['Centro', 'Palmar Abajo'],
                'Licey al Medio': ['Las Palomas', 'Monte Adentro']
            },
            'La Altagracia': {
                'Higüey': ['Centro', 'La Malena', 'Chilo Poueriet'],
                'Punta Cana': ['Bávaro', 'Verón', 'Uvero Alto', 'Cap Cana']
            },
            'Duarte': {
                'San Francisco de Macorís': ['Las Flores', 'El Capacito', 'Pueblo Nuevo', 'Arias']
            }
        },
        selectedProvincia: '{{ $titular->provincia ?? 'Distrito Nacional' }}',
        selectedMunicipio: '{{ $titular->municipio ?? 'Santo Domingo de Guzmán' }}',
        selectedSector: '{{ $titular->sector ?? 'Piantini' }}',
        tieneFormulario: {{ $titular->tiene_formulario ? 'true' : 'false' }},
        
        get municipios() {
            return this.provincias[this.selectedProvincia] ? Object.keys(this.provincias[this.selectedProvincia]) : [];
        },
        get sectores() {
            let mun = this.provincias[this.selectedProvincia];
            return mun && mun[this.selectedMunicipio] ? mun[this.selectedMunicipio] : [];
        },
        init() {
            this.$watch('selectedProvincia', () => {
                this.selectedMunicipio = this.municipios[0] || '';
            });
            this.$watch('selectedMunicipio', () => {
                this.selectedSector = this.sectores[0] || '';
            });
        }
    }">
        <form action="{{ route('ars.titulares.update', $titular->id) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')
            
            <!-- Datos Personales & Contacto -->
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
                <h3 class="text-sm font-bold text-blue-700 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>1. Datos Personales & Contacto</span>
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Nombres -->
                    <div>
                        <label for="nombres" class="block font-semibold text-slate-500 mb-1.5">Nombres</label>
                        <input type="text" name="nombres" id="nombres" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->nombres }}">
                    </div>

                    <!-- Primer Apellido -->
                    <div>
                        <label for="primer_apellido" class="block font-semibold text-slate-500 mb-1.5">Primer Apellido</label>
                        <input type="text" name="primer_apellido" id="primer_apellido" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->primer_apellido }}">
                    </div>

                    <!-- Segundo Apellido -->
                    <div>
                        <label for="segundo_apellido" class="block font-semibold text-slate-500 mb-1.5">Segundo Apellido</label>
                        <input type="text" name="segundo_apellido" id="segundo_apellido" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->segundo_apellido }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block font-semibold text-slate-500 mb-1.5">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->telefono }}">
                    </div>

                    <!-- Correo Electrónico -->
                    <div>
                        <label for="correo" class="block font-semibold text-slate-500 mb-1.5">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->correo }}">
                    </div>
                </div>
            </div>

            <!-- Residencia y Ubicación Geográfica Segregada (RD) -->
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
                <h3 class="text-sm font-bold text-blue-700 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>2. Ubicación de Residencia (División RD Dinámica)</span>
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Provincia (Select Dinámico) -->
                    <div>
                        <label for="provincia" class="block font-semibold text-slate-500 mb-1.5">Provincia <span class="text-rose-500">*</span></label>
                        <select name="provincia" id="provincia" x-model="selectedProvincia" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <template x-for="prov in Object.keys(provincias)" :key="prov">
                                <option :value="prov" x-text="prov" :selected="prov === selectedProvincia"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Municipio (Select Dinámico) -->
                    <div>
                        <label for="municipio" class="block font-semibold text-slate-500 mb-1.5">Municipio <span class="text-rose-500">*</span></label>
                        <select name="municipio" id="municipio" x-model="selectedMunicipio" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <template x-for="mun in municipios" :key="mun">
                                <option :value="mun" x-text="mun" :selected="mun === selectedMunicipio"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Sector (Select Dinámico) -->
                    <div>
                        <label for="sector" class="block font-semibold text-slate-500 mb-1.5">Sector / Barrio <span class="text-rose-500">*</span></label>
                        <select name="sector" id="sector" x-model="selectedSector" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <template x-for="sec in sectores" :key="sec">
                                <option :value="sec" x-text="sec" :selected="sec === selectedSector"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Dirección Detallada -->
                <div>
                    <label for="direccion" class="block font-semibold text-slate-500 mb-1.5">Dirección Detallada (Calle, Número, Apto, Residencial)</label>
                    <textarea name="direccion" id="direccion" rows="3" class="block w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Ej: Calle Winston Churchill #240, Residencial Blue, Apto 402">{{ $titular->direccion }}</textarea>
                </div>
            </div>

            <!-- Estatus Documental y Carnetización -->
            <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
                <h3 class="text-sm font-bold text-blue-700 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>3. Estatus de Carnet & Expediente Físico</span>
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- ¿Esta Carnetizado? -->
                    <div>
                        <label for="esta_carnetizado" class="block font-semibold text-slate-500 mb-1.5">Carnet Físico Entregado</label>
                        <select name="esta_carnetizado" id="esta_carnetizado" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="1" {{ $titular->esta_carnetizado ? 'selected' : '' }}>Sí, entregado y vigente</option>
                            <option value="0" {{ !$titular->esta_carnetizado ? 'selected' : '' }}>No, en proceso / pendiente</option>
                        </select>
                    </div>

                    <!-- ¿Tiene Formulario Físico? -->
                    <div>
                        <label for="tiene_formulario" class="block font-semibold text-slate-500 mb-1.5">Formulario Físico Firmado</label>
                        <select name="tiene_formulario" id="tiene_formulario" x-model="tieneFormulario" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="true">Sí, recibido en archivo</option>
                            <option value="false">No, pendiente de entrega</option>
                        </select>
                    </div>

                    <!-- Ubicación Física -->
                    <div x-show="tieneFormulario === 'true' || tieneFormulario === true">
                        <label for="ubicacion_formulario" class="block font-semibold text-slate-500 mb-1.5">Ubicación Física en Archivo</label>
                        <input type="text" name="ubicacion_formulario" id="ubicacion_formulario" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500" value="{{ $titular->ubicacion_formulario }}" placeholder="Ej: Archivero F-12, Caja 4">
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('ars.titulares.show', $titular->id) }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 border border-transparent rounded-xl shadow-xs text-xs font-bold text-white transition hover:-translate-y-0.5" style="background: linear-gradient(135deg, #0b57d0 0%, #1a73e8 100%);">
                    Actualizar Expediente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
