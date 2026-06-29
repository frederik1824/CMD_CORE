<section id="buscador-red" class="py-16 sm:py-24 bg-slate-50 border-b border-slate-100"
         x-data="{
            filtroTipo: '',
            filtroProvincia: '',
            filtroMunicipio: '',
            filtroEspecialidad: '',
            buscarTexto: '',
            prestadoras: [
                @foreach($prestadoras as $p)
                {
                    nombre: '{{ addslashes($p->nombre) }}',
                    tipo: '{{ addslashes($p->tipo_entidad) }}',
                    direccion: '{{ addslashes($p->direccion) }}',
                    provincia: '{{ addslashes($p->provincia ?? 'Santo Domingo') }}',
                    municipio: '{{ addslashes($p->municipio ?? 'Santo Domingo de Guzmán') }}',
                    telefono: '{{ $p->rnc ? '809-555-' . substr($p->rnc, 0, 4) : '809-555-0199' }}',
                    servicios: ['Consulta General', 'Laboratorios', 'Imágenes Médicas', 'Urgencias 24/7']
                },
                @endforeach
            ],
            get resultados() {
                return this.prestadoras.filter(p => {
                    const coincideTipo = !this.filtroTipo || p.tipo === this.filtroTipo;
                    const coincideProv = !this.filtroProvincia || p.provincia.toLowerCase().includes(this.filtroProvincia.toLowerCase());
                    const coincideMuni = !this.filtroMunicipio || p.municipio.toLowerCase().includes(this.filtroMunicipio.toLowerCase());
                    const coincideTexto = !this.buscarTexto || p.nombre.toLowerCase().includes(this.buscarTexto.toLowerCase());
                    return coincideTipo && coincideProv && coincideMuni && coincideTexto;
                });
            }
         }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-wider text-brand-blue bg-blue-50 px-3.5 py-1.5 rounded-full">Red de Cobertura</span>
            <h2 class="font-poppins font-bold text-3xl sm:text-4xl text-slate-900 tracking-tight mt-3">Consulta nuestra red de prestadores</h2>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Ubica médicos especialistas, clínicas, laboratorios y centros diagnósticos en todo el territorio nacional.</p>
        </div>

        <!-- Search Bar and Filters Box -->
        <div class="bg-white border border-slate-200/60 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            
            <!-- Filters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs font-semibold text-slate-700">
                <!-- Search by name -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Nombre de PSS</label>
                    <input type="text" x-model="buscarTexto" placeholder="Ej: Clínica Abreu..." class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-3 bg-slate-50 text-xs">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Tipo de Servicio</label>
                    <select x-model="filtroTipo" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-3 bg-slate-50 text-xs text-slate-600">
                        <option value="">Todos los tipos</option>
                        <option value="Clínica">Clínica</option>
                        <option value="Centro Médico">Centro Médico</option>
                        <option value="Laboratorio">Laboratorio</option>
                        <option value="Consultorio">Consultorio Médico</option>
                    </select>
                </div>

                <!-- Province -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Provincia</label>
                    <select x-model="filtroProvincia" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-3 bg-slate-50 text-xs text-slate-600">
                        <option value="">Todas las provincias</option>
                        <option value="Santo Domingo">Santo Domingo</option>
                        <option value="Distrito Nacional">Distrito Nacional</option>
                        <option value="Santiago">Santiago</option>
                        <option value="La Altagracia">La Altagracia</option>
                    </select>
                </div>

                <!-- Municipio -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Municipio / Localidad</label>
                    <select x-model="filtroMunicipio" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-3 bg-slate-50 text-xs text-slate-600">
                        <option value="">Todos los municipios</option>
                        <option value="Santo Domingo de Guzmán">Santo Domingo de Guzmán</option>
                        <option value="Santo Domingo Este">Santo Domingo Este</option>
                        <option value="Santiago de los Caballeros">Santiago de los Caballeros</option>
                        <option value="Higüey">Higüey</option>
                    </select>
                </div>

                <!-- Specialty -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Especialidad</label>
                    <select x-model="filtroEspecialidad" class="block w-full rounded-xl border-slate-200 focus:border-brand-blue focus:ring-brand-blue/20 py-2.5 px-3 bg-slate-50 text-xs text-slate-600">
                        <option value="">Todas las especialidades</option>
                        <option value="pediatria">Pediatría</option>
                        <option value="cardiologia">Cardiología</option>
                        <option value="gineco">Ginecología & Obstetricia</option>
                        <option value="med_general">Medicina General</option>
                    </select>
                </div>
            </div>

            <!-- Button search/clear -->
            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="button" @click="filtroTipo = ''; filtroProvincia = ''; filtroMunicipio = ''; filtroEspecialidad = ''; buscarTexto = '';"
                        class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:text-brand-blue hover:bg-slate-100 transition">
                    Limpiar Filtros
                </button>
            </div>
        </div>

        <!-- Directory Results Grid (ProviderCard demo) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="p in resultados">
                <div class="premium-card p-6 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex justify-between items-start">
                            <span x-text="p.tipo" class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-50 text-brand-green border border-emerald-100"></span>
                            <span class="material-symbols-outlined text-slate-300" data-icon="local_hospital">local_hospital</span>
                        </div>
                        
                        <h4 x-text="p.nombre" class="font-poppins font-bold text-slate-900 text-base mt-3 leading-snug"></h4>
                        
                        <div class="space-y-2 mt-4 text-xs text-slate-500 font-medium">
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-slate-400 text-sm mt-0.5" data-icon="pin_drop">pin_drop</span>
                                <span x-text="`${p.direccion}, ${p.provincia}`"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-slate-400 text-sm" data-icon="call">call</span>
                                <span x-text="p.telefono"></span>
                            </div>
                        </div>

                        <!-- Available Services -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <span class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Servicios Disponibles:</span>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <template x-for="s in p.servicios">
                                    <span x-text="s" class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[9px] font-semibold"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="alert(`Detalles de ${p.nombre}:\nDirección: ${p.direccion}\nTeléfono: ${p.telefono}\nEstatus de red: Vigente`)"
                                class="w-full text-center py-2.5 rounded-full text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200/50 transition">
                            Ver detalles
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty status -->
        <template x-if="resultados.length === 0">
            <div class="bg-white border border-slate-150 rounded-2xl p-12 text-center shadow-sm">
                <span class="material-symbols-outlined text-slate-300 text-4xl mb-3 block animate-pulse" data-icon="search_off">search_off</span>
                <p class="text-sm font-semibold text-slate-500">No se encontraron prestadores que coincidan con los filtros aplicados.</p>
            </div>
        </template>

    </div>
</section>
