@extends('layouts.ars')
@section('title', 'Solicitudes de Carnets')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Solicitudes de Carnets</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de pedidos de carnetización de afiliados.</p>
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
            <h3 class="font-bold text-slate-800">Solicitudes Registradas</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Afiliado</th>
                            <th class="px-4 py-3 text-left">Centro Impresión</th>
                            <th class="px-4 py-3 text-left">Tipo Pedido</th>
                            <th class="px-4 py-3 text-center">Fecha Pedido</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @foreach($solicitudes as $s)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-850">
                                    {{ $s->affiliate->nombres ?? 'Afiliado Demo' }} {{ $s->affiliate->primer_apellido ?? '' }}
                                    <span class="block text-[9px] text-slate-400 font-normal">NSS: {{ $s->affiliate->nss ?? '102930281' }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $s->printingCenter->name ?? 'Sede Central' }}</td>
                                <td class="px-4 py-3 text-blue-900 font-semibold">{{ $s->request_type }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $s->request_date }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                        {{ $s->status === 'Solicitado' ? 'bg-amber-50 text-amber-700 border border-amber-250' : 
                                           ($s->status === 'Impreso' ? 'bg-teal-50 text-teal-700 border border-teal-250' : 'bg-emerald-50 text-emerald-700 border border-emerald-250') }}">
                                        {{ $s->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
            <h3 class="font-bold text-slate-800">Registrar Solicitud</h3>
            <form action="{{ route('ars.carnetizacion.crear_solicitud') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Afiliado Solicitante</label>
                    <select name="affiliate_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white select-none" required>
                        @foreach($afiliados as $af)
                            <option value="{{ $af->id }}">{{ $af->nombres }} {{ $af->primer_apellido }} (NSS: {{ $af->nss }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Centro de Destino</label>
                    <select name="printing_center_id" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        @foreach($centros as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-450 mb-1.5 uppercase tracking-wider text-[9px]">Motivo del Pedido</label>
                    <select name="request_type" class="w-full rounded-full border border-slate-200 bg-slate-50/50 px-4 py-2 focus:bg-white" required>
                        <option value="Nuevo">Nueva Afiliación</option>
                        <option value="Extravío">Pérdida/Extravío</option>
                        <option value="Deterioro">Carnet Deteriorado</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#041e49] text-white rounded-full py-2.5 font-bold hover:bg-slate-800 transition">Generar Solicitud</button>
            </form>
        </div>
    </div>
</div>
@endsection