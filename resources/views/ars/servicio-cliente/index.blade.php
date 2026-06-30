@extends('layouts.ars')
@section('title', 'Servicio al Cliente')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Bandeja de Casos (PQRs)</h2>
            <p class="text-xs text-slate-500 font-medium">Recepción y resolución de quejas, reclamaciones y solicitudes.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Casos Registrados</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Afiliado</th>
                            <th class="px-4 py-3 text-left">Caso / Tipo</th>
                            <th class="px-4 py-3 text-center">Fecha Registro</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($casos as $cas)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">
                                    {{ $cas->affiliate->nombres ?? 'Afiliado Core' }} {{ $cas->affiliate->primer_apellido ?? '' }}
                                    <span class="block text-[9px] text-slate-400 font-normal">NSS: {{ $cas->affiliate->nss ?? '102930219' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-blue-900 block">{{ $cas->case_type }}</span>
                                    <span class="text-[9px] text-slate-500 font-normal">{{ $cas->description }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono">{{ $cas->created_at->format('d/m Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                        {{ $cas->status === 'Abierto' ? 'bg-rose-50 text-rose-700 border border-rose-250' : 'bg-emerald-50 text-emerald-700 border border-emerald-250' }}">
                                        {{ $cas->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($cas->status === 'Abierto')
                                        <button onclick="abrirResolucion('{{ $cas->id }}')" class="bg-blue-600 hover:bg-blue-750 text-white rounded-full px-3 py-1 font-bold text-[9px]">Resolver</button>
                                    @else
                                        <span class="text-[9px] text-slate-400 font-bold block">Resuelto</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Registrar PQR</h3>
            <form action="{{ route('ars.servicio_cliente.registrar_caso') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Afiliado</label>
                    <select name="affiliate_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($afiliados as $a)
                            <option value="{{ $a->id }}">{{ $a->nombres }} {{ $a->primer_apellido }} (NSS: {{ $a->nss }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Tipo Caso</label>
                    <select name="case_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="Reclamación Cobertura">Reclamación de Cobertura</option>
                        <option value="Queja por Cobro Indebido">Queja por Cobro Indebido PSS</option>
                        <option value="Solicitud Duplicado Carnet">Solicitud Duplicado de Carnet</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Descripción del Suceso</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3" placeholder="Detalle la solicitud o incidente..." required></textarea>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Registrar Ticket</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Resolucion -->
<div id="resolucion-modal" style="display:none;" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center z-50 animate-fade-in">
    <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl border border-slate-100 space-y-4">
        <h3 class="text-sm font-bold text-slate-800">Resolver Ticket PQR</h3>
        <form id="resolucion-form" action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-bold text-slate-400 mb-1.5 uppercase tracking-wider text-[9px]">Detalles de la Resolución</label>
                <textarea name="resolution_details" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Reembolso aprobado / Carnet impreso..." required></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="cerrarResolucion()" class="bg-slate-100 text-slate-650 rounded-full px-4 py-2 font-bold">Cancelar</button>
                <button type="submit" class="bg-blue-600 text-white rounded-full px-4 py-2 font-bold">Cerrar Caso</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirResolucion(id) {
        document.getElementById('resolucion-modal').style.display = 'flex';
        document.getElementById('resolucion-form').action = `/core/servicio-cliente/casos/${id}/resolver`;
    }
    function cerrarResolucion() {
        document.getElementById('resolucion-modal').style.display = 'none';
    }
</script>
@endsection