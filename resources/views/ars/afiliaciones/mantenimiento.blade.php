@extends('layouts.ars')

@section('title', 'Mantenimiento de Afiliados')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in" x-data="{ 
    selectedAfiliado: null,
    loading: false,
    showDrawer: false,
    loadDetail(id, tipo) {
        this.loading = true;
        this.showDrawer = true;
        fetch(`/core/afiliaciones/mantenimiento/${id}/detalle-json?tipo=${tipo}`)
            .then(r => r.json())
            .then(data => {
                this.selectedAfiliado = data;
                this.loading = false;
            })
            .catch(e => {
                this.loading = false;
                alert('Error al consultar el detalle del afiliado');
            });
    }
}">
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consola de Mantenimiento de Afiliados</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja maestra de gestión contractual, geográfica y transaccional de afiliados de la ARS.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('ars.solicitudes.titulares.nueva') }}" class="bg-[#041e49] hover:bg-slate-800 text-white rounded-full px-4 py-2 font-bold shadow-xs transition inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">person_add</span>
                Nuevo Afiliado
            </a>
            <button onclick="alert('Exportando base de datos filtrada a CSV...')" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-full px-4 py-2 font-bold shadow-xs transition inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">download</span>
                Exportar
            </button>
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-xs">
        <form action="{{ route('ars.afiliaciones.mantenimiento') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Buscar -->
            <div class="relative">
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Buscar</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nombre, Cédula o NSS..." class="w-full rounded-full border border-slate-200 bg-slate-50/50 pl-8 pr-4 py-1.5 focus:bg-white text-xs focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
                </div>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Afiliado</label>
                <select name="tipo" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-1.5 text-xs focus:bg-white focus:outline-none">
                    <option value="titular" {{ $tipo === 'titular' ? 'selected' : '' }}>Titular Cotizante</option>
                    <option value="dependiente" {{ $tipo === 'dependiente' ? 'selected' : '' }}>Dependiente Directo</option>
                </select>
            </div>

            <!-- Estado -->
            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Estado</label>
                <select name="estado" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-1.5 text-xs focus:bg-white focus:outline-none">
                    <option value="">Todos los Estados</option>
                    <option value="OK" {{ $estado === 'OK' ? 'selected' : '' }}>Activo</option>
                    <option value="PE" {{ $estado === 'PE' ? 'selected' : '' }}>Pendiente</option>
                    <option value="RE" {{ $estado === 'RE' ? 'selected' : '' }}>Inactivo/Rechazado</option>
                </select>
            </div>

            <!-- Régimen -->
            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Régimen</label>
                <select name="regimen" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-1.5 text-xs focus:bg-white focus:outline-none" {{ $tipo === 'dependiente' ? 'disabled' : '' }}>
                    <option value="">Todos los Regímenes</option>
                    <option value="Contributivo" {{ $regimen === 'Contributivo' ? 'selected' : '' }}>Contributivo</option>
                    <option value="Subsidiado" {{ $regimen === 'Subsidiado' ? 'selected' : '' }}>Subsidiado</option>
                </select>
            </div>

            <!-- Botones Filtrar -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full bg-[#041e49] hover:bg-slate-800 text-white rounded-full py-1.5 font-bold shadow-xs transition text-xs">Filtrar</button>
                <a href="{{ route('ars.afiliaciones.mantenimiento') }}" class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-full py-1.5 font-bold shadow-xs transition text-xs block">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Listado de Afiliados -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Código / NUI</th>
                        <th class="px-4 py-3 text-left">Cédula</th>
                        <th class="px-4 py-3 text-left">NSS</th>
                        <th class="px-4 py-3 text-left">Nombre Completo</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-left">Régimen</th>
                        <th class="px-4 py-3 text-left">Provincia</th>
                        <th class="px-4 py-3 text-center">Formulario</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($afiliados as $af)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $af->nui ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-mono text-slate-800">{{ $af->cedula }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $af->nss ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold text-[#041e49]">{{ $tipo === 'titular' ? $af->nombre_completo : $af->nombres . ' ' . $af->apellidos }}</td>
                            <td class="px-4 py-3 capitalize">{{ $tipo }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                    {{ $af->estado_afiliacion === 'OK' ? 'bg-emerald-50 text-emerald-700 border border-emerald-250' : 
                                       ($af->estado_afiliacion === 'PE' ? 'bg-amber-50 text-amber-700 border border-amber-250' : 'bg-rose-50 text-rose-700 border border-rose-250') }}">
                                    {{ $af->estado_afiliacion === 'OK' ? 'Activo' : ($af->estado_afiliacion === 'PE' ? 'Pendiente' : 'Inactivo') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $tipo === 'titular' ? ($af->regimen_actual ?? 'Contributivo') : 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $tipo === 'titular' ? ($af->provincia ?? 'N/A') : ($af->nacionalidad ?? 'N/A') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="material-symbols-outlined text-base {{ $tipo === 'titular' && $af->tiene_formulario ? 'text-emerald-500' : 'text-slate-350' }}">
                                    {{ $tipo === 'titular' && $af->tiene_formulario ? 'assignment_turned_in' : 'assignment_late' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap space-x-1.5">
                                <button @click="loadDetail('{{ $af->id }}', '{{ $tipo }}')" class="bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-2.5 py-1 text-[9px] hover:bg-blue-100 font-bold inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">visibility</span> Ver Detalle
                                </button>
                                <a href="{{ $tipo === 'titular' ? route('ars.novedades.create', ['afiliado_id' => $af->id]) : '#' }}" class="bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-2.5 py-1 text-[9px] hover:bg-amber-100 font-bold inline-flex items-center gap-1 {{ $tipo !== 'titular' ? 'opacity-50 pointer-events-none' : '' }}">
                                    <span class="material-symbols-outlined text-[10px]">edit_note</span> Novedad
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-slate-400 font-semibold">No se encontraron afiliados con los filtros especificados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="pt-4 border-t border-slate-100">
            {{ $afiliados->links() }}
        </div>
    </div>

    <!-- Drawer Lateral de Detalle del Afiliado -->
    <div x-show="showDrawer" 
         @keydown.window.escape="showDrawer = false" 
         class="fixed inset-0 z-50 overflow-hidden" 
         style="display: none;" 
         x-cloak>
        <div class="absolute inset-0 overflow-hidden">
            <!-- Overlay de fondo -->
            <div @click="showDrawer = false" class="absolute inset-0 bg-slate-900/40 transition-opacity"></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-2xl bg-white border-l border-slate-150 flex flex-col shadow-2xl">
                    <!-- Cabecera del Drawer -->
                    <div class="px-6 py-5 bg-slate-550 text-white flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[9px] uppercase tracking-wider font-bold text-slate-200" x-text="selectedAfiliado ? 'Detalle de Afiliado - ' + selectedAfiliado.tipo : 'Detalle de Afiliado'"></span>
                            <h3 class="text-base font-bold" x-text="selectedAfiliado ? selectedAfiliado.nombre_completo : 'Cargando...'"></h3>
                        </div>
                        <button @click="showDrawer = false" class="text-white hover:text-slate-200 focus:outline-none p-1.5 rounded-full hover:bg-white/10 transition">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>

                    <!-- Cuerpo del Drawer (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Loading State -->
                        <div x-show="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
                            <div class="w-8 h-8 border-4 border-[#041e49] border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-slate-400 font-semibold">Consultando base de datos ARS...</span>
                        </div>

                        <!-- Content State -->
                        <div x-show="!loading && selectedAfiliado" class="space-y-6">
                            <!-- Datos Personales -->
                            <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3">
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] border-b border-slate-200 pb-1.5">Información General</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Cédula</span>
                                        <span class="font-semibold text-slate-700 font-mono text-[11px]" x-text="selectedAfiliado?.afiliado?.cedula || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">NSS (Seguridad Social)</span>
                                        <span class="font-semibold text-slate-700 font-mono text-[11px]" x-text="selectedAfiliado?.afiliado?.nss || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">NUI (ID Único)</span>
                                        <span class="font-semibold text-slate-700 font-mono text-[11px]" x-text="selectedAfiliado?.afiliado?.nui || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Edad / Fecha Nacimiento</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado ? selectedAfiliado.edad + ' años' : 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Sexo</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado?.afiliado?.sexo || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Teléfono</span>
                                        <span class="font-semibold text-slate-700 font-mono" x-text="selectedAfiliado?.afiliado?.telefono || 'N/A'"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Correo Electrónico</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado?.afiliado?.correo || 'N/A'"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Dirección Residencial</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado?.afiliado?.direccion || 'N/A'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Datos de Afiliación Contractual -->
                            <div class="bg-slate-50 border border-slate-150 p-4 rounded-2xl space-y-3">
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] border-b border-slate-200 pb-1.5">Estatus Contractual</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Régimen</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado?.afiliado?.regimen_actual || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Número Contrato / Póliza</span>
                                        <span class="font-semibold text-slate-700 font-mono" x-text="selectedAfiliado?.afiliado?.numero_contrato || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Provincia / Sector</span>
                                        <span class="font-semibold text-slate-700" x-text="(selectedAfiliado?.afiliado?.provincia || 'N/A') + ' / ' + (selectedAfiliado?.afiliado?.sector || 'N/A')"></span>
                                    </div>
                                    <div>
                                        <span class="block text-slate-400 font-bold uppercase tracking-wider text-[8px]">Tipo Afiliación</span>
                                        <span class="font-semibold text-slate-700" x-text="selectedAfiliado?.afiliado?.tipo_afiliacion || 'Individual'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Núcleo Familiar (Dependientes si es Titular, Titular si es Dependiente) -->
                            <div class="bg-white border border-slate-150 p-4 rounded-2xl space-y-3">
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] border-b border-slate-150 pb-1.5" x-text="selectedAfiliado?.tipo === 'titular' ? 'Dependientes Asociados (Núcleo)' : 'Titular Asociado'"></h4>
                                <div class="space-y-2">
                                    <template x-if="!selectedAfiliado?.nucleo || selectedAfiliado.nucleo.length === 0">
                                        <p class="text-slate-400 italic">No posee familiares directos registrados en el sistema.</p>
                                    </template>
                                    <template x-for="item in selectedAfiliado?.nucleo || []" :key="item.id">
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100/50 transition">
                                            <div>
                                                <span class="font-bold text-slate-800" x-text="item.nombres + ' ' + (item.apellidos || item.primer_apellido || '')"></span>
                                                <span class="block text-[9px] text-slate-400 font-mono" x-text="'Céd: ' + item.cedula + ' | NSS: ' + item.nss"></span>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[8px] font-bold text-blue-700 border border-blue-200" x-text="selectedAfiliado?.tipo === 'titular' ? 'Dependiente' : 'Titular'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Carnetización -->
                            <div class="bg-white border border-slate-150 p-4 rounded-2xl space-y-3">
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] border-b border-slate-150 pb-1.5">Historial de Carnetización</h4>
                                <div class="space-y-2">
                                    <template x-if="!selectedAfiliado?.carnets || selectedAfiliado.carnets.length === 0">
                                        <p class="text-slate-400 italic">No registra solicitudes de impresión de carnets.</p>
                                    </template>
                                    <template x-for="c in selectedAfiliado?.carnets || []" :key="c.id">
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl">
                                            <div>
                                                <span class="font-bold text-slate-800" x-text="'Tipo: ' + c.request_type"></span>
                                                <span class="block text-[9px] text-slate-400 font-mono" x-text="'Fecha: ' + c.request_date + ' | Lote: ' + (c.batch_number || 'N/A')"></span>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[8px] font-bold"
                                                    :class="c.status === 'Solicitado' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                                                    x-text="c.status"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Novedades de Afiliación -->
                            <div class="bg-white border border-slate-150 p-4 rounded-2xl space-y-3">
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[9px] border-b border-slate-150 pb-1.5">Trazabilidad de Novedades (Unipago)</h4>
                                <div class="space-y-2">
                                    <template x-if="!selectedAfiliado?.novedades || selectedAfiliado.novedades.length === 0">
                                        <p class="text-slate-400 italic">No registra novedades procesadas ante el padrón.</p>
                                    </template>
                                    <template x-for="n in selectedAfiliado?.novedades || []" :key="n.id">
                                        <div class="p-2.5 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-[#041e49]" x-text="n.tipo_novedad?.descripcion || 'Modificación'"></span>
                                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[8px] font-bold text-amber-700 border border-amber-200" x-text="n.estado"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-500" x-text="n.motivo_estado"></p>
                                            <span class="block text-[8px] text-slate-400 font-mono" x-text="'Fecha: ' + n.fecha_novedad"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
