@extends('layouts.ars')

@section('title', 'Detalle de Lote de Reclamación')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Detalle de Lote de Reclamación</h2>
            <p class="text-xs text-slate-500 font-medium">Visualización de facturas incluidas en lote de pago.</p>
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

    
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-sm">Lote: <span class="font-mono text-blue-700">{{ $lote->batch_number }}</span></h3>
                <p class="text-xs text-slate-400">Fecha de Pago Programada: <span class="font-mono font-semibold">{{ $lote->scheduled_payment_date }}</span></p>
            </div>
            <a href="{{ route('ars.reclamaciones.lotes') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-full px-4 py-1.5 font-bold">Volver a lotes</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Monto Total Lote</span>
                <span class="text-xl font-bold text-slate-900 block mt-1 font-mono">DOP {{ number_format($lote->total_amount, 2) }}</span>
            </div>
            <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Total Items</span>
                <span class="text-xl font-bold text-slate-900 block mt-1 font-mono">{{ $lote->total_items }}</span>
            </div>
            <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase">Estado Lote</span>
                <span class="text-xl font-bold text-amber-600 block mt-1 font-semibold">{{ $lote->status }}</span>
            </div>
        </div>

        <h4 class="font-bold text-slate-800 mt-6 mb-2">Reclamaciones Asociadas a este Lote</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Claim</th>
                        <th class="px-4 py-3 text-left">PSS</th>
                        <th class="px-4 py-3 text-left">NCF</th>
                        <th class="px-4 py-3 text-right">Neto Aprobado</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($lote->items as $item)
                        @php
                            $claim = $item->accountPayable->claim;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $claim->claim_number }}</td>
                            <td class="px-4 py-3">{{ $claim->pss->nombre }}</td>
                            <td class="px-4 py-3 font-mono">{{ $claim->ncf }}</td>
                            <td class="px-4 py-3 text-right font-bold">DOP {{ number_format($item->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="corregirNcfLote('{{ $claim->id }}', '{{ $claim->claim_number }}', '{{ $claim->ncf }}')" class="bg-blue-600 text-white rounded-full px-2 py-0.5 font-bold text-[9px] hover:bg-blue-750">Corregir NCF</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-slate-400">No hay items en este lote.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Lote NCF -->
    <div id="lote-ncf-modal" style="display:none;" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl border border-slate-100 space-y-4">
            <h3 class="text-sm font-bold text-slate-800">Modificar NCF para <span id="modal-claim-num" class="font-mono text-blue-700"></span></h3>
            <form action="/core/reclamaciones/lotes/ncf/{{ $lote->id }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="claim_id" id="modal-claim-id">
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nuevo NCF</label>
                    <input type="text" name="ncf" id="modal-inp-ncf" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 font-mono" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Cambio</label>
                    <textarea name="reason" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarLoteNcf()" class="bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-full px-4 py-2 font-bold">Cancelar</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-4 py-2 font-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function corregirNcfLote(claimId, claimNum, ncf) {
            document.getElementById('lote-ncf-modal').style.display = 'flex';
            document.getElementById('modal-claim-id').value = claimId;
            document.getElementById('modal-claim-num').innerText = claimNum;
            document.getElementById('modal-inp-ncf').value = ncf;
        }
        function cerrarLoteNcf() {
            document.getElementById('lote-ncf-modal').style.display = 'none';
        }
    </script>


</div>
@endsection
