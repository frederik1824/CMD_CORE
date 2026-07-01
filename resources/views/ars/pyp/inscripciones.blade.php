@extends('layouts.ars')

@section('title', 'Inscripciones PyP')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Inscripciones PyP</h2>
            <p class="text-xs text-slate-500 font-medium">Afiliados matriculados activamente en programas.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                Ecosistema ARS
            </span>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{ session('success') }</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">error</span>
            <span class="font-semibold">{ session('error') }</span>
        </div>
    @endif

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Afiliados Inscritos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Afiliado</th>
                            <th class="px-4 py-3 text-left">Programa</th>
                            <th class="px-4 py-3 text-center">Fecha Inscripción</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($inscripciones as $i)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $i->affiliate->nombres }} {{ $i->affiliate->primer_apellido }}</td>
                                <td class="px-4 py-3 font-semibold text-blue-900">{{ $i->program->name }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $i->enrollment_date }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $i->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="abrirCancelacion('{{ $i->id }}')" class="bg-rose-50 text-rose-700 border border-rose-250 rounded-full px-2.5 py-1 text-[9px] hover:bg-rose-100 font-bold">Cancelar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400 font-semibold">No hay inscripciones activas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4" x-data="afiliadoSearch()">
            <h3 class="font-bold text-slate-800">Inscripción Manual</h3>
            <form action="{{ route('ars.pyp.inscribir_manual') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative">
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Afiliado <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            @focus="openDropdown = true" 
                            @input="openDropdown = true; selectedId = ''; fetchAfiliados()"
                            placeholder="Escriba nombre, cédula o NSS..." 
                            class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-xs transition"
                            autocomplete="off"
                            required
                        >
                        <!-- Dropdown list -->
                        <div 
                            x-show="openDropdown" 
                            @click.away="openDropdown = false"
                            class="absolute left-0 z-50 mt-1 w-full bg-white border border-slate-200 rounded-2xl shadow-lg max-h-48 overflow-y-auto py-1 text-xs"
                            x-cloak
                        >
                            <template x-if="afiliadosList.length === 0">
                                <div class="px-4 py-2 text-slate-400 text-center">
                                    Escriba para buscar afiliados...
                                </div>
                            </template>
                            <template x-for="af in afiliadosList" :key="af.id">
                                <button 
                                    type="button"
                                    @click="selectAfiliado(af)" 
                                    class="w-full text-left px-4 py-2 hover:bg-slate-50 flex flex-col space-y-0.5 border-b border-slate-50 last:border-0 transition"
                                >
                                    <span class="font-bold text-slate-800" x-text="af.nombre"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="'Céd: ' + af.cedula + ' | NSS: ' + af.nss"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <input type="hidden" name="affiliate_id" :value="selectedId" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Programa <span class="text-rose-500">*</span></label>
                    <select name="program_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white text-xs" required>
                        @foreach($programas as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs text-xs">Inscribir Afiliado</button>
            </form>
        </div>
    </div>

    <!-- Modal Cancelacion -->
    <div id="cancelacion-modal" style="display:none;" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl border border-slate-100 space-y-4">
            <h3 class="text-sm font-bold text-slate-800">Cancelar Inscripción PyP</h3>
            <form id="cancelacion-form" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Egreso</label>
                    <textarea name="cancellation_reason" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Egreso por mejoría..." required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarCancelacion()" class="bg-slate-100 text-slate-650 rounded-full px-4 py-2 font-bold">Cancelar</button>
                    <button type="submit" class="bg-rose-600 text-white rounded-full px-4 py-2 font-bold">Procesar Egreso</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirCancelacion(id) {
            document.getElementById('cancelacion-modal').style.display = 'flex';
            document.getElementById('cancelacion-form').action = `/core/pyp/inscripciones/${id}/cancelar`;
        }
        function cerrarCancelacion() {
            document.getElementById('cancelacion-modal').style.display = 'none';
        }
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('afiliadoSearch', () => ({
                searchQuery: '',
                selectedId: '',
                openDropdown: false,
                afiliadosList: [],
                fetchAfiliados() {
                    if (this.searchQuery.length < 2) {
                        this.afiliadosList = [];
                        return;
                    }
                    fetch(`/core/afiliados/buscar-ajax?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(r => r.json())
                        .then(data => {
                            this.afiliadosList = data;
                        });
                },
                selectAfiliado(af) {
                    this.selectedId = af.id;
                    this.searchQuery = af.nombre + ' (NSS: ' + af.nss + ')';
                    this.openDropdown = false;
                }
            }));
        });
    </script>


</div>
@endsection
