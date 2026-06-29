@extends('layouts.core')

@section('title', 'Detalle de Lote Unipago')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lote: {{ $lote->batch_number }}</h2>
            <p class="text-xs text-slate-500 font-medium">Detalle del procesamiento asíncrono ante la base mock de Unipago. Estado: <strong class="text-blue-900">{{ $lote->status }}</strong></p>
        </div>
        <div class="flex space-x-2">
            @if($lote->status === 'VE')
                <form action="{{ route('ars.unipago.lotes.procesar', $lote->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-teal-600 text-white rounded-full px-4 py-2 text-xs font-bold hover:bg-teal-700 transition shadow-xs flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Procesar Respuestas Mock</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('ars.unipago.lotes') }}" class="text-slate-600 hover:text-slate-900 border border-slate-200 rounded-full px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 transition shadow-xs">
                Volver a lotes
            </a>
        </div>
    </div>

    <!-- Detalles del Lote -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xs space-y-4">
        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-50 pb-2">
            Registros e Historial de Respuestas del Padrón
        </h3>

        <div class="border border-slate-100 rounded-2xl overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100 text-xs">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left w-24 text-[9px] uppercase tracking-wider">Lote ID</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Cédula</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Tipo Registro</th>
                        <th class="px-4 py-3 text-center text-[9px] uppercase tracking-wider">Cod. Respuesta</th>
                        <th class="px-4 py-3 text-left text-[9px] uppercase tracking-wider">Descripción del Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium text-slate-700">
                    @foreach($lote->details as $d)
                        @php
                            $persona = $d->afiliado ?: $d->dependiente;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 font-mono text-slate-400">{{ $d->id }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-slate-500">{{ $persona->cedula ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-slate-800">
                                {{ $persona->nombres ?? 'N/A' }} {{ $persona->primer_apellido ?? ($persona->apellidos ?? 'GÓMEZ') }}
                            </td>
                            <td class="px-4 py-3 uppercase text-[10px] text-slate-400">
                                {{ $d->afiliado_id ? 'Titular' : 'Dependiente' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[9px] font-bold tracking-wider
                                    {{ $d->status === 'OK' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                       ($d->status === 'PE64' || $d->status === 'PE75' || $d->status === 'PE10036' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                    {{ $d->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 font-semibold">{{ $d->reason_description ?: 'Validado Estructuralmente - En espera de procesamiento mock.' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
