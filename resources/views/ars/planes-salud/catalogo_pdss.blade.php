@extends('layouts.ars')

@section('title', 'Catálogo PDSS')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Catálogo PDSS Oficial</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de prestaciones oficiales del Plan Dominicano de Seguridad Social.</p>
        </div>
    </div>

    <!-- Catálogo de Prestaciones -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-50 pb-2">
            <h3 class="font-bold text-slate-800">Prestaciones Registradas (PDSS)</h3>
            <span class="text-[10px] font-mono text-slate-400">Total: {{ count($servicios) }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Código Simón</th>
                        <th class="px-4 py-3 text-left">Código CUPS</th>
                        <th class="px-4 py-3 text-left">Descripción Cobertura</th>
                        <th class="px-4 py-3 text-left">Tipo Cobertura</th>
                        <th class="px-4 py-3 text-mono text-right">Límite Monto</th>
                        <th class="px-4 py-3 text-center">Auditoría Requerida</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white font-medium">
                    @forelse($servicios as $s)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-3 font-mono text-[#041e49] font-bold">{{ $s->simon_code }}</td>
                            <td class="px-4 py-3 font-mono text-slate-500">{{ $s->cups_code ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-850">{{ $s->coverage_description }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $s->coverage_type }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">{{ $s->amount_coverage ?? 'Ilimitada' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="material-symbols-outlined text-base {{ $s->requires_medical_audit ? 'text-amber-500' : 'text-slate-350' }}">
                                    {{ $s->requires_medical_audit ? 'lock_reset' : 'check_circle' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado prestaciones en el catálogo PDSS.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
