@extends('layouts.ars')

@section('title', 'Habilitación de Servicios PSS')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Habilitación de Servicios PSS</h2>
            <p class="text-xs text-slate-500 font-medium">Buzón de habilitación y auditoría de autorizaciones y servicios ofrecidos por prestadoras.</p>
        </div>
    </div>

    <!-- Tabla de Habilitación -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Servicios y Especialidades Habilitadas</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Prestador (PSS)</th>
                        <th class="px-4 py-3 text-left">Servicio Médico Habilitado</th>
                        <th class="px-4 py-3 text-mono text-center">Fecha Habilitación</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($servicios as $s)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-bold text-[#041e49]">{{ $s->pss?->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $s->service?->codigo ?? 'N/A' }} - {{ $s->service?->descripcion ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center text-slate-500 font-mono">{{ $s->created_at ? $s->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-250">
                                    {{ $s->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado habilitaciones de servicios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
