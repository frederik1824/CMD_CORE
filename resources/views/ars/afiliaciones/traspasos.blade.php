@extends('layouts.ars')

@section('title', 'Gestión de Traspasos')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in" x-data="afiliadoSearch()">
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Gestión de Traspasos</h2>
            <p class="text-xs text-slate-500 font-medium">Registro de traspasos inter-ARS y transferencia de consumos.</p>
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
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Registrar Traspaso -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Registrar Nuevo Traspaso</h3>
            <form action="{{ route('ars.afiliaciones.registrar_traspaso') }}" method="POST" class="space-y-4">
                @csrf
                <div class="relative">
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Afiliado <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            @focus="openDropdown = true" 
                            @input="openDropdown = true; selectedId = ''; fetchAfiliados()"
                            placeholder="Escriba nombre o cédula..." 
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
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Concepto de Traspaso <span class="text-rose-500">*</span></label>
                    <textarea name="concept" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none text-xs" placeholder="Ej. Traspaso voluntario desde ARS anterior, unificación de consumos..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition text-xs">Procesar e Inscribir</button>
            </form>
        </div>

        <!-- Historial de Traspasos -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Historial de Transacciones de Traspaso</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha</th>
                            <th class="px-4 py-3 text-left">Afiliado</th>
                            <th class="px-4 py-3 text-left">Concepto</th>
                            <th class="px-4 py-3 text-left">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($traspasos as $t)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono text-slate-500 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-slate-850 block">{{ $t->affiliate?->nombre_completo ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">Céd: {{ $t->affiliate?->cedula ?? 'N/A' }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $t->concept }}</td>
                                <td class="px-4 py-3 text-slate-500 font-mono">{{ $t->user?->name ?? 'Sistema' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado transacciones de traspaso.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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
                this.searchQuery = af.nombre + ' (Céd: ' + af.cedula + ')';
                this.openDropdown = false;
            }
        }));
    });
</script>
@endsection
