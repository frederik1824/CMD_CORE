@extends('layouts.ars')

@section('title', 'Simulador SIMON')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Simulador del Portal SIMON (SISALRIL)</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja simulada del portal de control y recepción de la SISALRIL. Gestione envíos, aprobaciones y rechazos de auditoría.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 p-4 rounded-3xl flex items-center space-x-3">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabla de Envíos SIMON -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Bandeja de Submissions SIMON</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">N° Envío</th>
                        <th class="px-4 py-3 text-left">Esquema</th>
                        <th class="px-4 py-3 text-left">Periodo</th>
                        <th class="px-4 py-3 text-left">Fecha Envío</th>
                        <th class="px-4 py-3 text-center">Estado En SIMON</th>
                        <th class="px-4 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($submissions as $sub)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-bold text-[#041e49] font-mono">{{ $sub->submission_number }}</td>
                            <td class="px-4 py-3">
                                <span class="block font-bold">Esquema {{ $sub->schema?->schema_code }}</span>
                                <span class="text-[9px] text-slate-400 block font-normal">{{ $sub->schema?->name }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">{{ $sub->period?->period_code }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono">{{ $sub->submitted_at }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($sub->status === 'aprobado')
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">Aprobado</span>
                                @elseif($sub->status === 'rechazado')
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-[9px] font-bold text-rose-700 border border-rose-220">Rechazado</span>
                                @elseif($sub->status === 'validando_estructura')
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[9px] font-bold text-amber-700 border border-amber-200">Validando Estructura</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[9px] font-bold text-blue-700 border border-blue-200">Recibido</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sisalril.submission_detalle', $sub->id) }}" class="text-[#041e49] font-bold hover:underline">Auditar Detalle</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado envíos simulados en SIMON.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
