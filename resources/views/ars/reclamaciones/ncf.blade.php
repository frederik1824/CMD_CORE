@extends('layouts.ars')

@section('title', 'Bandeja de NCF y Correcciones')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Bandeja de NCF y Correcciones</h2>
            <p class="text-xs text-slate-500 font-medium">Corrección de comprobantes fiscales NCF con auditoría.</p>
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

    
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs">
        <h3 class="font-bold text-slate-800 mb-4">Corrección de NCF y Comprobante Fiscal</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Claim</th>
                        <th class="px-4 py-3 text-left">PSS</th>
                        <th class="px-4 py-3 text-left">NCF Actual</th>
                        <th class="px-4 py-3 text-center">Modificado por</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($reclamaciones as $r)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $r->claim_number }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $r->pss->nombre }}</td>
                            <td class="px-4 py-3 font-mono text-slate-600">{{ $r->ncf }}</td>
                            <td class="px-4 py-3 text-center text-slate-400 font-semibold">{{ $r->ncf_corrected_by ? 'Usuario ID ' . $r->ncf_corrected_by : 'Original' }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="abrirNcfModal('{{ $r->id }}', '{{ $r->claim_number }}', '{{ $r->ncf }}')" class="bg-blue-600 text-white rounded-full px-3 py-1 font-bold text-[10px] hover:bg-blue-750">Corregir NCF</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="py-3">
            {{ $reclamaciones->links() }}
        </div>
    </div>

    <!-- Modal de NCF -->
    <div id="ncf-modal" style="display:none;" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl border border-slate-100 space-y-4">
            <h3 class="text-sm font-bold text-slate-800">Modificar NCF para <span id="modal-claim-num" class="font-mono text-blue-700"></span></h3>
            <form id="modal-form" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Nuevo Comprobante (NCF)</label>
                    <input type="text" name="ncf" id="modal-inp-ncf" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Cambio</label>
                    <textarea name="reason" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Requerido para la DGII..." required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarNcfModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-full px-4 py-2 font-bold">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-4 py-2 font-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirNcfModal(id, claimNum, ncf) {
            document.getElementById('ncf-modal').style.display = 'flex';
            document.getElementById('modal-claim-num').innerText = claimNum;
            document.getElementById('modal-inp-ncf').value = ncf;
            document.getElementById('modal-form').action = `/core/reclamaciones/ncf/${id}`;
        }
        function cerrarNcfModal() {
            document.getElementById('ncf-modal').style.display = 'none';
        }
    </script>


</div>
@endsection
