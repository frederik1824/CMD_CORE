@extends('layouts.ars')

@section('title', 'Catálogos SIMON')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Catálogos Reguladores SIMON</h2>
            <p class="text-xs text-slate-500 font-medium">Buzón de catálogos y diccionarios oficiales requeridos para las validaciones cruzadas de los esquemas.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lista de Catálogos -->
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Catálogos de Validación</h3>
            <div class="space-y-2">
                @foreach($catalogos as $cat)
                    <button onclick="showCatalogItems({{ $cat->id }})" class="w-full text-left p-3 rounded-2xl border border-slate-100 hover:bg-slate-50 hover:border-slate-350 transition flex justify-between items-center group">
                        <div>
                            <span class="font-bold text-[#041e49] block font-mono text-[10px]">{{ $cat->catalog_code }}</span>
                            <span class="text-slate-500 text-[10px] font-medium leading-relaxed">{{ $cat->name }}</span>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">
                            {{ count($cat->items) }} ítems
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Visor de Ítems del Catálogo -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2 flex justify-between items-center">
                <span>Ítems del Catálogo</span>
                <span id="active-catalog-title" class="font-mono text-[#041e49] text-xs">Selecciona un catálogo...</span>
            </h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Código Ítem</th>
                            <th class="px-4 py-3 text-left">Descripción / Significado</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="catalog-items-body" class="divide-y divide-slate-100 bg-white font-medium">
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-slate-400 font-semibold">Haga clic en un catálogo lateral para inspeccionar sus códigos válidos.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const catalogData = @json($catalogos);

    function showCatalogItems(catId) {
        const cat = catalogData.find(c => c.id === catId);
        if (!cat) return;

        document.getElementById('active-catalog-title').innerText = cat.catalog_code;
        const body = document.getElementById('catalog-items-body');
        body.innerHTML = '';

        if (cat.items.length === 0) {
            body.innerHTML = '<tr><td colspan="3" class="px-4 py-8 text-center text-slate-400 font-semibold">Este catálogo no posee ítems configurados.</td></tr>';
            return;
        }

        cat.items.forEach(item => {
            body.innerHTML += `
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3 font-mono font-bold text-slate-850">${item.item_code}</td>
                    <td class="px-4 py-3 text-slate-650">${item.item_description}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                            ${item.status}
                        </span>
                    </td>
                </tr>
            `;
        });
    }
</script>
@endsection
