@extends('layouts.ars')
@section('title', 'Consulta Ciudadanos SUIR')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Consultar Ciudadano en SUIR</h2>
            <p class="text-xs text-slate-500 font-medium">Buscador y validador de cédulas contra el Maestro Unipago del SUIR.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Buscar por Cédula</h3>
            <div class="space-y-4">
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Número de Cédula</label>
                    <input type="text" id="search-cedula" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2.5 font-mono" placeholder="079-0017590-7" required>
                </div>
                <button type="button" onclick="ejecutarConsulta()" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-850 transition flex items-center justify-center space-x-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-sm">search</span>
                    <span>Consultar SUIR</span>
                </button>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs flex flex-col justify-center min-h-[220px]" id="result-box">
            <div class="text-center text-slate-400 space-y-2 py-10" id="empty-state">
                <span class="material-symbols-outlined text-4xl">search_hands_free</span>
                <p class="font-semibold">Ingrese una cédula de la lista lateral o de prueba para consultar sus datos en tiempo real.</p>
            </div>
            
            <div id="loader" class="hidden text-center py-10">
                <div class="w-6 h-6 border-2 border-[#041e49] border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p class="text-[10px] text-slate-450 mt-2 font-semibold">Consultando Web Services Unipago...</p>
            </div>

            <div id="profile-card" class="hidden space-y-4">
                <div class="flex items-start justify-between border-b border-slate-50 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800" id="res-nombre">JUAN PEREZ ALCANTARA</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">Cédula: <span class="font-mono font-semibold" id="res-cedula">079-0017590-7</span></p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200" id="res-estado">Activo</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">NSS</span>
                        <span class="text-xs font-bold text-slate-800 block mt-1 font-mono" id="res-nss">10790017590</span>
                    </div>
                    <div class="bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">NUI</span>
                        <span class="text-xs font-bold text-slate-800 block mt-1 font-mono" id="res-nui">30790017590</span>
                    </div>
                    <div class="bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Régimen</span>
                        <span class="text-xs font-bold text-slate-800 block mt-1" id="res-regimen">Contributivo</span>
                    </div>
                    <div class="bg-slate-50/50 p-3 rounded-2xl border border-slate-100">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Aporte TSS</span>
                        <span class="text-xs font-bold text-emerald-600 block mt-1" id="res-aporte">Acreditado (Sí)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Cédulas del Core -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800">Afiliados del Core Disponibles para Consulta</h3>
        <div class="overflow-x-auto max-h-[220px]">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Afiliado</th>
                        <th class="px-4 py-3 text-left font-mono">Cédula</th>
                        <th class="px-4 py-3 text-center">NSS</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($afiliados as $a)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $a->nombres }} {{ $a->primer_apellido }}</td>
                            <td class="px-4 py-3 font-mono text-slate-650">{{ $a->cedula }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $a->nss }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="consultarDeLista('{{ $a->cedula }}')" class="bg-slate-100 hover:bg-slate-200 text-[#041e49] rounded-full px-3 py-1 font-bold text-[9px]">Consultar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function consultarDeLista(cedula) {
        document.getElementById('search-cedula').value = cedula;
        ejecutarConsulta();
    }

    function ejecutarConsulta() {
        const ced = document.getElementById('search-cedula').value;
        if (!ced.trim()) return alert('Ingrese una cédula.');

        document.getElementById('empty-state').classList.add('hidden');
        document.getElementById('profile-card').classList.add('hidden');
        document.getElementById('loader').classList.remove('hidden');

        fetch(`/core/unipago/consultar-cedula?cedula=${ced}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loader').classList.add('hidden');
                
                if (data.error) {
                    alert(data.error);
                    document.getElementById('empty-state').classList.remove('hidden');
                    return;
                }

                document.getElementById('profile-card').classList.remove('hidden');
                document.getElementById('res-nombre').innerText = (data.nombres + ' ' + (data.primer_apellido || '') + ' ' + (data.segundo_apellido || '')).toUpperCase();
                document.getElementById('res-cedula').innerText = data.cedula;
                document.getElementById('res-nss').innerText = data.nss || 'N/A';
                document.getElementById('res-nui').innerText = data.nui || 'N/A';
                document.getElementById('res-regimen').innerText = data.regimen_actual || 'Contributivo';
                document.getElementById('res-estado').innerText = data.estado_afiliacion || 'OK';
                document.getElementById('res-aporte').innerText = data.activo_nomina ? 'Acreditado (Sí)' : 'No registrado';
            })
            .catch(err => {
                document.getElementById('loader').classList.add('hidden');
                document.getElementById('empty-state').classList.remove('hidden');
                alert('Error al consultar el Web Service.');
            });
    }
</script>
@endsection