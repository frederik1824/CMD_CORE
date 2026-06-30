@extends('layouts.ars')
@section('title', 'Entregas de Carnets')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Entregas de Carnets</h2>
            <p class="text-xs text-slate-500 font-medium">Registro de firmas y recibo de entrega física de plásticos impresos.</p>
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
            <h3 class="font-bold text-slate-800">Registro de Entregas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Afiliado Beneficiario</th>
                            <th class="px-4 py-3 text-left">Persona que Recibe</th>
                            <th class="px-4 py-3 text-center">Fecha Entrega</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($entregas as $ent)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">{{ $ent->request->affiliate->nombres ?? 'Afiliado Core' }} {{ $ent->request->affiliate->primer_apellido ?? '' }}</td>
                                <td class="px-4 py-3 text-blue-900 font-semibold">{{ $ent->recipient_name }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $ent->delivery_date }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-200">{{ $ent->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">No hay entregas registradas en el periodo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Registrar Entrega Física</h3>
            <form action="{{ route('ars.carnetizacion.registrar_entrega') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Seleccionar Carnet Impreso</label>
                    <select name="carnet_request_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($impresos as $imp)
                            <option value="{{ $imp->id }}">{{ $imp->affiliate->nombres }} {{ $imp->affiliate->primer_apellido }} (Lote: {{ $imp->batch_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Nombre de Quien Recibe</label>
                    <input type="text" name="recipient_name" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2" placeholder="Ej. Juan Perez (Titular)" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Firma Digital Simulada</label>
                    <div class="border border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50/50 text-slate-400">
                        <span class="material-symbols-outlined text-3xl">draw</span>
                        <p class="text-[9px] font-semibold">Firma electrónica vinculada al recibo</p>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Confirmar Entrega y Cerrar</button>
            </form>
        </div>
    </div>
</div>
@endsection