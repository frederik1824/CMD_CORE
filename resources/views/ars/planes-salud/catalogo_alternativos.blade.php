@extends('layouts.ars')

@section('title', 'Planes Alternativos')

@section('content')
<div class="space-y-6 font-sans text-xs animate-fade-in">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-slate-100 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Catálogo de Planes Alternativos</h2>
            <p class="text-xs text-slate-500 font-medium">Bandeja de prestaciones no reguladas por el PDSS o que corresponden a coberturas exclusivas complementarias.</p>
        </div>
    </div>

    <!-- Catálogo de Prestaciones -->
    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-800 border-b border-slate-50 pb-2">Prestaciones de Planes Alternativos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 font-bold text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Código Simón</th>
                        <th class="px-4 py-3 text-left">Código CUPS</th>
                        <th class="px-4 py-3 text-left">Descripción Cobertura</th>
                        <th class="px-4 py-3 text-left">Tipo Cobertura</th>
                        <th class="px-4 py-3 text-mono text-right">Límite Monto</th>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-semibold">No se han registrado prestaciones alternativas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
