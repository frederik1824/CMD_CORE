@extends('layouts.ars')
@section('title', 'Catálogo Oficial PDSS — Gestión de Prestaciones')
@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fade-in" x-data="{ showImportModal: false }">

    {{-- Breadcrumbs & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-1">
                <span>ARS Core</span><span>/</span><span>Catálogo PDSS</span><span>/</span><span class="text-gray-600">Bandeja de Prestaciones</span>
            </nav>
            <h1 class="text-2xl font-semibold text-gray-800">Catálogo de Prestaciones PDSS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Catálogo oficial del Plan de Servicios de Salud (Resolución 375-2 del CNSS)</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('ars.autorizaciones.reglas_pdss') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition bg-white shadow-sm">
                <span class="material-symbols-outlined text-sm mr-2" data-icon="settings">settings</span>
                Configurar Reglas
            </a>
            <button @click="showImportModal = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-[#0b57d0] text-white text-sm font-semibold hover:bg-[#0842a0] transition shadow-sm">
                <span class="material-symbols-outlined text-sm mr-2" data-icon="cloud_upload">cloud_upload</span>
                Importar Catálogo
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-500" data-icon="check_circle">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-700 text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-rose-500" data-icon="error">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Search & Filters Bento --}}
    <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
        <form action="{{ route('ars.pdss.catalogo') }}" method="GET" class="space-y-4">
            {{-- Text Search --}}
            <div class="relative w-full">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" data-icon="search">search</span>
                <input type="text" name="search" value="{{ $search }}" 
                       class="w-full pl-12 pr-4 py-3 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#0b57d0] text-sm text-gray-800 placeholder-gray-400 outline-none transition" 
                       placeholder="Buscar por código SIMON, CUPS, tipo de cobertura o descripción de prestación...">
            </div>

            {{-- Selects Filters --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Grupo --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Grupo PDSS</label>
                    <select name="group_id" onchange="this.form.submit()" 
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0b57d0]">
                        <option value="">-- Todos los Grupos --</option>
                        @foreach($grupos as $g)
                            <option value="{{ $g->id }}" {{ $grupoId == $g->id ? 'selected' : '' }}>
                                {{ $g->code }} - {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Subgrupo --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Subgrupo</label>
                    <select name="subgroup_id" onchange="this.form.submit()" 
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0b57d0]"
                            {{ $subgrupos->isEmpty() ? 'disabled' : '' }}>
                        <option value="">-- Todos los Subgrupos --</option>
                        @foreach($subgrupos as $sg)
                            <option value="{{ $sg->id }}" {{ $subgrupoId == $sg->id ? 'selected' : '' }}>
                                {{ $sg->code }} - {{ $sg->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo Cobertura --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Categoría Cobertura</label>
                    <select name="coverage_type" onchange="this.form.submit()" 
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0b57d0]">
                        <option value="">-- Todas las Categorías --</option>
                        @foreach($tiposCobertura as $tc)
                            <option value="{{ $tc }}" {{ $coberturaTipo == $tc ? 'selected' : '' }}>{{ $tc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nivel Cobertura --}}
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nivel de Atención Requerido</label>
                    <select name="nivel" onchange="this.form.submit()" 
                            class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#0b57d0]">
                        <option value="">-- Todos los Niveles --</option>
                        <option value="1" {{ $nivel == 1 ? 'selected' : '' }}>Nivel 1 (Atención Básica)</option>
                        <option value="2" {{ $nivel == 2 ? 'selected' : '' }}>Nivel 2 (Mediana Complejidad)</option>
                        <option value="3" {{ $nivel == 3 ? 'selected' : '' }}>Nivel 3 (Alta Especialización)</option>
                    </select>
                </div>
            </div>

            {{-- Bento Checkboxes (Filtros rápidos) --}}
            <div class="flex flex-wrap items-center gap-4 pt-2 border-t border-gray-100">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-2">Filtros rápidos:</span>
                
                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_high_cost" value="1" {{ $isHighCost ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-purple-700">Alto Costo</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_emergency" value="1" {{ $isEmergency ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-rose-700">Urgencias/Emergencias</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_hospitalization" value="1" {{ $isHospitalization ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-indigo-700">Hospitalización</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_surgery" value="1" {{ $isSurgery ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-blue-700">Cirugías</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_diagnostic_support" value="1" {{ $isDiagnosticSupport ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-emerald-700">Apoyo Diagnóstico</span>
                </label>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-100 px-3 py-1 rounded-full text-xs hover:bg-slate-100 transition select-none">
                    <input type="checkbox" name="is_medicine" value="1" {{ $isMedicine ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-[#0b57d0] focus:ring-[#0b57d0] w-3.5 h-3.5 border-gray-300">
                    <span class="font-semibold text-amber-700">Medicamentos</span>
                </label>
                
                <div class="flex-1 text-right">
                    <button type="submit" class="inline-flex items-center px-4 py-1.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 transition">
                        Filtrar
                    </button>
                    <a href="{{ route('ars.pdss.catalogo') }}" class="text-xs text-gray-400 hover:text-gray-600 font-semibold ml-3">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Main Catalog Table & Sidebar Detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        {{-- List of Services (Col Span 3) --}}
        <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden flex flex-col justify-between min-h-[500px]">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-400 font-bold uppercase border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3.5 text-left tracking-wide">SIMON</th>
                            <th class="px-6 py-3.5 text-left tracking-wide">CUPS</th>
                            <th class="px-6 py-3.5 text-left tracking-wide">Descripción</th>
                            <th class="px-6 py-3.5 text-left tracking-wide">Categoría</th>
                            <th class="px-6 py-3.5 text-center tracking-wide">Nivel 1/2/3</th>
                            <th class="px-6 py-3.5 text-left tracking-wide">Copago</th>
                            <th class="px-6 py-3.5 text-center tracking-wide">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($servicios as $srv)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3 font-mono text-xs font-bold text-[#0b57d0]">{{ $srv->simon_code }}</td>
                            <td class="px-6 py-3 font-mono text-xs text-gray-400">{{ $srv->cups_code ?: 'N/A' }}</td>
                            <td class="px-6 py-3 text-slate-800 font-medium max-w-xs truncate" title="{{ $srv->coverage_description }}">
                                {{ $srv->coverage_description }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $srv->coverage_type }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="inline-flex gap-1">
                                    <span class="w-5 h-5 rounded flex items-center justify-center text-[9px] font-bold {{ $srv->level_1_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300 border border-gray-100' }}">1</span>
                                    <span class="w-5 h-5 rounded flex items-center justify-center text-[9px] font-bold {{ $srv->level_2_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300 border border-gray-100' }}">2</span>
                                    <span class="w-5 h-5 rounded flex items-center justify-center text-[9px] font-bold {{ $srv->level_3_covered === 'S' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-300 border border-gray-100' }}">3</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $srv->copay_type ?: 'No' }}</td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('ars.pdss.show', $srv->id) }}" class="text-[#0b57d0] hover:underline font-bold text-xs inline-flex items-center gap-0.5">
                                    Ver Ficha
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center text-gray-400">
                                <span class="material-symbols-outlined text-4xl mb-2" data-icon="search_off">search_off</span>
                                <p class="text-sm">No se encontraron prestaciones en el catálogo con los filtros seleccionados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $servicios->links() }}
            </div>
        </div>

        {{-- Sidebar Panel: Logs & Detail (Col Span 1) --}}
        <div class="space-y-6">
            {{-- Resumen de Catálogo --}}
            <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Estado del Catálogo</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center">
                        <span class="text-[9px] font-semibold text-slate-400 uppercase block">Prestaciones</span>
                        <span class="text-xl font-bold text-slate-700 block">{{ number_format(\App\Models\PdssService::count()) }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center">
                        <span class="text-[9px] font-semibold text-slate-400 uppercase block">Subgrupos</span>
                        <span class="text-xl font-bold text-slate-700 block">{{ \App\Models\PdssSubgroup::count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Logs de Importación --}}
            <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-gray-50 pb-2">Bitácora de Cargas</h3>
                <div class="space-y-3">
                    @forelse($importLogs as $l)
                    <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-700 truncate max-w-[150px]">{{ $l->source_file }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase 
                                @if($l->status === 'Completado') bg-green-50 text-green-700 border border-green-150
                                @elseif($l->status === 'Error') bg-red-50 text-red-700 border border-red-150
                                @else bg-amber-50 text-amber-700 border border-amber-150
                                @endif">{{ $l->status }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400">{{ $l->created_at->format('d/m/Y H:i') }} | Páginas: {{ $l->total_pages }}</p>
                        @if($l->status === 'Completado')
                            <p class="text-[9px] text-emerald-600 font-semibold">Cargados: {{ $l->total_services }} servicios</p>
                        @elseif($l->status === 'Error')
                            <p class="text-[9px] text-rose-500 font-semibold truncate" title="{{ $l->errors }}">{{ $l->errors }}</p>
                        @endif
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center">No hay registros de cargas ejecutadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none" x-cloak>
        <div class="fixed inset-0 bg-slate-900/40 transition-opacity" @click="showImportModal = false"></div>
        
        <div class="relative w-auto my-6 mx-auto max-w-md bg-white rounded-3xl border border-slate-100 shadow-2xl p-6 space-y-6 z-50">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Importar Catálogo PDSS</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined" data-icon="close">close</span>
                </button>
            </div>
            
            <p class="text-xs text-slate-500 leading-relaxed">
                Elige el método de importación del catálogo oficial del PDSS. Se buscarán los archivos correspondientes cargados en el servidor:
            </p>

            <div class="grid grid-cols-1 gap-4">
                {{-- Import PDF --}}
                <form action="{{ route('ars.pdss.importar_pdf') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full p-4 rounded-2xl border border-gray-200 hover:bg-slate-50 flex items-center gap-4 transition text-left group">
                        <span class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-105 transition-transform select-none">
                            <span class="material-symbols-outlined text-xl" data-icon="picture_as_pdf">picture_as_pdf</span>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Importar desde Catalogo-PDSS.pdf</span>
                            <span class="text-[10px] text-slate-450 block">Parsea el PDF oficial de 581 páginas (límite demo 50 págs)</span>
                        </div>
                    </button>
                </form>

                {{-- Import CSV --}}
                <form action="{{ route('ars.pdss.importar_csv') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full p-4 rounded-2xl border border-gray-200 hover:bg-slate-50 flex items-center gap-4 transition text-left group">
                        <span class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform select-none">
                            <span class="material-symbols-outlined text-xl" data-icon="csv">description</span>
                        </span>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Importar desde pdss_catalog.csv</span>
                            <span class="text-[10px] text-slate-450 block">Carga directa desde la plantilla estructurada de 19 servicios clave</span>
                        </div>
                    </button>
                </form>
            </div>
            
            <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl flex items-start gap-2.5">
                <span class="material-symbols-outlined text-blue-500 text-sm mt-0.5" data-icon="info">info</span>
                <span class="text-[10px] text-blue-800 leading-relaxed">
                    <strong>Nota:</strong> Ambas importaciones previenen duplicados mediante la clave compuesta (plan_id + simon + subgrupo + cups).
                </span>
            </div>
        </div>
    </div>

</div>
@endsection
