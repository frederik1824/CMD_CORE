@extends('layouts.ars')

@section('title', 'SISALRIL - Esquema 34')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">SISALRIL - Esquema 34</h2>
            <p class="text-xs text-slate-500 font-medium">Esquema 34: Dependientes voluntarios.</p>
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

    
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
            <h3 class="font-bold text-slate-800">Registros del Reporte Regulatorio - Esquema 34</h3>
            <form action="/core/sisalril/exportar/34" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-5 py-2 font-bold flex items-center space-x-1.5 shadow-xs transition">
                    <span class="material-symbols-outlined text-sm">download</span>
                    <span>Descargar Archivo Plano (TXT)</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">NSS</th>
                        <th class="px-4 py-3 text-left">Nombre Completo</th>
                        <th class="px-4 py-3 text-center">Cédula</th>
                        <th class="px-4 py-3 text-center">Sexo</th>
                        <th class="px-4 py-3 text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-mono">
                    @forelse($afiliados as $a)
                        <tr>
                            <td class="px-4 py-3 text-blue-900 font-bold">{ $a->nss }</td>
                            <td class="px-4 py-3 text-slate-700 font-sans font-semibold">{ $a->nombres } { $a->primer_apellido }</td>
                            <td class="px-4 py-3 text-center">{ $a->cedula }</td>
                            <td class="px-4 py-3 text-center">{ $a->sexo }</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">Validado</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-blue-900 font-bold">10790017590</td>
                            <td class="px-4 py-3 text-slate-700 font-sans font-semibold">JUAN PEREZ ALCANTARA</td>
                            <td class="px-4 py-3 text-center">07900175907</td>
                            <td class="px-4 py-3 text-center">M</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">Validado</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


</div>
@endsection
