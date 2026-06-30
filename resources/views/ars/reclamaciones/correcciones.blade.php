@extends('layouts.ars')

@section('title', 'Correcciones de Radicación')

@section('content')
<div class="space-y-6 font-sans animate-fade-in text-xs">
    
    <!-- Encabezado de la página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Correcciones de Radicación</h2>
            <p class="text-xs text-slate-500 font-medium">Corrección de datos de facturas en reclamaciones.</p>
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

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs">
            <h3 class="font-bold text-slate-800 mb-4">Seleccione Radicación a Corregir</h3>
            <div class="overflow-x-auto max-h-[500px]">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Claim</th>
                            <th class="px-4 py-3 text-left">PSS</th>
                            <th class="px-4 py-3 text-right">Reclamado</th>
                            <th class="px-4 py-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($radicaciones as $r)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $r->claim_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $r->pss->nombre }}</td>
                                <td class="px-4 py-3 text-right font-bold">DOP {{ number_format($r->claimed_amount, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="cargarForm('{{ $r->id }}', '{{ $r->invoice_number }}', '{{ $r->claimed_amount }}', '{{ $r->claim_number }}')" class="bg-[#041e49] text-white rounded-full px-3 py-1 font-bold text-[10px] hover:bg-slate-850">Seleccionar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs" id="form-container">
            <div class="text-center py-10 text-slate-400" id="no-selected">
                <span class="material-symbols-outlined text-4xl mb-2">edit_note</span>
                <p class="font-semibold">Seleccione una reclamación de la lista para editar sus datos.</p>
            </div>

            <form id="correction-form" style="display:none;" action="" method="POST" class="space-y-4">
                @csrf
                <h3 class="font-bold text-slate-800">Formulario de Edición: <span id="claim-name-title" class="font-mono text-blue-700"></span></h3>
                
                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Número de Factura</label>
                    <input type="text" name="invoice_number" id="inp_invoice" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:bg-white focus:outline-none" required>
                </div>

                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Monto Reclamado (DOP)</label>
                    <input type="number" step="0.01" name="claimed_amount" id="inp_amount" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:bg-white focus:outline-none" required>
                </div>

                <div>
                    <label class="block font-bold text-slate-500 mb-1.5 uppercase tracking-wider text-[9px]">Justificación de la Corrección</label>
                    <textarea name="reason" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-slate-700 focus:bg-white focus:outline-none" placeholder="Motivo del cambio de datos..." required></textarea>
                </div>

                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition shadow-xs">Guardar Cambios y Auditoría</button>
            </form>
        </div>
    </div>

    <script>
        function cargarForm(id, invoice, amount, claimNum) {
            document.getElementById('no-selected').style.display = 'none';
            const form = document.getElementById('correction-form');
            form.style.display = 'block';
            form.action = `/core/reclamaciones/radicaciones/correcciones/${id}`;
            document.getElementById('claim-name-title').innerText = claimNum;
            document.getElementById('inp_invoice').value = invoice;
            document.getElementById('inp_amount').value = amount;
        }
    </script>


</div>
@endsection
