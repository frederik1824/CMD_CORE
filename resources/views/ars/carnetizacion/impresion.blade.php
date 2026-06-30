@extends('layouts.ars')
@section('title', 'Impresión de Carnets')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Impresión de Carnets</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de impresión por lote (descuenta stocks de plástico y ribbon).</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800">Bandeja de Impresión Pendiente</h3>
        
        <form action="{{ route('ars.carnetizacion.procesar_impresion') }}" method="POST" class="space-y-4">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50 font-bold text-slate-400">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">Select</th>
                            <th class="px-4 py-3 text-left">Afiliado</th>
                            <th class="px-4 py-3 text-left">Centro de Impresión</th>
                            <th class="px-4 py-3 text-left">Motivo</th>
                            <th class="px-4 py-3 text-center">Fecha Solicitud</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white font-medium">
                        @forelse($solicitudes as $s)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="request_ids[]" value="{{ $s->id }}" class="rounded text-blue-600 focus:ring-blue-100">
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-850 block">{{ $s->affiliate->nombres }} {{ $s->affiliate->primer_apellido }}</span>
                                    <span class="text-[9px] text-slate-400">NSS: {{ $s->affiliate->nss }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-650">{{ $s->printingCenter->name }}</td>
                                <td class="px-4 py-3 text-blue-900 font-semibold">{{ $s->request_type }}</td>
                                <td class="px-4 py-3 text-center font-mono">{{ $s->request_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No hay solicitudes pendientes de impresión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($solicitudes->count() > 0)
                <div class="flex justify-end pt-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-6 py-2.5 font-bold shadow-xs">Imprimir Lote Seleccionado</button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection