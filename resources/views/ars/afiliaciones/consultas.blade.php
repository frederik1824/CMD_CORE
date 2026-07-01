@extends('layouts.ars')

@section('title', 'Consultas de Afiliados')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in" x-data="{
    searchQuery: '',
    results: [],
    loading: false,
    buscar() {
        if (this.searchQuery.length < 2) return;
        this.loading = true;
        fetch(`/core/afiliados/buscar-ajax?q=${encodeURIComponent(this.searchQuery)}`)
            .then(r => r.json())
            .then(data => {
                this.results = data;
                this.loading = false;
            })
            .catch(e => {
                this.loading = false;
                alert('Error al realizar la consulta');
            });
    }
}">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Centro de Consultas de Afiliados</h2>
            <p class="text-xs text-slate-500 font-medium">Búsqueda rápida y auditoría de estado de afiliados activos y dependientes directos.</p>
        </div>
    </div>

    <!-- Buscador -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Consulta Rápida</h3>
        <div class="flex gap-2">
            <input type="text" x-model="searchQuery" @keydown.enter="buscar()" placeholder="Escriba nombre completo, Cédula o NSS..." class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 text-xs transition">
            <button @click="buscar()" class="bg-[#041e49] text-white rounded-full px-6 py-2 font-bold hover:bg-slate-800 transition text-xs flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">search</span>
                Buscar
            </button>
        </div>
    </div>

    <!-- Resultados -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Resultados de la Búsqueda</h3>
        <div x-show="loading" class="flex flex-col items-center justify-center py-10 space-y-2">
            <div class="w-6 h-6 border-2 border-[#041e49] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-slate-400 font-medium">Consultando padrón de afiliados...</span>
        </div>

        <div x-show="!loading && results.length === 0" class="py-10 text-center text-slate-400 font-medium">
            Ingrese un término de búsqueda para comenzar a consultar.
        </div>

        <div x-show="!loading && results.length > 0" class="overflow-x-auto" style="display: none;">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Cédula</th>
                        <th class="px-4 py-3 text-left">NSS</th>
                        <th class="px-4 py-3 text-left">Nombre Completo</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    <template x-for="r in results" :key="r.id">
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-mono text-slate-800" x-text="r.cedula"></td>
                            <td class="px-4 py-3 font-mono text-slate-500" x-text="r.nss || 'N/A'"></td>
                            <td class="px-4 py-3 font-bold text-[#041e49]" x-text="r.nombre"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">Activo</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a :href="'/core/afiliados/titulares/' + r.id" class="bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-2.5 py-1 text-[9px] hover:bg-blue-100 font-bold inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">visibility</span> Ver Ficha Completa
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
