@extends('layouts.ars')

@section('title', 'Nuevo Titular')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center space-x-4 border-b border-slate-200 pb-5">
        <a href="{{ route('ars.titulares.index') }}" class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-500 hover:text-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold leading-7 text-slate-900 tracking-tight">Afiliar Nuevo Titular</h2>
            <p class="mt-1 text-sm text-slate-500">Completa la información básica para registrar e iniciar el flujo de preclasificación simulada.</p>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('ars.titulares.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Sección 1: Identificación y Estado -->
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-bold text-brand-700 uppercase tracking-wider border-b border-slate-100 pb-2">1. Identificación y Régimen</h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Tipo Identificación -->
                <div>
                    <label for="tipo_identificacion_id" class="block text-xs font-semibold text-slate-500 mb-1.5">Tipo de Identificación <span class="text-rose-500">*</span></label>
                    <select name="tipo_identificacion_id" id="tipo_identificacion_id" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                        @foreach($tiposIdentificacion as $ti)
                            <option value="{{ $ti->id }}">{{ $ti->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Cédula -->
                <div>
                    <label for="cedula" class="block text-xs font-semibold text-slate-500 mb-1.5">Número de Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono" placeholder="00100000000">
                    <p class="text-[10px] text-slate-400 mt-1">El último dígito simula clasificaciones Unipago (0/1: Apto, 2: Duplicado, 3: Otra ARS, 6: Sin nómina).</p>
                </div>

                <!-- NSS -->
                <div>
                    <label for="nss" class="block text-xs font-semibold text-slate-500 mb-1.5">NSS (Seguridad Social)</label>
                    <input type="text" name="nss" id="nss" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono" placeholder="10000000000">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 font-semibold text-xs text-slate-700">
                <!-- NUI -->
                <div>
                    <label for="nui" class="block text-xs font-semibold text-slate-500 mb-1.5">NUI (Número Único de Identificación)</label>
                    <input type="text" name="nui" id="nui" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono" placeholder="30000000000">
                </div>

                <!-- Régimen -->
                <div>
                    <label for="regimen_actual" class="block text-xs font-semibold text-slate-500 mb-1.5">Régimen de Afiliación <span class="text-rose-500">*</span></label>
                    <select name="regimen_actual" id="regimen_actual" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                        @foreach($regimenes as $reg)
                            <option value="{{ $reg->descripcion }}">{{ $reg->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Número de Contrato/Formulario Unipago -->
                <div>
                    <label for="contract_number" class="block text-xs font-semibold text-slate-500 mb-1.5">Número de Contrato / Formulario</label>
                    <input type="text" name="contract_number" id="contract_number" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-805 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono" placeholder="Ej: 450000 (O vacío para autogeneración)">
                    <p class="text-[9.5px] text-slate-400 mt-1">Si se deja en blanco, el sistema asignará el próximo disponible de manera automática.</p>
                </div>
            </div>
        </div>

        <!-- Sección 2: Datos Personales -->
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4">
            <h3 class="text-sm font-bold text-brand-700 uppercase tracking-wider border-b border-slate-100 pb-2">2. Datos Personales</h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Nombres -->
                <div>
                    <label for="nombres" class="block text-xs font-semibold text-slate-500 mb-1.5">Nombres <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombres" id="nombres" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Nombres">
                </div>

                <!-- Primer Apellido -->
                <div>
                    <label for="primer_apellido" class="block text-xs font-semibold text-slate-500 mb-1.5">Primer Apellido <span class="text-rose-500">*</span></label>
                    <input type="text" name="primer_apellido" id="primer_apellido" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Primer Apellido">
                </div>

                <!-- Segundo Apellido -->
                <div>
                    <label for="segundo_apellido" class="block text-xs font-semibold text-slate-500 mb-1.5">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Segundo Apellido">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Fecha Nacimiento -->
                <div>
                    <label for="fecha_nacimiento" class="block text-xs font-semibold text-slate-500 mb-1.5">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <!-- Sexo -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-3">Sexo <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-6 mt-1">
                        <label class="inline-flex items-center text-xs font-medium text-slate-700">
                            <input type="radio" name="sexo" value="M" checked class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="ml-2">Masculino</span>
                        </label>
                        <label class="inline-flex items-center text-xs font-medium text-slate-700">
                            <input type="radio" name="sexo" value="F" class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="ml-2">Femenino</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Ubicación Geográfica Segregada (Rep. Dom. Dinámica) -->
        <div class="bg-white p-6 shadow-sm rounded-2xl border border-slate-200 space-y-4"
             x-data="{
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
                selectedProvincia: 'Distrito Nacional',
                selectedMunicipio: 'Santo Domingo de Guzmán',
                selectedSector: 'Piantini',
                tieneFormulario: false,
                
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
            <h3 class="text-sm font-bold text-brand-700 uppercase tracking-wider border-b border-slate-100 pb-2">3. Ubicación & Residencia</h3>
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Provincia (Select Dinámico) -->
                <div>
                    <label for="provincia" class="block text-xs font-semibold text-slate-500 mb-1.5">Provincia <span class="text-rose-500">*</span></label>
                    <select name="provincia" id="provincia" x-model="selectedProvincia" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                        <template x-for="prov in Object.keys(provincias)" :key="prov">
                            <option :value="prov" x-text="prov"></option>
                        </template>
                    </select>
                </div>

                <!-- Municipio (Select Dinámico) -->
                <div>
                    <label for="municipio" class="block text-xs font-semibold text-slate-500 mb-1.5">Municipio <span class="text-rose-500">*</span></label>
                    <select name="municipio" id="municipio" x-model="selectedMunicipio" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                        <template x-for="mun in municipios" :key="mun">
                            <option :value="mun" x-text="mun"></option>
                        </template>
                    </select>
                </div>

                <!-- Sector (Select Dinámico) -->
                <div>
                    <label for="sector" class="block text-xs font-semibold text-slate-500 mb-1.5">Sector / Barrio <span class="text-rose-500">*</span></label>
                    <select name="sector" id="sector" x-model="selectedSector" required class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                        <template x-for="sec in sectores" :key="sec">
                            <option :value="sec" x-text="sec"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Dirección Detallada -->
            <div>
                <label for="direccion" class="block text-xs font-semibold text-slate-500 mb-1.5">Dirección Detallada (Calle, Número, Apto, Residencial)</label>
                <textarea name="direccion" id="direccion" rows="3" class="block w-full rounded-xl border border-slate-300 p-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Ej: Calle Winston Churchill #240, Residencial Blue, Apto 402"></textarea>
            </div>

            <!-- Datos de Contacto -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2">
                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-xs font-semibold text-slate-500 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="809-000-0000">
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label for="correo" class="block text-xs font-semibold text-slate-500 mb-1.5">Correo Electrónico</label>
                    <input type="email" name="correo" id="correo" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="ejemplo@correo.com">
                </div>
            </div>

            <!-- Estatus de Expediente y Carnet -->
            <div class="border-t border-slate-100 pt-4 space-y-4">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Estatus Documental & Carnet</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- ¿Esta Carnetizado? -->
                    <div>
                        <label for="esta_carnetizado" class="block text-xs font-semibold text-slate-500 mb-1.5">Carnet Físico Entregado</label>
                        <select name="esta_carnetizado" id="esta_carnetizado" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                            <option value="0">No, pendiente de entrega</option>
                            <option value="1">Sí, entregado y vigente</option>
                        </select>
                    </div>

                    <!-- ¿Tiene Formulario Físico? -->
                    <div>
                        <label for="tiene_formulario" class="block text-xs font-semibold text-slate-500 mb-1.5">Formulario Físico Firmado</label>
                        <select name="tiene_formulario" id="tiene_formulario" x-model="tieneFormulario" class="block w-full rounded-xl border border-slate-300 py-2.5 px-3 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 bg-white">
                            <option value="false">No, pendiente de entrega</option>
                            <option value="true">Sí, recibido en archivo</option>
                        </select>
                    </div>

                    <!-- Ubicación Física -->
                    <div x-show="tieneFormulario === 'true' || tieneFormulario === true">
                        <label for="ubicacion_formulario" class="block text-xs font-semibold text-slate-500 mb-1.5">Ubicación Física en Archivo</label>
                        <input type="text" name="ubicacion_formulario" id="ubicacion_formulario" class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-xs text-slate-800 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500" placeholder="Ej: Archivero F-12, Caja 4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('ars.titulares.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 bg-white hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2.5 border border-transparent rounded-xl shadow-xs text-xs font-bold text-white transition hover:-translate-y-0.5" style="background: linear-gradient(135deg, #0b57d0 0%, #1a73e8 100%);">
                Prevalidar & Guardar Afiliación
            </button>
        </div>
    </form>
</div>
@endsection
