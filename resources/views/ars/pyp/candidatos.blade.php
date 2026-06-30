@extends('layouts.ars')

@section('title', 'Candidatos PyP')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Candidatos PyP</h2>
            <p class="text-xs text-slate-500 font-medium">Afiliados pre-identificados por patología listos para enrolar.</p>
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
        <h3 class="font-bold text-slate-800">Candidatos a Programas Detectados</h3>
        <p class="text-xs text-slate-400">Listado de afiliados identificados por algoritmo de riesgos para inclusión en programas.</p>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left">Programa Sugerido</th>
                        <th class="px-4 py-3 text-left">Criterio Diagnóstico</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($candidatos as $c)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-700">
                                {{ $c->affiliate->nombres }} {{ $c->affiliate->primer_apellido }}
                                <span class="block text-[9px] text-slate-400 font-normal">NSS: {{ $c->affiliate->nss }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold text-blue-900">{{ $c->program->name }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-[10px]">{{ $c->source ?? 'Algoritmo Diagnóstico' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-bold text-amber-700 border border-amber-200">{{ $c->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <form action="/core/pyp/candidatos/{{ $c->id }}/enrolar" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-full px-3 py-1 font-bold text-[9px]">Aceptar</button>
                                </form>
                                <button onclick="abrirDescarte('{{ $c->id }}')" class="bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 rounded-full px-3 py-1 font-bold text-[9px] ml-1">Descartar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400 font-semibold">No se encontraron candidatos detectados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Descarte -->
    <div id="descarte-modal" style="display:none;" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl border border-slate-100 space-y-4">
            <h3 class="text-sm font-bold text-slate-800">Descartar Candidato</h3>
            <form id="descarte-form" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Descarte</label>
                    <textarea name="reason" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Afiliado rechazó inclusión..." required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarDescarte()" class="bg-slate-100 text-slate-650 rounded-full px-4 py-2 font-bold">Cancelar</button>
                    <button type="submit" class="bg-rose-600 text-white rounded-full px-4 py-2 font-bold">Descartar Candidato</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirDescarte(id) {
            document.getElementById('descarte-modal').style.display = 'flex';
            document.getElementById('descarte-form').action = `/core/pyp/candidatos/${id}/descartar`;
        }
        function cerrarDescarte() {
            document.getElementById('descarte-modal').style.display = 'none';
        }
    </script>


</div>
@endsection
