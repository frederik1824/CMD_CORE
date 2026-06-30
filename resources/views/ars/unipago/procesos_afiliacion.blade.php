@extends('layouts.ars')
@section('title', 'Procesos de Afiliación Unipago')
@section('content')
<div class="space-y-6 font-sans text-xs">
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Procesos de Afiliación (Titulares)</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de lotes de afiliados titulares transmitidos ante el padrón.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4 animate-fade-in">
        <h3 class="font-bold text-slate-800">Lotes de Afiliación de Titulares</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Código Lote</th>
                        <th class="px-4 py-3 text-left">Lote Unipago ID</th>
                        <th class="px-4 py-3 text-right">Registros</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Fecha Envío</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @foreach($lotes as $b)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-blue-900">{{ $b->batch_number }}</td>
                            <td class="px-4 py-3 font-mono">{{ $b->unipago_lote_id }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">{{ $b->total_records }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold 
                                    {{ $b->status === 'PC' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-slate-450">{{ $b->submitted_at->format('d/m Y') }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <a href="/core/unipago/lotes/{{ $b->id }}" class="bg-slate-100 hover:bg-slate-200 text-[#041e49] rounded-full px-3 py-1 font-bold text-[9px] mr-1">Detalle</a>
                                @if($b->status === 'VE')
                                    <form action="/core/unipago/lotes/{{ $b->id }}/procesar" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-3 py-1 font-bold text-[9px]">Procesar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection